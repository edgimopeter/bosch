<?php

$fields = 
array(
      //Basic Text
      array(
      'var'         => 'name', 
      'name'        => 'Name', 
      'type'        => 'text',
      'desc'        => 'Please enter your name',
      'validate'    => 'required',
      'filter'      => 'trim|sanitize_string',
      'placeholder' => 'Enter your name'
      ),
      //Text variant - email
      array(
      'var'         => 'email', 
      'name'        => 'Email', 
      'type'        => 'email',
      'desc'        => 'Please enter your email address',
      'validate'    => 'required',
      'filter'      => 'trim|sanitize_string',
      'extras'      => ''
      ),
      //Text variant - password
      array(
      'var'         => 'password', 
      'name'        => 'Password', 
      'type'        => 'password',
      'desc'        => 'Please enter your password',
      'validate'    => 'required',
      'filter'      => 'sanitize_string',
      'extras'      => 'no-save'
      ),
      //Text variant - date
      array(
      'var'         => 'date', 
      'name'        => 'Date', 
      'type'        => 'date',
      'desc'        => 'Please pick a date',
      'validate'    => 'required',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - time
      array(
      'var'         => 'time', 
      'name'        => 'Time', 
      'type'        => 'time',
      'desc'        => 'Please pick a time',
      'validate'    => 'required',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - week
      array(
      'var'         => 'week', 
      'name'        => 'Week', 
      'type'        => 'week',
      'desc'        => 'Please pick a week',
      'validate'    => 'required',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - amount
      array(
      'var'         => 'amount', 
      'name'        => 'Amount', 
      'type'        => 'number',
      'desc'        => 'How many?',
      'validate'    => 'required',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - URL
      array(
      'var'         => 'website', 
      'name'        => 'Website', 
      'type'        => 'url',
      'desc'        => 'Homepage',
      'validate'    => 'required',
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
      'validate'    => 'required',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - phone
      array(
      'var'         => 'color', 
      'name'        => 'Background Color', 
      'type'        => 'color',
      'desc'        => 'Choose a color',
      'validate'    => 'required',
      'filter'      => 'trim|sanitize_string'
      ),
      //Text variant - phone
      array(
      'var'         => 'file', 
      'name'        => 'File Upload', 
      'type'        => 'file',
      'desc'        => '',
      'validate'    => '',
      'filter'      => ''
      )
);