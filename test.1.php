<?php

session_start();

include ('bosch/init.php');

$report_form = new Bosch('report_form');
$report_form->settings('submit-value', 'Submit & Add Another Patient');
$report_form->settings('submit-name', 'report-add');
$report_form->settings('honeypot', 'false');


$fields = array(
    array(
		'var'      => 'patient_id', 
		'name'     => 'Patient identifier', 
		'type'     => 'text',
		'extras' => 'readonly',
		'filter'   => 'trim|sanitize_string',
		'validate' => 'required',
		'default'  => '12345',
		'desc'     => 'The identifier will be automatically randomly generated. For your hospital’s purposes, record the identifier used in the data collection system and the patient identifier used in your electronic health record. Record this information in a log that is stored in a secure location at your hospital.<br /><a href="documents/patient-id-log-outcome-1.pdf" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Download blank log</a>.'
    ),
    array(
		'var'      => 'unit_id', 
		'name'     => 'Unit or Service Identification (Specific to hospital or institution)', 
		'type'     => 'text',
		'filter'   => 'trim|sanitize_string',
		'validate' => 'required|numeric'
    ),
    array(
		'var'      => 'diagnosed', 
		'name'     => 'Date patient duly diagnosed with <em>C. difficile</em> infection  ', 
		'type'     => 'date',
		'filter'   => 'trim|sanitize_string',
		'validate' => 'required',
		'desc' => 'This date must fall within the reporting period'
    ),
    array(
		'var'      => 'admitted', 
		'name'     => 'Date of admission', 
		'type'     => 'date',
		'filter'   => 'trim|sanitize_string',
		'validate' => 'required',
		'desc' => 'This date can fall outside of the reporting period.'
    ),
    array(
		'var'      => 'more_than_four', 
		'name'     => 'At the time of <em>C. difficile</em> diagnosis, the patient had been continuously in our institution for 4 or more weeks ', 
		'type'     => 'radio-inline',
		'options' => array('1' => 'Yes', '0' => 'No'),
		'filter'   => 'trim|sanitize_string',
		'validate' => 'required'
    ),
    array(
		'var'      => 'prior_admission', 
		'name'     => 'Prior to this admission (during which the patient was diagnosed with <em>C. difficile</em> infection), has the patient previously been admitted to your institution?', 
		'type'     => 'radio',
		'options' => array(
						'a' => 'Yes, the previous admission was less than 4 weeks before this admission', 
						'b' => 'Yes, but the previous admission was more than 4 weeks before this admission',
						'c' => 'No, this patient has never been admitted to our institution before'),
		'filter'   => 'trim|sanitize_string'
    ),
    array(
		'var'      => 'timeframe', 
		'name'     => 'Time frame when symptoms consistent with <em>C. difficile</em> infection (e.g., watery diarrhea, abdominal pain) were first reported by the patient (as recorded in medical record)', 
		'type'     => 'radio',
		'options' => array(
						'a' => 'Prior to date of admission', 
						'b' => 'Within 48 hours after admission',
						'c' => 'More than 48 hours after admission'),
		'filter'   => 'trim|sanitize_string',
		'validate' => 'required'
    )
);

$groups = array(
	array(
		'name' => 'Group 1',
		'hide_name' => true,
		'fields' => 'patient_id|unit_id|diagnosed|admitted|more_than_four'
	),
	array(
		'name' => 'Group 2',
		'hide_name' => true,
		'fields' => 'prior_admission|timeframe'
	)
);

$steps = array(
	array(
		'name' => 'step1',
		'groups' => 'group-1'
	),
	array(
		'name' => 'step2',
		'groups' => 'group-2'
	)
);

$buttons = array(
	array(
		'name' => 'report-resume',
		'value' => 'Submit & Resume Later',
		'class' => 'btn btn-primary'
		),
	array(
		'name' => 'report-complete',
		'value' => 'Period Complete',
		'class' => 'btn btn-primary'
		)
);

$report_form->setup( $fields, $groups, $steps, $buttons );

if ( $report_form->has_been_submitted() ){

    if ( isset($_POST['form']['unit_id']) && $_POST['form']['unit_id'] < 1 ){
        $report_form->set_error('unit_id', '<span>Unit ID</span> must be greater than zero');
    }

    $report_form->process();

    if ( !$report_form->has_errors() ){

        if ( $report_form->data[0]['more_than_four'] === '1' ){
            $report_form->remove_field_from_group( 'prior_admission', 'group-2' );
        }

    	if ( $report_form->final_submit() ){

    		$values = $report_form->get_all_data();

    		var_dump($values);

            $report_form->reset();
            
    	}
    }
}

?>

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

        <div class="container">

            <div class="header" style="margin:50px 0;">
                <h1>Welcome to the Block Form</h1>
            </div>

            <div class="main">    

                <?php

                if ( $report_form->has_errors() ){
			        $report_form->output_errors();
			    }

				$report_form->output();                
                ?>

            </div>

            <div class="footer" style="margin:50px 0;">
                <p class="text-muted">Thank you for using this form!</p>
            </div>

        </div>
    </body>
</html>