  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      Pərakəndə satışlar departamenti <strong><span>Bank Respublika</span></strong>.
    </div>
  </footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


<!-- Toast BS 5.3 -->
<div class="toast-container position-fixed p-3">
  <div id="toaster-bs" class="toast align-items-center border-0 reaction" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toast-message">
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<!-- Loading -->
<div id="loading" class="position-fixed w-100 h-100 top-0 d-flex justify-content-center align-items-center d-none" style="background-color: rgba(0, 0, 0, 0.15);z-index:281223">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Yüklənir...</span>
  </div>
</div>

<!-- Page's JS File -->
<?php foreach($this->scripts ?? [] as $script): ?>
<script src="<?=$script . "?v=" . mt_rand(0, 20)?>"></script>
<?php endforeach ?>