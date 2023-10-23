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
                            <label for="validationDefault01" class="form-label">Hesabatın adı</label>
                            <input value="test" model="Hesabatın adı" type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="validationDefault04" class="form-label">Tipi</label>
                            <div class="col-12 d-flex p-2 justify-content-between">
                                <label class="text-muted" for="">Statik</label>
                                <div class="form-check form-switch">
                                    <input model="Hesabatın tipi" name="type" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                </div>
                                <label class="text-muted" for="">Dinamik</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="validationDefault04" class="form-label">Baza</label>
                            <select name="database" class="form-select" required model="Baza">
                                <option disabled value="">Choose...</option>
                                <?php foreach (dblist() as $db) : ?>
                                    <option selected value="<?= $db ?>"><?= $db ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="">
                                <label for="sql" class="form-label">SQL</label>
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
                            <div class="col-8">
                                <div id="pivot" class="table-responsive"></div>
                            </div>
                            <div class="col-4" id="chart-container">
                                <canvas id="chart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div id="fields"></div>

                    <div id="navigation-steps" class="mt-5 d-flex justify-content-between">
                        <button id="prev" class="btn btn-warning d-none">Əvvəlki</button>
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
</main>