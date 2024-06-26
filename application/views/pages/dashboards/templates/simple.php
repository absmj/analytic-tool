<script src="node_modules/gridstack/dist/gridstack-all.js"></script>
<link href="node_modules/gridstack/dist/gridstack.min.css" rel="stylesheet"/>
<style>
    .dashboard-template > .row > :not(.action-panel) {
        position: relative;
        transition: all 0.23s ease-in-out;
    }

    .chart-wizard-actions {
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
        background-color: rgba(0, 0, 0, 0.2);
        opacity: 0;
        transition: all 0.19s ease-in;
        box-shadow: 5px 5px 15px rgba(255, 255, 255, 0.5);
    }

    [data-action='not-changed'] {
        opacity: 0.8;
    }

    [data-action] > div:hover {
        opacity: 1;
    }

    [data-action] > div:hover ~ .chart-wizard-actions {
        opacity: 1;
    }

    [data-action] {
        background-color: white;
        margin-left: .5em;
        padding: 1em;
    }

    /* :is:not([data-action='not-changed']) {
        opacity: 1;
    } */

    .grid-stack { background: #FAFAD2; }
    .grid-stack-item-content { background-color: #18BC9C; }
</style>

<?php if(!isset($page)): ?>
<div class="main dashboard-template">
    <div class="row sparkboxes mt-4 mb-4 position-releative flex-nowrap chart-row" data-row-index="1">
        <div data-action="not-changed" data-id="<?=uniqid()?>" class="col-md-4" data-col-index="1">
            <div class="box box1">
                <div id="spark1"></div>
            </div>
            <?php $this->view("pages/dashboards/components/chart-wizard-actions") ?>
        </div>
        <div data-action="not-changed" data-id="<?=uniqid()?>" class="col-md-4" data-col-index="2">
            <div class="box box2">
                <div id="spark2"></div>
            </div>
            <?php $this->view("pages/dashboards/components/chart-wizard-actions") ?>
        </div>
        <div data-action="not-changed" data-id="<?=uniqid()?>" class="col-md-4" data-col-index="3">
            <div class="box box3">
                <div id="spark3"></div>
            </div>
            <?php $this->view("pages/dashboards/components/chart-wizard-actions") ?>
        </div>
    </div>

    <div class="row mt-5 mb-4 position-releative flex-nowrap chart-row" data-row-index="2">
        <div data-action="not-changed" data-id="<?=uniqid()?>" class="col-md-6" data-col-index="1">
            <div class="box">
                <div id="bar"></div>
            </div>
            <?php $this->view("pages/dashboards/components/chart-wizard-actions") ?>
        </div>
        <div data-action="not-changed" data-id="<?=uniqid()?>" class="col-md-6" data-col-index="2">
            <div class="box">
                <div id="donut"></div>
            </div>
            <?php $this->view("pages/dashboards/components/chart-wizard-actions") ?>
        </div>
    </div>

    <div class="row mt-4 mb-4 position-releative flex-nowrap chart-row" data-row-index="3">
        <div data-action="not-changed" data-id="<?=uniqid()?>" class="col-md-6" data-col-index="1">
            <div class="box">
                <div id="area"></div>
            </div>
            <?php $this->view("pages/dashboards/components/chart-wizard-actions") ?>
        </div>
        <div data-action="not-changed" data-id="<?=uniqid()?>" class="col-md-6" data-col-index="2">
            <div class="box">
                <div id="line"></div>
            </div>
            <?php $this->view("pages/dashboards/components/chart-wizard-actions") ?>
        </div>
    </div>
</div>

<script>
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
        }
    }
    


</script>

<?php else: ?>
    <div id="main" class="main dashboard-template">
    <?php foreach($rows as $row_index => $row): $rowClass = end($rows[$row_index]); ?>
        <div class="<?=$rowClass['row_class']?>" data-row-index="<?=$row_index?>">
            <?php foreach($row as $colIndex => $col): ?>

            <div data-action class="<?=$col['col_class']?>" data-col-index="<?=$col['col_index']?>">
                <div class="d-none" id="col-pivot-<?=$col['id']?>"></div>
                <div class="box box1">
                    <div id="apex-<?=$col['chart_id']?>"></div>
                </div>
            </div>
            <?php endforeach?>
        </div>
    <?php endforeach?>
    </div>

    <script>
        const charts = <?=json_encode($charts)?>;
        const report = <?=json_encode($report ?? [])?>;
        const pivots = [];

        for(chart of charts) {
            const {id, type, title, slice} = {id: chart.chart_id, type: chart.chart_type, title: chart.title, slice: chart.slice}
            console.log(slice)
            pivots.push(
                new WebDataRocks({
                    container: `#col-pivot-${chart['id']}`,
                    toolbar: false,
                    report: {
                        dataSource: {
                            data: report,
                        },
                        slice: JSON.parse(slice)
                    },
                    dataloaded: function() {

                    },
                    reportcomplete: function() {

                        webdatarocks.getData({}, function(e) {
                            console.log(id)
                            // $(".wdr-ui-element").removeClass("wdr-ui-element");
                            const a = new Apex("#apex-" + id, e, type, title)
                            a.render()
                        })
                    }
                })
            )
        }
        console.log(charts);

    </script>
<?php endif?>
