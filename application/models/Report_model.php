<?php
require_once "common/CRUD.php";

class Report_model extends CRUD
{

    protected $table = 'reports';

    public function list($limit = 10)
    {
        return $this->db
            ->select("r.*,
                    q.id query_id, 
                    q.sql,
                    q.db,
                    q.params,
                    q.created_at query_created, 
                    c.job,
                    c.title cron,
                    j.date last_file,
                    fo.folder_name folder
                ")
            ->join("queries q", "r.query_id = q.id", "left")
            ->join("(select report_id, max(date) date, max(id) id from jobs group by report_id) j", "j.report_id = r.id")
            ->join("crons c", "c.id = q.cron_id")
            ->join("folders fo", "r.folder_id = fo.folder_id")
            ->where("r.is_deleted", false)
            ->from($this->table . " r")
            ->limit($limit)
            ->order_by("id", "desc")
            ->get()
            ->result_array();
    }

    public function get($id)
    {
        return $this->db->select("r.*, q.sql, q.cron_id,q.db,q.params,q.unique_field,q.fields_map, f.folder_id, f.folder_name")->where('r.id', $id)
            ->join("queries q", "q.id=r.query_id")
            ->join("folders f", "f.folder_id = r.folder_id")
            ->get($this->table . " r")->row_array();
        // return $this->db->select("r.*, q.sql, q.db,q.params,q.unique_field,q.fields_map, f.folder_id, f.folder_name, fi.location file, fi.id file_id")->where('r.id', $id)
        //     ->join("(select report_id, max(date) date, max(file_id) file_id, max(query_id) query_id, max(id) id from jobs group by report_id order by report_id desc) j", "j.report_id = r.id")
        //     ->join("files fi", "fi.id = j.file_id")
        //     ->join("queries q", "q.id=r.query_id")
        //     ->join("folders f", "f.folder_id = r.folder_id")
        //     ->get($this->table . " r")->row_array();
    }

    public function history($id)
    {
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

    public function run($database, $sql, $params = [])
    {
        try {
            $this->load->database($database);
            $sql = preg_replace("/\{@.*?@\}/muis", "?", $sql);
            $result = $this->db->query($sql, $params)->result_array();
            return $result;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function getReportData($table, $limit = null, $params = [], $filter = [], $filtering = [])
    {
        try {
            if (count($filter ?? []) > 0) {
                foreach ($filter as $f) {
                    if ($this->db->field_exists($f, $table)) {
                        if (isset($filtering[$f]) && !empty($filtering[$f])) {
                            $this->db->where_in($f, explode(",", $filtering[$f]));
                        }
                    }
                }
            }
            if (count($params ?? []) > 0) {
                foreach ($params as $p) {
                    if ($this->db->field_exists($p, $table)) {
                        if (isset($filtering[$p])  && !empty($filtering[$f])) {
                            $this->db->where_in($p, explode(",", $filtering[$p]));
                        }
                    }
                }
            }

            if ($limit && is_numeric($limit)) {
                $this->db->limit($limit);
            }

            $result = $this->db->select()->from($table)->get()->result_array();
            foreach ($result as &$r) {
                foreach ($r as $rk => $v) {
                    if (preg_match("/date$/i", $v)) {
                        $timestamp = strtotime($v);
                        $r[$rk . ".Date"] = date('d', $timestamp);
                        $r[$rk . ".Month"] = date('m', $timestamp);
                        $r[$rk . ".Year"] = date('Y', $timestamp);
                        $r[$rk . ".Timestamp"] = $timestamp;
                    }
                }
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function getReportFiles($report_id)
    {
        return $this->db->select("f.id, f.folder_id, f.name, j.date, j.is_cron, f.location, f.created_at,f.type, r.id")
            ->from($this->table . " r")
            ->join("jobs j", "j.report_id=r.id")
            ->join("files f", "f.id = j.file_id")
            ->order_by("f.id", "desc")
            ->where("r.id", $report_id)
            ->get()
            ->result_array();
    }

    public function createOrInsertOrUpdateReport($database, $table, $unique, $query, $params = [])
    {
        $tableExists = true;
        $this->db->trans_start();
        if (!$this->db->table_exists($table)) {
            $tableExists = false;
            $this->db->query("CREATE TABLE {$table} AS {$query}");
        }

        if (!$this->db->field_exists('id', $table)) {
            $this->db->query("ALTER TABLE {$table} ADD COLUMN id SERIAL PRIMARY KEY", [$table]);
        }


        if ($tableExists) {
            $queryResult = $this->run($database, $query, $params);
            $statements = [];
            $insertBatch = [];
            if ($unique) {
                $queryUnique = array_column($queryResult, $unique);
                $existData = $this->db->query("select * from {$table} where {$unique} in ?", [$queryUnique])->result_array();
                // $existData = $this->db->select("*")->from($table)->where_in($unique, $queryUnique)->get();
                $existDataUnique = array_column($existData, $unique);

                $keys = array_keys($queryResult[0]);
                // dd([$keys, $queryResult]);
                foreach ($queryResult as $data) {
                    if (in_array($data[$unique], $existDataUnique)) {
                        $update = "UPDATE $table SET ";
                        $setting = [];
                        foreach ($keys as $key) {
                            $d = $data[$key];
                            if (is_null($d)) {
                                $d = "null";
                            } else {
                                $d = "'" . $d . "'";
                            }

                            if (is_null($data[$unique])) {
                                $data[$unique] = "null";
                            } else {
                                $data[$unique] = $data[$unique];
                            }

                            $setting[] = '"' . $key . '"' . " = " . $d;
                        }
                        $update .= implode(",", $setting) . " WHERE " . $unique . " = '" . $data[$unique] . "'";
                        $statements[] = $update;
                    } else {
                        $insertBatch[] = $data;
                    }
                }


                if (count($statements) > 0) {
                    pg_query($this->db->conn_id, implode(";", $statements));
                }
            }

            if (count($insertBatch) > 0) {
                $this->db->insert_batch($table, $insertBatch);
            }
        }
        $this->db->trans_complete();
    }


    public function getFieldDistinctValues($table, $fields, $unique = null)
    {
        $jsonBuildObj = [];
        foreach ($fields as $key => $field) {
            if ($key == $unique) continue;
            if (preg_match("/(^id$)|(_id$)/", $key)) continue;
            $jsonBuildObj[] = "'" . $key . "', json_agg(distinct \"" . $key . "\")";
        }
        $result = count($jsonBuildObj) > 0 ? $this->db->query("SELECT json_build_object(" . implode(",", $jsonBuildObj) . ") filtering FROM $table")->row_array() : [];
        return json_decode($result['filtering'], 1);
    }

    public function getCsvFile($location)
    {
        return file_get_contents(APPPATH . 'reports/66893b3b8804b.csv');
        // dd(Request::get(config_item('ms-file') . '/get.php?uniqueField=' . $location));
        return Request::get(config_item('ms-file') . '/get.php?uniqueField=' . $location)['data'];
    }

    public function deleteFile($location)
    {
        return Request::delete(config_item('ms-file') . '/delete.php?uniqueField=' . $location);
    }

    public function upload($file)
    {
        $file = new CURLFile($file, 'text/csv', uniqid() . '.csv');
        $request = Request::post(config_item('ms-file') . '/uploader.php', ['file' => $file]);
        return $request['data'];
    }
}
