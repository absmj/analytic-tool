<?php
require_once 'common/CRUD.php';

class Chart_model extends CRUD
{

    protected $table = 'charts';

    public function pivot($table, $charts)
    {
        $filters = [];
        $isChartList = array_is_list($charts);
        if (!$isChartList) {
            $charts = [$charts];
        }
        // dd($charts);
        $sql = [];
        foreach ($charts as $chart) {
            $slice = $chart['slice'];
            foreach ($slice['filters'] as $filter) {
                foreach ($slice['filter'] as $key => $value) {
                    if (empty($value)) continue;
                    if ($key == $filter) {
                        $filters[$key] = $value;
                    }
                }
            }
            // var_dump($sql);
            if (is_array($slice['values'])) {

                foreach ($slice['values'] as $value) {
                    $sql[] = $this->db->compile_binds("SELECT * FROM psd_analytic(
                            ?, ?, ?, ?, ?, ?, ?::json, ?::json)", [
                        $table,
                        checkEmpty($slice['row'], $slice['column']),
                        checkEmpty($slice['column'], $slice['row']),
                        checkEmpty($value['field'], checkEmpty($slice['row'], $slice['column'])),
                        checkEmpty(strtolower($value['aggregation']), 'count'),
                        isset($chart['id']) ? $chart['id'] : 'result',
                        !empty($filters) ? json_encode($filters) : NULL,
                        isset($slice['exactFilters']) && !empty($slice['exactFilters']) ? json_encode($slice['exactFilters']) : NULL
                    ]);
                }
            }
        }
        $sql = implode("\nUNION ALL\n", $sql);
        // dd($sql);
        $result = $this->db->query($sql)->result_array();
        // dd($result);
        $result = array_map(function ($item) {
            return json_decode($item['result'], 1);
        }, $result);
        return $result;
    }
}
