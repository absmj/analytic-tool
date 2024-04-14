// folderExplorer.init("folder-tree", false)

const userPanel = document.getElementById("user-panel")
const chartWizard = new bootstrap.Modal('#chart-wizard', {
        keyboard: false
})
const choosingTemplate = new bootstrap.Modal('#choosing-template', {
        keyboard: false
})

const editor = CodeMirror.fromTextArea(document.getElementById('chart-options'), {
    mode: 'javascript',
    indentWithTabs: true,
    smartIndent: true,
    lineNumbers: true,
    matchBrackets: true,
    autorefresh: true
});


const pageWizard = {
    steps: [
        "Hesabatın seçilməsi", 
        "Hesabatla tanış olmaq", 
        "Dashboard şablonunun seçilməsi", 
        "Çartların/Səhifənin hazırlanması",
        "Səhifəyə baxış imkanlarının məhdudlaşdırılması"
    ],
    template: null,
    step: 0,
    stepDescription: $(".step-description"),
    selected: {
        folder: null,
        file: null
    },
    pivot: null,
    data: null,
    table: null,
    charts: [],
    // last chart
    get chart() {
        return this.charts[this.charts.length-1]
    },

    get chartPost() {
        return (this.charts.map(c => ({
            chart_id: c.id,
            slice: JSON.stringify(c.slice),
            title: c.title,
            chart_type: c.type,
            col_class: c.colClass,
            row_index: c.rowIndex,
            row_class: c.rowClass
        })))
    },

    // set last chart
    set chart(data) {
        this.charts.splice(-1, 1, data)
    },

    get currentStep() {
        return this.steps[this.step]
    },

    set currentStep(step) {
        if (Number.isInteger(step)) {
            this.step = step
            $("[stage]").attr("stage", step)
            this.stepDescription.text(this.currentStep)
        }
    },

    set folder(folder) {
        this.selected.folder = folder
    },

    set file(file) {
        this.selected.file = file
    }
}

$("[name='folder']").on("change", async (e) => {
    try {
        uiInterface.loading = true
        if (!$("body").hasClass("toggle-sidebar"))
            $(".toggle-sidebar-btn").click();

        // $("body").removeClass('toggle-sidebar');
        const response = await fetch(`${BASE_URL}folder/${e.target.value}/files`)
        if (!response.ok) {
            uiInterface.error.serverSide = true
            throw new Error("Error happened when getting file")
        }
        pageWizard.folder = e.target.value
        const file = await response.json()
        userPanel.innerHTML = file.data.view

    } catch (e) {
        uiInterface.error = e.message
    } finally {
        uiInterface.loading = false
    }
})

$("#action").on("click", ".file-explorer-file", async function() {
    try {
        uiInterface.loading = true
        $(".file-explorer-file").attr("data-selected", 0)
        $(this).attr("data-selected", 1)
        const response = await fetch(`${BASE_URL}files/get?file=${$(this).attr("file")}`)
        const file = await response.json()
        if(!response.ok) {
            uiInterface.error.serverSide = true
            throw new Error(file.message)
        }
        pageWizard.data = file.data
        pageWizard.file = $(this).attr("file-id")
        pageWizard.table = $("#table-component").DataTable({
            data: pageWizard.data,
            columns: Object.keys(file.data[0]).map(t => ({
                data: t,
                title: t
            }))
        })

        $("#fexplorer").addClass("d-none")
        pageWizard.currentStep = 1
    } catch (e) {
        uiInterface.error = e.message
    } finally {
        uiInterface.loading = false
    }
})

$("#next").click(() => {
    // $("#reading").addClass("d-none")
    // $("#writing").removeClass("d-none")
    pageWizard.currentStep = 3
    choosingTemplate.show()
})

