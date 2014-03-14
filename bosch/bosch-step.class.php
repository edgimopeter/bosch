<?php
/**
 * Bosch Step
 *
 * @author      Peter Adams (http://peteradamsdev.com)
 * @copyright   Copyright (c) 2013 Peter Adams
 * @link        https://github.com/edgimopeter/bosch
 * @version     1.0
 */

/**
 * Bosch Steps hold one or more groups
 */
class Bosch_Step{
    
    public $name      = '';
    public $desc      = '';
    public $groups    = array();
    public $prev      = true;
    public $prev_text = 'Go Back';
    public $next      = true;
    public $next_text = 'Continue';

    /**
     * Constructor
     * @param array $properties 
     */
    function __construct( $properties = array() ){

        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }        
    }

}