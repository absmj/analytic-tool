<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Chart extends BaseController
{
    protected $view = '';
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Chart_model", "chart");
    }

    public function index($type)
    {
        $data['view'] = $this->load->view("charts/" . $type, [], true);
        echo BaseResponse::ok("Success", $data);
    }

    public function pivot($table)
    {
        $slice = post();
        $this->chart->pivot($table, $slice);
        dd($slice);
    }
}
