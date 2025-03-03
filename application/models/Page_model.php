<?php
require_once 'common/CRUD.php';

class Page_model extends CRUD {

    protected $table = 'pages';

    public function list() {
        return $this->db->select("p.*, r.name report_name")->from($this->table . " p")
                            ->join("reports r", "r.id = report_id")
                            ->order_by("p.id", "desc")
                            ->get()
                            ->result_array();
    }

    public function get($id) {
        return $this->db->query("
            SELECT 
                p.id,p.access,p.title page_title, p.access, p.template, j.date,r.name,r.id report_id,r.report_table, q.sql,q.params,q.db,q.unique_field,q.fields_map,
                j.id job_id,
                f.id file_id, f.name file, location, f.created_at file_created_at, f.folder_id
            FROM {$this->table} p
            JOIN (select report_id, max(date) date, max(file_id) file_id, max(query_id) query_id, max(id) id from jobs group by report_id order by report_id desc) j ON j.report_id=p.report_id
            JOIN queries q ON q.id = j.query_id
            JOIN reports r ON r.id = j.report_id
            LEFT JOIN files f ON f.id = j.file_id
            WHERE p.id = ?
        ", [$id])->row_array();
    }

}

