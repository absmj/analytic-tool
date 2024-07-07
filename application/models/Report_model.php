
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

    public function generateCrosstabQuery($slice, $tableName) {
        // Extract the necessary parts from the slice
        $rows = $slice['rows'];
        $columns = $slice['columns'];
        $measures = $slice['measures'];
        $filters = $slice['filters'] ?? [];
    
        // Create the select part of the query
        $selectParts = [];
        foreach ($rows as $row) {
            $selectParts[] = '"' . $row['uniqueName'] . '"';
        }
        $selectPartsString = implode(", ", $selectParts);
    
        // Create the measures part of the query
        $measureParts = [];
        foreach ($measures as $measure) {
            $measureParts[] = strtoupper($measure['aggregation']) . '("' . $measure['uniqueName'] . '") AS "' . $measure['uniqueName'] . '"';
        }
        $measurePartsString = implode(", ", $measureParts);
    
        // Create the where clause based on filters
        $whereParts = [];
        foreach ($filters as $filter) {
            $members = implode("', '", $filter['filter']['members']);
            $whereParts[] = '"' . $filter['uniqueName'] . '" IN (\'' . $members . '\')';
        }
        $whereClause = !empty($whereParts) ? 'WHERE ' . implode(' AND ', $whereParts) : '';
    
        // Create the index columns part of the query
        $indexColumns = [];
        foreach ($columns as $column) {
            $indexColumns[] = '"' . $column['uniqueName'] . '"';
        }
        $indexColumnsString = implode(", ", $indexColumns);
    
        // Create the data selection query
        $dataSelectQuery = "
            SELECT
                $selectPartsString,
                $indexColumnsString,
                $measurePartsString
            FROM
                $tableName
            $whereClause
            GROUP BY
                $selectPartsString, $indexColumnsString
            ORDER BY
                $selectPartsString, $indexColumnsString
        ";
    
        // Create the distinct column values query
        $distinctColumnsQuery = "
            SELECT DISTINCT $indexColumnsString
            FROM $tableName 
            ORDER BY $indexColumnsString
        ";
    
        // Fetch distinct column values dynamically from the database
        $distinctColumnValues = $this->getDistinctColumnValues($distinctColumnsQuery);
    
        // Create the crosstab query
        $crosstabQuery = "
            CREATE EXTENSION IF NOT EXISTS tablefunc;
    
            SELECT *
            FROM crosstab(
                $$ $dataSelectQuery $$,
                $$ $distinctColumnsQuery $$
            ) AS ct (
                $selectPartsString,
        ";
    
        foreach ($distinctColumnValues as $columnValues) {
            $combinedValues = implode(" - ", $columnValues);
            $crosstabQuery .= '"' . $combinedValues . '" numeric, ';
        }
    
        // Remove the trailing comma and space
        $crosstabQuery = rtrim($crosstabQuery, ', ') . "\n);";
    
        return $crosstabQuery;
    }
    
    private function getDistinctColumnValues($query) {
        return [];
        $stmt = $this->db->query($query)->row_array();
        $distinctColumnValues = [];
        foreach($stmt as $row) {
            $distinctColumnValues[] = $row;
        }
        return $distinctColumnValues;
    }
}
