<?php


class CRUD extends CI_Model {

    public function list() {
        return $this->db->get($this->table)->result_array();
    }

    public function get($id) {
        return $this->db->where('id', $id)->get($this->table)->row_array();
    }

    protected function getBy($field, $argument) {
        return $this->db->where_in($field, $argument)->get($this->table)->row_array();
    }

    protected function findBy($field, $argument) {
        return $this->db->where_in($field, $argument)->get($this->table)->result_array();
    }

    public function insert($data) {
        if(!$this->db->insert($this->table, $data))
            throw new Exception(get_called_class() . " :: Yaradılma zamanı xəta baş verdi", 500);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        if(!$this->db->where('id', $id)->update($this->table, $data))
            throw new Exception(get_called_class() . " :: Yeniləmə zamanı xəta baş verdi", 500);
        
        return $this->db->affected_rows();
    }

    public function delete($id) {
        if(!$this->db->where('id', $id)->delete($this->table))
            throw new Exception(get_called_class() . " :: Silinmə zamanı xəta baş verdi", 500);
        
        return $this->db->affected_rows();
    }

    public function __call($name, $arguments) {
        preg_match("/((find|get)By)(.*)/", $name, $matches);

        if(isset($matches[1])) {
            return $this->{$matches[1]}(strtolower($matches[3]), $arguments);
        }
    }
}
