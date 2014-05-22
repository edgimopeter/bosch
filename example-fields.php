<?php

$fields = 
array(
      //Basic Text
      array(
      'var'         => 'name', 
      'name'        => 'Name', 
      'type'        => 'text',
      'desc'        => 'Please enter your name',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string',
      'placeholder' => 'Enter your name'
      ),
      //Text variant - email
      array(
      'var'         => 'email', 
      'name'        => 'Email', 
      'type'        => 'email',
      'desc'        => 'Please enter your email address',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string',
      'extras'      => ''
      ),
      //Text variant - password
      array(
      'var'         => 'password', 
      'name'        => 'Password', 
      'type'        => 'password',
      'desc'        => 'Please enter your password',
      'validate'    => '',
      'filter'      => 'sanitize_string',
      'extras'      => 'no-save'
      ),
      //Text variant - date
      array(
      'var'         => 'date', 
      'name'        => 'Date', 
      'type'        => 'date',
      'desc'        => 'Please pick a date',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'month', 
      'name'        => 'month', 
      'type'        => 'month',
      'desc'        => 'Please pick a month',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'year', 
      'name'        => 'year', 
      'type'        => 'year',
      'desc'        => 'Please pick a year',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'money', 
      'name'        => 'money', 
      'type'        => 'money',
      'desc'        => 'Please pick a money',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - time
      array(
      'var'         => 'time', 
      'name'        => 'Time', 
      'type'        => 'time',
      'desc'        => 'Please pick a time',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'hour', 
      'name'        => 'hour', 
      'type'        => 'hour',
      'desc'        => 'Please pick a hour',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'minute', 
      'name'        => 'minute', 
      'type'        => 'minute',
      'desc'        => 'Please pick a minute',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - amount
      array(
      'var'         => 'amount', 
      'name'        => 'Amount', 
      'type'        => 'number',
      'desc'        => 'How many?',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - URL
      array(
      'var'         => 'website', 
      'name'        => 'Website', 
      'type'        => 'url',
      'desc'        => 'Homepage',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - search
      array(
      'var'         => 'search', 
      'name'        => 'Search', 
      'type'        => 'search',
      'desc'        => 'Search',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - phone
      array(
      'var'         => 'tel', 
      'name'        => 'Phone Number', 
      'type'        => 'tel',
      'desc'        => 'Type your phone number',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - phone
      array(
      'var'         => 'color', 
      'name'        => 'Background Color', 
      'type'        => 'color',
      'desc'        => 'Choose a color',
      'validate'    => '',
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'radio', 
      'name'        => 'Required Choices', 
      'type'        => 'radio',
      'desc'        => 'Choose an option',
      'validate'    => 'required',
      'options' => array('yes' => 'Yes', 'no' => 'No'),
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'optional-radio', 
      'name'        => 'Optional Choices with Default Set', 
      'type'        => 'radio',
      'desc'        => 'Choose an option',
      'validate'    => 'required',
      'options' => array('yes' => 'Yes', 'no' => 'No'),
      'default' => 'no',
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'checkbox', 
      'name'        => 'Required Choices', 
      'type'        => 'checkbox',
      'desc'        => 'Choose an option',
      'validate'    => 'required',
      'options' => array('required_yes' => 'Required Yes', 'required_no' => 'Required No'),
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'optional-checkbox', 
      'name'        => 'Optional Choices with Default Set', 
      'type'        => 'checkbox',
      'desc'        => 'Choose an option',
      'validate'    => '',
      'options' => array('option_yes' => 'Optional Yes', 'option_no' => 'Optional No'),
      'default' => 'no',
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'select', 
      'name'        => 'Required Choices', 
      'type'        => 'select',
      'desc'        => 'Choose an option',
      'validate'    => 'required',
      'placeholder' => '-- Custom Text Here --',
      'options' => array('yes' => 'Yes', 'no' => 'No'),
      'filter'      => 'trim|sanitize_string'
      ),
      array(
      'var'         => 'multiselect', 
      'name'        => 'Multiple Choices', 
      'type'        => 'select',
      'desc'        => 'Choose an option',
      'validate'    => '',
      'extras' => 'multiple',
      'placeholder' => '-- Custom Text Here --',
      'options' => array('yes' => 'Yes', 'no' => 'No'),
      'filter'      => 'trim|sanitize_string'
      ),
       array(
      'var'         => 'wysiwyg', 
      'name'        => 'Lots of Text', 
      'type'        => 'wysiwyg',
      'desc'        => 'Rich test',
      'validate'    => '',
      'filter'      => 'trim'
      ),

);

?>