<?php include ('bosch/init.php'); ?>

<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Simple Form Test</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="bosch/js/bosch.js"></script>
    </head>
    <body>
    	
        <?php 
        
        $this_form = new Bosch('block-form');
        $this_form->settings('form-type', 'horizontal');

        include ( 'example-fields.php' );

        $this_form->setup( $fields );
        $this_form->reset();

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
