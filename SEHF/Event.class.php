<?php

/**
* The template for an executable Event
*
*/
abstract class Event
{
    /**
    * @var string $eventName;
    */
    protected $eventName;

    /**
    * @var int $lockTime
    */

    protected $lockTime = 1;

    public function __construct()
    {
        $this->eventName = get_called_class();
    }

    /**
    * your to-implement action
    */
    abstract public function run();

    /**
    * Returns the name of the event
    * 
    * @return string 
    */
    public function getName()
    {
        return $this->eventName;
    }
}