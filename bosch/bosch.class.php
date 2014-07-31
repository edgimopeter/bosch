<?php

/**
 * Bosch - PHP form framework
 *
 * @author      Peter Adams (http://peteradamsdev.com)
 * @copyright   Copyright (c) 2014 Peter Adams
 * @link        https://github.com/edgimopeter/bosch
 * @version     1.0
 */

//phpdoc -d C:\wamp\www\bosch\bosch -t C:\wamp\www\bosch\guide --template="clean"

/**
 * Main Bosch class comprises fields and groups
 */
class Bosch {

    
    /**
     * Array holding field objects for this bosch
     * @var array
     */
    private $fields = array();

    /**
     * Array holding group objects for this bosch
     * @var array
     */
    private $groups = array();

    /**
     * Array holding step objects for this bosch
     * @var array
     */
    private $steps = array();

    /**
     * Array holding errors for this bosch
     * @var array
     */
    protected $errors = array();

    /**
     * Array holding buttons
     * Always includes the default 3 buttons - prev, next, and submit
     * @var array
     */
    private $buttons = array();

    /**
     * After submit, array holding validated data for this bosch indexed by step
     * $data[0]['var_name'];
     * @var array
     */
    public $data = array();

    public static $valid_field_keys = array('var','name','type','options','desc','default','validate','filter','placeholder','hide_label','size','input_width','label_width','extras', 'html_before', 'html_after', 'select_null');

    /**
     * Various form settings
     * @var array
     */
    private static $bosch_settings = array(

        //set as the ID
        'form-name' => 'bosch_form',

        //this will wrap group headings
        'group-headings' => '<h2>',

        //block, inline, or horizontal
        'form-type' => 'block',

        //if horizontal, default column width for inputs
        'input-width' => 'col-md-10',

        //if horiztonal, default column width for labels
        'label-width' => 'col-md-2',

        //CSS class(es) applied to submit button
        'submit-class' => 'btn btn-primary',

        //CSS class(es) applied to prev and next buttons
        'nav-class' => 'btn btn-info',

        //value applied to submit button
        'submit-value' => 'Submit',

        //name applied to submit button
        'submit-name' => 'submit',

        //name applied to next button
        'next-name' => 'next',

        //name applied to next button
        'next-value' => 'Continue',

        //name applied to next button
        'prev-name' => 'prev',

        //name applied to prev button
        'prev-value' => 'Go Back',

        //hide labels => true or false
        'hide-labels' => false,

        //use honeypot for captcha, true or false
        'honeypot' => true,

        //HTML to display before errors are listed
        'error-header' => '<strong>Please correct the following errors</strong><br />',

        //show debug info
        'debug' => true
    );

    /**
     * Setup functions
     * Initialize the form, settings, and data
     * @package setup_functions
     */
    
    function __construct( $form_name = false ){

        if ( $form_name !== false ){
            $this->settings('form-name', $form_name);
        }
        
        if ( !isset($_SESSION[$this->settings('form-name').'-step']) ){
            $_SESSION[$this->settings('form-name').'-step'] = 0;
        }

        if (isset($_SESSION[$this->settings('form-name').'-last_activity']) && (time() - $_SESSION[$this->settings('form-name').'-last_activity'] > 1800)) {
            $this->reset();
        }

        $_SESSION[$this->settings('form-name').'-last_activity'] = time();
    }

    /**
     * Set/get for settings
     *
     * @param string $key The name of the setting to set or get
     * @param string $value Optional name of value to set
     * @return mixed
     */
    public static function settings( $key, $value = false ){

        try{
             if ( !array_key_exists($key, self::$bosch_settings) ){
                throw new Exception('Invalid Settings Key: <code>'.$key.'</code>');
             }

             if ( $value ){
                self::$bosch_settings[$key] = $value;
                return true;
            }

            return self::$bosch_settings[$key];

        }
        catch (Exception $e) {
            Bosch::exception( $e );                   
        }
    }

