<main id="main" class="main">
    <div class="pagetitle">
        <h1><?= $this->title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><?= $this->title ?></li>
                <li id="stepDescription" class="breadcrumb-item">Siyahı</li>
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
                            <th>Yaranma tarixi</th>
                            <th>Yenilənmə tarixi</th>
                            <th>Template</th>
                            <th>Hesabatın adı</th>
                            <th>Əməliyyatlar</th>
                        </thead>
                        <tbody>
                            <?php foreach($pages as $page): ?>
                                <tr>
                                    <td><?=$page['id']?></td>
                                    <td><?=$page['title']?></td>
                                    <td><?=$page['created_at']?></td>
                                    <td><?=$page['updated_at']?></td>
                                    <td><?=$page['template']?></td>
                                    <td><?=$page['report_name']?></td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <a href="<?=BASE_URL_REQUEST?>page/<?=$page['id']?>" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                            <button class="btn btn-sm btn-success"><i class="bi bi-play"></i></button>
                                            <!-- <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button> -->
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