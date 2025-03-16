<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Files extends \BaseController
{
    private $service;
    private $file;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("File_model", "file");
        $this->service = new \Google\Service\Drive(User::$client);
    }

    public function upload($file_name)
    {
        $file = new Google\Service\Drive\DriveFile(
            [
                'name' => $file_name
            ]
        );
    }

    public function index($folder)
    {
        $data['files'] = $this->file->findByFolderId($folder);
        echo BaseResponse::ok("Successfull", $data = [
            "view" => $this->view("index", $data, true),
            "data" => $data
        ]);
    }

    public function get()
    {
        $csv = base64_decode($this->input->get("file"));
        $csv = file_get_contents($csv);
        echo BaseResponse::ok("Success", csv2json($csv));
    }
}
