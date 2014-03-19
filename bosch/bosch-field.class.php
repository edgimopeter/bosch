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
class Bosch_Field extends Bosch{

    /**
     * Preset list of states for select fields
     * @var array
     */
    private $states = array("AL" => "Alabama", "AK" => "Alaska", "AZ" => "Arizona", "AR" => "Arkansas", "CA" => "California", "CO" => "Colorado", "CT" => "Connecticut", "DE" => "Delaware", "FL" => "Florida", "GA" => "Georgia", "HI" => "Hawaii", "ID" => "Idaho", "IL" => "Illinois", "IN" => "Indiana", "IA" => "Iowa", "KS" => "Kansas", "KY" => "Kentucky", "LA" => "Louisiana", "ME" => "Maine", "MD" => "Maryland", "MA" => "Massachusetts", "MI" => "Michigan", "MN" => "Minnesota", "MS" => "Mississippi", "MO" => "Missouri", "MT" => "Montana", "NE" => "Nebraska", "NV" => "Nevada", "NH" => "New Hampshire", "NJ" => "New Jersey", "NM" => "New Mexico", "NY" => "New York", "NC" => "North Carolina", "ND" => "North Dakota", "OH" => "Ohio", "OK" => "Oklahoma", "OR" => "Oregon", "PA" => "Pennsylvania", "RI" => "Rhode Island", "SC" => "South Carolina", "SD" => "South Dakota", "TN" => "Tennessee", "TX" => "Texas", "UT" => "Utah", "VT" => "Vermont", "VA" => "Virginia", "WA" => "Washington", "WV" => "West Virginia", "WI" => "Wisconsin", "WY" => "Wyoming");

    /**
     * Preset list of months for select fields
     * @var array
     */
    private $months = array('jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April', 'may' => 'May', 'june' => 'June', 'july' => 'July', 'aug' => 'August', 'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December');

