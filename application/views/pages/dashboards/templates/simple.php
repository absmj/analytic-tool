
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

<?php else: ?>
    <div id="main" class="main dashboard-template">
    <?php foreach($rows as $row_index => $row): $rowClass = end($rows[$row_index]); ?>
        <div class="<?=$rowClass['row_class']?>" data-row-index="<?=$row_index?>">
            <?php foreach($row as $colIndex => $col): ?>

            <div data-action class="<?=$col['col_class']?>" data-col-index="<?=$col['col_index']?>">
                <div class="d-none" id="col-pivot-<?=$col['chart_id']?>"></div>
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
        dashboard.template(charts, report)
    </script>
<?php endif?>
