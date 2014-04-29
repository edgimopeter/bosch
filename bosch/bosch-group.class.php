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
class Bosch_Group extends Bosch{
    
    /**
     * Name of the group to be publicly displayed
     * @var string
     */
    public $name = '';

    /**
     * Slugified name
     * @var string
     */
    public $var = '';

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
    public $html_before = '';
    
    /**
     * HTML to process after group
     * @var string
     */
    public $html_after = '';

    /**
     * Columnd width for group
     * @var string
     */
    public $width = 'col-md-12';

    /**
     * Constructor
     * @param array $properties 
     */
    function __construct( $properties = array() ){

        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }

        $this->var = parent::slugify( $this->name );
    }

    protected function init( $new_fields ){
        $temp = explode('|', $this->fields);
        $this->fields = array();
        foreach ($temp as $field_var) {
            $this->fields[$field_var] = $new_fields[$field_var];
        }
    }

    /**
     * Validate a group
     *
     * @return string 'valid' if valid, error string if not
     */
    public function validate_group(){

        //group must have at least one field associated with it
        if ( empty($this->fields) || $this->fields == false ){
            return 'No <code>fields</code> property set for group <code>'.$this->name.'</code>';
        }
        return 'valid';
    }

    /**
     * Output a single group
     *
     *
     * @param object $group Group object to output
     */
    public function output_group(){

        echo 
        $this->html_before . '
        <div class="'.$this->width.'">
            <div class="bosch-group group-'.$this->var.'">';

                if ( $this->hide_name === false ){
                    echo '
                    <div class="bosch-heading">
                        '.parent::settings('group-headings') . $this->name . $this->close_tag(parent::settings('group-headings')) .'
                    </div>';
                }

                if ( $this->desc !== '' ){
                    echo '            
                    <div class="bosch-group-desc">
                        '.$this->desc.'
                    </div>';
                }

                //cycle through the fields in this group
                //output the field if possible, throw exception if not
                foreach ( $this->fields as $field ){

                    try{
                         if ( !array_key_exists($field->var, $this->fields) ){
                            throw new Exception('Invalid Field <code>'.$field.'</code> in Group <code>'.$this->name.'</code>');
                         }

                          $field->output_field();

                    }
                    catch (Exception $e) {
                        $this->bosch_exception( $e );                   
                    }
                }

            echo '
            </div>
        </div>'.
        $this->html_after;

    }

    /**
     * Remove a single field from the group
     * @param  string $field Var name of field to remove
     * @return bool True on success, false on failure
     */
    protected function remove_field($field){

        if ( !array_key_exists($field, $this->fields) ){            
            return false;
        }
        unset( $this->fields[$field] );

        return true;
    }
}