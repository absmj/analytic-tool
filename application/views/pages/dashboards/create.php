
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

    <div class="row page-wizard" id="writing">
        <div class="col-12" id="dashboard">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        <button id="next" stage="1" type="button" class="mt-2 btn btn-sm btn-primary"><i class="bi bi-chevron-right"></i></button>
                    </div>
                    <div class="table-responsive">
                        <?php if(count($result ?? []) > 0):?>
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
                        <?php endif?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="chart-wizard" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title step-description">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="chart-type-versions"></div>
                    <div id="chart-wizard-by-user" class="row" style="height: 100%;">
                        <div class="col-4 card">
                            <div class="card-title">Pivotting</div>
                            <div id="wdr-component"></div>
                        </div>
                        <div class="col-4 card">
                            <div class="card-title">Chart</div>
                            <div id="chart-component"></div>
                        </div>
                        <div class="col-4 card">
                            <div class="card-title">Chart options</div>
                            <div class="card-body">
                                <div style="max-height: 70vh; overflow-y:auto" id="chart-options-form"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="add-chart" class="btn btn-primary" type="button">Add chart to page</button>
                </div>
            </div>
        </div>
    </div>

    <div id="choosing-template" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dashboard şablonunun seçilməsi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php $this->view("/pages/dashboards/templates/list") ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const result = JSON.parse(`<?= json_encode($result ?? []) ?>`)
</script>