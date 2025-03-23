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
        $charts = post('charts');

        $isChartList = array_is_list($charts);
        if (!$isChartList) {
            $charts = [$charts];
        }
        $pivot = $this->chart->pivot($table, $charts);
        foreach ($charts as &$chart) {
            foreach ($pivot as $p_) {
                foreach ($p_ as $v) {
                    if (isset($chart['id']) && $chart['id'] == $v['chart_id']) {
                        $chart['data'][] = $v;
                    } else if (!isset($chart['id'])) {
                        $chart['data'][] = $v;
                    }
                }
            }
        }

        // dd($charts);

        // $labels = array_column($pivot, "row_id");
        foreach ($charts as &$chart) {
            $labels = [];
            $dataset = [];
            $cols = [];
            if (!isset($chart['data'])) break;
            $labels = array_column($chart['data'], 'row_id');
            // dd($chart['data']);
            $cols = array_column($chart['data'], 'data');
            $columns = [];
            array_walk_recursive($cols, function ($item, $key) use (&$columns) {
                return $columns[] = $key;
            });
            $slice = $chart['slice'];
            $cols = array_values(array_unique($columns));
            $labels = array_values(array_unique($labels));
            // dd($cols);

            foreach ($slice['values'] as $ks => $vs) {
                foreach ($cols as $col) {
                    $data = [];
                    foreach ($chart['data'] as $data_) {
                        $data[] = isset($data_['data'][$col]) ? $data_['data'][$col] : 0;
                    }
                    $dataset[] = [
                        'label' => (!empty($vs['field']) && !empty($vs['aggregation'])) ? ($vs['aggregation'] ?? 'COUNT') . " of " . $col . " BY " . $vs['field'] : $col,
                        'data' => $data
                    ];
                }
            }
            // dd($dataset);
            $chart['options']['data']['datasets'] = $dataset;
            $chart['options']['data']['labels'] = $labels;
        }

        echo BaseResponse::ok("Success", ['charts' => $charts]);
    }
}
