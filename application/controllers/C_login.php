<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_login extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        //session_start();
        $this->lang->load('index');
    }

    function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang("login");
        $data['main'] = lang("login");

        $data['currencyDropDown'] = $this->M_currencies->getcurrencyDropDown();

        $this->load->view('v_login', $data);
        //$this->load->view('templates/footer');
    }

    function logout()
    {
        //for logging
        $msg = $_SESSION['username'];
        $this->M_logs->add_log($msg, "User", "logged out", "Admin");
        // end logging 

        unset($_SESSION['company_id']);
        unset($_SESSION['username']);
        unset($_SESSION['company_name']);
        //unset($_SESSION['time_zone']);
        unset($_SESSION['emp_id']);
        unset($_SESSION['emp_name']);
        unset($_SESSION['multi_currency']);
        unset($_SESSION['home_currency_code']);
        unset($_SESSION['home_currency_symbol']);
        unset($_SESSION['role']);

        //session_destroy();
        $this->cart->destroy(); //Destroy the cart if open.

        $this->session->set_flashdata('error', 'You have been logged out.!');
        redirect('C_login', 'refresh');
    }

    //verify username and password
    public function verify()
    {
        $data['title'] = lang("login");
        //$data['nav_list'] = $this->M_category->get_category();
        $data['main'] = lang("login");

        if ($this->input->post('username')) {
            $username = $this->input->post('username');
            $pass = md5($this->input->post('password', true));
            $this->M_login->verify($username, $pass);
        }
        //$this->load->view('admin/templates/header',$data);
        $this->load->view('v_login', $data);
        //$this->load->view('admin/templates/footer');
    }

    public function forget_password_email()
    {
        $data = array('langs' => $this->session->userdata('lang'));
        
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $email = $this->input->post('email_forgot');
            
            $user = $this->M_users->get_users_by_email($email);

            if (strlen(@$user[0]['email']) > 0) {

                $verification_key = md5(rand());
                $data = array(
                    'forgot_pass_identity'  => $verification_key,

                );
                $this->db->update('users', $data, array('email' => $email));

                $subject = lang('email_subject');
                $message = lang('first_para'). " " . site_url() . "tr/C_login/forget_password_reset/" . $verification_key . "
                \n".lang('second_para')."\n".lang('username').":".@$user[0]['username']."\n\n".lang('third_para');
                
                $this->load->library('email');

                $config['protocol'] = 'sendmail';
                $config['mailpath'] = '/usr/sbin/sendmail';
                $config['charset'] = 'iso-8859-1';
                $config['wordwrap'] = TRUE;

                $this->email->initialize($config);

                $this->load->library('email', $config);
                $this->email->set_newline("\r\n");
                $this->email->from('support@guvenfi.com', 'Support GuvenFi');
                $this->email->to($email);
                $this->email->subject($subject);
                $this->email->message($message);
                if ($this->email->send()) {
                    //$this->session->set_flashdata('msg', 'Check in your email for email verification mail');
                    // $this->session->set_flashdata('msg','New Account Created Successfully');
                    // redirect('Login','refresh');
                    echo 'Password reset link sent on your registered email';
                }
            } else {
                //echo $this->session->set_flashdata('error','User / Member is not active, please check your email address or contact your administrator');
                echo 'Email not found in database';
            }
        }
    }


    public function forget_password_reset($forgot_pass_identity = '')
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //CHECK OLD PASSWORD IN TABLE
            if (!$this->M_login->validate_forgot_password($this->input->post('forgot_pass_identity', true))) {
                echo 'false';
                die();
            } else {

                $data = array(
                    'password' => md5($this->input->post('password')),
                );

                echo $this->db->update('users', $data, array('forgot_pass_identity' => $this->input->post('forgot_pass_identity', true)));
                die();
                //$this->session->set_flashdata('msg','Password changed successfully');
                //redirect('Login','refresh');
            }
        }

        $data['title'] = lang('forget_password');
        $data['main'] = lang('forget_password');
        $data['forgot_pass_identity'] = $forgot_pass_identity;

        //$this->load->view('templates/header', $data);
        $this->load->view('v_reset_password',$data);
        //$this->load->view('templates/footer');
    }
}
