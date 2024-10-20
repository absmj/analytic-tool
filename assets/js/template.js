Apex.grid = {
    padding: {
        right: 0,
        left: 0
    }
}

Apex.dataLabels = {
    enabled: false
}


const apexInsancesData = [{
        el: "#spark1",
        type: 'area',
        op: spark1
    },
    {
        el: "#spark2",
        type: 'area',
        op: spark2
    },
    {
        el: "#spark3",
        type: 'area',
        op: spark3
    },
    {
        el: "#area",
        type: 'area',
        op: optionsArea
    },
    {
        el: "#bar",
        type: 'bar',
        op: optionsBar
    },
    {
        el: "#donut",
        type: 'donut',
        op: optionDonut
    },
    {
        el: "#line",
        type: 'line',
        op: optionsLine
    }
]

const dashboard = {
    instances: [],
    async init() {
        this.instances = renderMockData(apexInsancesData)
        let k = 0;
        for(let i in this.instances) {
            $(i).parents("[data-action]").attr({
                "data-target-modal": i
            })
            await this.instances[i].render();
            const el = document.
                querySelector(i).
                parentNode.
                nextElementSibling.querySelector(`.chart-list > button[data-type=${apexInsancesData[k].type}]`)
            el.classList.remove("btn-light")
            el.classList.add("btn-primary")
            k++;
        }
    },
    updateInstance(id, data) {
        this.instances[id].destroy()
        this.instances[id] = new ApexCharts(document.querySelector(id), data)
        this.instances[id].render()
    },

    template(charts, reports) {
        const chartInstances = {}

        for(let chart of charts) {
            const {id, type, title, options} = {id: chart.chart_id, type: chart.chart_type, title: chart.title, options: chart.options}
            console.log(options)
            // options.chart.xaxis.labels.style.fontSize='10px'
            options.chart.events = {}

            options.chart.events.legendClick = function(chartContext, seriesIndex, opts) {

                const lbl = $(chartContext.el).find(`.apexcharts-legend-series[rel=${seriesIndex + 1}]`).attr("seriesname")
                for(let c of charts) {
                    if(c.chart_id == id) continue;
                    $(`#apex-${c.chart_id}`).find(`.apexcharts-legend-series[seriesname*="${lbl}"]`).find(".apexcharts-legend-marker").click()
                }
            }
            chartInstances[id] = new ApexCharts(document.querySelector(`#apex-${id}`), options)
            console.log(chartInstances[id])
            chartInstances[id].render()

        }
        return chartInstances
    }
}