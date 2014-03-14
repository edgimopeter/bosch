<?php

/**
 * Bosch - PHP form framework
 *
 * @author      Peter Adams (http://peteradamsdev.com)
 * @copyright   Copyright (c) 2013 Peter Adams
 * @link        https://github.com/edgimopeter/bosch
 * @version     1.0
 */

//phpdoc -d C:\wamp\www\bosch\bosch -t C:\wamp\www\bosch\guide --template="clean"

/**
 * Main Bosch class comprises fields and groups
 */
class Bosch {

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
     * Array holding field data for this bosch
     * @var array
     */
    private $fields = array();

    /**
     * Array holding group data for this bosch
     * @var array
     */
    private $groups = array();

    /**
     * Array holding step data for this bosch
     * @var array
     */
    private $steps = array();

    /**
     * Array holding errors for this bosch
     * @var array
     */
    public $errors = array();

    /**
     * After submit, array holding validated data for this bosch
     * @var array
     */
    public $data = array();

    /**
     * Various form settings
     * @var array
     */
    private $bosch_settings = array();

    /**
     * Set the current form step to 1
     * @var int
     */
    private $current_step = 0;

    /**
     * Default constructor to generate default settings
     */
    public function __construct() {
        
        $this->bosch_settings = 
            array(
                //this will wrap group headings
                'group-headings' => '<h2>',

                //block => inline => or horizontal
                'form-type' => 'block',

                //if horizontal => default column width for inputs
                'input-width' => 'col-md-10',

                //if horiztonal => default column width for labels
                'label-width' => 'col-md-2',

                //CSS class(es) applied to submit button
                'submit-class' => 'btn btn-primary',

                //value applied to submit button
                'submit-value' => 'Submit',

                //name applied to submit button
                'submit-name' => 'submit',

                //hide labels => true or false
                'hide-labels' => false,

                //use honeypot for captcha, true or false
                'honeypot' => true,

                //show debug info
                'debug' => false
            );
    }

    /**
     * Set/get for settings
     *
     * @param string $key The name of the setting to set or get
     * @param string $value Optional name of value to set
     */
    public function settings( $key, $value = false ){

        if ( $value ){
            $this->bosch_settings[$key] = $value;
            return true;
        }

        return $this->bosch_settings[$key];
    }
    
    /**
     * Output an exception message
     *
     * @param object $e The exception
     */
    private function bosch_exception ( $e ){
        echo '
        <div class="alert alert-danger bosch-exception">
            Exception: <strong>'.$e->getMessage().'</strong><br />
            Found in '.$e->getFile().' on line '.$e->getLine().'<br />
            Code: <pre>'.$e->getTraceAsString().'</pre>
        </div>';

        return;
    }

    /**
     * Output an error message
     *
     * @param string $text The error text
     */
    private function bosch_error ( $text ){
        echo '
        <div class="alert alert-danger bosch-error">
           '.$text.'
        </div>';

        return;
    }

    /**
     * Set the fields from a supplied array
     *
     * @param array $fields Array of fields
     */
    public function set_fields( $fields = array() ){

        //cycle through supplied fields
        foreach ($fields as $k => $v) {

            $new_field = new Bosch_Field( $fields[$k] );

            //check if the var has already been used
            if ( array_key_exists($fields[$k]['var'], $this->fields) ){
                $this->bosch_error('Duplicate <code>var</code> name detected: <code>'.$fields[$k]['var'].'</code>');
            }

            //add new field to the bosch->fields array
            else{
                $this->fields[$fields[$k]['var']] = $new_field;

                //check if the field has been configured correctly
                $valid_response = $new_field->validate_field();

                //output error message if configured incorrectly
                if ( $valid_response !== 'valid' ){
                    $this->bosch_error( $valid_response );
                }
            }
        }

        return;        
    }

    /**
     * Set the groups from a supplied array. Defaults to a single unnamed group.
     *
     * @param array $groups Array of groups
     */
    public function set_groups( $groups = array() ){

        foreach ($groups as $k => $v) {

            $new_group = new Bosch_Group( $groups[$k] );

            //if no name supplied, set it to a blank string
            if ( !isset($groups[$k]['name']) ){
                $groups[$k]['name'] = '';
            }

            //check for duplicate group name
            if ( array_key_exists( $this->slugify($groups[$k]['name']), $this->groups) ){
                $this->bosch_error('Duplicate Group Name detected: <code>'.$groups[$k]['name'].'</code>');
            }
            else{
                $this->groups[$this->slugify($groups[$k]['name'])] = $new_group;

                $valid_response = $new_group->validate_group();

                if ( $valid_response !== 'valid' ){
                    $this->bosch_error( $valid_response );
                }
            } 
        }

        return;
    }

