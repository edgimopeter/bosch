<?php

	if(session_id() == '') {
		session_start();
	}

	if ( !isset($_SESSION['step']) ){
		$_SESSION['step'] = 0;
	}

	//$_SESSION['step'] = 0;

	if ( !class_exists('Bosch') ){
		include_once('bosch.class.php');
	}

	if ( !class_exists('Bosch_Button') ){
		include_once('bosch-button.class.php');
	}

	if ( !class_exists('Bosch_Step') ){
		include_once('bosch-step.class.php');
	}

	if ( !class_exists('Bosch_Group') ){
		include_once('bosch-group.class.php');
	}

	if ( !class_exists('Bosch_Field') ){
		include_once('bosch-field.class.php');
	}

	if ( !class_exists('Bosch_Validator') ){
		include_once('bosch-validator.class.php');
	}

?>