<?php

require_once "common/CRUD.php";

class Folder_model extends CRUD
{
    // Table name
    protected $table = 'folders';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Get folder hierarchy
    public function list()
    {
        $query = $this->db->query("
            WITH RECURSIVE FolderCTE AS (
                SELECT folder_id, folder_name, parent_folder_id, 1 AS level
                FROM {$this->table}
                WHERE parent_folder_id IS NULL

                UNION ALL

                SELECT f.folder_id, f.folder_name, f.parent_folder_id, level + 1
                FROM {$this->table} f
                JOIN FolderCTE cte ON f.parent_folder_id = cte.folder_id
            )
            SELECT folder_id, folder_name, parent_folder_id, level
            FROM FolderCTE
            ORDER BY COALESCE(parent_folder_id, folder_id), folder_id
        ");

        return $query->result_array();
    }

    public function update($id, $data) {
        if(!$this->db->where('folder_id', $id)->update($this->table, $data))
            throw new Exception(get_called_class() . " :: Yeniləmə zamanı xəta baş verdi", 500);
        
        return $this->db->affected_rows();
    }
}
