<style>
    .grid-stack {
        background: #FAFAD2;
    }

    .grid-stack-item-content:hover {
        box-shadow: 2px 2px 10px rgba(255, 0, 0, 0.3);
    }

    .grid-stack-item-content.active {
        border: 2px solid rgba(255, 0, 0, 0.5);
    }

    .grid-stack-item-content {
        border: 2px solid rgba(255, 0, 0, 0.2);
    }

    .grid-stack-item-content {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    [chart-type]:is([data-selected='true']) {
        background-color: var(--bs-btn-color);
        color: var(--bs-btn-active-color);
    }

    #playground :has(:not([data-selected='true']))+.chart-interface {
        display: none;
    }

    #playground :has(:is([data-selected='true']))+.chart-interface {
        display: block;
    }
</style>

<main id="main" class="main">
    <div class="d-flex justify-content-between align-items-center">
        <div class="pagetitle">
            <h1><?= $this->title ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Səhifələr</a></li>
                    <li class="breadcrumb-item"><?= $this->title ?></li>
                    <li class="breadcrumb-item step-description"></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="d-flex justify-content-end">
            <button onclick="actions.addGrid.apply(items)" type="button" class="mt-2 mr-2 btn btn-sm btn-primary"><i class="bi bi-plus"></i></button>
            <button stage="2" type="button" id="save-page" class="btn btn-success">Səhifəni yadda saxla</button>
        </div>
    </div>
    <div id="error" class="alert alert-danger alert-dismissible d-none" role="alert">
        <template id="error"></template>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <section class="section contact">
    </section>

    <div class="row" id="reading">
        <!-- <div class="col-6 col-md-2">
            <div class="card">
                <div class="card-header">
                    Qovluq seçin
                </div>
                <div class="card-body">
                    <div class="folder-tree mt-4" id="folder-tree"></div>
                </div>
            </div>
        </div> -->
        <!-- <div class="col-12">
            <div id="action" class="card">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="card-header step-description">
                    </div>
                    <button id="next" stage="0" type="button" class="btn btn-sm btn-primary me-3"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div id="user-panel" class="card-body">
                    <?php //$this->view("/pages/files/index") 
                    ?>
                </div>
            </div>
        </div> -->
    </div>

    <div class="row page-wizard d-none" id="writing">
        <div class="col-12" id="dashboard">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        <button id="next" stage="1" type="button" class="mt-2 btn btn-sm btn-primary"><i class="bi bi-chevron-right"></i></button>
                    </div>
                    <div class="table-responsive">
                        <?php if (count($result ?? []) > 0): ?>
                            <table id="table-component" class="table table-hover display">
                                <thead>
                                    <tr>
                                        <?php foreach (array_keys($result[0]) as $th) : ?>
                                            <th><?= $th ?></th>
                                        <?php endforeach ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result as $tr) : ?>
                                        <tr>
                                            <?php foreach (array_keys($result[0]) as $td) : ?>
                                                <td><?= $tr[$td] ?></td>
                                            <?php endforeach ?>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php endif ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-9">
            <div class="grid-stack"></div>
        </div>
        <div class="col-3">
            <div class="card">
                <form id="playground" class="card-body" onchange="handleForm(this)">
                    <div class="mt-2 chart-type">
                        <h6>Chart type</h6>
                        <div class="d-flex justify-content-center flex-wrap" role="group" aria-label="Basic radio toggle button group">
                            <div chart-type="line" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-graph-up"></i></div>
                            <div chart-type="bar" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-bar-chart-fill"></i></div>
                            <div chart-type="pie" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-pie-chart-fill"></i></div>
                            <div chart-type="donut" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-pie-chart"></i></div>
                            <div chart-type="radar" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-radar"></i></div>
                        </div>
                    </div>
                    <div class="chart-interface">
                        <hr>
                        <h5>Slice</h5>
                        <div class="row">
                            <input type="hidden" name="table" value="<?= $report['report_table'] ?>">
                            <div class="col-12">
                                <label for="validationDefault04" class="form-label d-flex justify-content-between">Row <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="SQL sorğunun icra ediləcəyi bazanı seçin."></i></label>
                                <select name="slice.row" class="form-select" required>
                                    <option value="">Choose...</option>
                                    <?php foreach ($columns as $col) : ?>
                                        <option value="<?= $col['column_name'] ?>"><?= $col['column_name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <hr>
                                <label for="validationDefault04" class="form-label d-flex justify-content-between">Column <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="SQL sorğunun icra ediləcəyi bazanı seçin."></i></label>
                                <select name="slice.column" class="form-select" required>
                                    <option value="">Choose...</option>
                                    <?php foreach ($columns as $col) : ?>
                                        <option value="<?= $col['column_name'] ?>"><?= $col['column_name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <hr>

                                <div class="d-flex justify-content-between">
                                    <h6>Value</h6>
                                    <button id="add-value" type="button" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i></button>
                                </div>
                                <div class="row value-0">
                                    <div class="col-6">
                                        <hr>
                                        <label for="validationDefault04" class="form-label d-flex justify-content-between">Field</label>
                                        <select name="slice.values[0].field" class="form-select" required>
                                            <option value="">Choose...</option>
                                            <?php foreach ($columns as $col) : ?>
                                                <option data-type="<?= $col['data_type'] ?>" value="<?= $col['column_name'] ?>"><?= $col['column_name'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <hr>
                                        <label for="validationDefault04" class="form-label d-flex justify-content-between">Aggregate</label>
                                        <select name="slice.values[0].aggregation" class="form-select" required>
                                            <option value="">Choose...</option>
                                            <?php foreach (["SUM", "COUNT", "AVG"] as $col) : ?>
                                                <option value="<?= $col ?>"><?= $col ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label d-flex justify-content-between">Filter <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="SQL sorğunun icra ediləcəyi bazanı seçin."></i></label>
                                        <select name="slice.filters" style="width: 100%;" id="filters" class="form-select select2" multiple required>
                                            <option value="">Choose...</option>
                                            <?php foreach (array_keys($filters) as $col) : ?>
                                                <option value="<?= $col ?>"><?= $col ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <?php foreach (($filters) as $key => $filter) : ?>
                                        <div class="col-12 filter-<?= $key ?> d-none">
                                            <hr>
                                            <label class="form-label d-flex justify-content-between"><?= $key ?></label>
                                            <select style="width: 100%;" name="slice.filter.<?= $key ?>" class="form-select select2" multiple required>
                                                <option value="">Choose...</option>
                                                <?php foreach ($filter as $col) : ?>
                                                    <option value="<?= $col ?>"><?= $col ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<script>
    const result = JSON.parse(`<?= json_encode($result ?? []) ?>`)
    const chartTemplate = () => {
        const uid = uuid();
        const chart = document.createElement("div");
        chart.className = 'chart';

        return chart;
    }

    const chartSlice = {
        slice: {
            row: null,
            column: null,
            filter: {},
            values: [{
                field: null,
                aggregation: null
            }]
        },
        options: null,
        type: null
    }

    var grid = GridStack.init();
    const items = {
        charts: [{
            grid: {
                w: 4,
                h: 3,
                content: chartTemplate()
            }, // will default to location (0,0) and 1x1
            slice: {
                row: null,
                column: null,
                filter: {},
                filters: [],
                values: [{
                    field: null,
                    aggregation: null
                }]
            },
            options: null,
            type: null
        }],
        options: null,
        selected: 0,

        get grids() {
            return this.charts.map(item => item.grid);
        },

        get chart() {
            return this.charts[this.selected]
        },
        init(grids = this.grids) {
            grid.load(grids);
        }
    }

    const actions = {
        addGrid: function() {
            const grids = [...this.grids];
            const index = grids.push({
                w: 4,
                h: 3,
                content: chartTemplate()
            });
            this.charts.push({
                grids,
                slice: {
                    row: null,
                    column: null,
                    filter: {},
                    filters: [],
                    values: [{
                        field: null,
                        aggregation: null
                    }]
                },
                options: null,
                type: null
            });
            this.init([grids.at(-1)]);

            $(".grid-stack-item-content").removeClass("active");
            $(".grid-stack-item-content").last().addClass("active").click()
        },
        select: function(id) {
            this.selected = id;
        }
    }

    async function handleForm() {
        $("[name^='slice']").each(function() {
            _.set(items.chart, $(this).attr("name"), $(this).val())
        })

        try {
            const request = await $.post(`/chart/pivot/${$('[name="table"]').val()}`, items.chart.slice);
            const respose = await request.json();
        } catch (e) {
            console.log(e)
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        items.init();
        $('.select2').select2();
        $(".grid-stack-item-content").addClass("active");
        $(".grid-stack").on("click", ".grid-stack-item-content", function() {
            $(".grid-stack-item-content").removeClass("active")
            $(this).toggleClass("active")
            actions.select.apply(items, [$(this).index(".grid-stack-item-content")]);
            $(`[chart-type]`).attr('data-selected', 'false');
            if (items.chart.type) {
                $(`[chart-type=${items.chart.type}]`).click();
            }

            $("[name^='slice']").each(function() {
                if (_.has(items.chart, $(this).attr("name"))) {
                    $(this).val(_.get(items.chart, $(this).attr("name"))).parent().removeClass("d-none")
                } else {
                    $(this).val([])
                    if (!$(this).is("[name='slice.filters']")) {
                        $(this).parent().addClass("d-none");
                    }
                }
            })
        })

        $("#add-value").click(function() {
            let template = $(".value-0").clone();
            template.find("input,select").val(null)
            template = template.html().replace(/\[(\d+)\]/smg, `[${$(".value-0").length}]`)
            $(".value-0").last().after(`<div class="row">${template}</div>`)
        })

        $("#filters").change(function() {
            $(`[class^='filter'`).addClass("d-none");
            for (const val of $(this).val()) {
                $(`.filter-${val}`).removeClass("d-none")
            }
        })

        $("[chart-type]").click(function() {
            $("[chart-type]").attr("data-selected", "false");
            $(this).attr("data-selected", "true");
            if (items.chart.type != $(this).attr("chart-type")) {
                items.chart.type = $(this).attr("chart-type");
            }
        })
    })
</script>