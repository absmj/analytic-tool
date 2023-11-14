
<?php

require_once "common/CRUD.php";

class Report_model extends CRUD {

    protected $table = 'reports';

    public function list($limit = 10) {
        return $this->db
                ->select("r.*, 
                            q.id query_id, 
                            q.sql,
                            q.db,
                            q.created_at query_created, 
                            c.job,
                            c.title cron,
                            f.id file_id,
                            f.name file_name,
                            f.created_at last_file,
                            fo.folder_name folder
                        ")
                ->join("queries q", "r.query_id = q.id", "left")
                ->join("files f", "f.query_id = r.id", "left")
                ->join("crons c", "c.id = q.cron_id")
                ->join("folders fo", "r.folder_id = fo.folder_id")
                ->from($this->table . " r")
                ->limit($limit)
                ->get()
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
