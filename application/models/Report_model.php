
<?php

require_once "common/CRUD.php";

class Report_model extends CRUD {

    protected $table = 'reports';

    public function list($limit = 10) {
        return $this->db
                ->select("{$this->table}.*, 
                            q.id query_id, 
                            q.sql,
                            q.db,
                            q.created_at query_created, 
                            c.job,
                            c.title,
                            f.id file_id,
                            f.name file_name
                        ")
                ->join("queries q", "{$this->table}.query_id = q.id")
                ->join("files f", "f.query_id = {$this->table}.id", "left")
                ->join("crons c", "c.id = q.cron_id")
                ->limit($limit)
                ->get($this->table)
                ->result_array();
    }

    public function get($id) {
        return $this->db->where('id', $id)->where('is_deleted', false)->get($this->table)->row_array();
    }

    public function run($database, $sql) {
        try {
            $this->load->database($database);
            return $this->db->query($sql)->result_array();
        } catch(Exception $e) {
            throw new Exception($e);
        }
    }

    // Additional methods as needed
}
