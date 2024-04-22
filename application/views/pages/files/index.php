<div id="fexplorer" class="row mt-4">
    <?php foreach ($files as $file) : ?>
        <div class="col-1 mx-2 file-explorer-file p-3 pb-0" file-id="<?=$file['id']?>" file="<?=base64_encode($file['location'])?>" data-selected="0">
            <img src="<?= BASE_PATH ?>assets/img/<?= $file['type'] ?>.svg" class="card-img-top" alt="" />
            <hr>
            <h6 class="text-center"><?= $file['name'] ?></h6>
        </div>
    <?php endforeach ?>
</div>
<div class="table-responsive">
    <table class="table table-bordered dataTable" id="table-component"></table>
</div>
