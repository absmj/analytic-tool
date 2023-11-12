<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Folders extends CI_Controller {

    public function __construct() {
        parent::__construct();
        header("Content-Type: application/json");
        $this->load->model('Folder_model');
    }

    // Get all folders (accessible via POST)
    public function all() {
        $folders = $this->Folder_model->list();
        echo json_encode($folders);
    }

    // Create a new folder (accessible via POST)
    public function create() {
        $data = array(
            'folder_name' => $this->input->post('folder_name'),
            'parent_folder_id' => $this->input->post('parent_folder_id')
        );

        $folder_id = $this->Folder_model->insert($data);

        echo json_encode(array('folder_id' => $folder_id));
    }

    // Update a folder (accessible via POST)
    public function update() {
        $folder_id = $this->input->post('folder_id');
        $data = array(
            'folder_name' => $this->input->post('folder_name')
        );

        $affected_rows = $this->Folder_model->update($folder_id, $data);

        echo json_encode(array('affected_rows' => $affected_rows));
    }

    // Delete a folder and its children (accessible via POST)
    public function delete() {
        $folder_id = $this->input->post('folder_id');

        $affected_rows = $this->Folder_model->delete($folder_id);

        echo json_encode(array('affected_rows' => $affected_rows));
    }
}
