<?php
require_once 'common/CRUD.php';

class Page_model extends CRUD {

    protected $table = 'pages';

    public function list() {
        return $this->db->select("p.*, r.name report_name")->from($this->table . " p")
                            ->join("reports r", "r.id = report_id")
                            ->get()
                            ->result_array();
    }

    public function get($id) {
        return $this->db->query("
            SELECT 
                p.id,p.title page_title, p.template, j.date,r.name,r.id report_id,r.report_table, q.sql,q.params,q.db,q.unique_field,q.fields_map,
                j.id job_id,
                f.id file_id, f.name file, location, f.created_at file_created_at, f.folder_id
            FROM {$this->table} p
            JOIN LATERAL (select * from jobs where report_id=p.report_id order by id desc limit 1) j ON true
            JOIN queries q ON q.id = j.query_id
            JOIN reports r ON r.id = j.report_id
            LEFT JOIN files f ON f.id = j.file_id
            WHERE p.id = ?
        ", [$id])->row_array();
    }

}

