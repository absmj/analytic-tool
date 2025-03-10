<?php
require_once 'common/CRUD.php';

class Chart_model extends CRUD
{

    protected $table = 'charts';

    public function pivot($table, $slice)
    {
        try {
            if (is_array($slice['values'])) {
                $sql = [];
                foreach ($slice['values'] as $value) {
                    $sql[] = $this->db->compile_binds("SELECT * FROM psd_analytic(
                        ?, ?, ?, ?, ?, ?::json)", [
                        $table,
                        $this->checkEmpty($slice['row'], $slice['column']),
                        $this->checkEmpty($slice['column'], $slice['row']),
                        $this->checkEmpty($value['field'], $this->checkEmpty($slice['row'], $slice['column'])),
                        $this->checkEmpty(strtolower($value['aggregation']), 'count'),
                        is_array($slice['filter'] ?? null) ? json_encode($slice['filter']) : NULL
                    ]);
                }

                $sql = implode("\nUNION ALL\n", $sql);
                $result = $this->db->query($sql)->result_array();
                $result = array_map(function ($item) {
                    return json_decode($item['result'], 1);
                }, $result);
                dd($result);
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    private function checkEmpty($field, $reserve)
    {
        return empty($field) ? $reserve : $field;
    }
}
