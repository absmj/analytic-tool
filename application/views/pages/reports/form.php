<main id="main" class="main">
    <div class="pagetitle">
        <h1><?= $this->title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Hesabatlar</a></li>
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
                    <form id="stepone" name="report-form" class="row g-3">
                        <div class="col-md-6">
                            <label for="validationDefault01" class="form-label d-flex justify-content-between">Hesabatın adı <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="Hesabatın sistemdə saxlanılacağı adı ifadə edir."></i></label>
                            <input value="<?= $report['name'] ?? '' ?>" model="Hesabatın adı" type="text" class="form-control" name="name" required>
                        </div>

                        <div class="col-md-6">
                            <input type="hidden" name="folder_name" value="<?= $report['folder_name'] ?? '' ?>">
                            <label for="validationDefault01" class="form-label d-flex justify-content-between">Qovluq <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="Yerləşmə qovluğu, hesabatların tFolderematika və ya digər meyarlar üzrə təsnifləşdirmə üçün nəzərdə tutulub.<br><a href='#' class='nav-link'>Yeni qovluğu yarat</a>"></i></label>
                            <select onmousedown="(function(e){ e.preventDefault(); this.blur(); window.focus(); document.getElementById('folder-select').click() })(event, this)" name="report_folder" class="form-select" required id="folder">
                                <?php if (isset($report['folder_name'])) : ?>
                                    <option selected value="<?= $report['folder_id'] ?>"><?= $report['folder_name'] ?></option>
                                <?php else : ?>
                                    <option selected value="">Choose...</option>
                                <?php endif ?>
                            </select>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#folder-tree" id="folder-select" class="d-none"></button>
                        </div>

                        <div class="col-md-3">
                            <label for="validationDefault04" class="form-label d-flex justify-content-between">Baza <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="SQL sorğunun icra ediləcəyi bazanı seçin."></i></label>
                            <select name="database" class="form-select" required model="Baza">
                                <option disabled value="">Choose...</option>
                                <?php foreach (dblist() as $db) : ?>
                                    <option <?= $db == ($report['db'] ?? '') ? 'selected' : '' ?> value="<?= $db ?>"><?= $db ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="validationDefault04" class="form-label d-flex justify-content-between">İşləmə tezliyi <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="SQL sorğunun icra edilmə tezliyi, hesabatın avtomatik işləyəcəyi tarixləri bildirir. Günlük icra edilmə səhər 9<sup>00</sup>, digərləri isə başlama tarixlərində icra ediləcək."></i></label>
                            <select class="form-select" name="cron_frequency" onchange="cron(event)">
                                <?php foreach ($crons as $cron) : ?>
                                    <option <?= $db == ($report['cron_id'] ?? '') ? 'selected' : '' ?> value="<?= $cron['job'] ?>"><?= $cron['title'] ?></option>
                                <?php endforeach ?>
                                <option value="">Fərdi</option>
                            </select>
                        </div>

                        <div id="special-cron" class="col-md-6 d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="cron-job" class="form-label d-flex justify-content-between">Cron job <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="Daxil edilən məlumatlar CRON job sintaksinı uyğun olmalıdır."></i></label>
                                    <input value="test" type="text" class="form-control" name="cron_job">
                                </div>
                                <div class="col-md-6">
                                    <label for="cron-title" class="form-label d-flex justify-content-between">Cron title <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="Məlumat daxil edilmədikdə, cron-job olaraq qəbul ediləcək."></i></label>
                                    <input value="test" type="text" class="form-control" name="cron_title">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="">
                                <label for="sql" class="form-label d-flex justify-content-between mb-1">SQL <i class="bi bi-question-circle-fill text-muted" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" data-bs-title="Yazılmış sorğular yalnız məlumat əldə edilməsi üçün nəzərdə tutulmalıdır"></i></label>
                                <textarea value="<?= $report['sql'] ?? '' ?>" model="SQL sorğu" class="form-control" id="sql" style="height: 100px;"></textarea>
                            </div>
                        </div>
                    </form>
                    <!-- End Browser Default Validation -->

                    <div id="steptwo" class="d-none">
                        <div class="table-responsive" id="table-data">

                        </div>
                    </div>
                </div>

                <div id="navigation-steps" class="d-flex justify-content-between mt-2">
                    <button data-step="0" id="next" onclick="formReport.run(this)" class="btn btn-primary">Növbəti</button>
                </div>
            </div>


        </div>

        <div class="modal fade" id="queryparams" tabindex="-1" aria-labelledby="queryparams" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="qyerparamstitle">Query params</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form name="queryparams-form">

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-step="0" onclick="formReport.run(this)" id="run-query" type="button" class="btn btn-primary">Run</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const cron = (e) => {
                const specialC = document.getElementById('special-cron');
                if (!e.target.value) {
                    specialC.classList.remove("d-none")
                } else {
                    specialC.classList.add("d-none")
                }
            }

            const isEdit = <?= isset($isEdit) ? 1 : 0 ?>;
            const reportId = <?= $report['id'] ?? 0 ?>;
            const currentSql = `<?= $report['sql'] ?? '' ?>`;
        </script>
    </section>

    <?php $this->load->view("modals/folder") ?>

</main>