<?php
class Bosch_Validator{	

	
	/**
	 * Sanitize the input data
	 * 
	 * @access public
	 * @param  array $data
	 * @return array
	 */
	public function sanitize(array $input, $fields = NULL, $utf8_encode = true)
	{
		$magic_quotes = (bool)get_magic_quotes_gpc();
		
		if(is_null($fields))
		{
			$fields = array_keys($input);
		}

		foreach($fields as $field)
		{
			if(!isset($input[$field]))
			{
				continue;
			}
			else
			{
				$value = $input[$field]; 
				
				if(is_string($value))
				{
					if($magic_quotes === TRUE)
					{
						$value = stripslashes($value);
					}
					
					if(strpos($value, "\r") !== FALSE)
					{
						$value = trim($value);
					}
					
					if(function_exists('iconv') && function_exists('mb_detect_encoding') && $utf8_encode)
					{
						$current_encoding = mb_detect_encoding($value);
						
						if($current_encoding != 'UTF-8' && $current_encoding != 'UTF-16') {
							$value = iconv($current_encoding, 'UTF-8', $value);
						}
					}
					
					$value = filter_var($value, FILTER_SANITIZE_STRING);
				}
				
				$input[$field] = $value;
			}
		}
		
		return $input;		
	}
}