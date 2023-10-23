<?php
class Report_model extends CI_Model {

    public function get_reports() {
        return $this->db->get('reports')->result();
    }

    public function getQuery($db, $query) {
        $this->load->database($db);

        return $this->db->query($query)->result_array();
    }
    // Add other methods as needed

}
