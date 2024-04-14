<div class="chart-wizard-actions">
    <div style="width: 300px; height: 300px" class="d-flex flex-column justify-content-center align-items-center">
        <div class="d-flex flex-wrap chart-list">
            <?php foreach (["bar", "line", "area", "radar", "polarArea", "donut", "pie", "gauge", "treeMap", "radialBar"] as $chart) : ?>
                <button data-type="<?= $chart ?>" class="btn btn-sm btn-light m-2 chart-type"><?= $chart ?></button>
            <?php endforeach ?>
        </div>
    </div>
</div>