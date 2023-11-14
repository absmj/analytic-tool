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
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <th>ID</th>
                            <th>Adı</th>
                            <th>Qovluq</th>
                            <th>İşləmə tezliyi</th>
                            <th>Yaranma tarixi</th>
                            <th>Yenilənmə tarixi</th>
                            <th>Sonuncu icra tarixi</th>
                            <th>Əməliyyatlar</th>
                        </thead>
                        <tbody>
                            <?php foreach($reports as $report): ?>
                                <tr>
                                    <td><?=$report['id']?></td>
                                    <td><?=$report['name']?></td>
                                    <td><?=$report['folder']?></td>
                                    <td><?=$report['cron']?></td>
                                    <td><?=$report['created_at']?></td>
                                    <td><?=$report['query_created']?></td>
                                    <td><?=$report['last_file']?></td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-sm btn-success"><i class="bi bi-play"></i></button>
                                            <button class="btn btn-sm btn-warning"><i class="bi bi-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>