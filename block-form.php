<?php include ('bosch/init.php'); ?>

<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Simple Form Test</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="bosch/css/bosch.css">
        <script src="bosch/js/jquery.min.js"></script>
        <script src="bosch/js/modernizr.js"></script>
        <script src="bosch/js/functions.js"></script>
    </head>
    <body>
    	
        <?php 
        
        $this_form = new Bosch;
        $this_form->settings('form-type', 'block');
        //$this_form->settings('input-width', 'col-sm-5');
        //$this_form->settings('label-width', 'col-sm-1');

        include ( 'example-fields.php' );

        $groups = array(array('name' => 'Group 1', 'fields' => 'file'));

        /*$groups = array(
            array(
                'name' => 'Group 1',
                'fields' => 'name|email|password|date|time|week',
                'desc' => 'This is the first group',
                'width' => 'col-md-3'
            ),
            array(
                'name' => 'Group 2',
                'fields' => 'amount|website|search|tel|color|file',
                'width' => 'col-md-9'
            )
        );*/

        $this_form->setup( $fields, $groups );

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

                $this_form->output();
                
                ?>

            </div>

            <div class="footer" style="margin:50px 0;">
                <p class="text-muted">Thank you for using this form!</p>
            </div>

        </div>
    </body>
</html>
