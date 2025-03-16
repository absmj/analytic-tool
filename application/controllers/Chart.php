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
        $pivot = $this->chart->pivot($table, $slice);
        $labels = [];
        $dataset = [];
        $cols = [];
        // $labels = array_column($pivot, "row_id");
        foreach ($pivot as $value) {
            $labels = array_merge(array_column($value, 'row_id'), $labels);
            foreach ($value as $k => $val) {
                $cols = array_merge(array_keys($val['data']), $cols);
            }
        }

        $cols = array_values(array_unique($cols));
        $labels = array_values(array_unique($labels));
        foreach ($cols as $col) {
            $data = [];
            foreach ($pivot as $value) {
                foreach ($value as $k => $val) {
                    $data[] = isset($val['data'][$col]) ? $val['data'][$col] : 0;
                }
            }
            $dataset[] = [
                'label' => $col,
                'data' => $data
            ];
        }
        echo BaseResponse::ok("Success", ['dataset' => $dataset, 'labels' => $labels]);
    }
}