    /**
     * Setup the form
     * Given an array of data, initializes the Bosch form object
     * Fields array is the only parameter required
     * 
     * @param  array  $fields Array of field data
     * @param  array  $groups Array of group data
     * @param  array  $steps  Array of step data
     * @return bool
     */
    public function setup( $fields = array(), $groups = array(), $steps = array(), $buttons = array() ){

        //fields must be supplied, so do those first, converting the array into objects
        foreach ($fields as $k => $v) {

            $new_field = new Bosch_Field( $fields[$k] );

            //check if the var has already been used
            if ( array_key_exists($fields[$k]['var'], $this->fields) ){
                Bosch::error('Duplicate field <code>var</code> name detected: <code>'.$fields[$k]['var'].'</code>');
            }
            else{
                $this->fields[$fields[$k]['var']] = $new_field;

                //check if the field has been configured correctly
                $valid_response = $new_field->validate_field_setup();

                //output error message if configured incorrectly
                if ( $valid_response !== 'valid' ){
                    Bosch::error( $valid_response );
                }
            }
        }

        //if no groups have been set yet, create a generic group for all fields
        if ( !isset($groups) || empty($groups) ){
            foreach ($this->fields as $field) {
                $field_vars[] = $field->var;
            }

            $groups = array( 
            array( 
                    'name' => 'Generic Group',
                    'var' => 'generic_group',
                    'hide_name' => true,
                    'fields' => implode('|', $field_vars)
                )
            );
        } 

        foreach ($groups as $group) {

            if ( !isset($group['var']) && !isset($group['name']) ){
                Bosch::error('You must set either a <code>var</code> or <code>name</code> field for every group.');
            }

            if ( !isset($group['var']) || $group['var'] == ''){
                $group['var'] = Bosch::slugify( $group['name'] );
            }

            //check for duplicate group name
            if ( array_key_exists( $group['var'], $this->groups) ){
                Bosch::error('Duplicate Group Name detected: <code>'.$group['name'].'</code>');
            }
            else{

                $group['fields'] = array_combine(explode('|', $group['fields']), explode('|', $group['fields']));

                $this->groups[$group['var']] = $group;

                //group must have at least one field associated with it
                if ( empty($this->fields) || $this->fields == false ){
                    Bosch::bosch_error('No <code>fields</code> property set for group <code>'.$group['name'].'</code>');
                }
            } 
        }

        //if no steps have been set, create a generic step for all groups
        if ( !isset($steps) || empty($steps) ){
            foreach ($this->groups as $group) {
                $group_names[] = $group['var'];
            }

            $steps = array(
            array( 'groups' => implode('|', $group_names) )
            );
        }
       
        foreach ($steps as $step) {
            $step['groups'] = array_combine(explode('|', $step['groups']), explode('|', $step['groups']));
            if ( !isset($step['prev']) ) $step['prev'] = true;
            if ( !isset($step['next']) ) $step['next'] = true;

            $this->steps[] = $step;
        }

        //Setup default buttons
        $this->buttons['submit'] = new Bosch_Button(
        array(
            'name'  => $this->settings('submit-name'),
            'var'   => 'submit',
            'value' => $this->settings('submit-value'),
            'class' => $this->settings('submit-class'),
            'type'  => 'submit'
        ));

        $this->buttons['prev'] = new Bosch_Button(
        array(
            'name'  => $this->settings('prev-name'),
            'var'   => 'prev',
            'value' => $this->settings('prev-value'),
            'class' => 'btn btn-info',
            'type'  => 'prev'
        ));

        $this->buttons['next'] = new Bosch_Button(
        array(
            'name'  => $this->settings('next-name'),
            'var'   => 'next',
            'value' => $this->settings('next-value'),
            'class' => 'btn btn-info',
            'type'  => 'next'
        ));

        if ( isset($buttons) && !empty($buttons) ){
            foreach ($buttons as $k => $v) {
                $new_button = new Bosch_Button( $buttons[$k] );
                $this->buttons[$new_button->var] = $new_button;
            }
        }

        return true; 
    }

    public function remove_field_from_group( $field, $group ){

        if ( $this->is_field_in_group( $field, array($group) ) ){
            unset($this->groups[$group]['fields'][$field]);
            return true;
        }

        Bosch::warning("Field <code>$field</code> not found in group <code>$group</code>");
        return false;
    }

    /**
     * Processing functions
     * Used by Bosch to process forms and perform state checking
     * @package processing_functions
     */

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

        $current_step = $_SESSION[$this->settings('form-name').'-step'];

