<?php

/*
FIELD ARRAY:
var         = The variable name, no spaces
name        = The display label
type        = text, money, date, select, checkbox, checkbox-inline, radio, radio-inline, textarea
options     = for select/radio/checkbox, an associative array of var=>label. For textarea, number of rows to display
desc        = Help text to display beneath field
default     = Default value
validate    = String of validators (see below), seperated by '|'
filter      = String of filters (see below), seperated by '|'
placeholder = Placeholder text
hide_label  = true or false, defaults to false
size        = control font sizing (lg or sm, blank is medium)
input_width = control width on horizontal form (col-md-4, col-xs-2, etc). Blank is set with $bosch->settings['default-column-width'];
label_width = control width of label on horizontal form
extras      = disabled|multiple|nosave



VALIDATORS:
required       = Ensures the specified key value exists and is not empty
valid_email    = Checks for a valid email address
max_len,n      = Checks key value length, makes sure it's not longer than the specified length. n = length parameter.
min_len,n      = Checks key value length, makes sure it's not shorter than the specified length. n = length parameter.
exact_len,n    = Ensures that the key value length precisely matches the specified length. n = length parameter.
alpha          = Ensure only alpha characters are present in the key value (a-z, A-Z)
alpha_numeric  = Ensure only alpha-numeric characters are present in the key value (a-z, A-Z, 0-9)
alpha_dash     = Ensure only alpha-numeric characters + dashes and underscores are present in the key value (a-z, A-Z, 0-9, _-)
numeric        = Ensure only numeric key values
integer        = Ensure only integer key values
boolean        = Checks for PHP accepted boolean values, returns TRUE for "1", "true", "on" and "yes"
float          = Checks for float values
valid_url      = Check for valid URL or subdomain
url_exists     = Check to see if the url exists and is accessible
valid_ip       = Check for valid generic IP address
valid_ipv4     = Check for valid IPv4 address
valid_ipv6     = Check for valid IPv6 address
valid_cc       = Check for a valid credit card number (Uses the MOD10 Checksum Algorithm)
valid_name     = Check for a valid format human name
contains,n     = Verify that a value is contained within the pre-defined value set
street_address = Checks that the provided string is a likely street address. 1 number, 1 or more space, 1 or more letters
iban           = Check for a valid IBAN


FILTERS:
sanitize_string  = Remove script tags and encode HTML entities, similar to GUMP::xss_clean();
urlencode        = Encode url entities
htmlencode       = Encode HTML entities
sanitize_email   = Remove illegal characters from email addresses
sanitize_numbers = Remove any non-numeric characters
trim             = Remove spaces from the beginning or end of strings
base64_encode    = Base64 encode the input
base64_decode    = Base64 decode the input
sha1             = Encrypt the input with the secure sha1 algorithm
md5              = MD5 encode the input
noise_words      = Remove noise words from string
json_encode      = Create a json representation of the input
json_decode      = Decode a json string
rmpunctuation    = Remove all known puncutation characters from a string
basic_tags       = Remove all layout orientated HTML tags from text. Leaving only basic tags

*/


$fields = 
array(
array(
'var'         => 'example-name', 
'name'        => 'Name', 
'type'        => 'text',
'desc'        => 'Please enter your name',
'validate'    => 'required|valid_name',
'filter'      => 'trim|sanitize_string',
'placeholder' => 'Enter your name',
'extras' => 'no-save'
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
)

?>