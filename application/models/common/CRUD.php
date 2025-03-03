<?php
class CRUD extends CI_Model
{
    public function columns($table)
    {
        return $this->db->select("column_name, data_type")->from("information_schema.columns")->where("table_name", $table)->get()->result_array();
    }

    public function list()
    {
        return $this->db->get($this->table)->result_array();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row_array();
    }

    protected function getBy($field, $argument)
    {
        return $this->db->where_in($field, $argument)->get($this->table)->row_array();
    }

    protected function findBy($field, $argument)
    {
        return $this->db->where_in($field, $argument)->get($this->table)->result_array();
    }

    protected function filterBy($field, $argument)
    {
        $this->db->where($field, $argument);
        return $this;
    }

    public function insert($data)
    {
        if (!$this->db->insert($this->table, $data))
            throw new Exception(get_called_class() . " :: Yaradılma zamanı xəta baş verdi", 500);
        return $this->db->insert_id();
    }

    public function insertBatch($data)
    {
        if (!$this->db->insert_batch($this->table, $data))
            throw new Exception(get_called_class() . " :: Yaradılma zamanı xəta baş verdi", 500);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!$this->db->where('id', $id)->update($this->table, $data))
            throw new Exception(get_called_class() . " :: Yeniləmə zamanı xəta baş verdi", 500);

        return $this->db->affected_rows();
    }

    public function updateBatch($id, $data)
    {
        $statements = [];
        foreach ($data as $key => $value) {
            // dd($id);
            if (!isset($value[$id])) continue;
            $statements[] = $this->db->set(array_diff_key($value, array_flip([$id])))->where($id, $value[$id])->get_compiled_update($this->table);
        }
        // dd($data);

        if (count($statements) > 0) $this->db->query(implode(";", $statements));

        return count($statements);
    }

    public function delete($id, $field)
    {
        if (!$this->db->where($field, $id)->delete($this->table))
            throw new Exception(get_called_class() . " :: Silinmə zamanı xəta baş verdi", 500);

        return $this->db->affected_rows();
    }

    public function __call($name, $arguments)
    {
        preg_match("/((find|get|filter)By)(.*)/", $name, $matches);

        if (isset($matches[1])) {
            return $this->{$matches[1]}(camelToSnake($matches[3]), $arguments);
        }
    }
}
