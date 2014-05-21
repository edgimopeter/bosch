<?php

	if(session_id() == '') {
		session_start();
	}

	if ( !class_exists('Bosch') ){
		include_once('bosch.class.php');
	}

	if ( !class_exists('Bosch_Button') ){
		include_once('bosch-button.class.php');
	}	

	if ( !class_exists('Bosch_Field') ){
		include_once('bosch-field.class.php');
	}

	if ( !class_exists('Bosch_Validator') ){
		include_once('bosch-validator.class.php');
	}

?>