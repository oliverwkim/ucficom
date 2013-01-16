<?php

/**
 * public.php
 * UC FiCom - January 2013
 * Aaron Watanabe - awatanabe@college.harvard.edu
 * 
 * Controller for publically accessable pages of FiCom Portal
 */

/**
 * Description of external
 *
 * @author aaronwatanabe
 */
class External extends UC_Controller {
    
    public function __construct() {
        parent::__construct(EXTERNAL);
        // Load user model - controller for managing user table
        $this->load->model("users");    
        
        // Load helpers
        $this->load->helper("form");
        $this->load->helper("validation");        
        
        // Load libraries
        $this->load->library("form_validation");
        $this->load->library("table");            
    }

    public function index(){
        $this->display($this->get_view("content/external/index"));
    }
    
    public function login(){
        
        // Load user model
        $this->load->model("users");
        
        $this->load->library("table_form");
        $this->load->library("authentication");
        
         // Attempt to log user in if data entered
        if($this->input->post(SUBMIT_NAME) == TRUE){
            
            // Set rules
            $this->form_validation->set_rules(USERS_EMAIL, "Email", "required|valid_email");
            $this->form_validation->set_rules(USERS_PASSWORD, "Password", "required");
            
            // Validate form
            if($this->form_validation->run() == TRUE){
                // Get the data associated with the entered email address
                $user_data = $this->users->get_unique(USERS_EMAIL, 
                        $this->input->post(USERS_EMAIL));

                // If good credentials, log in and redirect to internal home
                if($user_data == TRUE && $user_data[USERS_PASSWORD] == 
                    $this->authentication->hash_password($this->input->post(USERS_PASSWORD))){

                    // Log user in
                    $this->authentication->log_in($user_data[USERS_SECURITY_LEVEL]);

                    // Set message to notify user
                    $this->set_message("Login Successful", "Welcome back ".
                            $user_data[USERS_FIRST_NAME]);

                    // Check if user has a specific page to be redirected to
                    $redirect = ($this->session->userdata(LOGIN_REDIRECT)) ?
                        $this->session->userdata(LOGIN_REDIRECT) :
                        INTERNAL_HOME;
                    redirect($redirect);

                }
                else{
                    // Notify of login failure and allow user to try again
                    $this->set_message("Login Failed", "Invalid email address or password",
                            MESSAGE_ALERT);
                }
            }
            // Notify user of error in form
            else{
                $this->set_message("Error", validation_errors(), MESSAGE_ALERT);
            }            
        }
        
        // Load login form
        $template_data["login_form"] = $this->get_view("content/forms/login");
               
        $this->display_view("content/external/login", $template_data);
    }
}
?>