    /**
     * Set the steps from a supplied array. Defaults to a single-step form.
     *
     * @param array $steps Array of steps
     */
    public function set_steps( $steps = array() ){

        foreach ($steps as $k => $v) {
            $this->steps[] = new Bosch_Step( $steps[$k] );
        }

        return;
    }

    /**
     * Output the entire bosch form
     * @todo finalize multistep buttons
     */
    public function output(){

        //if no groups have been set yet, create a generic group for all fields
        if ( !isset($this->groups) || empty($this->groups) ){
            foreach ($this->fields as $field) {
                $field_vars[] = $field->var;
            }

            $this->set_groups( array(
                array( 
                    'name' => 'generic_group',
                    'hide_name' => true,
                    'fields' => implode('|', $field_vars) )
                ) 
            );
        }

        //if no steps have been set yet, create a generic step for all groups
        if ( !isset($this->steps) || empty($this->steps) ){
            foreach ($this->groups as $group) {
                $group_vars[] = $group->name;
            }

            $this->set_steps(array(array('groups' => implode('|', $group_vars))));      
        }

        //get form type from settings
        switch ( $this->settings('form-type') ){
            case 'block' : $class = ''; break;
            case 'inline' : $class = 'form-inline'; break;
            case 'horizontal' : $class = 'form-horizontal'; break;
            default: $class = '';
        }

        echo '
        <form role="form" class="bosch-form step-'.$this->current_step.' '.$class.'" method="post">';
    
            $this->output_current_step();

            echo '
            <input type="submit" value="'.$this->settings('submit-value').'" name="'.$this->settings('submit-name').'" class="'.$this->settings('submit-class').'">';
            //$this->buttons();

        echo '</form>';

        return;

    }

    public function output_current_step(){

        foreach ( explode('|', $this->steps[$this->current_step]->groups) as $group){
            $this->output_group( $group );               
        }
    }

    /**
     * Output a single group
     *
     * When called directly with $group as a string, converts the string group name into the corresponding group object
     *
     * @param mixed $group Name of group to output, or group object to output
     */
    public function output_group( $group ){

        if ( !is_object($group) ){
            $group = $this->groups[$this->slugify($group)];
        }

        //hide the name if set
        isset($group->hide_name) && $group->hide_name === true ? $heading = '' : $heading = $group->name;

        echo 
        $group->html_before . '                    
        <div class="bosch-group group-'.$this->slugify( $group->name ).'">';

        if ( $group->hide_name === false ){
            echo '
            <div class="bosch-heading">
                '.$this->settings('group-headings') . $group->name . $this->close_tag($this->settings('group-headings')) .'
            </div>';
        }

        echo '            
            <div class="bosch-group-desc">
                '.$group->desc.'
            </div>';

            //cycle through the fields in this group
            //output the field if possible, throw exception if not
            foreach ( explode('|', $group->fields) as $field){

                try{
                     if ( !array_key_exists($field, $this->fields) ){
                        throw new Exception('Invalid Field <code>'.$field.'</code> in Group <code>'.$group->name.'</code>');
                     }

                      $this->output_field( $this->fields[$field] );

                }
                catch (Exception $e) {
                    $this->bosch_exception( $e );                   
                }
            }

        echo '
        </div>'.
        $group->html_after;

    }

