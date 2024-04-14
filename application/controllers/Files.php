<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Files extends BaseController
{
	public function __construct()
	{
		parent::__construct();
        $this->load->model("File_model", "file");
	}

	public function index($folder)
	{
        $data['files'] = $this->file->findByFolderId($folder);
        echo BaseResponse::ok("Successfull", $data = [
            "view" => $this->view("index", $data, true),
            "data" => $data
        ]); 
	}

    public function get() {
        $csv = base64_decode($this->input->get("file"));
        $csv = file_get_contents($csv);
        echo BaseResponse::ok("Success", csv2json($csv));
    }


}
