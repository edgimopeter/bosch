<?php

class Bosch {

    private $states = array("AL" => "Alabama", "AK" => "Alaska", "AZ" => "Arizona", "AR" => "Arkansas", "CA" => "California", "CO" => "Colorado", "CT" => "Connecticut", "DE" => "Delaware", "FL" => "Florida", "GA" => "Georgia", "HI" => "Hawaii", "ID" => "Idaho", "IL" => "Illinois", "IN" => "Indiana", "IA" => "Iowa", "KS" => "Kansas", "KY" => "Kentucky", "LA" => "Louisiana", "ME" => "Maine", "MD" => "Maryland", "MA" => "Massachusetts", "MI" => "Michigan", "MN" => "Minnesota", "MS" => "Mississippi", "MO" => "Missouri", "MT" => "Montana", "NE" => "Nebraska", "NV" => "Nevada", "NH" => "New Hampshire", "NJ" => "New Jersey", "NM" => "New Mexico", "NY" => "New York", "NC" => "North Carolina", "ND" => "North Dakota", "OH" => "Ohio", "OK" => "Oklahoma", "OR" => "Oregon", "PA" => "Pennsylvania", "RI" => "Rhode Island", "SC" => "South Carolina", "SD" => "South Dakota", "TN" => "Tennessee", "TX" => "Texas", "UT" => "Utah", "VT" => "Vermont", "VA" => "Virginia", "WA" => "Washington", "WV" => "West Virginia", "WI" => "Wisconsin", "WY" => "Wyoming");

    private $months = array('jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April', 'may' => 'May', 'june' => 'June', 'july' => 'July', 'aug' => 'August', 'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December');

