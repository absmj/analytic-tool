// folderExplorer.init("folder-tree", false)

const userPanel = document.getElementById("user-panel")
const chartWizard = new bootstrap.Modal('#chart-wizard', {
        keyboard: false
})
const choosingTemplate = new bootstrap.Modal('#choosing-template', {
        keyboard: false
})

// const editor = CodeMirror.fromTextArea(document.getElementById('chart-options'), {
//     mode: 'javascript',
//     indentWithTabs: true,
//     smartIndent: true,
//     lineNumbers: true,
//     matchBrackets: true,
//     autorefresh: true
// });


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
    charts: new Map,
    // last chart
    get chart() {
        return this.charts[this.charts.length-1]
    },

    chartOptions(index) {
        return this.charts.get(index).instance.apex.opts || this.charts.get(index).instance.apex.options
    },

    get chartData() {
        return JSON.parse(editor.getValue())
    },

    set chartData(options) {
        editor.setValue(JSON.stringify(options, null, 2))
    },

    get chartPost() {

        return ([...this.charts.values()].map(c => {
            
            const {series, ...chartOptions} = this.chartOptions(c.id)
            seriesLabels = series.map(s => s.name || s.group)
            chartOptions.series = seriesLabels;
            return {
                chart_id: c.id,
                slice: JSON.stringify(c.slice),
                title: c.title,
                chart_options: JSON.stringify(chartOptions),
                chart_type: c.type,
                col_class: c.colClass,
                row_index: c.rowIndex,
                col_index: c.colIndex,
                row_class: c.rowClass
            }
        }))
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

$("#writing").on("click", ".chart-type", async function() {
    try {
        // const req = await $.get(BASE_URL +  "chart/" + $(this).attr("data-type"))
        // $("#chart-type-versions").html(req.data.view)
        const uuid = $(this).parents("[data-action]").attr("data-id")
        pageWizard.charts.set(uuid, {
            id: uuid,
            file: pageWizard.selected.file,
            folder: pageWizard.selected.folder,
            title: null,
            type: $(this).attr("data-type"),
            colClass: $(this).parents("[data-action]").attr("class"),
            rowClass: $(this).parents(".chart-row").attr("class"),
            rowIndex:  $(this).parents(".chart-row").attr("data-row-index"),
            colIndex: $(this).parents("[class^='col']").attr("data-col-index"),
            instance: null,
            slice: $(this).parent().attr("data-report-slice") || null
        })
    
        $("#add-chart").attr({
            'data-affected-element': $(this).parents("[data-action]").attr("data-target-modal"),
            'data-saving-chart-type': $(this).attr("data-type"),
            'data-uuid': uuid,
        })
    
        const chart = pageWizard.charts.get(uuid)
        
        chart.slice = JSON.parse(chart.slice)
    
        new WebDataRocks({
            container: "#wdr-component",
            toolbar: false,
            report: {
                dataSource: {
                    data: pageWizard.data
                },
                slice: chart.slice
            },
    
            reportchange: function() {
                webdatarocks.getData({}, function(e) {
                    if(chart.instance instanceof Apex) {
                        chart.instance.apex.destroy()
                    }

                    chart.slice = webdatarocks.getReport().slice;
                    $("#add-chart").attr({
                        'data-report-slice': JSON.stringify(webdatarocks.getReport().slice)
                    })
                    chart.instance = new Apex("#chart-component", e, chart.type, $(".chart-title").val() || '')
                    console.log(chart.instance[chart.type])
                    chart.instance.render()
                    $("#chart-options-form").html('');
                    generateInputs(pageWizard.chartOptions(uuid), uuid)
                })
            },
            reportcomplete: function() {
                webdatarocks.getData({}, function(e) {
                    if(chart.instance instanceof Apex) {
                        chart.instance.apex.updateOptions(pageWizard.chartOptions(uuid))
                        return;
                    }
                    chart.slice = webdatarocks.getReport().slice
                    if(!(chart.instance instanceof Apex))
                        chart.instance = new Apex("#chart-component", e, chart.type, $(".chart-title").val() || '')
                    chart.instance.render()
                    console.log(chart.instance[chart.type])
                    $("#chart-options-form").html('');
                    generateInputs(pageWizard.chartOptions(uuid), uuid)
                    $("#add-chart").attr({
                        'data-report-slice': JSON.stringify(webdatarocks.getReport().slice)
                    })
                    
                })
            }
        });
    
        chartWizard.show()
    } catch(e) {
        uiInterface.error = e?.responseJSON?.message ?? e.message
    }
})


function generateInputs(data, index, parentKey = '') {
    for (let key in data) {
        const value = data[key];
        const inputId = parentKey ? `${parentKey}-${key}` : key;
        const label = parentKey ? `${parentKey}.${key}` : key;
        // console.log(data)
        if(/^.*?data$/gsi.test(inputId)) continue;
        if(!parentKey)
            document.getElementById('chart-options-form').insertAdjacentHTML('beforeend', `<hr><h4 data-bs-toggle="collapse"  data-bs-target=".${inputId}" class="card-title pt-0 mt-0" role="button" aria-expanded="false" aria-controls="${inputId}">${key}</h4>`);

        if (typeof value === 'object') {
            // Recursively generate inputs for nested objects
            generateInputs(value, index, inputId);
        } else {
            // Generate input fields based on the type of value
            if (typeof value === 'boolean') {
                // For boolean values, generate a switcher
                const switcherHTML = `
                    <div class="collapse form-check form-switch ${parentKey.replace(/(.*?)-.*/gm, "$1")}">
                        <input onchange="changeState('${index}', () => pageWizard.chartOptions('${index}')${inputId.replace(/-*(\w+)-*/g, '.$1').replace(/\.*(\d+)/gm, "[$1]")} = this.checked)" class="form-check-input" type="checkbox" id="${inputId}">
                        <label class="form-check-label" for="${inputId}">${label}</label>
                    </div>
                `;
                document.getElementById('chart-options-form').insertAdjacentHTML('beforeend', switcherHTML);
            } else if(/#([a-zA-Z0-9])/.test(value)) {
                const colorinput = `
                    <div class="collapse mb-3 ${parentKey.replace(/(.*?)-.*/gm, "$1")}">
                        <label for="${inputId}" class="form-label">${label}</label>
                        <input onchange="changeState('${index}', () => pageWizard.chartOptions('${index}')${inputId.replace(/-*(\w+)-*/g, '.$1').replace(/\.*(\d+)/gm, "[$1]")} = this.value)" type="color" class="form-control" id="${inputId}" value="${value}">
                    </div>
                `;
                document.getElementById('chart-options-form').insertAdjacentHTML('beforeend', colorinput);
            } else if(/\d+(px)/.test(value)) {
                const rangeinput = `
                    <div class="collapse mb-3 ${parentKey.replace(/(.*?)-.*/gm, "$1")}">
                        <label for="${inputId}" class="form-label">${label}</label>
                        <input oninput="changeState('${index}', () => pageWizard.chartOptions('${index}')${inputId.replace(/-*(\w+)-*/g, '.$1').replace(/\.*(\d+)/gm, "[$1]")} = this.value)" type="range" class="form-range" id="${inputId}" value="${value}">
                    </div>
                `;
                document.getElementById('chart-options-form').insertAdjacentHTML('beforeend', rangeinput);
            } else {
                // For other types, generate a text input
                const inputHTML = `
                    <div class="collapse mb-3 ${parentKey.replace(/(.*?)-.*/gm, "$1")}">
                        <label for="${inputId}" class="form-label">${label}</label>
                        <input oninput="changeState('${index}', () => pageWizard.chartOptions('${index}')${inputId.replace(/-*(\w+)-*/g, '.$1').replace(/\.*(\d+)/gm, "[$1]")} = this.value)" type="text" class="form-control" id="${inputId}" value="${value}">
                    </div>
                `;
                document.getElementById('chart-options-form').insertAdjacentHTML('beforeend', inputHTML);
            }
        }
    }
}

$('#json-tab').on('click', function() {
    // if(!editor.getValue())
    //     editor.setValue(JSON.stringify(chart.instance[pageWizard.chart.type], null, 2))
})

$("#save-chart-option").on("click", () => {
    pageWizard.chart.instance.apex.updateOptions(pageWizard.chartData)
    $("#data-tab").click()
})

$("#chart-wizard").on('hide.bs.modal', function() {
    const uuid = $(this).find("#add-chart").attr("data-uuid")
    pageWizard.charts.get(uuid).instance.destroy()
})

$("#add-chart").on("click", function() {
    const uuidBtn = $(this).attr("data-uuid")
    const el = $($(this).attr('data-affected-element')).parents("[data-action]").find(".chart-type")
    el.addClass("btn-light")
    $($(this).attr('data-affected-element')).parents("[data-action]").find(`.chart-type[data-type="${pageWizard.charts.get(uuidBtn).type}"]`).
        removeClass("btn-light").
        addClass("btn-primary");
    $($(this).attr('data-affected-element')).parents("[data-action]").attr({
        "data-action": "added"
    })
    el.parent().attr("data-report-slice", $(this).attr("data-report-slice"))
    dashboard.updateInstance($(this).attr('data-affected-element'), pageWizard.chartOptions(uuidBtn))
    chartWizard.hide()
})



$("#main").on("click", "#save-page", async function() {
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
    pageWizard.currentStep = 1
    pageWizard.data = result
    $("#table-component").DataTable({
        responsive: true
    })
})

function changeState(index, callback) {
    callback()
    pageWizard.charts.get(index).instance.apex.updateOptions(pageWizard.chartOptions(index))
}