<?php include ('bosch.class.php'); ?>

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

        <div class="container">
    	
        <?php 

            $this_form = new Bosch;
            $this_form->settings['group-headings'] = '<h3>';
            $this_form->settings['form-type'] = 'block';

            include ('fields.php');
            $this_form->set_fields( $fields );

            $this_form->set_groups(
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
                )
            );

            $this_form->errors = $this_form->process();

        ?>

        <p>Welcome to the form! Blah blah blah!</p>

        <?php $this_form->output_result(); ?>

        <h1>Full form</h1>
        <div class="bosch-example">            
            <?php $this_form->output(); ?>
        </div>

        </div>
    </body>
</html>