    private $days = array(1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29', 30 => '30', 31 => '31');
    
    private $fields = array();
    private $groups = array();
    public $errors = array();
    public $data = array();

    public $settings = array(
        'group-headings' => '<h2>',
        'form-type'      => 'block',
        'input-width'    => 'col-md-10',
        'label-width'    => 'col-md-2',
        'submit-class'   => 'btn btn-primary',
        'submit-value'   => 'Submit',
        'submit-name'   => 'submit',
        'hide-labels'    => false,
        'honeypot'      => true
    );

    private $requires_options = array('checkbox', 'checkbox-inline', 'select', 'radio', 'radio-inline');

    function bosch_exception ( $e ){
        echo '
        <div class="alert alert-danger bosch-exception">
            Exception: <strong>'.$e->getMessage().'</strong><br />
            Found in '.$e->getFile().' on line '.$e->getLine().'<br />
            Code: <pre>'.$e->getTraceAsString().'</pre>
        </div>';
    }

    function bosch_error ( $text ){
        echo '
        <div class="alert alert-danger bosch-error">
           '.$text.'
        </div>';
    }

    function set_fields( $fields = array() ){

        foreach ($fields as $k => $v) {

            $new_field = new Field( $fields[$k] );
            if ( array_key_exists($fields[$k]['var'], $this->fields) ){
                $this->bosch_error('Duplicate <code>var</code> name detected: <code>'.$fields[$k]['var'].'</code>');
            }
            else{
                $this->fields[$fields[$k]['var']] = $new_field;

                $valid_response = $this->validate_field($this->fields[$fields[$k]['var']]);

                if ( $valid_response !== 'valid' ){
                    $this->bosch_error( $valid_response );
                }
            }
        }        
    }

    function set_groups( $groups = array() ){

        foreach ($groups as $k => $v) {
            $new_group = new Group( $groups[$k] );

            //make sure name is set
            if ( !isset($groups[$k]['name']) ){
                $groups[$k]['name'] = '';
            }

            if ( array_key_exists( slugify($groups[$k]['name']), $this->groups) ){
                $this->bosch_error('Duplicate Group Name detected: <code>'.$groups[$k]['name'].'</code>');
            }
            else{
                $this->groups[slugify($groups[$k]['name'])] = $new_group;

                $valid_response = $this->validate_group($this->groups[slugify($groups[$k]['name'])]);

                if ( $valid_response !== 'valid' ){
                    $this->bosch_error( $valid_response );
                }
            } 
        }
    }

    function output(){

        if ( !isset($this->groups) || empty($this->groups) ){
            foreach ($this->fields as $field) {
                $field_vars[] = $field->var;
            }

            $this->set_groups( array(array( 'fields' => implode('|', $field_vars) )) );
        }

        switch ( $this->settings['form-type'] ){
            case 'block' : $class = ''; break;
            case 'inline' : $class = 'form-inline'; break;
            case 'horizontal' : $class = 'form-horizontal'; break;
            default: $class = '';
        }

        echo '
        <form role="form" class="bosch-form '.$class.'" method="post">';

            foreach ($this->groups as $group) {
                $this->output_group( $group );               
            }

        if ( $this->settings['honeypot'] ){
            $this->output_honeypot();
        }

        $this->submit_button();

        echo '</form>';

    }

    function output_group( $group ){

        //when called directly, convert the string group name into the corresponding group object
        if ( !is_object($group) ){
            $group = $this->groups[slugify($group)];
        }

        echo 
        $group->html_before . '                    
        <div class="bosch-group group-'.slugify( $group->name ).'">
            <div class="bosch-heading">
                '.$this->settings['group-headings'] . $group->name . close_tag($this->settings['group-headings']) .'
            </div>
            <div class="bosch-group-desc">
                '.$group->desc.'
            </div>';

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

    function output_field( $field ){

        //when called directly, convert the string field name into the corresponding field object
        if ( !is_object($field) ){
            $field = $this->fields[slugify($field)];
        }

        $input_class = 'form-control';
        $input_col_pre = ''; $input_col_post = '';

        in_array('required', explode('|', $field->validate) )? $required = 'required' : $required = '';
        isset($this->errors) && array_key_exists($field->var, $this->errors) ? $error = 'has-error' : $error = '';
        isset($field->placeholder) ? $placeholder = $field->placeholder : $placeholder = '';
        isset($field->hide_label) && $field->hide_label === true ? $label_class = 'sr-only' : $label_class = '';

        isset($this->settings['hide-labels']) && $this->settings['hide-labels'] === true ? $label_class = 'sr-only' : $label_class = '';
        isset($field->size) ? $input_class .= ' input-'.$field->size : $input_class .= '';
        isset($field->desc) ? $desc = '<p class="help-block">'.$field->desc.'</p>' : $desc = '';
        isset($field->extras) ? $extras = str_replace('|', ' ', $field->extras) : $extras = '';
        isset($field->value) && !strstr($extras, 'no-save') ? $field_value = $field->value : $field_value = $field->default;

        if ( $this->settings['form-type'] == 'horizontal' ){
            isset($field->input_width) ? $col = $field->input_width : $col = $this->settings['input-width'];
            $input_col_pre = '<div class="'.$col.'">';
            $input_col_post = '</div>';
            isset($field->label_width) ? $col = $field->label_width : $col = $this->settings['label-width'];
            $label_class .= ' '.$col;
        }

        $valid_response = $this->validate_field($field);

        if ( $valid_response !== 'valid' ){
            $this->bosch_error( $valid_response );
            return;
        }

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
                            <input id="'.$field->var.'-'.slugify($name).'" '.$extras.' type="radio" name="form['.$field->var.']" value="'.$id.'" '.$checked.'>
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
                            <input id="'.$field->var.'-'.slugify($name).'" '.$extras.' type="radio" name="form['.$field->var.']" value="'.$id.'" '.$checked.'>
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

                //checkbox always uses arrays to allow for multiple checkbox values
                //if it's a single checkbox, convert it to an array for checking
                if ( !is_array( $field_value ) ){
                    $field_value = array( $field_value );
                } 

                echo $checkbox_pre;

                foreach( $field->options as $id => $name ){
                    in_array($id, $field_value) ? $checked = 'checked' : $checked = '';
                    echo '
                    <div class="'.$checkbox_class.'">
                        <label>
                            <input id="'.$field->var.'-'.slugify($name).'" '.$extras.' type="checkbox" name="form['.$field->var.'][]" value="'.$id.'" '.$checked.'>
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

    }

    function validate_field( $field ){

        //validate the var property
        if ( !isset($field->var) || $field->var == '' || empty($field->var) || preg_match('/\s/', $field->var) ){
           return 'Valid <code>var</code> property required for field with <code>name => '.$field->name.'</code>';
        }

        //Check if a valid options array is supplied when it's required
        if (
            in_array($field->type, $this->requires_options) 
            && (
                !isset($field->options) ||
                empty($field->options) ||
                !is_array($field->options)
                ) 
            ){

           return 'Valid array required for <code>options</code> property in field <code>'.$field->var.'</code><br />Currently set to <code>'.$field->options.'</code>';
        }

        return 'valid';

    }

    function validate_group( $group ){

        $fields = explode('|', $group->fields);

        if ( empty($fields) || $fields == false || $fields[0] == '' || empty($fields[0]) ){
            return 'No <code>fields</code> property set for group <code>'.$group->name.'</code>';
        }

        return 'valid';

    }

    function has_been_submitted(){

        if ( !isset($_POST[$this->settings['submit-name']]) )
            return false;

        if ( $_POST[$this->settings['submit-name']] !== $this->settings['submit-value'] )
            return false;

        return true;

    }

    function process(){
        
        if ( !$this->has_been_submitted() )
            return;

        if ( !class_exists('GUMP') )
            include_once('gump.class.php');

        $gump = new GUMP();
        $_POST = $gump->sanitize($_POST);

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
            $gump->validation_rules($validate);

        if ( !empty($filter) )
            $gump->filter_rules($filter);

        $validated_data = $gump->run($_POST['form']);
        $errors = array();

        //  returns an associative array
        //      'fieldname' => 'errortext',
        //      'fieldname' => 'errortext'
        
        //required checkboxes must be checked manually, as they are not present in the $_POST array
        $missing_checkboxes = $this->validate_required_checkboxes($_POST['form']);

        if ( !empty($missing_checkboxes) ){
            $validated_data = false;
            $errors = $missing_checkboxes;
        }

        if ( $validated_data === false ){
            $errors = array_merge($errors, $gump->get_readable_errors(false));
            $this->errors = $errors;
            return false;
        }
        else{
            $this->data = $validated_data;
            return true;
        }

    }

    function validate_required_checkboxes( $post_data ){

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

    //Call this in the page where you want submission results to be shown to the user
    function output_errors(){

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

    function output_honeypot(){
        echo '
        <div class="sr-only"><label for="form[hp]">Honeypot: If you see this field, leave it blank</label><input name="form[hp]" type="text" value=""></div>';
    }

    function blank_honeypot(){
        if ( isset($this->data['hp']) && !empty($this->data['hp']) && $this->data['hp'] !== '' ){
            return false;
        }

        return true;
    }

    function submit_button(){
        echo '<input type="submit" value="'.$this->settings['submit-value'].'" name="'.$this->settings['submit-name'].'" class="'.$this->settings['submit-class'].'">';
    }
}

class Field{

    public $var;
    public $name;
    public $type;
    public $options;
    public $desc;
    public $default;
    public $validate;
    public $filter;
    public $size;

    function __construct( $properties = array() ){

        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }        
    }
}

class Group{

    public $name;
    public $desc;
    public $fields;
    public $html_before;
    public $html_after;

    function __construct( $properties = array() ){

        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }        
    }
}

if ( !function_exists('slugify') ){
    function slugify($text){ 
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

}



function close_tag( $tag ){ 

    if ( $tag == '' )
        return;

    $out = ''; 
    $temp = substr($tag, 1); 
    $out = substr_replace($tag,'/', 1); 
    $out .= $temp; 
    return $out; 
} 

?>