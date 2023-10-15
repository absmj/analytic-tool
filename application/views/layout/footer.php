  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      Pərakəndə satışlar departamenti <strong><span>Bank Respublika</span></strong>.
    </div>
  </footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<?php foreach($this->vendorScripts ?? [] as $script): ?>
<script src="<?=$script?>"></script>
<?php endforeach ?>

<!-- Page's JS File -->
<?php foreach($this->scripts ?? [] as $script): ?>
<script src="<?=$script . "?v=" . mt_rand(0, 20)?>"></script>
<?php endforeach ?>