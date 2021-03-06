<?php

/* * *
 * services.php
 * UC FiCom - January 2013
 * Aaron Watanabe - awatanabe@college.harvard.edu
 * 
 * This is a general library for handling user services - i.e. functions involving sesssions and
 * data that persists across pages. This is meant to provide abstraction from the sessions variable
 */

/**
 * Description of services
 *
 * @author aaronwatanabe
 */
class Service {
    
    // Codeigniter superobject
    private $CI;
    
    // Constants for field names for sessions data
    const USER_ID =  "user_id"; 
    const LOGIN_REDIRECT = "login_redirect";    
    const MESSAGE = "message";
    const LAST_PAGE = "last_page";
    
    public function __construct() {    
        // Get instance of CodeIgniter object
        $this->CI =& get_instance();
        

        // Load helpers
        #$this->CI->load->helpers("form");        
        #$this->CI->load->helpers("html");
        #$this->CI->load->helpers("url");
        
        // Load libraries
        #$this->CI->load->library("authentication");
        $this->CI->load->library("display");     
        $this->CI->load->library("session");        
        #$this->CI->load->library("table");     
        #$this->CI->load->library("table_form");    
    }
    
    /** 
     * Generic getter and setter for sessions variables
     * @param string $field The name of the field to access in value
     * @param string $value The value to store in sessions if set. Otherwise, returns the value of
     * the field if the value is set, otherwise false.
     * @param string $return_default Value to return if user attempts to access a field in sessions
     * that is not set
     */
    
    protected function access_value($field, $return_default, $value = NULL){
        
        // Retrieve value if none given
        if($value == NULL){
            $result = $this->CI->session->userdata($field);
            
            return ($result == TRUE) ? $result : $return_default;
        }
        else{
            // Set the value in sessions
            $this->CI->session->set_userdata($field, $value);
        }
    }
    
    /***********************************************************************************************
     * User Data
     * 
     * Gets data cached in sessions about the current user and returns it
     **********************************************************************************************/    
    
    /**
     * Returns the user's security level cached in SESSIONS. If there is no data cached, returns
     * FALSE.
     * 
     * @param int $security_level Optional. If set, stores $security_level in SESSIONS. Otherwise,
     * simply returns the cached value.
     */
    
    public function security_level($security_level = NULL){
        
        return $this->access_value(SECURITY_LEVEL, FALSE, $security_level);
    }    
    /**
     * Returns a user's cached ID number. If the user is not logged in, returns FALSE
     * @param int $user_id If passed, sets the value as the user's ID number
     */
    
    public function user_id($user_id = NULL){
        
        // Get or set the value
        return $this->access_value(self::USER_ID, FALSE, $user_id);
    }
    
    /***********************************************************************************************
     * User Credentialling
     * 
     * Functions for logging in and out a user. Note, that these are not function for actual 
     * authentication
     **********************************************************************************************/
    
    /**
     * Returns the URL of the page to redirect the user to after logging in. If no URL is set, 
     * returns FALSE. Sets the value if URL given.
     * @param string $redirect_url Optional. URL to redirect user to after login
     */
    
    public function login_redirect($redirect_url = NULL){
        
        // Retrieve value if none given
        if($redirect_url == NULL){
            $result = $this->CI->session->flashdata(self::LOGIN_REDIRECT);
            
            return ($result == TRUE) ? $result : INTERNAL_HOME;
        }
        else{
            // Set the value in sessions
            $this->CI->session->set_flashdata(self::LOGIN_REDIRECT, $redirect_url);
        }
    }
    
    /**
     * Clears the login redirect.
     */
    
    public function preserve_redirect(){
        
        $this->CI->session->keep_flashdata(self::LOGIN_REDIRECT);
    
        return TRUE;
    } 
    
    /***********************************************************************************************
     * User Tracking
     **********************************************************************************************/
    
    /**
     * Returns the prior page within the site that the user visited. If the user has not been on any
     *  pages on the site, return the external home page. If value is set, then updates last page.
     */
    
    public function last_page($last = NULL){
        
        return $this->access_value(self::LAST_PAGE, EXTERNAL_HOME, $last);
        
    }
    
    /***********************************************************************************************
     * User Notification
     **********************************************************************************************/    
    
    /**
     * Returns notification for the user to be displayed on the page. If there is no message, then 
     * returns the empty string.
     * If arguments are passed, then sets the message.
     * 
     * @param string $title The title of the message to display
     * @param string $message Text of the message to display
     * @param string $message_type Type of message to display. By default, MESSAGE_NORMAL. Messages
     * to notify user of database updates should be MESSAGE_SUCCESS and those to notify the user of
     * some kind of problem should be MESSAGE_ALERt.
     */
    
    public function message($title = "", $message = "", $message_type = MESSAGE_NORMAL){
        
        // Check whether get or set
        if ($title == "" && $message == ""){
            // Extract information from sessions
            $message_data = $this->CI->session->userdata(self::MESSAGE);
            
            // Determine if there is a message set and return accordingly
            return ($message_data == TRUE) ? 
                $this->CI->display->get_view("universal/message",$message_data) : "";    
        }

        // Otherwise, set the new message
       $this->CI->session->set_userdata(MESSAGE, array(
           "content" =>    $message,
           "title" =>      $title,
           "type" =>       $message_type));

        return;    
    }
    
    /**
     * Clears the message
     */
    
    public function clear_message(){
        
        $this->CI->session->unset_userdata(self::MESSAGE);
    
        return TRUE;
    }     
    
    /***********************************************************************************************
     * SESSION CLEARING
     **********************************************************************************************/    
    
    /**
     * Clears all data from SESSIONS except the default system information
     * 
     */
    
    public function clear_session(){
        // Hack to unset only the non-critical parts of the session
        // Source: http://stackoverflow.com/questions/10509022/codeigniter-unset-all-userdata-but-not-destroy-the-session
        $user_data = $this->CI->session->all_userdata();

        foreach ($user_data as $key => $value) {
            if ($key != 'session_id' && $key != 'ip_address' && $key != 'user_agent' && $key != 'last_activity') {
                $this->CI->session->unset_userdata($key);
            }
        }
        
        return TRUE;    
    }
    
}

?>
