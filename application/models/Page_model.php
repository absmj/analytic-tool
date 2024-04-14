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
        return $this->db->select("p.*,c.*,j.date,r.name")
                    ->from("charts c")
                    ->join($this->table ." p", "p.id=c.page_id")
                    ->join("jobs j", "j.report_id=p.report_id")
                    ->join("reports r", "r.id=p.report_id")
                    ->where("p.id", $id)
                    ->get()
                    ->result_array();
    }

}

