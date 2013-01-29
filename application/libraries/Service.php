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
    
    /***********************************************************************************************
     * User Data
     * 
     * Gets data cached in sessions about the current user and returns it
     **********************************************************************************************/    
    
    /**
     * Returns a user's cached ID number. If the user is not logged in, returns FALSE
     */
    
    public function user_id(){
        
        $user_id = $this->CI->session->userdata(self::USER_ID);
        
        // Return false if the user_id is not set
        return ($user_id == TRUE) ? $user_id : FALSE;
        
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
    
    public function login_redirect($redirect_url = ''){
        
        if($redirect_url == ''){
            $result = $this->CI->session->userdata(self::LOGIN_REDIRECT);
            
            return ($result == TRUE) ? $result : INTERNAL_HOME;
        }
        else{
            // Set the redirect URL
            $this->CI->session->set_userdata(self::LOGIN_REDIRECT, $redirect_url);
        }
    }
    
    /**
     * Clears the login redirect.
     */
    
    public function clear_redirect(){
        
        $this->CI->session->unset_userdata(self::LOGIN_REDIRECT);
    
        return TRUE;
    } 
    
    /***********************************************************************************************
     * User Tracking
     **********************************************************************************************/
    
    /**
     * Returns the prior page within the site that the user visited. If the user has not been on any
     *  pages on the site, return the external home page. If value is set, then updates last page.
     */
    
    public function last_page($last = ''){
        
        // Check if get or set
        if($last != ""){
            return $this->CI->session->set_userdata(self::LAST_PAGE, $last);
        }
        // Otherwise, retrieve from sessions
        $prior = $this->CI->session->userdata(LAST_PAGE);
        return ($prior == TRUE) ? $prior : EXTERNAL_HOME;
    }
    
    /***********************************************************************************************
     * User Notification
     **********************************************************************************************/    
    
    /**
     * DEPRCIATED
     * 
     * Creates an message to display on the next time display is called. 
     * Message must contain content in order to be set.
     * 
     * Returns TRUE if message set; returns FALSE if no content in message and 
     * thus message not set.
     * 
     * @param string $message Text of the message to display
     * @param string $message_type Type of message to display. 
     */
    
    public function set_message($title, $message, $message_type = MESSAGE_NORMAL){
        
        if($message != ''){
            
            $this->CI->session->set_userdata(MESSAGE, array(
                "content" =>    $message,
                "type" =>       $message_type,
                "title" =>      $title));

            return TRUE;            
        }
        return FALSE;   
    }
    
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
    
}

?>