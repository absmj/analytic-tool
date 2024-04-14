<div class="modal fade" id="folder-tree" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hesabat qovluğunun seçilməsi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="folder-error" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
                    <button id='folder-modal-close' type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div id="folder-success" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                    <button id='folder-modal-close' type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="folder-tree-wrapper">
                    <ul class="folder-tree" id="folderTree">

                    </ul>
                </div>

            </div>
            <div class="modal-footer">
                <button data-bs-dismiss="modal" type="button" class="btn btn-primary" id="select-folder">Seç</button>
            </div>
            <script>
                const folders = <?= json_encode($folders ?? []) ?>;
            </script>
            <script src="<?= BASE_PATH ?>assets/js/folder-explorer.js"></script>
            <script>
                folderExplorer.init()
                document.getElementById("select-folder").addEventListener("click", (e) => {
                    const el = document.querySelector("#folder > option:first-child")
                    const folderName = document.querySelector("[name='folder_name'")
                    el.value = folderExplorer.selected.id
                    el.textContent = folderExplorer.selected.text
                    folderName.value = el.textContent;
                })
            </script>
        </div>
    </div>
</div><!-- End Modal Dialog Scrollable-->