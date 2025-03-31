<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class User extends \BaseController
{
    protected $ldap;
    protected $view = 'user';
    public $user;
    public $session;
    public static $client;

    public function __construct()
    {
        parent::__construct();
        $this->load->model("User_model", "user");
    }

    public function login()
    {
        $this->title = "LDAP Authentication";
        $this->styles[] = BASE_PATH . "assets/css/login.css";
        $this->styles[] = BASE_PATH . "assets/css/form.css";

        if (isPostRequest()) {
            $post = post();
            $username = trim($post['username']);
            $password = trim($post['password']);

            if (empty($username) || empty($password)) {
                $this->session->set_flashdata('error', 'Username and password are required.');
                $this->page("login", [], false);
                return;
            }
            if ($this->user->login($username, $password)) {
                $this->session->set_flashdata('success', 'Login successful.');
                redirect("/pages");
            }
        }

        $this->page("login", [], false);
    }

    public function getGroups()
    {
        echo BaseResponse::ok("Success", $this->user->getGroups());
        exit;
    }

    public function logout()
    {
        session_destroy();
        redirect("/login");
    }
}
