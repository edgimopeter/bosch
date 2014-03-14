<?php
/**
 * Bosch Group
 *
 * @author      Peter Adams (http://peteradamsdev.com)
 * @copyright   Copyright (c) 2013 Peter Adams
 * @link        https://github.com/edgimopeter/bosch
 * @version     1.0
 */

/**
 * Bosch Groups hold one or more Fields
 */
class Bosch_Group{
    
    /**
     * Name of the group to be publicly displayed
     * @var string
     */
    public $name = '';

    /**
     * Hide the name
     * @var bool
     */
    public $hide_name = false;
    
    /**
     * Helper text to display
     * @var string
     */
    public $desc = '';
    
    /**
     * String of field vars seperated by '|'
     * @var string
     */
    public $fields;
    
    /**
     * HTML to process before group
     * @var string
     */
    public $html_before;
    
    /**
     * HTML to process after group
     * @var string
     */
    public $html_after;

    /**
     * Constructor
     * @param array $properties 
     */
    function __construct( $properties = array() ){

        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }        
    }

    /**
     * Validate a group
     *
     * @return string 'valid' if valid, error string if not
     */
    public function validate_group(){

        $fields = explode('|', $this->fields);

        //group must have at least one field associated with it
        if ( empty($fields) || $fields == false || $fields[0] == '' || empty($fields[0]) ){
            return 'No <code>fields</code> property set for group <code>'.$this->name.'</code>';
        }
        return 'valid';
    }
}