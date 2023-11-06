<?php

class Folder_model extends CI_Model
{
    // Table name
    private $table = 'folders';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Get folder hierarchy
    public function get_folders()
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

    // Insert new folder
    public function insert_folder($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Update folder
    public function update_folder($folder_id, $data)
    {
        $this->db->where('folder_id', $folder_id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    // Delete folder and its children
    public function delete_folder($folder_id)
    {
        $this->db->where('folder_id', $folder_id);
        $this->db->delete($this->table);

        // You might also want to handle deleting children here

        return $this->db->affected_rows();
    }
}
