<?php include ('bosch/init.php'); ?>

<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/bosch.css">
        <script src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/modernizr.js"></script>
        <script type="text/javascript" src="js/functions.js"></script>
    </head>
    <body>
    	
        <?php 
        
        $this_form = new Bosch;

        $fields = 
        array(
            array(
            'var'         => 'name', 
            'name'        => 'Name', 
            'type'        => 'text',
            'desc'        => 'Please enter your name',
            'validate'    => 'required',
            'filter'      => 'trim|sanitize_string',
            'placeholder' => 'Enter your name'
            ),
            array(
            'var'         => 'email', 
            'name'        => 'Email', 
            'type'        => 'email',
            'desc'        => 'Please enter your email address',
            'validate'    => 'required',
            'filter'      => 'trim|sanitize_string',
            'extras'      => ''
            )
        );

        $this_form->setup( $fields );

        ?>

        <div class="container">

            <div class="header" style="margin:50px 0;">
                <h1>Welcome to the Block Form</h1>
            </div>

            <div class="main">    

                <?php

                if ( $this_form->has_been_submitted() ){

                    $this_form->process();

                    if ( $this_form->has_errors() ){
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

                //$this_form->output();
                //
                ?>

                <form class="form" method="post">
                    <?php
                $this_form->get_field('name');
                $this_form->get_field('email');
                echo $this_form->submit_button();
                ?>
            </form>

            </div>

            <div class="footer" style="margin:50px 0;">
                <p class="text-muted">Thank you for using this form!</p>
            </div>

        </div>
    </body>
</html>