    /**
     * Preset list of days for select fields
     * @var array
     */
    private $days = array(1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29', 30 => '30', 31 => '31');

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

        parent::__construct();

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

    /**
     * Output a single field
     *
     * @todo add captcha support
     */
    public function output_field(){

        //set default input class
        $input_class = 'form-control';

        //check of field is required
        in_array('required', explode('|', $this->validate) )? $required = 'required' : $required = '';

        //check if the form has been submitted and the field has an error
        isset($this->errors) && array_key_exists($this->var, $this->errors) ? $error = 'has-error' : $error = '';

        //set the placeholder value
        isset($this->placeholder) ? $placeholder = $this->placeholder : $placeholder = '';

        //hide the label if defined by the field variable
        isset($this->hide_label) && $this->hide_label === true ? $label_class = 'sr-only' : $label_class = '';

        //hide the label if the global setting is true
        $this->settings('hide-labels') === true ? $label_class = 'sr-only' : $label_class = '';

        //if the field size is set, add it to the input class string
        isset($this->size) ? $input_class .= ' input-'.$this->size : $input_class .= '';

        //set the description HTML
        isset($this->desc) ? $desc = '<p class="help-block">'.$this->desc.'</p>' : $desc = '';

        //add any applicable extras
        isset($this->extras) ? $extras = str_replace('|', ' ', $this->extras) : $extras = '';

        //if this field has already been filled in on a previous step, grab the value
        if ( isset($_SESSION['storage'][$_SESSION['step']][$this->var]) ){
            $this->value = $_SESSION['storage'][$_SESSION['step']][$this->var];
        }

        //if no-save is present, do not repopulate field data on failed submit, but revert to original default value
        isset($this->value) && !strstr($extras, 'no-save') ? $field_value = $this->value : $field_value = $this->default;

        //set column pre and post HTML if form is set to horizontal
        $input_col_pre = ''; $input_col_post = '';
        if ( $this->settings('form-type') == 'horizontal' ){
            isset($this->input_width) ? $col = $this->input_width : $col = $this->settings('input-width');
            $input_col_pre = '<div class="'.$col.'">';
            $input_col_post = '</div>';
            isset($this->label_width) ? $col = $this->label_width : $col = $this->settings('label-width');
            $label_class .= ' '.$col;
        }

        //validate the field
        $valid_response = $this->validate_field();

        //output error and cancel field output if invalid
        if ( $valid_response !== 'valid' ){
            $this->bosch_error( $valid_response );
            return;
        }

        //begin HTML output
        echo '
            <div id="wrap-'.$this->var.'" class="form-group '.$required.' '.$error.'">
                <label for="form['.$this->var.']" class="control-label '.$label_class.'">
                   '.$this->name.'
                </label>'.
                $input_col_pre;

        switch ( $this->type ){

            case 'text' :
            case 'password' :
            case 'date' :
            case 'time' :
            case 'week' :
            case 'number' :
            case 'email' :
            case 'url' :
            case 'search' :
            case 'tel' :
            case 'color' :

                echo '<input id="'.$this->var.'" '.$extras.' type="'.$this->type.'" class="'.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$this->var.']" />';
            break;

            case 'money' : echo '
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input id="'.$this->var.'" '.$extras.' type="number" class="'.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$this->var.']" />
                    <span class="input-group-addon">.00</span>
                </div>';
            break;

            case 'textarea' : echo '
                <textarea id="'.$this->var.'" '.$extras.' class="'.$input_class.'" rows="'.$this->options.'" name="form['.$this->var.']">'.$field_value.'</textarea>';
            break;

            case 'state' : 
            case 'month' : 
            case 'day' : 
            case 'select' : 

                if ( $this->type == 'state' ) $this->options = parent::states();
                if ( $this->type == 'month' ) $this->options = parent::months();
                if ( $this->type == 'day' ) $this->options = parent::days();

            echo '
                <select id="'.$this->var.'" '.$extras.' class="'.$input_class.'" name="form['.$this->var.']">';
                    if ( $placeholder )
                        echo '<option value="">'.$placeholder.'</option>';
                    else
                        echo '<option value="">-- Choose --</option>';
                    foreach( $this->options as $id => $name ){
                        $field_value == $id ? $selected = 'selected' : $selected = '';
                        echo '<option '.$selected.' value="'.$id.'">'.$name.'</option>';
                    }

                echo 
                '</select>';
            break;

            case 'radio-inline' :
                echo '<div class="radio">';
                foreach( $this->options as $id => $name ){
                    $field_value == $id ? $checked = 'checked' : $checked = '';
                    echo '
                    <div class="radio-inline">
                        <label>
                            <input id="'.$this->var.'-'.parent::slugify($name).'" '.$extras.' type="radio" name="form['.$this->var.']" value="'.$id.'" '.$checked.'>
                            '.$name.'
                        </label>
                    </div>';
                }
                echo '</div>';

            break;

            case 'radio' :
                foreach( $this->options as $id => $name ){
                    $field_value == $id ? $checked = 'checked' : $checked = '';
                    echo '
                    <div class="radio">
                        <label>
                            <input id="'.$this->var.'-'.parent::slugify($name).'" '.$extras.' type="radio" name="form['.$this->var.']" value="'.$id.'" '.$checked.'>
                            '.$name.'
                        </label>
                    </div>';
                }

            break;

            case 'checkbox-inline' :
            case 'checkbox' :

                if ( $this->type == 'checkbox-inline' ){
                    $checkbox_pre   = '<div class="checkbox">';
                    $checkbox_post  = '</div>';
                    $checkbox_class = 'checkbox-inline';
                }
                else{
                    $checkbox_pre   = '';
                    $checkbox_post  = '';
                    $checkbox_class = 'checkbox';
                }

                //checkbox values always use arrays to allow for multiple checkbox values
                //if it's a single checkbox, convert it to an array for validating
                if ( !is_array( $field_value ) ){
                    $field_value = array( $field_value );
                } 

                echo $checkbox_pre;

                foreach( $this->options as $id => $name ){
                    in_array($id, $field_value) ? $checked = 'checked' : $checked = '';
                    echo '
                    <div class="'.$checkbox_class.'">
                        <label>
                            <input id="'.$this->var.'-'.parent::slugify($name).'" '.$extras.' type="checkbox" name="form['.$this->var.'][]" value="'.$id.'" '.$checked.'>
                            '.$name.'
                        </label>
                    </div>';
                }

                echo $checkbox_post;

            break;

            case 'captcha':
                echo 'CAPTCHA';
                break;

            default : 
                $this->bosch_error('Invalid type property <code>'.$this->type.'</code> in field <code>'.$this->var.'</code>');
                break;

        }

        echo 
        $desc.
        $input_col_post.
        '</div>';

        return;

    }
}
