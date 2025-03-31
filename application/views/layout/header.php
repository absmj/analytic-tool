<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?= $this->title ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">


    <!-- Vendor Stylesheets Files -->
    <?php foreach ($this->vendorStyles as $style) : ?>
        <link type="text/css" rel="stylesheet" href="<?= $style ?>">
        </script>
    <?php endforeach ?>


    <!-- Page's JS File -->
    <?php foreach ($this->styles ?? [] as $style) : ?>
        <link rel="stylesheet" href="<?= $style ?>">
        </script>
    <?php endforeach ?>

    <!-- Vendor JS Files -->
    <?php foreach ($this->vendorScripts ?? [] as $script): ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach ?>

    <script>
        const BASE_URL = `<?= BASE_URL_REQUEST ?>`
    </script>
</head>

<body class="<?= !$this->sidebar ? 'toggle-sidebar' : '' ?>">
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="/" class="logo d-flex align-items-center">
                <img src="<?= route_to("assets/img/br-logo.svg") ?>" alt="">
            </a>
            <?php if ($this->sidebar): ?>
                <i class="bi bi-list toggle-sidebar-btn"></i>
            <?php endif ?>
        </div><!-- End Logo -->
        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle " href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li><!-- End Search Icon-->
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <span data-i18n="language" class="d-none d-md-block dropdown-toggle ps-2">Azerbaijani</span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li>
                            <a onclick="window.language.locale = 'az'" class="dropdown-item d-flex align-items-center">
                                <span>Azerbaijani</span>
                            </a>
                        </li>
                        <li>
                            <a onclick="window.language.locale = 'en'" class="dropdown-item d-flex align-items-center">
                                <span>English</span>
                            </a>
                        </li>
                        <li>
                            <a onclick="window.language.locale = 'ru'" class="dropdown-item d-flex align-items-center">
                                <span>Russian</span>
                            </a>
                        </li>
                    </ul><!-- End Profile Dropdown Items -->
                </li>
                <?php if (is_logged()): ?>
                    <li class="nav-item dropdown pe-3">
                        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                            <span class="d-none d-md-block dropdown-toggle ps-2"><?= $_SESSION['fullName'] ?></span>
                        </a><!-- End Profile Iamge Icon -->

                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                            <li class="dropdown-header">
                                <h6><?= $_SESSION['fullName'] ?></h6>
                                <span><?= implode(", ", $_SESSION['groups']) ?></span>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/logout">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Sign Out</span>
                                </a>
                            </li>

                        </ul><!-- End Profile Dropdown Items -->
                    </li><!-- End Profile Nav -->
                <?php endif ?>
            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->