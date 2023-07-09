<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_company extends MY_Controller{
    
    function __construct()
    {
        parent::__construct();
       $this->lang->load('index');
    } 
    
    function index($company_id=NULL)
    {
        $data = array('langs' => $this->session->userdata('lang'));
        
        if($this->input->post('name'))
        {
            //////////////////////////////////
            //upload upload_pics of the Company..
              if($_FILES['upload_pic']['name'] !== ''){
                      $errors= array();
                      $file_name = $_FILES['upload_pic']['name'];
                      $file_size =$_FILES['upload_pic']['size'];
                      $file_tmp =$_FILES['upload_pic']['tmp_name'];
                      $file_type=$_FILES['upload_pic']['type'];
                      //$file_ext=strtolower(end(explode('.',$_FILES['upload_pic']['name'])));
                      $file_ext=pathinfo($_FILES['upload_pic']['name']); 
                      $expensions= array("jpeg","jpg","png","gif");
                      
                      if(in_array($file_ext['extension'],$expensions)=== false){
                         $this->session->set_flashdata('error', 'extension not allowed, please choose a JPEG or PNG file.');
                         redirect('companies/C_company/', 'refresh');
                      }
                      
                      if($file_size > 2097152){
                        $this->session->set_flashdata('error', 'File size must be excately 2 MB');
                         redirect('companies/C_company/', 'refresh');
                      }
                      
                      if(empty($errors)==true){
                        //FIRST delete picture from folders
                         $picture = $this->input->post('image');
                         @unlink(FCPATH.'images/company/thumb/'.$picture);
                         
                         move_uploaded_file($file_tmp,"images/company/thumb/".$file_name);
                        // echo "Success";
                         //return true;
                      }else{
                         //print_r($errors);
                         //return $errors;
                         $this->session->set_flashdata('error', 'You did not select a file to upload.');
                         redirect('companies/C_company/', 'refresh');
                      }
                   }
                //upload an image options
                 ////////////////////////
            
            $this->M_companies->updateCompany();
            $this->session->set_flashdata('message','Company Updated');
            redirect('C_login/logout','refresh');
           
        }
        else
        {
            $data['title'] = lang('update').' '. lang('company');
            $data['main'] = lang('edit').' '. lang('company');
            
            $company_id = $_SESSION['company_id'];
            $data['Company'] = $this->M_companies->get_companies($company_id);
            $data['currencyDropDown'] = $this->M_currencies->getcurrencyDropDown();
             
            $this->load->view('templates/header',$data);
            $this->load->view('companies/company/edit',$data);
            $this->load->view('templates/footer');
        }
    }

    function unsubscribe()
    {
        $data = array('langs' => $this->session->userdata('lang'));
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        if($this->input->server('REQUEST_METHOD') === 'POST')
        {
            //form Validation
            $this->form_validation->set_rules('business_name', 'Business Name', 'required');
            $this->form_validation->set_rules('customer_name', 'Customer Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><a class="close" data-dismiss="alert">�</a><strong>', '</strong></div>');
            
            if($this->form_validation->run())
            {
                $business_name = $this->input->post('business_name', true);
                $customer_name = $this->input->post('customer_name', true);
                $email = $this->input->post('email', true);
                $contact_no = $this->input->post('contact_no', true);
                $comments = $this->input->post('comments', true);

                $subject = "Request for unsubscription";
                $message = "Request has been sent from the following customer for unsubscription.". 
                "\nEmail: $email \nBusiness Name: $business_name \nCustomer Name: $customer_name \nContact No: $contact_no \nComments: $comments".
                "\n\nThanks";
                
                $this->load->library('email');

                $config['protocol'] = 'sendmail';
                $config['mailpath'] = '/usr/sbin/sendmail';
                $config['charset'] = 'iso-8859-1';
                $config['wordwrap'] = TRUE;

                $this->email->initialize($config);

                $this->load->library('email', $config);
                $this->email->set_newline("\r\n");
                $this->email->from('support@guvenfi.com', 'Support GuvenFi');
                $this->email->to("accounting@bookkeepingfor.com");
                $this->email->cc("ahsankhan50@gmail.com");
                $this->email->subject($subject);
                $this->email->message($message);
                
                if ($this->email->send()) {
                    //$this->session->set_flashdata('msg', 'Check in your email for email verification mail');
                    // $this->session->set_flashdata('msg','New Account Created Successfully');
                    // redirect('Login','refresh');
                    $email_sent = 1;
                }else{ $email_sent = 0; }
                
                $data = array(
                    'company_id' => $_SESSION['company_id'],
                    'business_name' => $business_name,
                    'customer_name' => $customer_name,
                    'email' => $email,
                    'phone_no' => $contact_no,
                    'comments' => $comments,
                    'email_sent' => $email_sent,
                    'date_created' => date("Y-m-d"),
                    );
                $this->db->insert('unsubscribe', $data);
                
                //update 30 days expiration
                $_30_days =time()+60*60*24*30; // 30 days
                $this->db->update('companies', array('locked'=> 1, 'expire'=>$_30_days),array('id'=>$_SESSION['company_id']));
                ///////////////
                
                $this->session->set_flashdata('message', 'Your request for unsubscribe is submitted.');
                
                if($email_sent == 0 ){
                    $this->session->set_flashdata('error', 'email not sent, please check again.');
                }
                redirect('companies/C_company', 'refresh');
            }

        }
            $data['title'] = 'Unsubscribe';
            $data['main'] = 'Unsubscribe';
            
            $company_id = $_SESSION['company_id'];
            $data['Company'] = $this->M_companies->get_companies($company_id);
            $data['currencyDropDown'] = $this->M_currencies->getcurrencyDropDown();
             
            $this->load->view('templates/header',$data);
            $this->load->view('companies/company/unsubscribe',$data);
            $this->load->view('templates/footer');
       
    }
}