<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_connections extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
        $this->load->library('curl');
    }

    public function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('bank') . ' ' . lang('deposit');
        $data['main'] = lang('bank') . ' ' . lang('deposit');

        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_connections', $data);
        $this->load->view('templates/footer');
    }

    public function all()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('all') . ' ' . lang('bank') . ' ' . lang('deposit');
        $data['main'] = lang('all') . ' ' . lang('bank') . ' ' . lang('deposit');

        $data['connections'] = array(); // $this->M_connections->get_connections(false, $start_date, $to_date,null);
        
        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_all', $data);
        $this->load->view('templates/footer');
    }


    function create_link_token()
    {
        $url = "https://sandbox.plaid.com";
        $api = "/link/token/create"; //"/einvoicing";
        $urn = ''; //"/oauth2/token";
        $uri = $url . $api . $urn;

        $client_id = getenv('PLAID_CLIENT_ID');
        $client_secret = getenv('PLAID_SECRET'); //ponto secret id
        $headers = [
            //"Accept: application/vnd.api+json",
            // "Authorization: Basic " . base64_encode($ponto_client_id . ":" . $ponto_client_secret),
            "Content-Type: application/json"
        ];
        
        $post_fields_1 = [
            "client_id" => $client_id,
            "secret" => $client_secret,
            "client_name" => "Plaid Test App",
            "user" => ["client_user_id" => "user-id"],
            "products" => ["identity"],
            "country_codes" => ["US"],
            "language" => "en",
            "webhook" => "https://webhook.example.com",
            "redirect_uri" => getenv('PLAID_REDIRECT_URI'),
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode((object)$post_fields_1, JSON_HEX_APOS | JSON_HEX_QUOT),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo $err;
            // echo null;
        } else {
            echo $response;
        }
    }
}