        //previous button was clicked
        if ( isset($_POST[$this->settings('prev-name')]) && $_POST[$this->settings('prev-name')] == $this->settings('prev-value') && $current_step > 0 ){
            $_SESSION[$this->settings('form-name').'-step']--;
        }

        $has_errors = false;        

        if ( !empty($_POST['form']) ){

            $_POST['form'] = $this->sanitize($_POST['form']);

            foreach ( $this->fields as $k => $v ) {
                //only work with fields in the current step/group
                if ( $this->is_field_in_group( $k, $this->steps[$current_step]['groups'] ) ){

                    //for processing, value is copied from $_POST or NULL if not present
                    isset( $_POST['form'][$k] ) ? $value = $_POST['form'][$k] : $value = NULL;
                  
                    $success = $this->fields[$k]->process( $value );
                    
                    if ( !$success ){
                        $has_errors = true;
                    }
                }                
            }
        }

        if ( $has_errors ){
            foreach ($this->fields as $field) {
                if ( $this->is_field_in_group( $field->var, $this->steps[$current_step]['groups'] ) && !empty($field->errors)){
                    $this->errors[] = $field->errors['message'];                        
                }
            }

            return false;
        }
        else{

            //put the recently submitted data in the $_SESSION[$this->settings('form-name').'-storage'] array in the current step number
            //eg: $_SESSION[$this->settings('form-name').'-storage'][0][var_name] = value;
            foreach ( $this->fields as $k => $v ) {
                if ( $this->is_field_in_group( $k, $this->steps[$current_step]['groups'] ) ){
                    $_SESSION[$this->settings('form-name').'-storage'][$current_step][$this->fields[$k]->var] = $this->fields[$k]->default;
                }
            }

            //update the form's stored data to all the submitted data so far
            //$this->data[step_num][var_name] = value;
            foreach ($_SESSION[$this->settings('form-name').'-storage'] as $step_num => $step_array) {
                foreach ($step_array as $k => $v) {                    
                    $this->data[$step_num][$k] = $step_array[$k];
                }       
            }

            //increase the step if Next was clicked
            if ( isset($_POST[$this->settings('next-name')]) && $_POST[$this->settings('next-name')] == $this->settings('next-value') && ($current_step + 1) < count( $this->steps )  )
                $_SESSION[$this->settings('form-name').'-step']++;

            return true;
        }        
    }

    public function is_field_in_group( $field, array $current_groups ){
        foreach ($current_groups as $current_group) {
            if ( in_array($field, $this->groups[$current_group]['fields'] ) ){
                return true;
            }
        }

        return false;
    } 

    /**
     * Public method to manually add an error to a form field
     * @return bool
     */
    public function set_error( $field, $message ){

        if ( !array_key_exists($field, $this->fields) ){
            Bosch::error( 'Cannot call <code>set_error</code> on non-existant field <code>'.$field.'</code>' );
            return false;
        }

        //if a <span> is present, convert it to a link like normal errors
        if ( strstr($message, '<span>') ){
            preg_match('/<span>(.*?)<\/span>/s', $message, $matches);
            $pretty = $matches[1];
            $message = str_replace('<span>'.$pretty.'</span>', '<a class="alert-link" href="#'.$field.'" title="'.$pretty.'">'.$pretty.'</a>', $message);
        }

        $this->fields[$field]->errors['message'] = $message;
        $this->errors[] = $message;

        return true;
    }

    /**
     * Reset the form to the first step and clear stored data
     * @return bool
     */
    public function reset(){

        $_SESSION[$this->settings('form-name').'-step'] = 0;
        unset($_SESSION[$this->settings('form-name').'-storage']);
        unset($this->data);

        foreach ($this->fields as $field) {
            $field->default = '';
        }

        return true;
    }

    /**
     * Check if form has been submitted, either final submit or prev/next step
     * @return bool 
     */
    public function has_been_submitted(){

        $found = false;

        foreach ($this->buttons as $button) {
            if ( isset($_POST[$button->name]) )
                $found = true;
        }

        return $found;
    }

    /**
     * Check if the final step has been submitted
     * @return bool 
     */
    public function final_submit(){

        $found = false;

        foreach ($this->buttons as $button) {
            if ( $button->type === 'submit' && isset($_POST[$button->name]) && $_POST[$button->name] === $button->value )        
                $found = true;
        }

        return $found;
    }

    /**
     * Check if the honeypot field has data
     * @return bool
     */
    public function blank_honeypot(){
        if ( isset($this->data['hp']) && !empty($this->data['hp']) && $this->data['hp'] !== '' ){
            return false;
        }

        return true;
    }    

    /**
     * Check if the form is displaying the final step
     * @return boolean
     */
    public function is_final_step(){

        if ( ($_SESSION[$this->settings('form-name').'-step'] + 1) === count( $this->steps ) || count( $this->steps ) === 1 ){
            return true;
        }

        return false;
    }

    /**
     * Output functions
     * Used to output form elements, usually public
     * @package output_functions
     */
    
    /**
     * Output the entire bosch form
     *  @return bool
     */
    public function output(){

        $this->output_form_header();

        echo '<div class="row">';
    
            $this->output_step($_SESSION[$this->settings('form-name').'-step']);
            $this->output_buttons();

        echo '</div>
        </form>';

        return true;
    }

    public function output_form_header(){

        //get form type from settings
        switch ( $this->settings('form-type') ){
            case 'block' : $class = ''; break;
            case 'inline' : $class = 'form-inline'; break;
            case 'horizontal' : $class = 'form-horizontal'; break;
            default: $class = '';
        }

        $this->has_file_inputs() ? $enc = 'enctype="multipart/form-data"' : $enc = '';

        echo '<form id="'.$this->settings('form-name').'" role="form" class="bosch-form step-'.$_SESSION[$this->settings('form-name').'-step'].' '.$class.'" method="post" '.$enc.'>';
    }

    public function output_step( $step_num ){
        $step = $this->steps[$step_num];
        foreach ($step['groups'] as $group_var) {
            $this->output_group($group_var);
        }
    }

    public function output_group( $group_var ){

        $group = $this->groups[$group_var];

        $html_before = isset($group['html_before']) ? $group['html_before'] : '';
        $html_after  = isset($group['html_after']) ? $group['html_after'] : '';
        $width       = isset($group['width']) ? $group['width'] : 'col-md-12';
        $desc        = isset($group['desc']) ? $group['desc'] : '';
        $hide_name   = isset($group['hide_name']) ? $group['hide_name'] : false;

        echo 
        $html_before . '
        <div class="'.$width.'">
            <div class="bosch-group group-'.$group['var'].'">';

                if ( !$hide_name ){
                    echo '
                    <div class="bosch-heading">
                        '.Bosch::settings('group-headings') . $group['name'] . Bosch::close_tag(Bosch::settings('group-headings')) .'
                    </div>';
                }

                if ( $desc !== '' ){
                    echo '            
                    <div class="bosch-group-desc">
                        '.$desc.'
                    </div>';
                }

                //cycle through the fields in this group
                //output the field if possible, throw exception if not
                foreach ( $group['fields'] as $field_var ){

                    try{
                         if ( !array_key_exists($field_var, $this->fields) ){
                            throw new Exception('Invalid Field <code>'.$field_var.'</code> in Group <code>'.$group['name'].'</code>');
                         }

                         $this->fields[$field_var]->output_field();

                    }
                    catch (Exception $e) {
                        Bosch::exception( $e );                   
                    }
                }

            echo '
            </div>
        </div>'.
        $html_after;
    }

    /**
     * Merge all the data into a single array
     * @return array Associate array of saved values
     */
    public function get_all_data(){

        $values = array();

        foreach ($this->steps as $k => $v) {
            $values = array_merge( $values, $this->data[$k]);
        }

        return $values;
    }

    public function get_field( $field ){

        if ( array_key_exists($field, $this->fields) ){
            $field = $this->fields[$field];
            $field->output_field();
            return true;
        }

        Bosch::error('No field found with var <code>'.$field.'</code>');
        return false;

    }

    /**
     * Outputs any errors after $this->process has been called
     * @return bool
     */
    public function output_errors(){

        if ( !$this->has_been_submitted() )
            return false;

        if ( isset($this->errors) && !empty($this->errors) ){
            echo '
            <div class="alert alert-danger">'.$this->settings('error-header');
                foreach ($this->errors as $error) {
                    echo $error . '<br />';
                }
            echo '</div>';

            return true;
        }
    }

    public function output_button($button){
        echo $this->buttons[$button]->get_html();
    }
    
    /**
     * Get HTML for the buttons
     * @return string
     */
    public function output_buttons(){

        $btns = '';

        //single step form - output submit button(s)
        if ( count( $this->steps ) === 1 ){
            
            if ( count($this->buttons) <= 3 ){
                if ( $this->settings('form-type') == 'horizontal' ){
                    $pre = '<div class="'.$this->settings('label-width').'"></div><div class="'.$this->settings('input-width').' bosch-submit-row bosch-single-submit">';
                }
                else{
                    $pre = '<div class="col-md-12 bosch-submit-row bosch-single-submit">';
                }

                $post = '</div>';
            }
            else{
                $pre = '<div class="col-md-12 bosch-submit-row">';
                $post = '</div>';
            }

            $btns .= $pre;

            foreach ($this->buttons as $button) {
                if ( $button->type === 'submit' ){
                    $btns .= $button->get_html();
                }
            }

            if ( $this->settings('honeypot') === true ){
                $btns .= $this->honeypot();
            }

            $btns .= $post;
        }
        else{
            
            $btns .= '
            <div class="col-md-6">';
                if ( $this->steps[$_SESSION[$this->settings('form-name').'-step']]['prev'] === true && $_SESSION[$this->settings('form-name').'-step'] != 0 ){
                    $btns .= $this->buttons['prev']->get_html();
                }
            $btns .= '
            </div>';

            //with custom submit buttons, put all submit buttons on a new row
            if ( count($this->buttons) > 3 ){
                $pre = '<div class="col-md-12 bosch-submit-row">';
                $post = '</div></div>';
            }

            //otherwise, submit button is placed on right
            else{
                $pre = '<div class="col-md-6">';
                $post = '</div>';
            }

            $btns .= $pre;

            if ( ($_SESSION[$this->settings('form-name').'-step'] + 1) === count( $this->steps ) ){

                if ( $this->settings('honeypot') === true ){
                    $btns .= $this->honeypot();
                }

                foreach ($this->buttons as $button) {

                    if ( $button->type === 'submit' ){
                        $btns .= $button->get_html();
                    }
                }
            }
            else{
                if ( $this->steps[$_SESSION[$this->settings('form-name').'-step']]['next'] === true && ($_SESSION[$this->settings('form-name').'-step'] + 1) !== count( $this->steps ) ){
                    $btns .= $this->buttons['next']->get_html();
                }
            }
                
            $btns .= $post;
        }

        echo $btns;
    }

    /**
     * Output the honeypot field
     * @return string
     */
    public function honeypot(){
        return '<div class="sr-only" style="display:none;"><label for="form[hp]">Bot test: If you see this field, leave it blank</label><input name="form[hp]" type="text" value=""></div>';
    }

    /**
     * Output an exception message
     * @param object $e The exception
     * @return bool
     */
    public static function exception ( $e ){
        echo '
        <div class="alert alert-danger bosch-exception">
            Exception: <strong>'.$e->getMessage().'</strong><br />
            Found in '.$e->getFile().' on line '.$e->getLine().'<br />
            Code: <pre>'.$e->getTraceAsString().'</pre>
        </div>';

        return true;
    }

    /**
     * Output an error message
     * @param string $text The error text
     * @return bool
     */
    public static function error ( $text ){
        echo '
        <div class="alert alert-danger bosch-error">
           '.$text.'
        </div>';

        return true;
    }

    /**
     * Output a warning message if debug is on
     * @param string $text The warning text
     * @return bool
     */
    public static function warning ( $text ){

        if ( !self::settings('debug') ){
            return false;
        }

        echo '
        <div class="alert alert-warning bosch-error">
           '.$text.'
        </div>';

        return true;
    }

    /**
     * Utility functions
     * General functions used by Bosch children
     * @package utlity_functions
     */
    
    private function has_file_inputs(){
        
        foreach ($this->fields as $field) {
            if ( $field->type == 'file' )
                return true;
        }

        return false;
    }

    public function has_errors(){
        if ( empty($this->errors) ){
            return false;
        }

        return true;
    }

    /**
     * Convert any string to a variable-type slug
     * @param string $text The string to be converted
     * @return string
     */
    public static function slugify($text){ 
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
     */
    protected function close_tag( $tag ){ 
        if ( $tag == '' )
            return;

        $out = ''; 
        $temp = substr($tag, 1); 
        $out = substr_replace($tag,'/', 1); 
        $out .= $temp; 
        return $out; 
    }

    /**
     * Array of states for select fields
     * @return array
     */
    public static function states(){
        return array("AL" => "Alabama", "AK" => "Alaska", "AZ" => "Arizona", "AR" => "Arkansas", "CA" => "California", "CO" => "Colorado", "CT" => "Connecticut", "DE" => "Delaware", "DC" => "District of Columbia", "FL" => "Florida", "GA" => "Georgia", "HI" => "Hawaii", "ID" => "Idaho", "IL" => "Illinois", "IN" => "Indiana", "IA" => "Iowa", "KS" => "Kansas", "KY" => "Kentucky", "LA" => "Louisiana", "ME" => "Maine", "MD" => "Maryland", "MA" => "Massachusetts", "MI" => "Michigan", "MN" => "Minnesota", "MS" => "Mississippi", "MO" => "Missouri", "MT" => "Montana", "NE" => "Nebraska", "NV" => "Nevada", "NH" => "New Hampshire", "NJ" => "New Jersey", "NM" => "New Mexico", "NY" => "New York", "NC" => "North Carolina", "ND" => "North Dakota", "OH" => "Ohio", "OK" => "Oklahoma", "OR" => "Oregon", "PA" => "Pennsylvania", "RI" => "Rhode Island", "SC" => "South Carolina", "SD" => "South Dakota", "TN" => "Tennessee", "TX" => "Texas", "UT" => "Utah", "VT" => "Vermont", "VA" => "Virginia", "WA" => "Washington", "WV" => "West Virginia", "WI" => "Wisconsin", "WY" => "Wyoming");
    }

    /**
     * Array of months for select fields
     * @return array
     */
    public static function months(){
        return array('jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April', 'may' => 'May', 'june' => 'June', 'july' => 'July', 'aug' => 'August', 'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December');
    }

    /**
     * Array of days for select fields
     * @return array 
     */
    public static function days(){

        $values = array();

        for ( $i = 1; $i < 32; $i++ ){
            $values[$i] = $i;
        }

        return $values;
    }

    /**
     * Array of hours for select fields
     * @return array 
     */
    public static function hours(){

        $values = array();

        for ( $i = 0; $i < 24; $i++ ){
            $values[sprintf("%02d", $i)] = sprintf("%02d", $i);
        }

        return $values;
    }

    /**
     * Array of minutes for select fields
     * @return array 
     */
    public static function minutes(){
        
        $values = array();

        for ( $i = 0; $i < 60; $i++ ){
            $values[sprintf("%02d", $i)] = sprintf("%02d", $i);
        }

        return $values;
    }

    /**
     * Sanitize the input data
     * 
     * @access public
     * @param  array $data
     * @return array
     */
    private function sanitize(array $input, $fields = NULL, $utf8_encode = true){

        $magic_quotes = (bool)get_magic_quotes_gpc();
        
        if(is_null($fields)){            
            $fields = array_keys($input);
        }

        foreach($fields as $field){

            //skip empty fields and wysiwyg
            if ( !isset($input[$field]) ){
                continue;
            }

            else{

                $value = $input[$field]; 
                
                if(is_string($value))
                {
                    if($magic_quotes === TRUE)
                    {
                        $value = stripslashes($value);
                    }
                    
                    if(strpos($value, "\r") !== FALSE)
                    {
                        $value = trim($value);
                    }
                    
                    if(function_exists('iconv') && function_exists('mb_detect_encoding') && $utf8_encode)
                    {
                        $current_encoding = mb_detect_encoding($value);
                        
                        if($current_encoding != 'UTF-8' && $current_encoding != 'UTF-16') {
                            $value = iconv($current_encoding, 'UTF-8', $value);
                        }
                    }
                    
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                }
                
                $input[$field] = $value;
            }
        }
        
        return $input;      
    }


}

