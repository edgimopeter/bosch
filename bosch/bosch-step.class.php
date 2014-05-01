<?php
/**
 * Bosch Step
 *
 * @author      Peter Adams (http://peteradamsdev.com)
 * @copyright   Copyright (c) 2013 Peter Adams
 * @link        https://github.com/edgimopeter/bosch
 * @version     1.0
 */

/**
 * Bosch Steps hold one or more groups
 */
class Bosch_Step{
    
    public $name      = '';
    public $desc      = '';
    public $groups    = array();
    public $prev      = true;
    public $prev_text = 'Go Back';
    public $next      = true;
    public $next_text = 'Continue';

    /**
     * Constructor
     * @param array $properties 
     */
    function __construct( $properties = array() ){
        
        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }        
    }

    public function init( $new_groups ){

        $temp = explode('|', $this->groups);
        $this->groups = array();
        foreach ($temp as $group_var) {
            $group_var = Bosch::slugify($group_var);
            $this->groups[$group_var] = $new_groups[$group_var];
        }
    }

    public function output_step(){
        foreach ($this->groups as $group) {
            $group->output_group();
        }
    }

    public function get_fields(){

        $fields = array();

        foreach ( $this->groups as $group){
            foreach ( $group->fields  as $field){
                $fields[$field->var] = $field;
            }
        }

        return $fields;
    }

    public function process_step(){

        $fields = $this->get_fields();

        $validator = new Bosch_Validator( $fields );
        $_POST = $validator->sanitize($_POST);

        foreach ($_POST['form'] as $k => $v) {

            //ignore the honeypot field
            if ( $k !== 'hp' ){
                if ( is_array($_POST['form'][$k]) ){
                    $_POST['form'][$k] = implode('|', $_POST['form'][$k]);
                }

                if ( !empty($fields[$k]->validate) )
                    $validate[$k] = $fields[$k]->validate;
                
                if ( !empty($fields[$k]->filter) )
                    $filter[$k] = $fields[$k]->filter;

                $fields[$k]->value = $v;
            }
        }

        if ( !empty($validate) )
            $validator->validation_rules($validate);

        if ( !empty($filter) )
            $validator->filter_rules($filter);

        $validated_data = $validator->run($_POST['form']);

        return $validated_data;
       
    }

    function remove_field_from_step_group($field, $group_var){
        if ( isset($this->groups[$group_var]->fields[$field]) && !is_null($this->groups[$group_var]->fields[$field]) )
            unset($this->groups[$group_var]->fields[$field]);
    }

}