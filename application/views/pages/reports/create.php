<main id="main" class="main">
    <div class="pagetitle">
        <h1><?= $this->title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Hesabatlar</a></li>
                <li class="breadcrumb-item"><?= $this->title ?></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section contact">

        <div class="row gy-4">

            <div class="col-12">
                <div class="card p-4">
                    <!-- Browser Default Validation -->
                    <form class="row g-3">
                        <div class="col-md-4">
                            <label for="validationDefault01" class="form-label">Hesabatın adı</label>
                            <input type="text" class="form-control" id="validationDefault01" value="John" required>
                        </div>
                        <div class="col-md-4">
                            <label for="validationDefault02" class="form-label">Tipi</label>
                            <input type="text" class="form-control" id="validationDefault02" value="Doe" required>
                        </div>
                        <div class="col-md-4">
                            <label for="validationDefault04" class="form-label">Baza</label>
                            <select class="form-select" id="validationDefault04" required>
                                <option selected disabled value="">Choose...</option>
                                <?php foreach(dblist() as $db): ?>
                                <option value="<?=$db?>"><?=$db?></option>
                                <?php endforeach?>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="">
                                <label for="sql" class="form-label">SQL</label>
                                <textarea class="form-control" placeholder="Address" id="sql" style="height: 100px;"></textarea>
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Submit form</button>
                        </div>
                    </form>
                    <!-- End Browser Default Validation -->
                </div>

            </div>

        </div>

    </section>
</main>
