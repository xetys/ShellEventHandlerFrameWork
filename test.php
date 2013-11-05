<?php

require("SEHF/EventHandler.class.php");


class TestEvent extends Event 
{
    
    public function run()
    {
        sleep(rand(1,10));
    }
}

class TestHandler extends EventHandler
{
    protected $timeoutMicroSeconds = 10000;

    public function getEvents()
    {
        $events = array();
        if(rand(1,10) == 1)
        {
            $events[] = new TestEvent();
        }

        return $events;
    }
}

TestHandler::launch();
