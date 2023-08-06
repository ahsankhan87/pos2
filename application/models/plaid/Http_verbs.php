<?php

class Http_verbs extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('curl');
        $this->load->model('plaid/Uri_path');
    }


/*enum HTTP_VERB: string
{
    case GET = "GET";
    case POST = "POST";
    case DELETE = "DELETE";
    case PATCH = "PATCH";
}*/

function get($urn, $headers = [])
{   
    $curl = curl_init();
    $uri = $this->Uri_path->get_uri_path_from_urn($urn);

    curl_setopt_array($curl, [
        CURLOPT_URL => $uri,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_TIMEOUT => 30,
        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2TLS,
        CURLOPT_SSLCERT => __DIR__  . '/cert/pos/certificate.pem',
        CURLOPT_SSLKEY => __DIR__  . '/cert/pos/private_key.pem',
        CURLOPT_SSLKEYPASSWD => getenv("SSL_PASSWORD"),
        CURLOPT_HTTPHEADER => $headers,
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return null;
    } else {
        return $response;
    }
}

function post($uri, $headers, $post_fields)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $uri,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_TIMEOUT => 30,
        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2TLS,
        CURLOPT_SSLCERT => __DIR__  . '/cert/pos/certificate.pem',
        CURLOPT_SSLKEY => __DIR__  . '/cert/pos/private_key.pem',
        CURLOPT_SSLKEYPASSWD => getenv("SSL_PASSWORD"),
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post_fields,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return null;
    } else {
        return $response;
    }
}

function delete($uri, $headers)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $uri,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2TLS,
        CURLOPT_SSLCERT => __DIR__  . '/cert/pos/certificate.pem',
        CURLOPT_SSLKEY => __DIR__  . '/cert/pos/private_key.pem',
        CURLOPT_SSLKEYPASSWD => getenv("SSL_PASSWORD"),
        CURLOPT_CUSTOMREQUEST => "DELETE",
//        CURLOPT_CUSTOMREQUEST => HTTP_VERB::DELETE->value,
        CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return null;
    } else {
        return $response;
    }
}

function patch($uri, $headers, $post_fields)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $uri,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2TLS,
        CURLOPT_SSLCERT => __DIR__  . '/cert/pos/certificate.pem',
        CURLOPT_SSLKEY => __DIR__  . '/cert/pos/private_key.pem',
        CURLOPT_SSLKEYPASSWD => getenv("SSL_PASSWORD"),
        CURLOPT_CUSTOMREQUEST => "PATCH",
//        CURLOPT_CUSTOMREQUEST => HTTP_VERB::PATCH->value,
        CURLOPT_POSTFIELDS => $post_fields,
        CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return null;
    } else {
        return $response;
    }
}

}