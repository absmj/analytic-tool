
class Report_model extends CI_Model {

    protected $table = 'reports';

    public function getAllReports() {
        return $this->db->where('is_deleted', false)->get($this->table)->result_array();
    }

    public function getReport($id) {
        return $this->db->where('id', $id)->where('is_deleted', false)->get($this->table)->row_array();
    }

    public function createReport($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function updateReport($id, $data) {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function deleteReport($id) {
        return $this->updateReport($id, ['is_deleted' => true]);
    }

    public function getReportWithQuery($id) {
        $this->db->select('reports.*, queries.sql');
        $this->db->from($this->table);
        $this->db->join('queries', 'reports.query_id = queries.id', 'left');
        $this->db->where('reports.id', $id);
        return $this->db->get()->row_array();
    }

    // Additional methods as needed
}
