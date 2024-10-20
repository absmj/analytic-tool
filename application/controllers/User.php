<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class User extends BaseController {

	public function __construct()
	{
		parent::__construct();
	}

    public function index() {
        echo BaseResponse::ok("Success", [
            "ses_fullname" => "Test Test",
            "ses_groups" => ["576", "445", "433"],
            "isAdmin" => true
        ]);
    }

    public function groups() {
        echo BaseResponse::ok("Success", [
            "576" => "SphereAdmin",
            "614" => "Universal",
            "11" => "All"
        ]);
    }

}