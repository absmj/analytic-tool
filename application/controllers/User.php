<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class User extends BaseController {

	public function __construct()
	{
		parent::__construct();
	}

    public function index() {
        $user = json_decode('{"__ci_last_regenerate": 1729506027,"ses_authtoken": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbGllbnRJZGVudGlmaWVyIjoiVkVHQS01MjM2OTc3NDQ0NTQiLCJpc3MiOiJhdXRoMCIsImNoYW5uZWwiOiJTUEhFUkUiLCJ1c2VyTmFtZSI6IkxBTEFIIiwiZXhwIjoxNzI5NTQ5MTc5fQ.Bw2BLDIawkBa99xVqiX_Nd9rfLwcu_njNbaU-ynMhys","user_mail": "Lala.Farajova@bankrespublika.az","user_login": "LalaH","logged_in": 1,"ses_hrb_fullname": "Lalə Fərəcova Əflatun qızı","ses_hrb_job_position": "Baş mütəxəssis","ses_hrb_department": "MXD / VİP şöbəsi","ses_hrb_department_code": "03470000","ses_branch_code": "2","ses_hrb_employee_code": "460686","ses_hrb_cif": "1460686","ses_hrb_post_code": "015","ses_director_fullname": "Ziyad Kərimzadə","ses_director_email": "Ziyad.Kerimzade@bankrespublika.az","ses_director_department": "MXD / VIP şöbəsi","ses_director_cif": "013283","ses_hrb_work_phone": "","ses_hrb_ip_phone": "","ses_user_groups": "3, 11, 17, 663","ses_it_bonus": 0,"ses_itbonus": 0,"randSidebarImg": 6,"warning": true,"logged_in1": null,"__ci_vars": {"logged_in": 1729534841}}', 1);
        $user['isAdmin'] = true;
        echo BaseResponse::ok("Success", $user);
    }

    public function groups() {
        echo BaseResponse::ok("Success", [
            "576" => "SphereAdmin",
            "614" => "Universal",
            "11" => "All"
        ]);
    }

}
