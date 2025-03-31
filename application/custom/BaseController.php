<?php
require_once APPPATH . "custom/BaseException.php";
class BaseController extends CI_Controller
{
    public $title;
    public $vendorScripts;
    public $vendorStyles;
    public $styles;
    public $scripts;
    public $sidebar;

    function __construct()
    {
        parent::__construct();

        if (!is_logged() && !in_array('login', $this->uri->segments)) {
            session_destroy();
            redirect("/login");
            exit;
        } elseif (is_logged() && in_array('login', $this->uri->segments)) {
            redirect("/");
            exit;
        }

        if (isPostRequest()) {
            set_error_handler([BaseException::class, 'api_error_handler']);
            set_exception_handler([BaseException::class, 'api_exception_handler']);
        }

        $this->title = "Hesabatların idarə edilməsi sistemi";

        // Vendor Styles
        $this->vendorStyles[] = BASE_PATH . "assets/vendor/bootstrap/css/bootstrap.min.css";
        $this->vendorStyles[] = BASE_PATH . "assets/vendor/bootstrap-icons/bootstrap-icons.css";
        $this->vendorStyles[] = BASE_PATH . "assets/vendor/boxicons/css/boxicons.min.css";
        $this->vendorStyles[] = BASE_PATH . "assets/vendor/quill/quill.snow.css";
        $this->vendorStyles[] = BASE_PATH . "assets/vendor/quill/quill.bubble.css";
        $this->vendorStyles[] = BASE_PATH . "assets/vendor/remixicon/remixicon.css";
        $this->vendorStyles[] = BASE_PATH . "assets/vendor/simple-datatables/style.css";
        $this->vendorStyles[] = BASE_PATH . "assets/vendor/toastify/toastify.css";
        // Vendor Scripts
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/jquery/jquery.min.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/jquery/jquery-ui.min.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/apexcharts/apexcharts.min.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/bootstrap/js/bootstrap.bundle.min.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/chart.js/chart.umd.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/echarts/echarts.min.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/quill/quill.min.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/simple-datatables/simple-datatables.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/tinymce/tinymce.min.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/php-email-form/validate.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/lodash/lodash.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/axios/axios.js";
        $this->vendorScripts[] = BASE_PATH . "assets/vendor/toastify/toastify.js";
        $this->vendorScripts[] = BASE_PATH . "assets/js/language.js";
        $this->styles[] = BASE_PATH . "assets/css/style.css";
        $this->styles[] = BASE_PATH . "assets/css/cat.css";
        $this->scripts[] = BASE_PATH . "assets/js/app.js";
        $this->scripts[] = BASE_PATH . "assets/js/fetch.js";
        $this->scripts[] = BASE_PATH . "assets/js/main.js";
        $this->scripts[] = BASE_PATH . "assets/js/ui.js";
    }

    public function page($view, $data = [], $sidebar = true, $json = false)
    {
        $this->sidebar = $sidebar;
        $this->header();
        if ($sidebar) {
            $this->sidebar();
        }

        $this->load->view("pages/" . $this->defaultView() . $view, $data, $json);
        $this->footer();
    }

    public function view($view, $data = [], $json = false)
    {
        return $this->load->view("pages/" . $this->defaultView() . $view, $data, $json);
    }

    private function sidebar()
    {
        $this->load->view("layout/sidebar");
        return $this;
    }

    private function header()
    {
        $this->load->view("layout/header");
        return $this;
    }

    private function footer()
    {
        $this->load->view("layout/footer");
        return $this;
    }

    protected function set($variable, $data)
    {
        if (is_array($data)) {
            $this->{$variable} = array_merge($this->{$variable}, array_map(function ($s) {
                return BASE_PATH . "assets/" . $s;
            }, $data));
        } else {
            $this->{$variable}[] = BASE_PATH . "assets/" . $data;
        }
        return $this;
    }

    private function defaultView()
    {
        return get_class_vars(get_class($this))['view']  . "/";
    }
}
