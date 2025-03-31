<main id="main" class="main">
    <div class="d-flex justify-content-between align-items-center">
        <div class="pagetitle">
            <h1><?= $this->title ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Səhifələr</a></li>
                    <li class="breadcrumb-item"><?= $this->title ?></li>
                    <li class="breadcrumb-item step-description"><?= $page['page_title'] ?? '' ?></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
    </div>
    <div class="d-flex justify-content-between">
        <button data-bs-toggle="modal" data-bs-target="#dashboardModal" type="button" id="save-page-dash" class="btn btn-sm btn-success"><i class="bi bi-save"></i></button>
        <button onclick="actions.addGrid.apply(items)" type="button" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i></button>
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
                <div style="max-height: 70vh; overflow-y: auto; overflow-x: hidden" id="playground" class="card-body">
                    <div class="mt-2 chart-type">
                        <h6>Chart type</h6>
                        <div class="d-flex justify-content-center flex-wrap" role="group" aria-label="Basic radio toggle button group">
                            <div chart-type="line" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-graph-up"></i></div>
                            <div chart-type="area" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-graph-up-arrow"></i></div>
                            <div chart-type="bar" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-bar-chart-fill"></i></div>
                            <div chart-type="bar-h" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-bar-chart-steps"></i></div>
                            <div chart-type="pie" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-pie-chart-fill"></i></div>
                            <div chart-type="donut" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-pie-chart"></i></div>
                            <div chart-type="radar" data-selected="false" class="btn btn-outline-primary mx-2 mt-1"><i class="bi bi-radar"></i></div>
                        </div>
                        <div class="chart-playground">
                            <hr>
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-Slice-tab" data-bs-toggle="tab" data-bs-target="#nav-Slice" type="button" role="tab" aria-controls="nav-Slice" aria-selected="true">Slice</button>
                                    <button class="nav-link" id="nav-Options-tab" data-bs-toggle="tab" data-bs-target="#nav-Options" type="button" role="tab" aria-controls="nav-Options" aria-selected="false">Playground</button>
                                    <!-- <button class="nav-link disabled" id="nav-Dataset-tab" data-bs-toggle="tab" data-bs-target="#nav-Dataset" type="button" role="tab" aria-controls="nav-Dataset" aria-selected="false">Dataset</button> -->
                                    <button class="nav-link" id="nav-options-json-tab" data-bs-toggle="tab" data-bs-target="#nav-options-json" type="button" role="tab" aria-controls="nav-options-json" aria-selected="false">JSON</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="chart-interface tab-pane fade show active" role="tabpanel" id="nav-Slice" aria-labelledby="nav-Slice-tab">
                                    <h5>Slice</h5>
                                    <form id="slice-form" onchange="handleForm(this)" class="row">
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
                                                    <select name="slice.values[0].field" class="form-select value-select" required>
                                                        <option value="">Choose...</option>
                                                        <?php foreach ($columns as $col) : ?>
                                                            <option data-type="<?= $col['data_type'] ?>" value="<?= $col['column_name'] ?>"><?= $col['column_name'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                                <div class="col-6 d-none">
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
                                    </form>
                                </div>
                                <form class="tab-pane fade" tab-pane id="nav-Options" aria-labelledby="nav-Slice-tab">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#general" aria-expanded="true" aria-controls="collapseOne">
                                                    General
                                                </button>
                                            </h2>
                                            <div id="general" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <label>BG color</label>
                                                            <input type="color" class="form-control form-control-color" name="plugins.customCanvasBackgroundColor.color">

                                                        </div>
                                                        <div class="col-12">
                                                            <label>Title</label>
                                                            <select class="form-select" name="plugins.title.display">
                                                                <option value="true">Show</option>
                                                                <option value="false">Hide</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-9">
                                                            <input placeholder="Title" type="text" class="form-control" name="plugins.title.text">
                                                        </div>
                                                        <div class="col-2">
                                                            <input type="color" class="form-control form-control-color" name="plugins.title.color">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-6">
                                                            <input type="number" class="form-control" name="plugins.title.padding.top" placeholder="Top">
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="number" class="form-control" name="plugins.title.padding.bottom" placeholder="Bottom">
                                                        </div>
                                                        <div class="col-6 mt-1">
                                                            <input type="number" class="form-control" name="plugins.title.padding.left" placeholder="Left">
                                                        </div>
                                                        <div class="col-6 mt-1">
                                                            <input type="number" class="form-control" name="plugins.title.padding.right" placeholder="Right">
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <label>Subtitle</label>
                                                            <select class="form-select" name="plugins.subtitle.display">
                                                                <option value="true">Show</option>
                                                                <option selected value="false">Hide</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-9">
                                                            <input placeholder="Subtitle" type="text" class="form-control" name="plugins.subtitle.text">
                                                        </div>
                                                        <div class="col-2">
                                                            <input type="color" class="form-control form-control-color" name="plugins.subtitle.color">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-6">
                                                            <input type="number" class="form-control" name="plugins.subtitle.padding.top" placeholder="Top">
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="number" class="form-control" name="plugins.subtitle.padding.bottom" placeholder="Bottom">
                                                        </div>
                                                        <div class="col-6 mt-1">
                                                            <input type="number" class="form-control" name="plugins.subtitle.padding.left" placeholder="Left">
                                                        </div>
                                                        <div class="col-6 mt-1">
                                                            <input type="number" class="form-control" name="plugins.subtitle.padding.right" placeholder="Right">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#legend" aria-expanded="true" aria-controls="legend">
                                                    Legend
                                                </button>
                                            </h2>
                                            <div id="legend" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <!-- Display & Position -->
                                                    <div class="row mb-2">
                                                        <div class="col-6">
                                                            <label>Display</label>
                                                            <select class="form-select" name="plugins.legend.display">
                                                                <option value="true">Show</option>
                                                                <option value="false">Hide</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label>Position</label>
                                                            <select class="form-select" name="plugins.legend.position">
                                                                <option value="top">Top</option>
                                                                <option value="left">Left</option>
                                                                <option value="bottom">Bottom</option>
                                                                <option value="right">Right</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Alignment & Background Color -->
                                                    <div class="row mb-2">
                                                        <div class="col-6">
                                                            <label>Alignment</label>
                                                            <select class="form-select" name="plugins.legend.align">
                                                                <option value="start">Start</option>
                                                                <option value="center">Center</option>
                                                                <option value="end">End</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label>Bg Color</label>
                                                            <input type="color" class="form-control form-control-color" name="plugins.legend.backgroundColor">
                                                        </div>
                                                    </div>

                                                    <!-- Label Options -->
                                                    <h6>Label Options</h6>
                                                    <div class="row mb-2">
                                                        <div class="col-2">
                                                            <input type="color" class="form-control form-control-color" name="plugins.legend.labels.color">
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="number" class="form-control" name="plugins.legend.labels.font.size" placeholder="Font Size">
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-6">
                                                            <input type="number" class="form-control" name="plugins.legend.labels.boxWidth" placeholder="Width">
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="number" class="form-control" name="plugins.legend.labels.padding" placeholder="Padding">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#tooltip" aria-expanded="true" aria-controls="tooltip">
                                                    Tooltip
                                                </button>
                                            </h2>
                                            <div id="tooltip" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-8">
                                                            <select class="form-select" name="plugins.tooltip.enabled">
                                                                <option value="true">Yes</option>
                                                                <option value="false">No</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <input type="color" class="form-control form-control-color" name="plugins.tooltip.titleColor">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#animation" aria-expanded="true" aria-controls="animation">
                                                    Animation
                                                </button>
                                            </h2>
                                            <div id="animation" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row mb-2">
                                                        <div class="col-6">
                                                            <label>Duration (ms)</label>
                                                            <input type="number" class="form-control" name="animation.duration" placeholder="1000">
                                                        </div>
                                                        <div class="col-6">
                                                            <label>Easing</label>
                                                            <select class="form-select" name="animation.easing">
                                                                <option value="linear">Linear</option>
                                                                <option value="easeInOutQuad">Ease In Out</option>
                                                                <option value="easeOutBounce">Bounce</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Scale Options -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#scales" aria-expanded="true" aria-controls="scales">
                                                    Scales
                                                </button>
                                            </h2>
                                            <div id="scales" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <!-- X-Axis -->
                                                    <h6>X-Axis</h6>
                                                    <hr>
                                                    <div class="row mb-2">
                                                        <div class="col-8">
                                                            <select class="form-select" name="scales.x.display">
                                                                <option value="true">Show</option>
                                                                <option value="false">Hide</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <input type="color" class="form-control form-control-color" name="scales.x.grid.color">
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <label for="">Stacked</label>
                                                            <select class="form-select" name="scales.x.stacked">
                                                                <option value="true">True</option>
                                                                <option selected value="false">False</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Y-Axis -->
                                                    <h6>Y-Axis</h6>
                                                    <hr>
                                                    <div class="row mb-2">
                                                        <div class="col-8">
                                                            <select class="form-select" name="scales.y.display">
                                                                <option value="true">Show</option>
                                                                <option value="false">Hide</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <input type="color" class="form-control form-control-color" name="scales.y.grid.color">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <label for="">Stacked</label>
                                                            <select class="form-select" name="scales.y.stacked">
                                                                <option value="true">True</option>
                                                                <option selected value="false">False</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="tab-pane fade" id="nav-Dataset">

                                </div>
                                <div class="tab-pade fade" id="nav-options-json">
                                    <h6>Chart.js options [JSON]</h6>
                                    <hr>
                                    <textarea id="chart-js-options" id="" cols="30" rows="10"></textarea>
                                    <hr>
                                    <button id="save-from-json" class="btn btn-sm btn-warning pull-right">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<!-- Bootstrap Modal -->
<div tabindex="-1" class="modal fade" id="dashboardModal" aria-labelledby="dashboardModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dashboardModalLabel">Səhifə barədə məlumatlar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dashboardSettings" class="row" method="POST">
                    <div class="col-12 mb-3">
                        <label for="dashboardName" class="form-label">Dashboard adı</label>
                        <input required type="text" class="form-control" id="dashboardName" name="dashboardName" placeholder="Dashboard adı" value="<?php echo htmlspecialchars($page['page_title'] ?? ''); ?>">
                    </div>

                    <div class="col-12 mb-3">
                        <label for="ldapAccess" class="form-label">LDAP icazələr</label>
                        <select multiple class="form-select select2" id="ldapAccess" name="ldapAccess">
                            <option value="" disabled>---</option>
                            <?php foreach ($groups ?? [] as $group): ?>
                                <option <?php echo in_array($group, $access['ldap'] ?? []) ? 'selected' : ''; ?> value="<?php echo $group; ?>">
                                    <?php echo $group; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 col-6">
                        <label class="form-label">Məlumatların əks olunması şərtləri</label>
                        <select class="form-select" id="specAccessKey">
                            <option selected value="">---</option>
                            <?php foreach ($columns as $column): ?>
                                <option value="<?php echo htmlspecialchars($column['column_name']); ?>" <?php echo in_array($column['column_name'], array_keys($access['special'] ?? [])) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($column['column_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 col-6">
                        <label class="form-label">Equal to</label>
                        <select class="form-select" id="specAccessVal">
                            <option selected value="">---</option>
                            <?php foreach ($_SESSION as $key => $session): ?>
                                <?php if (!is_string($session)) continue; ?>
                                <option value="<?= $key; ?>" <?php echo (in_array($key, $access['special'] ?? [])) ? 'selected' : ''; ?>>
                                    <?= $key; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveDashboard()">Yadda saxla</button>
            </div>
        </div>
    </div>
</div>


<script>
    const reportId = <?= $report['id'] ?>;
    const pageId = <?= $page['id'] ?? 'null' ?>;
    const charts = <?= json_encode($charts ?? []) ?>;
    const exactFilters = {};
    let filters = {};
    const crossCharts = new Set;

    let editor = null

    const chartTemplate = (uid = null) => {
        uid ??= uuid();
        return `
            <div style="width: 100%; height: 100%" class="position-relative">
                <button style="position: absolute; right: 0" class="btn btn-sm btn-danger remove-chart"><i class="bi bi-trash"></i></button>
                <canvas class='chart' data-uuid="${uid}"></canvas>
            </div>`;
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
        charts: pageId ? charts : [{
            grid: {
                x: 0,
                y: 0,
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

        get options() {
            const {
                options,
                slice,
                grid
            } = this.chart;
            return {
                charts: [{
                    options,
                    slice,
                    grid
                }]
            }
        },

        init(grids = this.grids, firstLoaded = true) {
            if (pageId) {
                for (const chart of charts) {
                    chart.grid.content = chartTemplate(chart?.options?.uuid || null);
                    chart.options.uuid = chart.grid.content?.replace(/.*data-uuid=['"](.*?)['"].*/si, "$1");
                }
            }
            grid.load(grids);
            firstLoaded && pageId && charts.length && initExistsCharts(charts)
        }
    }

    const actions = {
        addGrid: function() {
            const grid = {
                w: 4,
                h: 3,
                content: chartTemplate()
            };
            this.charts.push({
                grid,
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
            this.init([grid], false);

            $(".grid-stack-item-content").removeClass("active");
            $(".grid-stack-item-content").last().addClass("active").click()
            $("#nav-Slice-tab").click();
        },
        select: function(id) {
            this.selected = id;
        }
    }

    async function handleForm() {
        $("[name^='slice']").each(function() {
            _.set(items.chart, $(this).attr("name"), $(this).val())
        })

        if (!(items.chart.slice.row || items.chart.slice.column)) {
            return;
        }

        try {
            const request = await fetch(`/chart/pivot/${$('[name="table"]').val()}`, {
                method: 'POST',
                body: JSON.stringify(items.options)
            });
            const response = await request.json();
            const instance = new ChartJs();

            const {
                datasets,
                labels
            } = response.data[0];


            const chartElement = document.querySelector(`[data-uuid="${items.chart.grid.content?.replace(/.*data-uuid=['"](.*?)['"].*/si, "$1")}"]`);

            if (!items.chart.options || (items?.chart?.instance?.config?.type != items.chart.type)) {
                items.chart.options = instance.chart(datasets, labels, items.chart.type);
            } else {
                items.chart.options.plugins.push(plugin)
                items.chart.options.data = {
                    datasets,
                    labels
                }
            }

            if (items.chart.instance instanceof Chart) {
                items.chart.instance.destroy();
                items.chart.instance = null;
            }

            items.chart.instance = new Chart(chartElement, items.chart.options);
        } catch (e) {
            console.error(e)
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        editor = CodeMirror.fromTextArea(document.getElementById('chart-js-options'), {
            mode: 'javascript',
            indentWithTabs: true,
            smartIndent: true,
            lineNumbers: true,
            matchBrackets: true,
            autofocus: true,
            extraKeys: {
                "Ctrl-Space": "autocomplete"
            },
        });
        items.init();
        $('.select2').select2({
            width: '100%'
        });

        $("#ldapAccess").select2({
            width: '100%',
            dropdownParent: $("#dashboardModal")
        })
        $(".grid-stack-item-content").addClass("active");
        $(".grid-stack").on("click", ".grid-stack-item-content", function() {
            $(".grid-stack-item-content").removeClass("active")
            $(this).toggleClass("active");
            const uuid = $(this).find('canvas').data('uuid');
            const chartByUid = items.charts.findIndex(c => c.options.uuid == uuid);

            actions.select.apply(items, [chartByUid]);
            $(`[chart-type]`).attr('data-selected', 'false');
            if (items.chart?.type) {
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
            $("#nav-Slice-tab").click();
            if (items.chart?.options || items.chart?.id) autoFillChartOptions(items.chart.id ? items.chart.instance.options : items.chart?.options);
        })

        $("#add-value").click(function() {
            let template = $(".value-0").clone();
            template.find("input,select").val(null)
            template = template.html().replace(/\[(\d+)\]/smg, `[${$(".value-0").length}]`)
            $(".value-0").last().after(`<div class="row value-0">${template}</div>`)
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
                $("#slice-form").trigger("change")
            }


        })

        grid.on('resizestop dragstop', function(event, el) {
            let {
                x,
                y,
                w,
                h
            } = el.gridstackNode;

            items.chart.grid = {
                x,
                y,
                w,
                h,
                content: items.chart.grid.content
            };
        });
    })

    $("#nav-Options").on("input change", function(e) {
        let {
            name,
            value
        } = e.target;

        if (value == 'true' || value == 'false') value = value == 'true';
        if (items.chart.id) {
            _.set(items.chart.options.options, name, value);
            items.chart.instance.options = items.chart.options.options
        } else {
            _.set(items.chart.options.options, name, value);
        }

        items.chart.instance.update();
    });

    $("#nav-Options-tab").click(function(e) {
        e.preventDefault();
        if (items.chart.options || items.chart.id)
            autoFillChartOptions(items.chart.options)
    })

    function autoFillChartOptions(chartConfig) {
        const form = document.getElementById('nav-Options');
        if (!form) {
            console.error('Form not found:', formSelector);
            return;
        }

        function setInputValue(name, value) {
            const input = form.querySelector(`[name="${name}"]`);
            if (input) {
                if (typeof value === 'object') {
                    input.value = JSON.stringify(value, null, 2);
                } else {
                    input.value = value;
                }
            }
        }

        function traverseOptions(options, prefix = '') {
            for (const key in options) {
                const fullName = prefix ? `${prefix}.${key}` : key;
                if (typeof options[key] === 'object' && !Array.isArray(options[key])) {
                    traverseOptions(options[key], fullName);
                } else {
                    setInputValue(fullName, options[key]);
                }

            }
        }

        traverseOptions(chartConfig.options);
    }

    const saveDashboard = async (e) => {
        const form = $("#dashboardSettings")[0];

        if (!form.checkValidity()) {
            form.reportValidity()
            return false;
        }
        try {
            setUi("loading", true);
            const data = items.charts
                .filter((chart) => chart.options != null)
                .map((chart, key) => {
                    const {
                        data: _,
                        ...options
                    } = chart.options;
                    options.uuid = chart?.uuid ?? null;
                    const {
                        content: __,
                        ...grid
                    } = chart.grid;
                    return {
                        id: chart?.id ?? null,
                        chart_type: options.type,
                        chart_options: JSON.stringify(options),
                        slice: JSON.stringify(chart.slice),
                        grid: JSON.stringify(chart.grid),
                    };
                });


            const special = {};

            if ($("#specAccessKey").val() && $("#specAccessVal").val())
                special[$("#specAccessKey").val()] = $("#specAccessVal").val();


            const access = JSON.stringify({
                special,
                ldap: $("#ldapAccess").val()
            });
            const id = null ? id : reportId;
            const request = await fetch(`/dashboard/${pageId ? `update/${pageId}` : `create/${id}`}`, {
                method: 'POST',
                body: JSON.stringify({
                    charts: data,
                    title: $("#dashboardName").val(),
                    access
                }),
            })
            const response = await request.json();
            setUi("success", "Success");
            if (response.status == 200) window.location.href = ('/pages')
        } catch (e) {
            console.error(e)
            setUi("error", "Success");
        } finally {
            setUi("loading", false);
        }

    };

    async function initExistsCharts(charts) {
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
            const responseCharts = response.data;
            for (const chart of items.charts) {
                const responsedChart = responseCharts.find(item => item.id == chart.id);
                if (responsedChart) {
                    const {
                        datasets,
                        labels
                    } = responsedChart;
                    const chartElement = document.querySelector(`[data-uuid="${chart.grid.content?.replace(/.*data-uuid=['"](.*?)['"].*/si, "$1")}"]`);
                    chartElement.id = chart.id;
                    if (chart?.instance?.config?.type != chart.type && chart.instance instanceof Chart) {
                        chart.instance.destroy();
                        chart.instance = null;
                    }

                    if (!(chart.instance instanceof Chart)) {

                        chart.options.plugins.push(plugin)
                        chart.options.data = {
                            datasets,
                            labels
                        }

                        chart.instance = new Chart(chartElement, chart.options);
                    } else {
                        _.set(chart.options, 'data', {
                            datasets,
                            labels
                        });
                        chart.instance.data = chart.options.data;
                        chart.instance.update();
                    }
                }
            }
            // items.charts = responseCharts;
        } catch (e) {
            console.error(e)
        }
    }

    $(".grid-stack").on("click", ".remove-chart", function() {
        const parentItem = $(this).closest(".grid-stack-item");
        const uuid = $(this).next().data('uuid'); // Ensure this is correctly set
        // Remove from your `items.charts` array
        items.charts = items.charts.filter(chart => chart.options.uuid != uuid);

        // Remove from GridStack
        grid.removeWidget(parentItem.get(0)); // Convert jQuery object to DOM element
    });

    $("#nav-options-json-tab").click(function() {
        editor.setValue(JSON.stringify(items.chart.options, null, 2))
    })

    $("#save-from-json").click(function() {
        try {
            const {
                type,
                options,
                data,
                plugins
            } = JSON.parse(editor.getValue());
            if (type) items.chart.instance.config.type = type;
            if (options) items.chart.instance.options = options;
            if (data) items.chart.instance.data = data;
            if (plugins) items.chart.instance.plugins = plugins;
            items.chart.instance.update();
        } catch {

        }
    })

    $("#slice-form").on("change", ".value-select", function() {
        const colType = $(this).find("option:selected").data('type')
        const aggrElement = $(this).closest('.col-6').next();
        aggrElement.find("select").val(null);
        if ($(this).val()) {
            aggrElement.removeClass("d-none").find(`option[value='SUM'],option[value='AVG']`).prop('disabled', !['integer', 'real'].includes(colType));
        } else {
            aggrElement.addClass("d-none")
        }
    })
</script>