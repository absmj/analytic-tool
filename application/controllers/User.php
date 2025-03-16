<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "custom/BaseController.php";

class User extends \BaseController
{

    public static $client;
    public function __construct()
    {
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Methods: GET");

        // Handle preflight (OPTIONS) requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            // Preflight requests should return a 200 status code with the headers
            http_response_code(200);
            exit;
        }
        parent::__construct();
        $this->load->model("User_model", "user");
        $client = new Google\Client;

        $client->setClientId(GOOGLE_CID);
        $client->setClientSecret(GOOGLE_CS);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST']  . "/user/login");
        self::$client = &$client;
    }

    public function auth()
    {
        // dd($_COOKIE);
        if ((isset($_COOKIE['oauth']) && $this->checkJwt(base64_decode($_COOKIE['oauth'])))) {
            $_SESSION['auth'] = decodeJwt(base64_decode($_COOKIE['oauth']));
            echo BaseResponse::ok("Success", decodeJwt(base64_decode($_COOKIE['oauth'])));
            exit;
        } else {
            unset($_COOKIE['oauth']);
            unset($_SESSION['auth']);
            http_response_code(401);
            echo "Unauth!";
            exit;
        }
    }

    public function groups()
    {
        echo BaseResponse::ok("Success", [
            [
                'group_id' => 576,
                'group_name' => 'SphereAdmin'
            ],
            [
                'group_id' => 614,
                'group_name' => 'All'
            ],
            [
                'group_id' => 663,
                'group_name' => 'Branch'
            ],
        ]);
    }

    public function oauth()
    {
        header("Cross-Origin-Opener-Policy: same-origin");
        header("Cross-Origin-Embedder-Policy: require-corp");
        self::$client->addScope("email");
        self::$client->addScope("profile");

        // dd($client);
        redirect(self::$client->createAuthUrl());
    }

    public function login($redirecting = 0)
    {
        try {

            if (!(isset($_COOKIE['oauth']) && $this->checkJwt((base64_decode($_COOKIE['oauth']))))) {
                unset($_COOKIE['oauth']);
                if (! isset($_GET["code"])) {
                    throw new Exception("Login failed");
                }
                $token = self::$client->fetchAccessTokenWithAuthCode($_GET["code"]);
                setcookie("oauth", base64_encode(json_encode($token['id_token'])), time() + (86400 * 30), "/");
                self::$client->setAccessToken($token["access_token"]);
                $oauth = new Google\Service\Oauth2(self::$client);



                $userinfo = $oauth->userinfo->get();
                if (isset($userinfo['error'])) {
                    throw new Exception($userinfo['error'], 400);
                }

                $user = [
                    "email" => $userinfo->email,
                    "firstname" => $userinfo->givenName,
                    "lastname" => $userinfo->familyName,
                    "client_id" => $userinfo->id,
                    "profile_picture" => $userinfo->picture,
                    'client' => 'google'
                ];

                if (!$this->user->getByClientId($userinfo->id)) {
                    $this->user->insert($user);
                }
                // $_SESSION['auth'] = $user;
            }

            echo '<script>document.write("Redirecting...");setTimeout(() => window.close(), 500)</script>';
            exit;
        } catch (Exception $e) {
            echo BaseResponse::error($e->getMessage(), 500);
        }
    }

    private function checkJwt($jwt)
    {

        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            return false;
        }

        $payload = json_decode(base64UrlDecode($parts[1]), true);

        if (!is_array($payload) || !isset($payload['exp'])) {
            return false;
        }

        // Get the current time
        $currentTime = time();

        // Check if the token is expired
        return $currentTime <= $payload['exp'];
    }

    public function index()
    {
        $user = json_decode('{"__ci_last_regenerate": 1729506027,"ses_authtoken": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbGllbnRJZGVudGlmaWVyIjoiVkVHQS01MjM2OTc3NDQ0NTQiLCJpc3MiOiJhdXRoMCIsImNoYW5uZWwiOiJTUEhFUkUiLCJ1c2VyTmFtZSI6IkxBTEFIIiwiZXhwIjoxNzI5NTQ5MTc5fQ.Bw2BLDIawkBa99xVqiX_Nd9rfLwcu_njNbaU-ynMhys","user_mail": "Lala.Farajova@bankrespublika.az","user_login": "LalaH","logged_in": 1,"ses_hrb_fullname": "AbdullaK","ses_hrb_job_position": "Baş mütəxəssis","ses_hrb_department": "MXD / VİP şöbəsi","ses_hrb_department_code": "03470000","ses_branch_code": "2","ses_hrb_employee_code": "460686","ses_hrb_cif": "1460686","ses_hrb_post_code": "015","ses_director_fullname": "Ziyad Kərimzadə","ses_director_email": "Ziyad.Kerimzade@bankrespublika.az","ses_director_department": "MXD / VIP şöbəsi","ses_director_cif": "013283","ses_hrb_work_phone": "","ses_hrb_ip_phone": "","ses_user_groups": "3, 11, 17, 663","ses_it_bonus": 0,"ses_itbonus": 0,"randSidebarImg": 6,"warning": true,"logged_in1": null,"__ci_vars": {"logged_in": 1729534841}}', 1);
        $user['isAdmin'] = true;
        echo BaseResponse::ok("Success", $user);
    }
}
