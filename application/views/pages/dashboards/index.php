<main id="main" class="main">
    <div class="d-flex justify-content-between align-items-center">
        <div class="pagetitle">
            <h1><?= $page['page_title'] ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Səhifələr</a></li>
                    <li class="breadcrumb-item">Dashboard</li>
                    <li class="breadcrumb-item step-description"><?= $page['page_title'] ?></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
    </div>

    <div class="row">
        <div class="col-3">
            <div class="card">
                <div style="max-height: 70vh; overflow-y: auto; overflow-x: hidden" id="playground" class="card-body">
                    <div class="mt-2 chart-type">
                        <div class="chart-playground">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="chart-interface tab-pane fade show active" role="tabpanel" id="nav-Slice" aria-labelledby="nav-Slice-tab">
                                    <form onchange="handleForm(this)" class="row">
                                        <div class="col-12">
                                            <h5>Filters</h5>
                                            <div class="row">
                                                <?php foreach (($fieldMaps) as $key => $filter) :
                                                    if (!isset($filters[$key])) continue;
                                                ?>
                                                    <div class="col-12 filter-<?= $filter ?>">
                                                        <label class="form-label d-flex justify-content-between"><?= $filter ?></label>
                                                        <select style="width: 100%;" name="<?= $filter ?>" class="form-select select2" multiple required>
                                                            <option value="">Choose...</option>
                                                            <?php foreach ($filters[$key] ?? [] as $col) : ?>
                                                                <option value="<?= $col ?>"><?= $col ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="grid-stack"></div>
        </div>
    </div>

</main>


<script>
    const charts = JSON.parse(`<?= json_encode($charts ?? [], 1) ?>`);
    var grid = GridStack.init();
    const defaultLegendClickHandler = Chart.defaults.plugins.legend.onClick;
    const pieDoughnutLegendClickHandler = Chart.controllers.doughnut.overrides.plugins.legend.onClick;
    const exactFilters = {};
    let filters = {};
    const crossCharts = new Set;
    const chartTemplate = () => {
        const uid = uuid();
        return `<canvas class='chart' data-uuid="${uid}"></canvas>`;
    }
    const items = {
        charts,
        selected: 0,

        get grids() {
            return this.charts.map(item => item.grid);
        },

        get chart() {
            return this.charts[this.selected]
        },

        get options() {
            return {
                slice: this.chart.slice,
                grid: this.chart.grid,
                options: this.chart.options,
                type: this.chart.type
            }
        },

        init(grids = this.grids) {
            for (const chart of charts) {
                chart.grid.content = chartTemplate();
            }
            initCharts(charts);
            grid.load(grids);
        }
    }

    const newLegendClickHandler = function(e, legendItem, legend) {
        const index = legendItem.datasetIndex;
        const item = legendItem;
        const chart = legend.chart
        const {
            type
        } = chart.config;
        const id = chart.canvas.id;
        const chartItem = items.charts.findIndex(item => item.id == id);
        const exactFilterField = items.charts[chartItem].slice.row && items.charts[chartItem].slice.column ? items.charts[chartItem].slice.row : (items.charts[chartItem].slice.row || items.charts[chartItem].slice.column);


        if (!exactFilters?.[exactFilterField]) {
            exactFilters[exactFilterField] = new Set;
        }

        if (!legendItem.hidden) {
            exactFilters[exactFilterField].add(legendItem.text);
        } else {
            exactFilters[exactFilterField].delete(legendItem.text);
        }

        document.querySelectorAll(`canvas:not([id="${id}"])`).forEach(element => {
            const otherChartId = element.id;
            const otherChart = items.charts.find(item => item.id == otherChartId);

            const otherExactFilterField = otherChart.slice.row && otherChart.slice.column ? otherChart.slice.row : (otherChart.slice.row || otherChart.slice.column);
            if (otherExactFilterField == exactFilterField) {
                console.log(otherChart)
                const otherChartType = otherChart.instance.config.type;
                const otherChartInstance = otherChart.instance;
                if (otherChartType === 'pie' || otherChartType === 'doughnut') {
                    // Pie and doughnut charts only have a single dataset and visibility is per item
                    otherChartInstance.toggleDataVisibility(legendItem.index);
                } else {
                    otherChartInstance.setDatasetVisibility(item.datasetIndex, !otherChartInstance.isDatasetVisible(item.datasetIndex));
                }
                otherChartInstance.update();
            } else {
                crossCharts.add(otherChartId);
            }
        })

        if (type === 'pie' || type === 'doughnut') {
            // Pie and doughnut charts only have a single dataset and visibility is per item
            chart.toggleDataVisibility(legendItem.index);
        } else {
            chart.setDatasetVisibility(item.datasetIndex, !chart.isDatasetVisible(item.datasetIndex));
        }
        chart.update();

        const crossCh = items.charts.filter(c => crossCharts.has(c.id))

        initCharts(crossCh.map(ch => ({
            slice: ch.slice,
            options: ch.options,
            grid: ch.grid,
            id: ch.id
        })))
    };

    async function initCharts(charts) {
        try {
            const ef = {};
            for (const field in exactFilters) {
                if (exactFilters[field] && exactFilters[field]?.size)
                    ef[field] = [...exactFilters[field]];
            }

            const request = await fetch(`/chart/pivot/<?= $page['report_table'] ?>`, {
                method: 'POST',
                body: JSON.stringify({
                    charts: charts.map(item => ({
                        ...item,
                        slice: {
                            ...item.slice,
                            filter: filters,
                            filters: Object.keys(filters),
                            exactFilters: ef
                        }
                    }))
                })
            });
            const response = await request.json();
            const responseCharts = response.data.charts;
            for (const chart of items.charts) {
                const responsedChart = responseCharts.find(item => item.id == chart.id);
                if (responsedChart && responsedChart.hasOwnProperty('options')) {
                    const {
                        options
                    } = responsedChart;

                    const {
                        datasets,
                        labels
                    } = options.data;
                    const chartElement = document.querySelector(`[data-uuid="${chart.grid.content?.replace(/.*data-uuid=['"](.*?)['"].*/si, "$1")}"]`);
                    chartElement.id = chart.id;
                    if (chart?.instance?.config?.type != chart.type && chart.instance instanceof Chart) {
                        chart.instance.destroy();
                        chart.instance = null;
                    }
                    console.log("dataset", datasets);
                    if (!(chart.instance instanceof Chart)) {
                        options.options.plugins.legend.onClick = newLegendClickHandler;
                        chart.instance = new Chart(chartElement, options);
                    } else {
                        console.log("sadds", chart)
                        _.set(chart.instance, 'data', {
                            datasets,
                            labels
                        });
                        chart.instance.update();
                    }
                }
            }
            // items.charts = responseCharts;
        } catch (e) {
            console.error(e)
        }
    }

    async function handleForm(form) {
        const formdata = new FormData(form);
        filters = {};
        for (const [key, value] of formdata) {
            filters[key] = $(`[name="${key}"]`).val();
        }

        initCharts(items.charts.map(ch => ({
            slice: ch.slice,
            options: ch.options,
            grid: ch.grid,
            id: ch.id
        })))
    }

    document.addEventListener("DOMContentLoaded", () => {
        $(".toggle-sidebar-btn").click();
        items.init();
        $('.select2').select2();

        $("#filters").change(function() {
            $(`[class^='filter'`).addClass("d-none");
            for (const val of $(this).val()) {
                $(`.filter-${val}`).removeClass("d-none")
            }
        })
    })
</script>