    /**
     * Output a single field
     *
     * @param mixed $field Name of field to output, or field object to output
     * @todo add captcha support
     */
    public function output_field( $field ){

        //when called directly by user, convert the string field name into the corresponding field object
        if ( !is_object($field) ){
            $field = $this->fields[$this->slugify($field)];
        }

        //set default input class
        $input_class = 'form-control';

        //check of field is required
        in_array('required', explode('|', $field->validate) )? $required = 'required' : $required = '';

        //check if the form has been submitted and the field has an error
        isset($this->errors) && array_key_exists($field->var, $this->errors) ? $error = 'has-error' : $error = '';

        //set the placeholder value
        isset($field->placeholder) ? $placeholder = $field->placeholder : $placeholder = '';

        //hide the label if defined by the field variable
        isset($field->hide_label) && $field->hide_label === true ? $label_class = 'sr-only' : $label_class = '';

        //hide the label if the global setting is true
        $this->settings('hide-labels') === true ? $label_class = 'sr-only' : $label_class = '';

        //if the field size is set, add it to the input class string
        isset($field->size) ? $input_class .= ' input-'.$field->size : $input_class .= '';

        //set the description HTML
        isset($field->desc) ? $desc = '<p class="help-block">'.$field->desc.'</p>' : $desc = '';

        //add any applicable extras
        isset($field->extras) ? $extras = str_replace('|', ' ', $field->extras) : $extras = '';

        //if no-save is present, do not repopulate field data on failed submit, but revert to original default value
        isset($field->value) && !strstr($extras, 'no-save') ? $field_value = $field->value : $field_value = $field->default;

        //set column pre and post HTML if form is set to horizontal
        $input_col_pre = ''; $input_col_post = '';
        if ( $this->settings('form-type') == 'horizontal' ){
            isset($field->input_width) ? $col = $field->input_width : $col = $this->settings('input-width');
            $input_col_pre = '<div class="'.$col.'">';
            $input_col_post = '</div>';
            isset($field->label_width) ? $col = $field->label_width : $col = $this->settings('label-width');
            $label_class .= ' '.$col;
        }

        //validate the field
        $valid_response = $field->validate_field();

        //output error and cancel field output if invalid
        if ( $valid_response !== 'valid' ){
            $this->bosch_error( $valid_response );
            return;
        }

        //begin HTML output
        echo '
            <div id="wrap-'.$field->var.'" class="form-group '.$required.' '.$error.'">
                <label for="form['.$field->var.']" class="control-label '.$label_class.'">
                   '.$field->name.'
                </label>'.
                $input_col_pre;

        switch ( $field->type ){

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

                echo '<input id="'.$field->var.'" '.$extras.' type="'.$field->type.'" class="'.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$field->var.']" />';
            break;

            case 'money' : echo '
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input id="'.$field->var.'" '.$extras.' type="number" class="'.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$field->var.']" />
                    <span class="input-group-addon">.00</span>
                </div>';
            break;

            case 'textarea' : echo '
                <textarea id="'.$field->var.'" '.$extras.' class="'.$input_class.'" rows="'.$field->options.'" name="form['.$field->var.']">'.$field_value.'</textarea>';
            break;

            case 'state' : 
            case 'month' : 
            case 'day' : 
            case 'select' : 

                if ( $field->type == 'state' ) $field->options = $this->states;
                if ( $field->type == 'month' ) $field->options = $this->months;
                if ( $field->type == 'day' ) $field->options = $this->days;

            echo '
                <select id="'.$field->var.'" '.$extras.' class="'.$input_class.'" name="form['.$field->var.']">';
                    if ( $placeholder )
                        echo '<option value="">'.$placeholder.'</option>';
                    else
                        echo '<option value="">-- Choose --</option>';
                    foreach( $field->options as $id => $name ){
                        $field_value == $id ? $selected = 'selected' : $selected = '';
                        echo '<option '.$selected.' value="'.$id.'">'.$name.'</option>';
                    }

                echo 
                '</select>';
            break;

            case 'radio-inline' :
                echo '<div class="radio">';
                foreach( $field->options as $id => $name ){
                    $field_value == $id ? $checked = 'checked' : $checked = '';
                    echo '
                    <div class="radio-inline">
                        <label>
                            <input id="'.$field->var.'-'.$this->slugify($name).'" '.$extras.' type="radio" name="form['.$field->var.']" value="'.$id.'" '.$checked.'>
                            '.$name.'
                        </label>
                    </div>';
                }
                echo '</div>';

            break;

            case 'radio' :
                foreach( $field->options as $id => $name ){
                    $field_value == $id ? $checked = 'checked' : $checked = '';
                    echo '
                    <div class="radio">
                        <label>
                            <input id="'.$field->var.'-'.$this->slugify($name).'" '.$extras.' type="radio" name="form['.$field->var.']" value="'.$id.'" '.$checked.'>
                            '.$name.'
                        </label>
                    </div>';
                }

            break;

            case 'checkbox-inline' :
            case 'checkbox' :

                if ( $field->type == 'checkbox-inline' ){
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

                foreach( $field->options as $id => $name ){
                    in_array($id, $field_value) ? $checked = 'checked' : $checked = '';
                    echo '
                    <div class="'.$checkbox_class.'">
                        <label>
                            <input id="'.$field->var.'-'.$this->slugify($name).'" '.$extras.' type="checkbox" name="form['.$field->var.'][]" value="'.$id.'" '.$checked.'>
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
                $this->bosch_error('Invalid type property <code>'.$field->type.'</code> in field <code>'.$field->var.'</code>');
                break;

        }

        echo 
        $desc.
        $input_col_post.
        '</div>';

        return;

    }

    /**
     * Check if form has been submitted
     *
     * @return bool 
     */
    public function has_been_submitted(){

        if ( !isset($_POST[$this->settings('submit-name')]) )
            return false;

        if ( $_POST[$this->settings('submit-name')] !== $this->settings('submit-value') )
            return false;

        return true;

    }

    /**
     * Process a submitted form
     * Populates either $this->errors or $this->data
     * Returns true if no errors
     *
     * @return bool
     * @todo implement local version of GUMP
     */
    public function process(){
        //don't run if the form hasn't been submitted
        if ( !$this->has_been_submitted() )
            return;

        $validator = new Bosch_Validator( $this->fields );
        $_POST = $validator->sanitize($_POST);

        foreach ($_POST['form'] as $k => $v) {

            //ignore the honeypot field
            if ( $k !== 'hp' ){
                if ( is_array($_POST['form'][$k]) ){
                    $_POST['form'][$k] = implode('|', $_POST['form'][$k]);
                }

                if ( !empty($this->fields[$k]->validate) )
                    $validate[$k] = $this->fields[$k]->validate;
                
                if ( !empty($this->fields[$k]->filter) )
                    $filter[$k] = $this->fields[$k]->filter;

                $this->fields[$k]->value = $v;
            }
        }

        if ( !empty($validate) )
            $validator->validation_rules($validate);

        if ( !empty($filter) )
            $validator->filter_rules($filter);

        $validated_data = $validator->run($_POST['form']);
        $errors = array();
        
        //required checkboxes must be checked manually, as they are not present in the $_POST array
        $missing_checkboxes = $this->validate_required_checkboxes($_POST['form']);
        if ( !empty($missing_checkboxes) ){
            $validated_data = false;
            $errors = $missing_checkboxes;
        }

        //GUMP validation failed, merge GUMP errors with checkbox errors
        if ( $validated_data === false ){
            $errors = array_merge($errors, $validator->get_readable_errors(false));
            $this->errors = $errors;
            return false;
        }
        
        //no errors, save the validated data and return true
        else{
            $this->data = $validated_data;

            foreach ($validated_data as $k => $v) {
                $_SESSION['storage'][$this->current_step][$k] = $v;
                $this->current_step++;
            }

            return true;
        }
    }

    /**
     * Validates the user has input a value for required checkbox
     * Requires its own function because checkbox values are stored in a sub-array to allow for multiple checks
     *
     * @param array $post_data The $_POST['form'] array
     * @return array
     */
    private function validate_required_checkboxes( $post_data ){

        $missing = array();

        foreach ($this->fields as $field) {
            if ( $field->type == 'checkbox' || $field->type == 'checkbox-inline' ){
                if ( !array_key_exists($field->var, $post_data) && strstr($field->validate, 'required') ){
                    $missing[$field->var] = 'The '.$field->name.' field is required';
                }
            }
        }

        return $missing;
    }

    /**
     * Outputs any errors after $this->process has been called
     *
     */
    public function output_errors(){

        if ( !$this->has_been_submitted() )
            return;

        if ( isset($this->errors) && !empty($this->errors) ){
            echo '<div class="alert alert-danger">';
                foreach ($this->errors as $error) {
                    echo $error . '<br />';
                }
            echo '</div>';

            return;
        }
    }

    /**
     * Check if the honeypot field has data
     * @return bool
     *
     */
    function blank_honeypot(){
        if ( isset($this->data['hp']) && !empty($this->data['hp']) && $this->data['hp'] !== '' ){
            return false;
        }

        return true;
    }

    /**
     * Output the buttons
     * @return string
     */
    public function buttons(){

        $btns = '<div class="row">';

        //last step
        if ( ($this->current_step + 1) === count( $this->steps ) ){
            if ( $this->settings('honeypot') ){
                $btns .= '
                <div class="sr-only"><label for="form[hp]">Honeypot: If you see this field, leave it blank</label><input name="form[hp]" type="text" value=""></div>';
            }

            $btns .= '<input type="submit" value="'.$this->settings('submit-value').'" name="'.$this->settings('submit-name').'" class="'.$this->settings('submit-class').'">';
        }
        else{
            $btns .= '
            <input type="hidden" name="step" value="'.$this->current_step.'"';
            if ( $this->steps[$this->current_step]->$prev === true ){
                $btns .= '
                
                ';
            }
        }

        $btns .= '</div>';

        return $btns;

        
    }

    /**
     * Convert any string to a variable-type slug
     * @param string $text The string to be converted
     * @return string
     *
     */
    private function slugify($text){ 
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)){
            return 'n-a';
        }

        return $text;
    }

    /**
     * Close an HTML tag
     * @param string $tag The tag to be closed, e.g. '<h3>' or '<p>'
     * @return string
     *
     */
    private function close_tag( $tag ){ 
        if ( $tag == '' )
            return;

        $out = ''; 
        $temp = substr($tag, 1); 
        $out = substr_replace($tag,'/', 1); 
        $out .= $temp; 
        return $out; 
    }
}

