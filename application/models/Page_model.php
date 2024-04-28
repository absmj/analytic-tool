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
        return $this->db->select("p.id,p.title page_title,j.date,r.name,r.id report_id,q.sql")
                    ->from($this->table ." p")
                    ->join("(select * from jobs where report_id=p.report_id order by id desc limit 1) j", "j.report_id=p.report_id")
                    ->join("queries q", "q.id = j.query_id")
                    ->join("reports r", "r.id=j.report_id")
                    ->where("p.id", $id)
                    ->get()
                    ->result_array();
    }

}

