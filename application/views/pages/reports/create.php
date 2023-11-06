<main id="main" class="main">
    <div class="pagetitle">
        <h1><?= $this->title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Hesabatlar</a></li>
                <li class="breadcrumb-item"><?= $this->title ?></li>
                <li id="stepDescription" class="breadcrumb-item"></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div id="error" class="alert alert-danger alert-dismissible d-none" role="alert">
        <template id="error"></template>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <section class="section contact">

        <div class="row gy-4">

            <div class="col-12">
                <div class="card p-4">
                    <!-- Browser Default Validation -->
                    <form id="stepone" name="stepone-form" class="row g-3">
                        <div class="col-md-4">
                            <label for="validationDefault01" class="form-label d-flex justify-content-between">Hesabatın adı <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="Hesabatın sistemdə saxlanılacağı adı ifadə edir."></i></label>
                            <input value="test" model="Hesabatın adı" type="text" class="form-control" name="name" required>
                        </div>

                        <div class="col-md-3">
                            <label for="validationDefault01" class="form-label d-flex justify-content-between">Qovluq <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="Yerləşmə qovluğu, hesabatların tematika və ya digər meyarlar üzrə təsnifləşdirmə üçün nəzərdə tutulub.<br><a href='#' class='nav-link'>Yeni qovluğu yarat</a>"></i></label>
                            <button id='folder-select' type="button" class="btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#folder-tree">S</button>
                            <select onmousedown="(function(e){ e.preventDefault(); this.blur(); window.focus(); document.getElementById('folder-select').click() })(event, this)" name="database" class="form-select" required model="folder">
                                <option disabled value="">Choose...</option>
                                <?php foreach (dblist() as $db) : ?>
                                    <option selected value="<?= $db ?>"><?= $db ?></option>
                                <?php endforeach ?>
                            </select>

                        </div>

                        <div class="col-md-2">
                            <label for="validationDefault04" class="form-label d-flex justify-content-between">Baza <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="SQL sorğunun icra ediləcəyi bazanı seçin."></i></label>
                            <select name="database" class="form-select" required model="Baza">
                                <option disabled value="">Choose...</option>
                                <?php foreach (dblist() as $db) : ?>
                                    <option selected value="<?= $db ?>"><?= $db ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="validationDefault04" class="form-label d-flex justify-content-between">İşləmə tezliyi <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="SQL sorğunun icra edilmə tezliyi, hesabatın avtomatik işləyəcəyi tarixləri bildirir. Günlük icra edilmə səhər 9<sup>00</sup>, digərləri isə başlama tarixlərində icra ediləcək."></i></label>
                            <select class="form-select" name="cron_frequency">
                                <option value="s">Bir dəfə</option>
                                <option value="0 9 * * *">Günlük</option>
                                <option value="0 0 * * 0">Həftəlik</option>
                                <option selected value="0 0 1 * *">Aylıq</option>
                                <option value="0 0 1 1,4,7,10 *">Rüblük</option>
                                <option value="0 0 1 1,7 *">6 aylıq</option>
                                <option value="0 0 1 1 *">İllik</option>
                                <option value="">Fərdi</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="col-6 form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="run-or-save" id="run-or-save">
                                <label for="run-or-save">Nəticəyə baxdıqdan sonra, yadda saxla <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="Nəticə əldə edilmədikdə, yadda saxlanılma yerinə yetirilməyəcək."></i></label>
                            </div>
                            <hr>
                            <div class="">
                                <label for="sql" class="form-label d-flex justify-content-between mb-1">SQL <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="Yazılmış sorğular yalnız məlumat əldə edilməsi üçün nəzərdə tutulmalıdır"></i></label>
                                <textarea model="SQL sorğu" model="sql" class="form-control" id="sql" style="height: 100px;"></textarea>
                            </div>
                        </div>
                    </form>
                    <!-- End Browser Default Validation -->

                    <div id="steptwo" class="d-none">
                        <div class="table-responsive" id="table-data">

                        </div>
                    </div>

                    <div id="stepthree" class="d-none">
                        <div class="row">
                            <div class="col-7">
                                <div id="pivot" class="table-responsive"></div>
                            </div>
                            <div class="col-5">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Çartın tipi</label>
                                        <select id="chartType" class="form-select" required>
                                            <option value="line">Line</option>
                                            <option value="bar">Bar</option>
                                            <option value="column">Column</option>
                                            <option value="pie">Pie</option>
                                            <option value="area">Area</option>
                                            <option value="histogram">Histogram</option>
                                            <option value="scatter">Scatter</option>
                                            <option value="bubble">Bubble</option>
                                            <option value="combo">Combo</option>
                                        </select>

                                    </div>

                                    <div class="col-6">
                                        <label class="form-label">Legendası</label>
                                        <input id="legend" type="text" class="form-control" required>
                                    </div>
                                    <hr>
                                    <div class="col-12" data-chart="apex-2">
                                        <div id="chart-container" style="position:relative;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="stepfour" class="d-none">
                        <div class="row">
                            <div class="col-4">
                                <div id="info-report-labels"></div>
                                <hr>
                                <div id="info-report-columns"></div>
                            </div>

                            <div class="col-8" data-chart="apex-3">
                                <div style="position:relative;" id="chart-container-preview"></div>
                            </div>
                        </div>
                    </div>

                    <div id="fields"></div>

                    <div id="navigation-steps" class="d-flex justify-content-between">
                        <button id="prev" class="btn btn-warning invisible">Əvvəlki</button>
                        <button id="next" class="btn btn-primary">Növbəti</button>
                    </div>
                </div>

            </div>

            <div id="steptwo" class="col-12 d-none">
                <div class="card p-4">

                </div>
            </div>


        </div>

    </section>

    <?php $this->load->view("modals/folder")?>

</main>