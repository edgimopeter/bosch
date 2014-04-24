<?php
/**
 * Bosch Button
 *
 * @author      Peter Adams (http://peteradamsdev.com)
 * @copyright   Copyright (c) 2013 Peter Adams
 * @link        https://github.com/edgimopeter/bosch
 * @version     1.0
 */

/**
 * All buttons will have type="submit"
 */
class Bosch_Button extends Bosch{
    
    /**
     * Button name
     * @var string
     */
    public $name = '';

    /**
     * Var handle
     * @var string
     */
    public $var = '';

    /**
     * Button value
     * @var string
     */
    public $value = '';

    /**
     * CSS classes applied to button
     * @var string
     */
    public $class = '';

    /**
     * either prev, next, or submit
     * @var string
     */
    public $type = 'submit';

    /**
     * Constructor
     * @param array $properties 
     */
    function __construct( $properties = array() ){
        
        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }

        if ( $this->name === '' ){
            $this->bosch_error('<code>name</code> needed for button');
        }

        $this->var = $this->slugify($this->name); 

        if ( $this->var === '' ){
            $this->bosch_error('<code>var</code> needed for button '.$this->name);
        }       
    }

    protected function get_html(){
        return '<input type="submit" value="'.$this->value.'" name="'.$this->name.'" class="'.$this->class.'">';
    }

    

}