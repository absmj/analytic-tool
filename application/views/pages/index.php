<main id="main" class="main">
    <div class="pagetitle">
        <h1 data-i18n="page.title"><?= $this->title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item" data-i18n="page.title"><?= $this->title ?></li>
                <li id="stepDescription" class="breadcrumb-item" data-i18n="breadcrumb.list">Siyahı</li>
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
                            <th data-i18n="table.id">ID</th>
                            <th data-i18n="table.name">Adı</th>
                            <th data-i18n="table.created">Yaranma tarixi</th>
                            <th data-i18n="table.updated">Yenilənmə tarixi</th>
                            <th data-i18n="table.report">Hesabatın adı</th>
                            <th data-i18n="table.last_exec">Sonuncu icra tarixi</th>
                            <th data-i18n="table.actions">Əməliyyatlar</th>
                        </thead>
                        <tbody>
                            <?php foreach ($pages as $page): ?>
                                <tr>
                                    <td><?= $page['id'] ?></td>
                                    <td><?= $page['title'] ?></td>
                                    <td><?= $page['created_at'] ?></td>
                                    <td><?= $page['updated_at'] ?></td>
                                    <td><?= $page['report_name'] ?></td>
                                    <td><?= $page['last_file'] ?></td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <a href="<?= BASE_URL_REQUEST ?>page/<?= $page['id'] ?>" class="btn btn-sm btn-primary" data-i18n="button.view"><i class="bi bi-eye"></i></a>
                                            <a href="<?= BASE_URL_REQUEST ?>page/<?= $page['id'] ?>/edit" class="btn btn-sm btn-warning" data-i18n="button.edit"><i class="bi bi-pencil"></i></a>
                                            <button data-id="<?= $page['id'] ?>" class="btn btn-sm btn-danger remove-page" data-i18n="button.delete"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    $(".remove-page").click(function() {
        if (!confirm(window?.locale?.confirm || "Əminsinizmi?")) return;
        dashboardDelete({
            id: $(this).data("id"),
            onSuccess: () => $(this).closest("tr").remove()
        })
    })
</script>