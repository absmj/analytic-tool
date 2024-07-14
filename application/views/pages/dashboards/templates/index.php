<div class="row">
    <?php if(count($filters ??[]) > 0):?>
    <div class="col-2">
        <form class="accordion" style="margin-top: 3.4em; max-height: calc(100vh - 3.5em); overflow-y:auto" id="filtering-form">
            <?php foreach($filters as $k => $f):?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$fieldMaps[$k]?>" aria-expanded="true" aria-controls="collapse<?=$fieldMaps[$k]?>">
                        <?=$k?>
                    </button>
                </h2>
                <div id="collapse<?=$fieldMaps[$k]?>" class="accordion-collapse collapse" data-bs-parent="#filtering-form">
                    <div class="accordion-body">
                        <?php foreach($f as $kv=>$v):?>
                        <div class="form-check">
                            <input data-field="<?=$fieldMaps[$k]?>" class="form-check-input filter_it" type="checkbox" value="<?=$v?>" id="flexCheckChecked<?=$kv?>">
                            <label class="form-check-label" for="flexCheckChecked<?=$kv?>">
                                <?=$v?>
                            </label>
                        </div>
                        <?php endforeach?>
                    </div>
                </div>
            </div>
            <?php endforeach?>
        </form>
    </div>
    <?php endif?>
    <div class="col-10" id="chart-section-visualization">
        <?php $this->load->view("pages/dashboards/templates/".$template)?>
    </div>
</div>
<script>
    const body = {}
    $(".filter_it").change(async function() {
        if(!Array.isArray(body[$(this).attr("data-field")])) body[$(this).attr("data-field")] = new Array;
        const findIndex = body[$(this).attr("data-field")].findIndex(f => f == $(this).val())

        if(findIndex == -1) {
            body[$(this).attr("data-field")].push($(this).val())
        } else {
            body[$(this).attr("data-field")].splice(findIndex, 1)
        }

        


        const urlSearch = new URLSearchParams();

        for(let i in body) {
            urlSearch.append(i, body[i].join(","))
        }
        const request = await $.get(`<?=BASE_URL_REQUEST?>pages/get/<?=$page['id']?>/true?${urlSearch.toString()}`)
        $("#template-view").remove();
        $("#chart-section-visualization").html(request.data.view)
    })
</script>