  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">
          <li class="nav-item">
              <a class="nav-link <?= active("reports") ?>" href="<?= route_to("reports") ?>">
                  <i class="bi bi-clipboard-data-fill"></i>
                  <span data-i18n="breadcrumb.reports">Hesabatlar</span>
              </a>
          </li>

          <li class="nav-item">
              <a class="nav-link <?= active("pages") ?>" href="<?= route_to("pages") ?>">
                  <i class="bi bi-columns-gap"></i>
                  <span data-i18n="page.title">Səhifələr</span>
              </a>
          </li>
      </ul>

  </aside><!-- End Sidebar-->