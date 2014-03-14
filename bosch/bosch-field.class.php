<?php
/**
 * Bosch Field
 *
 * @author      Peter Adams (http://peteradamsdev.com)
 * @copyright   Copyright (c) 2013 Peter Adams
 * @link        https://github.com/edgimopeter/bosch
 * @version     1.0
 */

/**
 * Bosch Fields hold properties for a single input field
 */
class Bosch_Field{

    /**
     * The variable name, no spaces
     * @var string
     */
    public $var;

    /**
     * The display label
     * @var string
     */
    public $name;

    /**
     * Type of data
     * Choices: text, money, date, select, checkbox, checkbox-inline, radio, radio-inline, textarea
     * @var string
     */
    public $type;

    /**
     * For select/radio/checkbox, an associative array of varlabel. For textarea, number of rows to display
     * @var mixed
     */
    public $options;

    /**
     * Help text to display beneath field
     * @var string
     */
    public $desc;

    /**
     * Default value
     * @var string
     */
    public $default;

    /**
     * String of validators (see below), seperated by '|'
     * @var string
     */
    public $validate;

    /**
     * String of filters (see below), seperated by '|'
     * @var string
     */
    public $filter;

    /**
     * Placeholder text
     * @var string
     */
    public $placeholder;

    /**
     * Show or hide the label
     * @var bool
     */
    public $hide_label;

    /**
     * Control font sizing (lg or sm, blank is medium)
     * @var string
     */
    public $size;

    /**
     * Control width on horizontal form (col-md-4, col-xs-2, etc). Blank is set with $bosch->settings['default-column-width'];
     * @var string
     */
    public $input_width;

    /**
     * Control width of label on horizontal form
     * @var string
     */
    public $label_width;

    /**
     * Choices are disabled|multiple|nosave
     * @var string
     */
    public $extras;

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
     * Validate a field
     *
     * @return string 'valid' if valid, error string if not
     */
    public function validate_field(){

        //validate the var property, must not be blank and not contain spaces
        if ( !isset($this->var) || $this->var == '' || empty($this->var) || preg_match('/\s/', $this->var) ){
           return 'Valid <code>var</code> property required for field with <code>name => '.$this->name.'</code>';
        }

        //Check if a valid $options array is supplied when it's required by the field type (select, radio, etc)
        if (
            in_array($this->type, array('checkbox', 'checkbox-inline', 'select', 'radio', 'radio-inline') ) 
            && (
                !isset($this->options) ||
                empty($this->options) ||
                !is_array($this->options)
                ) 
            ){

           return 'Valid array required for <code>options</code> property in field <code>'.$this->var.'</code><br />Currently set to <code>'.$this->options.'</code>';
        }

        return 'valid';

    }
}
