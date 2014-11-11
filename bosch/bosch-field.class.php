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
    public $validate = NULL;

    /**
     * String of filters (see below), seperated by '|'
     * @var string
     */
    public $filter = NULL;

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
     * Choices are disabled|multiple|nosave|readonly
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
     * Show or hide the generic '-- Choose --' value for select fields. Can be overwitten by the placeholder value
     * @var string
     */
    public $select_null = true;

    /**
     * After processing, store error message for field here (if applicable)
     * @var string
     */
    public $errors = array();

    /**
     * Constructor
     * @param array $properties 
     */
    function __construct( $properties = array() ){

        foreach ($properties as $k => $v) {
            if ( !empty($v) )
                $this->$k = $v;
            if ( !in_array($k, Bosch::$valid_field_keys) ){
                Bosch::error('Invalid field variable detected: <code>'.$k.'</code>');
            }
        }
    }

    /**
     * Validate a field as it's being setup
     *
     * @return string 'valid' if valid, error string if not
     */
    public function validate_field_setup(){

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
        isset($this->hide_label) && $this->hide_label === true || $this->hide_label === 'true' ? $label_class = 'sr-only' : $label_class = '';

        //hide the label if the global setting is true
        Bosch::settings('hide-labels') === true ? $label_class .= 'sr-only' : $label_class .= '';

        //if the field size is set, add it to the input class string
        isset($this->size) ? $input_class .= ' input-'.$this->size : $input_class .= '';

        //set the description HTML
        isset($this->desc) ? $desc = '<p class="help-block">'.$this->desc.'</p>' : $desc = '';

        //add any applicable extras
        isset($this->extras) ? $extras = str_replace('|', ' ', $this->extras) : $extras = '';

        $field_value = $this->get_updated_value();        

        //set column pre and post HTML if form is set to horizontal
        $input_col_pre = ''; $input_col_post = '';
        if ( Bosch::settings('form-type') === 'horizontal' ){
            isset($this->input_width) ? $col = $this->input_width : $col = Bosch::settings('input-width');
            $input_col_pre = '<div class="'.$col.'">';
            $input_col_post = '</div>';
            isset($this->label_width) ? $col = $this->label_width : $col = Bosch::settings('label-width');
            $label_class .= ' '.$col;
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

            case 'time' :
                echo '
                <input id="'.$this->var.'" '.$extras.' type="text" class="timepicker '.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$this->var.']" />';
                break;

            case 'date' :
                echo '
                <input id="'.$this->var.'" '.$extras.' type="text" class="datepicker '.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$this->var.']" />';
                break;

            case 'month' : 
                echo '
                <input id="'.$this->var.'" '.$extras.' data-date-format="mm." data-date-viewmode="months" data-date-minviewmode="months" type="text" class="datepicker '.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$this->var.']" />';
                break;

            case 'year' : 
                echo '
                <input id="'.$this->var.'" '.$extras.' data-date-format="yyyy." data-date-viewmode="years" data-date-minviewmode="years" type="text" class="datepicker '.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$this->var.']" />';
                break;

            case 'money' : echo '
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input id="'.$this->var.'" '.$extras.' type="number" class="'.$input_class.'" placeholder="'.$placeholder.'" value="'.$field_value.'" name="form['.$this->var.']" />
                    <span class="input-group-addon">.00</span>
                </div>';
            break;

            case 'wysiwyg' : echo '
                <textarea id="'.$this->var.'" '.$extras.' class="bosch-wysiwyg '.$input_class.'" rows="'.$this->options.'" name="form['.$this->var.']">'.$field_value.'</textarea>';
            break;

            case 'textarea' : echo '
                <textarea id="'.$this->var.'" '.$extras.' class="'.$input_class.'" rows="'.$this->options.'" name="form['.$this->var.']">'.$field_value.'</textarea>';
            break;

            case 'state' : 
            case 'month' : 
            case 'day' : 
            case 'hour' :
            case 'minute' :
            case 'select' : 

                if ( $this->type == 'state' ) $this->options = Bosch::states();
                if ( $this->type == 'month' ) $this->options = Bosch::months();
                if ( $this->type == 'day' ) $this->options = Bosch::days();
                if ( $this->type == 'hour' ) $this->options = Bosch::hours();
                if ( $this->type == 'minute' ) $this->options = Bosch::minutes();

            echo '
                <select id="'.$this->var.'" '.$extras.' class="'.$input_class.'" name="form['.$this->var.']">';
                    if ( $placeholder ){
                        echo '<option value="">'.$placeholder.'</option>';
                    }                        
                    else{
                        if ( $this->select_null )
                            echo '<option value="">-- Choose --</option>';
                    }
                        
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
                            <input id="'.$this->var.'-'.Bosch::slugify($name).'" '.$extras.' type="radio" name="form['.$this->var.']" value="'.$id.'" '.$checked.'>
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
                            <input id="'.$this->var.'-'.Bosch::slugify($name).'" '.$extras.' type="radio" name="form['.$this->var.']" value="'.$id.'" '.$checked.'>
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
                            <input id="'.$this->var.'-'.Bosch::slugify($name).'" '.$extras.' type="checkbox" name="form['.$this->var.'][]" value="'.$id.'" '.$checked.'>
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
                Bosch::error('Invalid type property <code>'.$this->type.'</code> in field <code>'.$this->var.'</code>');
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

        if ( isset($this->errors) && !empty($this->errors) ){
            return true;
        }

        return false;
    }

    private function get_updated_value(){

        //if this field has already been filled in on a previous step, grab the value
        if ( isset($_SESSION[Bosch::settings('form-name').'-storage'][$_SESSION[Bosch::settings('form-name').'-step']][$this->var]) ){
            $this->value = $_SESSION[Bosch::settings('form-name').'-storage'][$_SESSION[Bosch::settings('form-name').'-step']][$this->var];
        }

        //if no-save is present, do not repopulate field data on failed submit, but revert to original default value
        isset($this->value) && !strstr($this->extras, 'no-save') ? $field_value = $this->value : $field_value = $this->default;

        return $field_value;
    }

    /**
     * Given an input value (from $_POST), runs any defined validation and filtering and updates the value
     * @param  mixed $post_data input value
     * @return true if passes validation, false otherwise
     */
    public function process( $post_data ){
       
        
        $filters = is_null($this->filter) ? false : explode('|', $this->filter);
        $validate = is_null($this->validate) ? false : explode('|', $this->validate);

        if ( !is_null($post_data) && $filters ){
            foreach($filters as $filter){   
                $params = NULL;
                
                if(strstr($filter, ',') !== FALSE){
                    $filter = explode(',', $filter);
                    
                    $params = array_slice($filter, 1, count($filter) - 1);
                    
                    $filter = $filter[0];
                }
                
                if(is_callable(array($this, 'filter_'.$filter))){
                    $method = 'filter_'.$filter;
                    if ( is_array($post_data) ){
                        foreach ($post_data as $k => $v) {
                            $post_data[$k] = $this->$method($post_data[$k], $params);
                        }
                    }
                    else{
                        $post_data = $this->$method($post_data, $params);
                    }
                }
                else if(function_exists($filter)){
                    if ( is_array($post_data) ){
                        foreach ($post_data as $k => $v) {
                            $post_data[$k] = $filter($post_data[$k]);
                        }
                    }
                    else{
                        $post_data = $filter($post_data);                        
                    }                    
                }
                else{
                    Bosch::error("Filter method '$filter' does not exist.");
                }
            }
        }



        $this->default = $post_data;

        if ( $validate ){
            foreach($validate as $rule){
                $method = NULL;
                $param  = NULL;
                
                if(strstr($rule, ',') !== FALSE){
                    $rule   = explode(',', $rule);
                    $method = 'validate_'.$rule[0];
                    $param  = $rule[1];
                }
                else{
                    $method = 'validate_'.$rule;
                }

                try{
                     if( !is_callable(array($this, $method))){
                        Bosch::error("Validator method '$method' does not exist for field $this->var.");
                        return false;
                    }

                    if ( is_array($post_data) ){
                        foreach ($post_data as $k => $v) {
                            $result = $this->$method($post_data[$k], $param);                            
                        }                        
                    }
                    else{
                        $result = $this->$method($post_data, $param);                       
                    }

                     if(is_array($result)){
                        $this->errors = $result;
                        $this->get_readable_error();
                    }

                }
                catch (Exception $e) {
                    Bosch::exception( $e );
                    return false;        
                }
            }
        }        

        if ( empty($this->errors) )
            return true;

        return false;
    }    

    /**
     * Get the 'message' part of a field's error array
     * 
     */
    public function get_readable_error(){

        $param = $this->errors['param'];
        $pretty = ucfirst(str_replace('_', ' ',$this->var));
        $field = '<a class="alert-link" href="#'.$this->var.'" title="'.$pretty.'">'.$pretty.'</a>';

        switch($this->errors['rule']) {

            case 'validate_required':
                $this->errors['message'] = "The $field field is required";
                break;
            case 'validate_valid_email':
                $this->errors['message'] = "The $field field is required to be a valid email address";
                break;
            case 'validate_max_len':
                if($param == 1) {
                    $this->errors['message'] = "The $field field needs to be shorter than $param character";
                } else {
                    $this->errors['message'] = "The $field field needs to be shorter than $param characters";
                }
                break;
            case 'validate_min_len':
                if($param == 1) {
                    $this->errors['message'] = "The $field field needs to be longer than $param character";
                } else {
                    $this->errors['message'] = "The $field field needs to be longer than $param characters";
                }
                break;
            case 'validate_exact_len':
                if($param == 1) {
                    $this->errors['message'] = "The $field field needs to be exactly $param character in length";
                } else {
                    $this->errors['message'] = "The $field field needs to be exactly $param characters in length";
                }
                break;
            case 'validate_alpha':
                $this->errors['message'] = "The $field field may only contain alpha characters(a-z)";
                break;
            case 'validate_alpha_numeric':
                $this->errors['message'] = "The $field field may only contain alpha-numeric characters";
                break;
            case 'validate_alpha_dash':
                $this->errors['message'] = "The $field field may only contain alpha characters &amp; dashes";
                break;
            case 'validate_numeric':
                $this->errors['message'] = "The $field field may only contain numeric characters";
                break;
            case 'validate_integer':
                $this->errors['message'] = "The $field field may only contain a numeric value";
                break;
            case 'validate_boolean':
                $this->errors['message'] = "The $field field may only contain a true or false value";
                break;
            case 'validate_float':
                $this->errors['message'] = "The $field field may only contain a float value";
                break;
            case 'validate_valid_url':
                $this->errors['message'] = "The $field field is required to be a valid URL";
                break;
            case 'validate_url_exists':
                $this->errors['message'] = "The $field URL does not exist";
                break;
            case 'validate_valid_ip':
                $this->errors['message'] = "The $field field needs to contain a valid IP address";
                break;
            case 'validate_valid_cc':
                $this->errors['message'] = "The $field field needs to contain a valid credit card number";
                break;
            case 'validate_valid_name':
                $this->errors['message'] = "The $field field needs to contain a valid human name";
                break;
            case 'validate_contains':
                $this->errors['message'] = "The $field field needs contain one of these values: ".implode(', ', $param);
                break;
            case 'validate_street_address':
                $this->errors['message'] = "The $field field needs to be a valid street address";
                break;
        }
    }


// ** ------------------------- Filters --------------------------------------- ** //
    
    /**
     * Replace noise words in a string (http://tax.cchgroup.com/help/Avoiding_noise_words_in_your_search.htm)
     * 
     * Usage: '<index>' => 'noise_words'
     *  
     * @access protected
     * @param  string $value
     * @param  array $params
     * @return string
     */
    protected function filter_noise_words($value, $params = NULL)
    {
        $value = preg_replace('/\s\s+/u', chr(32),$value);
        
        $value = " $value ";
                
        $words = explode(',', self::$en_noise_words);
        
        foreach($words as $word)
        {
            $word = trim($word);
            
            $word = " $word "; // Normalize
            
            if(stripos($value, $word) !== FALSE)
            {
                $value = str_ireplace($word, chr(32), $value);
            }
        }

        return trim($value);
    }
    
    /**
     * Remove all known punctuation from a string
     * 
     * Usage: '<index>' => 'rmpunctuataion'
     *  
     * @access protected
     * @param  string $value
     * @param  array $params
     * @return string
     */
    protected function filter_rmpunctuation($value, $params = NULL)
    {
        return preg_replace("/(?![.=$'€%-])\p{P}/u", '', $value);
    }
    
    /**
     * Translate an input string to a desired language [DEPRECIATED]
     *
     * Any ISO 639-1 2 character language code may be used 
     *
     * See: http://www.science.co.il/language/Codes.asp?s=code2
     *
     * @access protected
     * @param  string $value
     * @param  array $params
     * @return string
     */
    /*
    protected function filter_translate($value, $params = NULL)
    {
        $input_lang  = 'en';
        $output_lang = 'en';
        
        if(is_null($params))
        {
            return $value;
        }
        
        switch(count($params))
        {
            case 1:
                $input_lang  = $params[0];
                break;
            case 2:
                $input_lang  = $params[0];
                $output_lang = $params[1];
                break;
        }
        
        $text = urlencode($value);

        $translation = file_get_contents(
            "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q={$text}&langpair={$input_lang}|{$output_lang}" 
        );
        
        $json = json_decode($translation, true);
        
        if($json['responseStatus'] != 200)
        {
            return $value;
        }
        else
        {
            return $json['responseData']['translatedText'];
        }       
    }
    */
        
    /**
     * Sanitize the string by removing any script tags
     * 
     * Usage: '<index>' => 'sanitize_string'
     *  
     * @access protected
     * @param  string $value
     * @param  array $params
     * @return string
     */
    protected function filter_sanitize_string($value, $params = NULL)
    {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }
    
    /**
     * Sanitize the string by urlencoding characters
     * 
     * Usage: '<index>' => 'urlencode'
     *  
     * @access protected
     * @param  string $value
     * @param  array $params    
     * @return string
     */
    protected function filter_urlencode($value, $params = NULL)
    {
        return filter_var($value, FILTER_SANITIZE_ENCODED);  
    }
    
    /**
     * Sanitize the string by converting HTML characters to their HTML entities
     * 
     * Usage: '<index>' => 'htmlencode'
     *  
     * @access protected
     * @param  string $value
     * @param  array $params
     * @return string
     */
    protected function filter_htmlencode($value, $params = NULL)
    {
        return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);  
    }
    
    /**
     * Sanitize the string by removing illegal characters from emails
     * 
     * Usage: '<index>' => 'sanitize_email'
     *  
     * @access protected
     * @param  string $value
     * @param  array $params
     * @return string
     */
    protected function filter_sanitize_email($value, $params = NULL)
    {
        return filter_var($value, FILTER_SANITIZE_EMAIL);  
    }
    
    /**
     * Sanitize the string by removing illegal characters from numbers
     * 
     * @access protected
     * @param  string $value
     * @param  array $params
     * @return string
     */
    protected function filter_sanitize_numbers($value, $params = NULL)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);  
    }
    
    /**
     * Filter out all HTML tags except the defined basic tags
     * 
     * @access protected
     * @param  string $value
     * @param  array $params
     * @return string
     */ 
    protected function filter_basic_tags($value, $params = NULL)
    {
        return strip_tags($value, self::$basic_tags);
    }
    
    // ** ------------------------- Validators ------------------------------------ ** //   
    
    /**
     * Verify that a value is contained within the pre-defined value set
     * 
     * Usage: '<index>' => 'contains,value value value'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_contains($input, $param = NULL)
    {
        $param = trim(strtolower($param));
        
        $value = trim(strtolower($input));
        
        if (preg_match_all('#\'(.+?)\'#', $param, $matches, PREG_PATTERN_ORDER)) {
            $param = $matches[1];
        } else  {
            $param = explode(chr(32), $param);
        }

        if(in_array($value, $param)) { // valid, return nothing
            return;
        } else {
            return array(
                'field' => $this->var,
                'value' => $value,
                'rule'  => __FUNCTION__,
                'param' => $param
            );          
        }
    }   
    
    /**
     * Check if the specified key is present and not empty
     * 
     * Usage: '<index>' => 'required'
     *
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_required($input, $param = NULL)
    {
        if(isset($input) && trim($input) != '')
        {
            return;
        }
        else
        {
            return array(
                'field' => $this->var,
                'value' => NULL,
                'rule'  => __FUNCTION__,
                'param' => $param
            );
        }
    }
    
    /**
     * Determine if the provided email is valid
     * 
     * Usage: '<index>' => 'valid_email'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_valid_email($input, $param = NULL)
    {
        if(!isset($input) || empty($input))
        {
            return;
        }
    
        if(!filter_var($input, FILTER_VALIDATE_EMAIL))
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the provided value length is less or equal to a specific value
     * 
     * Usage: '<index>' => 'max_len,240'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_max_len($input, $param = NULL)
    {
        if(!isset($input))
        {
            return;
        }
        
        if(function_exists('mb_strlen'))
        {
            if(mb_strlen($input) <= (int)$param)
            {
                return;
            }
        }
        else
        {
            if(strlen($input) <= (int)$param)
            {
                return;
            }
        }

        return array(
            'field' => $this->var,
            'value' => $input,
            'rule'  => __FUNCTION__,
            'param' => $param
        );      
    }
    
    /**
     * Determine if the provided value length is more or equal to a specific value
     * 
     * Usage: '<index>' => 'min_len,4'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_min_len($input, $param = NULL)
    {
        if(!isset($input))
        {
            return;
        }
        
        if(function_exists('mb_strlen'))
        {
            if(mb_strlen($input) >= (int)$param)
            {
                return;
            }
        }
        else
        {
            if(strlen($input) >= (int)$param)
            {
                return;
            }
        }

        return array(
            'field' => $this->var,
            'value' => $input,
            'rule'  => __FUNCTION__,
            'param' => $param           
        );
    }
    
    /**
     * Determine if the provided value length matches a specific value
     * 
     * Usage: '<index>' => 'exact_len,5'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_exact_len($input, $param = NULL)
    {
        if(!isset($input))
        {
            return;
        }

        if(function_exists('mb_strlen'))
        {
            if(mb_strlen($input) == (int)$param)
            {
                return;
            }
        }
        else
        {
            if(strlen($input) == (int)$param)
            {
                return;
            }
        }

        return array(
            'field' => $this->var,
            'value' => $input,
            'rule'  => __FUNCTION__,
            'param' => $param           
        );
    }
    
    /**
     * Determine if the provided value contains only alpha characters
     * 
     * Usage: '<index>' => 'alpha'
     *
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_alpha($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!preg_match("/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i", $input) !== FALSE)
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the provided value contains only alpha-numeric characters
     * 
     * Usage: '<index>' => 'alpha_numeric'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */ 
    protected function validate_alpha_numeric($input, $param = NULL)
    {   
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i", $input) !== FALSE)
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the provided value contains only alpha characters with dashed and underscores
     * 
     * Usage: '<index>' => 'alpha_dash'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_alpha_dash($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_-])+$/i", $input) !== FALSE)
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the provided value is a valid number or numeric string
     * 
     * Usage: '<index>' => 'numeric'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_numeric($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!is_numeric($input))
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the provided value is a valid integer
     * 
     * Usage: '<index>' => 'integer'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_integer($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!filter_var($input, FILTER_VALIDATE_INT))
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the provided value is a PHP accepted boolean
     * 
     * Usage: '<index>' => 'boolean'
     *
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_boolean($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        $bool = filter_var($input, FILTER_VALIDATE_BOOLEAN);
        
        if(!is_bool($bool))
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the provided value is a valid float
     * 
     * Usage: '<index>' => 'float'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_float($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!filter_var($input, FILTER_VALIDATE_FLOAT))
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the provided value is a valid URL
     * 
     * Usage: '<index>' => 'valid_url'
     *
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_valid_url($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!filter_var($input, FILTER_VALIDATE_URL))
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if a URL exists & is accessible
     *
     * Usage: '<index>' => 'url_exists'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_url_exists($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        $url = str_replace(
            array('http://', 'https://', 'ftp://'), '', strtolower($input)
        );

        if(function_exists('checkdnsrr'))
        {
            if(!checkdnsrr($url))
            {
                return array(
                    'field' => $this->var,
                    'value' => $input,
                    'rule'  => __FUNCTION__,
                    'param' => $param                   
                );
            }   
        }
        else
        {
            if(gethostbyname($url) == $url)
            {
                return array(
                    'field' => $this->var,
                    'value' => $input,
                    'rule'  => __FUNCTION__,
                    'param' => $param                   
                );
            }
        }
    }
    
    /**
     * Determine if the provided value is a valid IP address
     * 
     * Usage: '<index>' => 'valid_ip'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_valid_ip($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!filter_var($input, FILTER_VALIDATE_IP) !== FALSE)
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the provided value is a valid IPv4 address
     * 
     * Usage: '<index>' => 'valid_ipv4'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_valid_ipv4($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== FALSE)
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }   
    
    /**
     * Determine if the provided value is a valid IPv6 address
     * 
     * Usage: '<index>' => 'valid_ipv6'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_valid_ipv6($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        if(!filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== FALSE)
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }   
    
    /**
     * Determine if the input is a valid credit card number 
     *
     * See: http://stackoverflow.com/questions/174730/what-is-the-best-way-to-validate-a-credit-card-in-php
     * Usage: '<index>' => 'valid_cc'   
     * 
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_valid_cc($input, $param = NULL)
    {
        if(!isset($input) || empty($input) )
        {
            return;
        }
        
        $number = preg_replace('/\D/', '', $input);     
        
        if(function_exists('mb_strlen'))
        {
            $number_length = mb_strlen($input);
        }
        else
        {
            $number_length = strlen($input);
        }       
    
        $parity = $number_length % 2;
    
        $total = 0;
    
        for($i = 0; $i < $number_length; $i++) 
        {
            $digit = $number[$i];

            if ($i % 2 == $parity) 
            {
                $digit *= 2;
    
                if ($digit > 9) 
                {
                    $digit -= 9;
                }
            }

            $total += $digit;
        }   
    
        if($total % 10 == 0)
        {
            return; // Valid
        }
        else
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param               
            );
        }
    }
    
    /**
     * Determine if the input is a valid human name [Credits to http://github.com/ben-s]
     *
     * See: https://github.com/Wixel/GUMP/issues/5
     * Usage: '<index>' => 'valid_name'
     * 
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_valid_name($input, $param = NULL)
    {
        if(!isset($input)|| empty($input) )
        {
            return;
        }
        
        if(!preg_match("/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïñðòóôõöùúûüýÿ '-])+$/i", $input) !== FALSE)
        {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param   
            );
        }
    }
    
    /**
     * Determine if the provided input is likely to be a street address using weak detection
     * 
     * Usage: '<index>' => 'street_address'
     *  
     * @access protected
     * @param  string $field
     * @param  array $input
     * @return mixed
     */
    protected function validate_street_address($input, $param = NULL)
    {   
        if(!isset($input)|| empty($input) )
        {
            return;
        }
        
        // Theory: 1 number, 1 or more spaces, 1 or more words
        $hasLetter = preg_match('/[a-zA-Z]/', $input);
        $hasDigit  = preg_match('/\d/'      , $input);
        $hasSpace  = preg_match('/\s/'      , $input);
        
        $passes = $hasLetter && $hasDigit && $hasSpace;
                
        if(!$passes) {
            return array(
                'field' => $this->var,
                'value' => $input,
                'rule'  => __FUNCTION__,
                'param' => $param
            );
        }
    }   
}
