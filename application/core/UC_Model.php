<?php

/* * *
 * UC_Model.php
 * UC FiCom - January 2013
 * Aaron Watanabe - awatanabe@college.harvard.edu
 * 
 * Template model for specific models to draw off. There should be one implementation of the model
 * per primary table. 
 */

/**
 * Description of UC_Model
 *
 * @author aaronwatanabe
 */
class UC_Model extends CI_Model{
   
    // Name of the primary table
    protected $primary_table;
    protected $primary_key_column;
    protected $status_column;
    protected $inactive_value;
    
    public function __construct(){
        
        // Lovely inheritance
        parent::__construct();

        // Load database connects    
        $this->load->database();
        
        // Set the name of the primary table
        $this->primary_table = '';
        $this->primary_key = '';
        $this->status_column = "type_code";
        $this->inact_value = 0;
    }    
    
    /**
     * Initializes the model with information about the tables to access. Should be called in the 
     * constructor.
     * 
     * @param string $primary_table Name of the primary table for the model
     * @param string $primary_key Name of the primary key column of the primary table
     * @param string $status_column Name of the column that records an element's status where it
     * may be marked inactive.
     */
    
    protected function initialize($primary_table, $primary_key, $status_column = 'type_code'){
        $this->primary_table = $primary_table;
        $this->primary_key_column = $primary_key;
        $this->status_column = $status_column;
    }
    
    /**
     * This abstract function is for performing other joins on reference tables
     */
    protected function prep_reference() {
        return;
    }
    
    /**
     * Prep dependent tables (i.e. tables that contain additional information for element in the
     * primary tables).
     */
    protected function prep_secondary() {
        return;
    }
    
    /**
     * Creates a new entry in the primary table.
     * 
     * @param array $entry_data The data to enter into the primary table. Keys are column names. 
     * Must respect column naming and unique elements in entry data - otherwise database entry will
     * fail. Any data for reference columns must use the reference id as opposed to the reference
     * value
     */
    
    public function new_entry($entry_data){

        // Insert information into database
        $this->db->insert($this->primary_table, $entry_data);

        return TRUE;
    }
    
    /**
     * Updates the entry given by primary key with the inserted information
     * 
     * @param type $primary_key The primary key value of the entry to update
     * @param type $update_fields Data for updating. Keys are names of columns to update
     */
    
    public function update_entry($primary_key, $update_fields){
        // Check that primary key is not being changed
        if(array_key_exists($this->primary_key_column, $update_fields) == TRUE){
            return FALSE;
        }
        
        $this->db->where($this->primary_key_column, $primary_key);
        $this->db->update($this->primary_table, $update_fields);
        
        return TRUE;        
    }
    
    public function deactivate_record($entry_id){
        
        // Deactivate by setting the security level to inactive
        $this->db->where($this->primary_key_column, $entry_id);
        $this->db->update($this->primary_table, array(
            $this->status_column => $this->inactive_value
        ));
        
        return TRUE;        
        
    }
    
    /**
     * Loads a single record defined by a unique value in the table. 
     * @return Mixed. If there is no unique record, returns FALSE. Otherwise, returns array with
     * the record data
     */
    
    public function get_record($lookup_column, $unique_value){
        
        // Call functions to get joins on dependent and reference tables
        $this->prep_reference();
        $this->prep_secondary();
        
        $results = $this->db->get_where($this->primary_table, 
                array($lookup_column => $unique_value));
        
        // Check results, returning false if there is no unique match
        if($this->db->count_all_results() != 1){
            return FALSE;
        }
        else{
            return $results->row_array();
        }        
        
    }
    
    /**
     * Gets active entries from the primary table.
     */
    
        /**
     * Gets active groups from the database
     * 
     * @return Query Object
     */
    
    public function get_active(){
 
        // Call functions to get joins on reference tables. Secondary tables are not displayed here
        $this->prep_reference();      
        
        return $this->db->get_where($this->primary_table, 
                array($this->primary_table.".".$this->status_column." !=" => INACTIVE));        
    }     
    
    
    /**
     * Updates the user given by $user_id. Will replace each column specified in $fields with the new value
     * 
     * @param type $user_id
     * @param array $fields Array of fields to update where the key is the column and its value is the new value
     */
    /*
    public function update_user($user_id, array $fields){
        
        // Check that some fields are not being updated
        if(array_key_exists(USERS_USER_ID, $fields) == TRUE){
            return FALSE;
        }
        
        $this->db->where(USERS_USER_ID, $user_id);
        $this->db->update(USERS_TABLE, $fields);
        
        return TRUE;
    }
    
    public function deactivate_user($user_id){
        
        // Deactivate by setting the security level to inactive
        $this->db->where(USERS_USER_ID, $user_id);
        $this->db->update(USERS_TABLE, array(
            USERS_SECURITY_LEVEL => INACTIVE
        ));
        
        return TRUE;
    }
    
    /**
     * Gets active users from the database. Will return all columns except 
     * password
     * 
     * @return Query Object
     */
    /*
    public function get_active(){
        
        // Do not display the user's password
        $results = $this->db->get_where(USERS_TABLE, 
                array(USERS_SECURITY_LEVEL." !=" => INACTIVE));
        
        return $results;
    }   
    
    /**
     * Selects a single user where column equals value. Will return false if 
     * the value is not unique
     * 
     * @param string    $column The column with the unique value to access
     * @param string    $value  The unique value
     * @return array
     */
    /*
    public function get_unique($column, $value){
        
        $results = $this->db->get_where(USERS_TABLE,
                array($column => $value));
        
        // Check results, returning false if there is no unique match
        if($this->db->count_all_results() != 1){
            return FALSE;
        }
        else{
            return $results->row_array();
        }
            
    }
    */

}

?>
