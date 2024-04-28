
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
                            j.date last_file,
                            fo.folder_name folder
                        ")
                ->join("queries q", "r.query_id = q.id", "left")
                ->join("jobs j", "j.report_id = r.id", "left")
                ->join("crons c", "c.id = q.cron_id")
                ->join("folders fo", "r.folder_id = fo.folder_id")
                ->where("r.is_deleted", false)
                ->from($this->table . " r")
                ->limit($limit)
                ->order_by("id", "desc")
                ->get()
                ->result_array();
    }

    public function get($id) {
        return $this->db->select("r.*, q.sql, q.cron_id,q.db,q.params, f.folder_id, f.folder_name")->where('r.id', $id)
                        ->join("queries q", "q.id=r.query_id")
                        ->join("folders f", "f.folder_id = r.folder_id")
                        ->get($this->table . " r")->row_array();
    }

    public function history($id) {
        return $this->db->query("
                    WITH RECURSIVE history AS (
                        SELECT id, name, base, id as start_id, query_id, 0 AS level
                        FROM reports
                        WHERE base = 0

                        UNION ALL

                        SELECT r.id, r.name, r.base, his.start_id, r.query_id, his.level + 1
                        FROM history his
                        JOIN reports r ON r.base = his.id
                    )
            select * from reports join (select unnest(children) history_id from (select (array_agg(id order by level)) as children
            from history
            group by start_id) a where ? = ANY(a.children)) on history_id=id order by id desc;
        
        ", [$id])->result_array();
    }

    public function run($database, $sql, $params = []) {
        try {
            $this->load->database($database);
            $sql = preg_replace("/\{@.*?@\}/muis", "?", $sql);
            return $this->db->query($sql, $params)->result_array();
        } catch(Exception $e) {
            throw new Exception($e);
        }
    }

    public function getReportFiles($report_id) {
        return $this->db->select("f.id, f.folder_id, f.name, j.date, j.is_cron, f.location, f.created_at,f.type, r.id")
                    ->from($this->table . " r")
                    ->join("jobs j", "j.report_id=r.id")
                    ->join("files f", "f.id = j.file_id")
                    ->order_by("f.id", "desc")
                    ->where("r.id", $report_id)
                    ->get()
                    ->result_array();
    }
}
