<?php
class Report_model extends CI_Model {

    public function get_reports() {
        return $this->db->get('reports')->result();
    }

    // Add other methods as needed

}
