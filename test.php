<?php include ('bosch/init.php'); ?>

<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bosch.css">
        <script src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/modernizr.js"></script>
        <script type="text/javascript" src="js/functions.js"></script>
    </head>
    <body>
    	
        <?php 
        
        $this_form = new Bosch;

        $this_form->settings('group-headings', '<h2>');
        $this_form->settings('form-type', 'block');
        $this_form->settings('debug', true);

        $fields = 
        array(
            array(
            'var'         => 'example-name', 
            'name'        => 'Name', 
            'type'        => 'text',
            'desc'        => 'Please enter your name',
            'validate'    => 'required|valid_name',
            'filter'      => 'trim|sanitize_string',
            'placeholder' => 'Enter your name'
            ),
            array(
            'var'         => 'example-password', 
            'name'        => 'Password', 
            'type'        => 'date',
            'options'     => '',
            'desc'        => 'Please enter your hometown',
            'default'     => '',
            'validate'    => 'required',
            'filter'      => 'trim|sanitize_string',
            'size'        => 'lg',
            'extras'      => ''
            ),
            array(
            'var'         => 'example-state', 
            'name'        => 'State', 
            'type'        => 'day',
            'desc'        => '',
            'default'     => '',
            'validate'    => 'required',
            'filter'      => 'trim|sanitize_string'
            ),
            array(
            'var'         => 'example-married', 
            'name'        => 'Are you married?', 
            'type'        => 'radio',
            'options'     => array('yes' => 'Yes', 'no' => 'No', 'no-response' => 'No Response'), 
            'desc'        => '',
            'default'     => 'no-response',
            'validate'    => 'required',
            'filter'      => 'trim|sanitize_string',
            'size'        => 'sm'
            ),
            array(
            'var'         => 'example-fat', 
            'name'        => 'Are you fat?', 
            'type'        => 'radio-inline',
            'options'     => array('yes' => 'Yes', 'no' => 'No', 'no-response' => 'No Response'), 
            'desc'        => '',
            'default'     => 'no-response',
            'validate'    => 'required',
            'filter'      => 'trim|sanitize_string',
            'size'        => 'sm'
            ),
            array(
            'var'         => 'example-schools', 
            'name'        => 'Choose your schools', 
            'type'        => 'checkbox',
            'options'     => array('1' => 'This school', '2' => 'That school'), 
            'desc'        => '',
            'default'     => '',
            'validate'    => 'required',
            'filter'      => 'trim|sanitize_string',
            'size'        => 'sm'
            ),
            array(
            'var'         => 'example-shoes', 
            'name'        => 'Choose your shoes', 
            'type'        => 'checkbox-inline',
            'options'     => array('1' => 'Adidas', '2' => 'Nike', '3' => 'Reebok'), 
            'desc'        => '',
            'default'     => '',
            'validate'    => '',
            'filter'      => 'trim|sanitize_string',
            'size'        => 'sm'
            ),
            array(
            'var'         => 'example-bio', 
            'name'        => 'Biography', 
            'type'        => 'textarea',
            'options'     => '6',
            'desc'        => 'Describe yourself',
            'default'     => '',
            'validate'    => '',
            'filter'      => 'trim|sanitize_string',
            'size'        => 'lg'
            ),
            array(
            'var'         => 'example-salary', 
            'name'        => 'Desired Salary', 
            'type'        => 'money',
            'options'     => '',
            'desc'        => '',
            'default'     => '',
            'validate'    => '',
            'filter'      => 'trim|sanitize_string',
            'size'        => 'lg'
            ),
        );

        $groups = 
        array(
            array(
            'name'        => 'Basic Info',
            'desc'        => 'Please provide information about the traveller for which this reservation is being made.',
            'fields'      => 'example-name|example-password|example-schools|example-shoes',
            'html_before' => '',
            'html_after'  => '<hr>'
            ),
            array(
            'name'        => 'More Info',
            'desc'        => 'Please enter this traveller\'s address',
            'fields'      => 'example-state|example-married|example-fat',
            'html_before' => '',
            'html_after'  => ''
            ),
            array(
            'name'        => 'For fun',
            'desc'        => 'I like milk',
            'fields'      => 'example-bio|example-salary',
            'html_before' => '',
            'html_after'  => ''
            )
        );

        $steps = array(
            array(
            'name'      => 'This is the First Step',
            'desc'      => 'Step 1 description',
            'groups'    => 'Basic Info'
            ),
            array(
            'name'      => 'This is the Second Step',
            'desc'      => 'Step 2 description',
            'groups'    => 'More Info'
            )
        );

        //$this_form->set_fields( $fields );
        //$this_form->set_groups( $groups );
        //$this_form->set_steps( $steps );

        $this_form->setup( $fields, $groups, $steps );

        ?>

        <div class="container">

            <div class="header" style="margin:50px 0;">
                <h1>Welcome to the Block Form</h1>
            </div>

            <div class="main">    

                <?php

                if ( $this_form->has_been_submitted() ){

                    $this_form->process();

                    if ( !empty($this_form->errors) ){
                        $this_form->output_errors();
                    }
                    else{

                        if( $this_form->final_submit() && $this_form->blank_honeypot() ){
                            var_dump($this_form->data);
                            $this_form->reset();
                            echo '<div class="alert alert-success">Thank you! We will be in touch with you shortly!</div>';
                        }
                    }
                }  

                $this_form->output();

               ?>

            </div>

            <div class="footer" style="margin:50px 0;">
                <p class="text-muted">Thank you for using this form!</p>
            </div>

        </div>
    </body>
</html>
