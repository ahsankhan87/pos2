<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PrivacyPolicy extends CI_Controller{
    
    function __construct()
    {
        parent::__construct();
       
    } 
    function index()
    {
        $data['title'] = 'Privay Policy';
        $data['main'] = 'Privay Policy';
        
        // $this->load->view('templates/header',$data);
        $this->load->view('privacy_policy',$data);
        // $this->load->view('templates/footer');
    }
    
}