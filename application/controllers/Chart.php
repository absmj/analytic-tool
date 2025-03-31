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
        $result = [];
        $isChartList = array_is_list($charts);
        if (!$isChartList) {
            $charts = [$charts];
        }
        $pivot_ = $this->chart->pivot($table, $charts);
        $pivot = [];
        $oldVal = null;
        $i = 0;
        foreach ($pivot_ as $key =>  $p) {
            foreach ($p as $k => $d) {
                $chartId = $d['chart_id'] ?? 'result';
                $pivot[$chartId][$i][] = $d;

                if ($oldVal != $chartId) $i++;
                else $i = 0;
                $oldVal = $chartId;
            }
        }

        foreach ($charts as $key => $chart) {
            $labels = [];
            $dataset = [];
            $columns = [];

            // if (!isset($chart['data'])) break;

            $chartData = $pivot[$chart['id'] ?? 'result'] ?? [];

            array_walk_recursive($chartData, function ($item, $key) use (&$labels) {
                if ($key == 'row_id')
                    $labels[] = $item;
            });


            array_walk_recursive($chartData, function ($item, $key) use (&$columns) {
                if (!in_array($key, ['row_id', 'chart_id']))
                    $columns[] = $key;
            });

            $slice = $chart['slice'];
            $cols = array_values(array_unique($columns));
            $labels = array_values(array_unique($labels));
            // dd($cols);

            foreach ($slice['values'] as $ks => $vs) {
                // dd()
                // if ($pivot[$key]['chart_id'] != $chart['id']) continue;
                if (!empty($slice['column']) && !empty($slice['row'])) {
                    foreach ($cols as $col) {
                        $data = [];
                        foreach ($chartData[$ks] as $data_) {
                            $data[] = isset($data_['data'][$col]) ? $data_['data'][$col] : 0;
                        }

                        $dataset[] = [
                            'label' => (!empty($vs['field']) && !empty($vs['aggregation'])) ? ($vs['aggregation'] ?? 'COUNT') . " of " . $col . " BY " . $vs['field'] : $col,
                            'name' => $col,
                            'hidden' => false,
                            'data' => $data
                        ];
                    }
                } else {
                    $data = [];
                    foreach ($labels as $lk => $label) {
                        $data[] = $pivot[$chart['id'] ?? 'result'][$ks][$lk]['data'][$label] ?? 0;
                    }

                    $dataset[] = [
                        'data' => $data,
                        'name' => $label,
                        'label' => (!empty($vs['field']) && !empty($vs['aggregation'])) ? ($vs['aggregation'] ?? 'COUNT') . " of " . $label . " BY " . $vs['field'] : $label
                    ];
                }
            }

            foreach ($dataset as $dk => $dv) {
                foreach ($slice['exactFilters'] ?? [] as $ef) {
                    foreach ($ef ?? [] as $e) {
                        if (!empty($slice['column']) && !empty($slice['row'])) {
                            if ($e == $dv['label']) {
                                $dataset[$dk]['hidden'] = true;
                            }
                        } else {
                            $search = array_search($e, $chart['labels']);
                            if ($search >= 0) {
                                $dataset[$dk]['data'][$search] = null;
                            }
                        }
                    }
                    if (!empty($slice['column']) && !empty($slice['row'])) {
                        $diff = array_diff($ef, $cols);
                        $k = 0;

                        foreach ($diff ?? [] as $d) {
                            if (in_array($d, $chart['columns'])) {
                                $dataset[++$k] = [
                                    'label' => $d,
                                    'data' => [],
                                    'hidden' => true
                                ];
                            }
                        }
                    }
                }
            }

            $result[$key]['id'] = $chart['id'] ?? 'result';
            $result[$key]['datasets'] = $dataset;
            $result[$key]['labels'] = empty($chart['labels']) ? $labels : $chart['labels'];
        }

        echo BaseResponse::ok("Success", $result);
    }
}
