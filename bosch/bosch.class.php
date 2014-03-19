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
    public $errors = array();

    /**
     * After submit, array holding validated data for this bosch indexed by step
     * $data[0]['var_name'];
     * @var array
     */
    public $data = array();

    /**
     * Various form settings
     * @var array
     */
    protected $bosch_settings = array();


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

                //CSS class(es) applied to prev and next buttons
                'nav-class' => 'btn btn-info',

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
     * Setup functions
     * Initialize the form, settings, and data
     * @package setup_functions
     */

    /**
     * Set/get for settings
     *
     * @param string $key The name of the setting to set or get
     * @param string $value Optional name of value to set
     * @return mixed
     */
    public function settings( $key, $value = false ){

        if ( $value ){
            $this->bosch_settings[$key] = $value;
            return true;
        }

        return $this->bosch_settings[$key];
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
    public function setup( $fields = array(), $groups = array(), $steps = array() ){

        //fields must be supplied, so do those first, converting the array into objects
        foreach ($fields as $k => $v) {

            $new_field = new Bosch_Field( $fields[$k] );

            //check if the var has already been used
            if ( array_key_exists($fields[$k]['var'], $this->fields) ){
                $this->bosch_error('Duplicate <code>var</code> name detected: <code>'.$fields[$k]['var'].'</code>');
            }
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

        //if no groups have been set yet, create a generic group for all fields
        if ( !isset($groups) || empty($groups) ){
            foreach ($new_fields as $field) {
                $field_vars[] = $field->var;
            }

            $groups = 
            array( 
                    'name' => 'generic_group',
                    'hide_name' => true,
                    'fields' => implode('|', $field_vars)
                );
        }

        foreach ($groups as $k => $v) {

            $new_group = new Bosch_Group( $groups[$k] );

            //check for duplicate group name
            if ( array_key_exists( $this->slugify($new_group->name), $this->groups) ){
                $this->bosch_error('Duplicate Group Name detected: <code>'.$groups[$k]['name'].'</code>');
            }
            else{

                $new_group->init( $this->fields );

                $this->groups[$this->slugify($new_group->name)] = $new_group;

                $valid_response = $new_group->validate_group();

                if ( $valid_response !== 'valid' ){
                    $this->bosch_error( $valid_response );
                }
            } 
        }

        //if no steps have been set, create a generic step for all groups
        if ( !isset($steps) || empty($steps) ){
            foreach ($groups as $group) {
                $group_names[] = $group['name'];
            }

            $this->steps[] = new Bosch_Step( array( 'groups' => implode('|', $group_vars) ) );
                
        }
        else{
            foreach ($steps as $k => $v) {
                $new_step = new Bosch_Step( $steps[$k] );
                $new_step->init( $this->groups );
                $this->steps[] = $new_step;
            }
        }

        return true; 
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

        if ( isset($_POST['prev']) && $_POST['prev'] == 'prev' && $_SESSION['step'] > 0 ){
            $_SESSION['step']--;
            return true;
        }            

        $validate = array();
        $filter = array();
        $errors = array();

        $fields = $this->steps[$_SESSION['step']]->get_fields();

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
       
        //required checkboxes must be checked manually, as they are not present in the $_POST array
        $missing_checkboxes = $this->validate_required_checkboxes($_POST['form']);
        if ( !empty($missing_checkboxes) ){
            $validated_data = false;
            $errors = $missing_checkboxes;
        }

        //validation failed, merge errors with checkbox errors
        if ( $validated_data === false ){
            $errors = array_merge($errors, $validator->get_readable_errors(false));
            $this->errors = $errors;
            return false;
        }
        
        //no errors, save the validated data and return true
        else{

            //put the recently submitted data in the $_SESSION['storage'] array in the current step number
            //eg: $_SESSION['storage'][0][var_name] = value;
            foreach ($validated_data as $k => $v) {
                $_SESSION['storage'][$_SESSION['step']][$k] = $v;
            }

            //update the form's stored data to all the submitted data so far
            //$this->data[0][var_name] = value;
            foreach ($_SESSION['storage'] as $step_num => $step_array) {
                foreach ($step_array as $k => $v) {
                    $this->data[$step_num][$k] = $step_array[$k];
                }       
            }

            if ( isset($_POST['next']) && $_POST['next'] == 'next' && ($_SESSION['step'] + 1) < count( $this->steps )  )
                $_SESSION['step']++;

            return true;
        }
    }

    /**
     * Reset the form to the first step and clear stored data
     * @return bool
     */
    public function reset(){
        $_SESSION['step'] = 0;
        unset($_SESSION['storage']);
        return true;
    }

    /**
     * Check if form has been submitted, either final submit or prev/next step
     * @return bool 
     */
    public function has_been_submitted(){

        if ( !isset($_POST['prev']) && !isset($_POST['next']) && !isset($_POST[$this->settings('submit-name')]) )
            return false;

        return true;
    }

    /**
     * Check if the final step has been submitted
     * @return bool 
     */
    public function final_submit(){

        if ( !isset($_POST[$this->settings('submit-name')]) )
            return false;

        if ( $_POST[$this->settings('submit-name')] !== $this->settings('submit-value') )
            return false;

        return true;

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
        $fields = $this->steps[$_SESSION['step']]->get_fields();

        foreach ($fields as $field) {
            if ( $field->type == 'checkbox' || $field->type == 'checkbox-inline' ){
                if ( !array_key_exists($field->var, $post_data) && strstr($field->validate, 'required') ){
                    $missing[$field->var] = 'The '.$field->name.' field is required';
                }
            }
        }

        return $missing;
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
     * Output functions
     * Used to output form elements, usually public
     * @package output_functions
     */
    
    /**
     * Output the entire bosch form
     *  @return bool
     */
    public function output(){

        //get form type from settings
        switch ( $this->settings('form-type') ){
            case 'block' : $class = ''; break;
            case 'inline' : $class = 'form-inline'; break;
            case 'horizontal' : $class = 'form-horizontal'; break;
            default: $class = '';
        }

        echo '
        <form role="form" class="bosch-form step-'.$_SESSION['step'].' '.$class.'" method="post">';
    
            $this->steps[$_SESSION['step']]->output_step();
            echo $this->get_buttons();

        echo '</form>';

        return true;

    }

    /**
     * Outputs any errors after $this->process has been called
     * @return bool
     */
    public function output_errors(){

        if ( !$this->has_been_submitted() )
            return false;

        if ( isset($this->errors) && !empty($this->errors) ){
            echo '<div class="alert alert-danger">';
                foreach ($this->errors as $error) {
                    echo $error . '<br />';
                }
            echo '</div>';

            return true;
        }
    }
    
    /**
     * Get HTML for the buttons
     * @return string
     */
    public function get_buttons(){

        $btns = '
        <div class="row">';
            $btns .= '
            <div class="col-md-6">';
                if ( $this->steps[$_SESSION['step']]->prev === true ){
                    $btns .= $this->previous_button();
                }
                $btns .= '
            </div>
            <div class="col-md-6">';
                if ( ($_SESSION['step'] + 1) === count( $this->steps ) ){
                    if ( $this->settings('honeypot') ){
                        $btns .= $this->honeypot();
                    }

                    $btns .= $this->submit_button();
                }
                else{
                    if ( $this->steps[$_SESSION['step']]->next === true ){
                        $btns .= $this->next_button();
                    }
                }
                
                $btns .= '
            </div>';
        $btns .= '
        </div>';

        return $btns;
    }

    /**
     * Output the next button
     * @return string
     */
    public function next_button(){
        //check if this is the last step
        if ( ($_SESSION['step'] + 1) === count( $this->steps ) ){
            return '';
        }
        return '<input type="submit" value="next" name="next" class="'.$this->settings('nav-class').'">';
    }

    /**
     * Output the previous button
     * @return string
     */
    public function previous_button(){
        //check if this is the first step
        if ( $_SESSION['step'] == 0 ){
            return '';
        }
        return '<input type="submit" value="prev" name="prev" class="'.$this->settings('nav-class').'">';
    }

    /**
     * Output the (final) submit button
     * @return string
     */
    public function submit_button(){
        return '<input type="submit" value="'.$this->settings('submit-value').'" name="'.$this->settings('submit-name').'" class="'.$this->settings('submit-class').'">';
    }

    /**
     * Output the honeypot field
     * @return string
     */
    public function honeypot(){
        return '<div class="sr-only"><label for="form[hp]">Bot test: If you see this field, leave it blank</label><input name="form[hp]" type="text" value=""></div>';
    }

    /**
     * Output an exception message
     * @param object $e The exception
     * @return bool
     */
    private function bosch_exception ( $e ){
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
    private function bosch_error ( $text ){
        echo '
        <div class="alert alert-danger bosch-error">
           '.$text.'
        </div>';

        return true;
    }

    /**
     * Utility functions
     * General functions used by Bosch children
     * @package utlity_functions
     */

    /**
     * Convert any string to a variable-type slug
     * @param string $text The string to be converted
     * @return string
     */
    protected function slugify($text){ 
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
    protected function states(){
        return array("AL" => "Alabama", "AK" => "Alaska", "AZ" => "Arizona", "AR" => "Arkansas", "CA" => "California", "CO" => "Colorado", "CT" => "Connecticut", "DE" => "Delaware", "FL" => "Florida", "GA" => "Georgia", "HI" => "Hawaii", "ID" => "Idaho", "IL" => "Illinois", "IN" => "Indiana", "IA" => "Iowa", "KS" => "Kansas", "KY" => "Kentucky", "LA" => "Louisiana", "ME" => "Maine", "MD" => "Maryland", "MA" => "Massachusetts", "MI" => "Michigan", "MN" => "Minnesota", "MS" => "Mississippi", "MO" => "Missouri", "MT" => "Montana", "NE" => "Nebraska", "NV" => "Nevada", "NH" => "New Hampshire", "NJ" => "New Jersey", "NM" => "New Mexico", "NY" => "New York", "NC" => "North Carolina", "ND" => "North Dakota", "OH" => "Ohio", "OK" => "Oklahoma", "OR" => "Oregon", "PA" => "Pennsylvania", "RI" => "Rhode Island", "SC" => "South Carolina", "SD" => "South Dakota", "TN" => "Tennessee", "TX" => "Texas", "UT" => "Utah", "VT" => "Vermont", "VA" => "Virginia", "WA" => "Washington", "WV" => "West Virginia", "WI" => "Wisconsin", "WY" => "Wyoming");
    }

    /**
     * Array of months for select fields
     * @return array
     */
    protected function months(){
        return array('jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April', 'may' => 'May', 'june' => 'June', 'july' => 'July', 'aug' => 'August', 'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December');
    }

    /**
     * Array of days for select fields
     * @return array 
     */
    protected function days(){
        return array(1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29', 30 => '30', 31 => '31');
    }

}

