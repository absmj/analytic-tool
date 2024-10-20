<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class Cron extends BaseController {

	public function __construct()
	{
		parent::__construct();
        $this->load->model("Cron_model", "cron");
	}

    public function list() {
        echo BaseResponse::ok("Success", $this->cron->list());
    }

}