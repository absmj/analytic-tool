
<?php

require_once "common/CRUD.php";

class Query_model extends CRUD {

    protected $table = 'queries';

    public function list() {
        return $this->db->where('is_deleted', false)->get($this->table)->result_array();
    }

    public function get($id) {
        return $this->db->where('id', $id)->where('is_deleted', false)->get($this->table)->row_array();
    }

    // public function getQueryWithCron($id) {
    //     $this->db->select('queries.*, crons.job');
    //     $this->db->from($this->table);
    //     $this->db->join('crons', 'queries.cron_id = crons.id', 'left');
    //     $this->db->where('queries.id', $id);
    //     return $this->db->get()->row_array();
    // }

    // Additional methods as needed
}
