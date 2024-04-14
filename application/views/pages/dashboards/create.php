<main id="main" class="main">
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
        <div class="col-12">
            <div id="action" class="card">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="card-header step-description">
                    </div>
                    <button id="next" stage="0" type="button" class="btn btn-sm btn-primary me-3"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div id="user-panel" class="card-body">
                    <?php $this->view("/pages/files/index") ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row page-wizard d-none" id="writing">
        <div class="col-12" id="dashboard">
            <div class="row">
            </div>
        </div>
    </div>

    <div id="chart-wizard" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title step-description">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div>
                                <div id="wdr-component"></div>
                            </div>

                        </div>
                        <div class="col-6">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="data-tab" data-bs-toggle="tab" data-bs-target="#data-tab-pane" type="button" role="tab" aria-controls="data-tab-pane" aria-selected="true">PREVIEW</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="json-tab" data-bs-toggle="tab" data-bs-target="#json-tab-pane" type="button" role="tab" aria-controls="json-tab-pane" aria-selected="false">OPTIONS</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="data-tab-pane">
                                    <div class="card p-2">
                                        <input type="text" class="form-control chart-title" placeholder="Untitled" />
                                        <hr>
                                        <div id="chart-component"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="json-tab-pane">
                                    <div class="card p-2">
                                        <div class="card-title">Chart options</div>
                                        <div class="card-body">
                                            <textarea id="chart-options"></textarea>
                                        </div>
                                        <div class="card-footer d-grid gap-2">
                                            <button type="button" id="save-chart-option" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                    
                                </div>
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
                    <?php $this->view("/pages/dashboards/templates/index")?>
                </div>
            </div>
        </div>
    </div>

    <script>
        const folders = <?= json_encode($folders ?? []) ?>;
    </script>
    <script src="<?=BASE_PATH?>assets/js/folder-explorer.js">
        
    </script>
</main>