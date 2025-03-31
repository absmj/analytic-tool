<main id="main" class="main">
    <div class="pagetitle">
        <h1 data-i18n="page.title"><?= $this->title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html" data-i18n="breadcrumb.reports">Hesabatlar</a></li>
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
                <div class="d-flex justify-content-end">
                    <a class="btn btn-sm btn-primary" href="<?= BASE_URL_REQUEST ?>reports/create" data-i18n="button.create_report">Hesabat yarat</a>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <th data-i18n="table.id">ID</th>
                            <th data-i18n="table.name">Adı</th>
                            <th data-i18n="table.folder">Qovluq</th>
                            <th data-i18n="table.frequency">İşləmə tezliyi</th>
                            <th data-i18n="table.created">Yaranma tarixi</th>
                            <th data-i18n="table.updated">Yenilənmə tarixi</th>
                            <th data-i18n="table.last_exec">Sonuncu icra tarixi</th>
                            <th data-i18n="table.actions">Əməliyyatlar</th>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr data-report="<?= $report['id'] ?>">
                                    <td><?= $report['id'] ?></td>
                                    <td><?= $report['name'] ?></td>
                                    <td><?= $report['folder'] ?></td>
                                    <td><?= $report['cron'] ?></td>
                                    <td><?= $report['created_at'] ?></td>
                                    <td><?= $report['query_created'] ?></td>
                                    <td class="last-run"><?= $report['last_file'] ?></td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <button data-params='<?= $report['params'] ?? "null" ?>' data-id="<?= $report['id'] ?>" class="btn btn-sm btn-success run-report" data-i18n="button.run"><i class="bi bi-play"></i></button>
                                            <a href="<?= BASE_URL_REQUEST ?>report/<?= $report['id'] ?>/edit" class="btn btn-sm btn-warning" data-i18n="button.edit"><i class="bi bi-pencil"></i></a>
                                            <a href="<?= BASE_URL_REQUEST ?>dashboard/create/<?= $report['id'] ?>" class="btn btn-sm btn-primary" data-i18n="button.add_dashboard"><i class="bi bi-plus"></i></a>
                                            <button data-id="<?= $report['id'] ?>" class="btn btn-sm btn-danger remove-report" data-i18n="button.delete"><i class="bi bi-trash"></i></button>
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

<div class="modal fade" id="queryparams" tabindex="-1" aria-labelledby="queryparams" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="qyerparamstitle">Query params</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form name="queryparams-form">
                    <fieldset>

                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="runParams(this)" id="run-query" type="button" class="btn btn-primary">Run</button>
            </div>
        </div>
    </div>
</div>
<script>
    const paramsForm = $('[name="queryparams-form"]');
    $(".run-report").click(function() {
        if ($(this).data("params") != 'null') {
            const params = $(this).data("params");
            paramsForm.find("fieldset").html("");
            Object.keys(params).forEach(param => {
                paramsForm.find("fieldset").append(`
                    <div class="mb-3">
                        <label for="${param}" class="col-form-label">${param}:</label>
                        <input value="${params[param]}" required name="${param}" type="text" class="form-control" id="${param}">
                    </div>`)
            })

            $("#run-query").attr("data-id", $(this).data("id"));
            $("#queryparams").modal("show");
            return;
        }

        reportRun({
            id: $(this).data("id"),
            onSuccess: (d) => {
                $(this).closest("tr").find('.last-run').text(d.data.data.date)
            }
        })
    })

    function runParams(e) {
        if (!paramsForm[0].checkValidity()) {
            paramsFormp[0].reportValidity();
            return;
        }
        const paramsData = new FormData(paramsForm[0]);
        const params = Object.fromEntries(paramsData);

        reportRun({
            id: $(e).data("id"),
            query: params,
            onSuccess: (d) => {
                $(`tr[data-report="${$(e).data("id")}"]`).find('.last-run').text(d.data.data.date)
                $("#queryparams").modal("hide");
            }
        })
    }


    $(".remove-report").click(function() {
        if (!confirm(window?.locale?.confirm || "Əminsinizmi?")) return;
        reportDelete({
            id: $(this).data("id"),
            onSuccess: () => $(this).closest("tr").remove()
        })
    })
</script>