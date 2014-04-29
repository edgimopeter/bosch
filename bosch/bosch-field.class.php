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
     * HTML to process before field
     * @var string
     */
    public $html_before = '';
    
    /**
     * HTML to process after field
     * @var string
     */
    public $html_after = '';

    /**
     * Null field for select options
     * @var string
     */
    public $select_null = '-- Choose --';

    /**
     * After processing, store error message for field here (if applicable)
     * @var string
     */
    protected $error;

    private $valid_field_keys = array('var','name','type','options','desc','default','validate','filter','placeholder','hide_label','size','input_width','label_width','extras', 'html_before', 'html_after', 'select_null');

    /**
     * Constructor
     * @param array $properties 
     */
    function __construct( $properties = array() ){

        foreach ($properties as $k => $v) {
            $this->$k = $v;
            if ( !in_array($k, $this->valid_field_keys) ){
                $this->bosch_error('Invalid field variable detected: <code>'.$k.'</code>');
            }
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
        $this->has_error() ? $error = 'has-error' : $error = '';

        //set the placeholder value
        isset($this->placeholder) ? $placeholder = $this->placeholder : $placeholder = '';

        //hide the label if defined by the field variable
        isset($this->hide_label) && $this->hide_label === true ? $label_class = 'sr-only' : $label_class = '';

        //hide the label if the global setting is true
        parent::settings('hide-labels') === true ? $label_class = 'sr-only' : $label_class = '';

        //if the field size is set, add it to the input class string
        isset($this->size) ? $input_class .= ' input-'.$this->size : $input_class .= '';

        //set the description HTML
        isset($this->desc) ? $desc = '<p class="help-block">'.$this->desc.'</p>' : $desc = '';

        //add any applicable extras
        isset($this->extras) ? $extras = str_replace('|', ' ', $this->extras) : $extras = '';

        $field_value = $this->get_updated_value();        

        //set column pre and post HTML if form is set to horizontal
        $input_col_pre = ''; $input_col_post = '';
        if ( parent::settings('form-type') == 'horizontal' ){
            isset($this->input_width) ? $col = $this->input_width : $col = parent::settings('input-width');
            $input_col_pre = '<div class="'.$col.'">';
            $input_col_post = '</div>';
            isset($this->label_width) ? $col = $this->label_width : $col = parent::settings('label-width');
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
        echo 
        $this->html_before.
        '<div id="wrap-'.$this->var.'" class="form-group '.$required.' '.$error.'">';
            if ( $this->name !== '' ){
                echo '
                <label for="form['.$this->var.']" class="control-label '.$label_class.'">
                   '.$this->name.'
                </label>';
            }

            echo $input_col_pre;

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
            case 'hidden' :

                echo '
                <input id="'.$this->var.'" '.$extras.' type="'.$this->type.'" class="'.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$this->var.']" />';
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

                if ( $this->type == 'state' ) $this->options = $this->states();
                if ( $this->type == 'month' ) $this->options = $this->months();
                if ( $this->type == 'day' ) $this->options = $this->days();

            echo '
                <select id="'.$this->var.'" '.$extras.' class="'.$input_class.'" name="form['.$this->var.']">';
                    if ( $placeholder )
                        echo '<option value="">'.$placeholder.'</option>';
                    else
                        echo '<option value="">'.$this->select_null.'</option>';
                    foreach( $this->options as $id => $name ){
                        ($field_value == $id && !is_null($field_value)) ? $selected = 'selected' : $selected = '';
                        echo '<option '.$selected.' value="'.$id.'">'.$name.'</option>';
                    }

                echo 
                '</select>';
            break;

            case 'radio-inline' :
                echo '<div class="radio">';
                foreach( $this->options as $id => $name ){
                    ($field_value == $id && !is_null($field_value)) ? $checked = 'checked' : $checked = '';
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
                    ($field_value == $id && !is_null($field_value)) ? $checked = 'checked' : $checked = '';
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

            case 'file':
                echo '<input id="'.$this->var.'" '.$extras.' type="file" class="'.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$this->var.']" />';
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
        '</div>'.
        $this->html_after;

        return;

    }

    protected function has_error(){

        if ( isset($this->error) && $this->error !== '' && !empty($this->error) ){
            return true;
        }

        return false;
    }

    private function get_updated_value(){

        //if this field has already been filled in on a previous step, grab the value
        if ( isset($_SESSION['storage'][$_SESSION['step']][$this->var]) ){
            $this->value = $_SESSION['storage'][$_SESSION['step']][$this->var];
        }

        //if no-save is present, do not repopulate field data on failed submit, but revert to original default value
        isset($this->value) && !strstr($this->extras, 'no-save') ? $field_value = $this->value : $field_value = $this->default;

        return $field_value;
    }
}
