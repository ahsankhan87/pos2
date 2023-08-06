<?php

class Uri_path extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('plaid/Http_verbs');
    }

    function get_uri_path_from_urn($urn)
    {
        //var_dump($urn);
        try {
            $host = "https://".getenv('HOST')."";
            
            if (empty($urn)) {
                throw new Exception("URN shouldn't empty");
            // } else if (str_starts_with($urn, "/")) {
            } else if (substr($urn,0,1) === "/") {
                return $host . substr($urn, 1);
            } else if (filter_var($urn, FILTER_VALIDATE_URL)) {
                return $urn;
            } else {
                return "$host/$urn";
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage();
            return null;
        }
    }
    
    function remove_path_id($uri, $resource)
    {
        $pathId = "/{{$resource}Id}";
        // if (str_ends_with($uri, $pathId)) {
        if (substr( $uri, -strlen($pathId) ) === $pathId) {
            $uri = substr($uri, 0, strpos($uri, $pathId));
        }
        return $uri;
    }
    
    function configure_path($uri, $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $uri = str_replace("{" . $key . "}", $value, $uri);
        }
        return $uri;
    }
    
    function configure_query_parameters($uri, $parameters)
    {
        foreach ($parameters as $key => $value) {
            if ($key == 'page_limit') {
                $uri .= urlencode("page[limit]=${$value}");
            } else if ($key == 'page_before') {
                $uri .= urlencode("page[before]=${$value}");
            } else if ($key == 'page_after') {
                $uri .= urlencode("page[after]=${$value}");
            } else {
    // TODO: implement a class for the filter object
                $uri .= urlencode("filter[][]=${$value}");
            }
        }
        return $uri;
    }
}