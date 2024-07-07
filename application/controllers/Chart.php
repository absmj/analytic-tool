<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Chart extends BaseController {

	public function __construct()
	{
		parent::__construct();

	}

    public function index($type) {
        $data['view'] = $this->load->view("charts/" . $type, [], true);
        echo BaseResponse::ok("Success", $data);
    }

}