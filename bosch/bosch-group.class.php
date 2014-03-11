<?php
class Bosch_Group{
    
    /**
     * $name Name of the group to be publicly displayed
     * @var string
     */
    public $name;
    
    /**
     * $desc Helper text to display
     * @var string
     */
    public $desc;
    
    /**
     * $fields String of field vars seperated by '|'
     * @var string
     */
    public $fields;
    
    /**
     * $html_before HTML to process before group
     * @var string
     */
    public $html_before;
    
    /**
     * $html_after HTML to process after group
     * @var string
     */
    public $html_after;

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