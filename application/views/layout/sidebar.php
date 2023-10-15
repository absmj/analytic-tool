  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">
          <li class="nav-item">
              <a class="nav-link <?=active("reports")?>" href="<?=route_to("reports")?>">
                  <i class="bi bi-clipboard-data-fill"></i>
                  <span>Hesabatlar</span>
              </a>
          </li>

          <li class="nav-item">
              <a class="nav-link <?=active("pages")?>" href="<?=route_to("pages")?>">
                  <i class="bi bi-columns-gap"></i>
                  <span>Səhifələr</span>
              </a>
          </li>

          <li class="nav-item">
              <a class="nav-link <?=active("variables")?>" href="<?=route_to("variables")?>">
                  <i class="bi bi-braces-asterisk"></i>
                  <span>Dəyişənlər</span>
              </a>
          </li>
      </ul>

  </aside><!-- End Sidebar-->