$("[data-template]").on("click", async function() {
    try {
        uiInterface.loading = true
        pageWizard.template = $(this).attr("data-template")
        const request = await fetch(`${BASE_URL}dashboard/template/${$(this).attr("data-template")}`)
        const response = await request.json()

        if(!request.ok) {
            uiInterface.error.serverSide = true
            throw new Error("Error happened when getting template")
        }

        $("#reading").addClass("d-none")
        $("#writing").removeClass("d-none")
        $("#dashboard").html(response.data.view)
        dashboard.init()
        choosingTemplate.hide()

        pageWizard.currentStep = 3
    } catch(e) {
        uiInterface.error = e.message
    } finally {
        uiInterface.loading = false
    }
})

$("#writing").on("click", ".chart-type", function() {
    pageWizard.charts.push({
        id: `apex-chart-${uuid()}`,
        file: pageWizard.selected.file,
        folder: pageWizard.selected.folder,
        title: null,
        type: $(this).attr("data-type"),
        colClass: $(this).parents("[data-action]").attr("class"),
        rowClass: $(this).parents(".chart-row").attr("class"),
        rowIndex:  ($(this).parents(".chart-row").index() + 1) - $(".chart-row").length,
        instance: null,
        slice: null
    })

 

    $("#add-chart").attr({
        'data-affected-element': $(this).parents("[data-action]").attr("data-target-modal"),
        'data-saving-chart-type': $(this).attr("data-type")
    })

    pageWizard.pivot = new WebDataRocks({
        container: "#wdr-component",
        toolbar: false,
        report: {
            dataSource: {
                data: pageWizard.data
            }
        },
        reportcomplete: function() {
            webdatarocks.getData({}, function(e) {
                if(pageWizard.chart.instance instanceof Apex) {
                    pageWizard.chart.instance.destroy()
                    editor.setValue('')
                }
                pageWizard.chart.slice = webdatarocks.getReport().slice
                pageWizard.chart.instance = new Apex("#chart-component", e, pageWizard.chart.type, $(".chart-title").val() || 'Untitled')
                
                pageWizard.chart.instance.render()
                
            })
        }
    });


    chartWizard.show()

})

$('#json-tab').on('click', function() {
    if(!editor.getValue())
        editor.setValue(JSON.stringify(pageWizard.chart.instance[pageWizard.chart.type], null, 2))
})

$("#save-chart-option").on("click", () => {
    pageWizard.chart.instance.update(editor.getValue())
    $("#data-tab").click()
})

$("#chart-wizard").on('hide.bs.modal', () => {
    pageWizard.chart.instance.destroy()
})

$("#add-chart").on("click", function() {
    $($(this).attr('data-affected-element')).parents("[data-action]").find(".chart-type").addClass("btn-light")
    $($(this).attr('data-affected-element')).parents("[data-action]").find(`.chart-type[data-type="${pageWizard.chart.type}"]`).
        removeClass("btn-light").
        addClass("btn-primary");
    $($(this).attr('data-affected-element')).parents("[data-action]").attr({
        "data-action": "added"
    })
    dashboard.updateInstance($(this).attr('data-affected-element'), pageWizard.chart.instance[pageWizard.chart.type])
    chartWizard.hide()
})

$(".chart-title").on("input", function(){
    pageWizard.chart.instance.title = $(this).val()
    pageWizard.chart.instance.apex.updateOptions(pageWizard.chart.instance[pageWizard.chart.type])
})

$("#dashboard").on("click", "#save-page", async function() {
    try {
        uiInterface.loading = true

        if(pageWizard.chartPost.length == 0) {
            throw new Error("Ən azı bir çart yaradılmalıdır. Nümunədəki çartlar səhifə yaratmaq üçün əsas sayılmır")
        }

        const request = await fetch(`?template=${pageWizard.template}`, {
            method: "POST",
            body: JSON.stringify(pageWizard.chartPost),
            headers: {
                "Content-Type": "application/json"
            } 
        })

        const response = await request.json() 
        if(!request.ok) {
            uiInterface.error.serverSide = true
            throw new Error(response.error)
        }
        uiInterface.success = true
    } catch(e) {
        uiInterface.error = e.message
    } finally {
        uiInterface.loading = false
    }
})

document.addEventListener("DOMContentLoaded", () => {
    pageWizard.currentStep = 0
})