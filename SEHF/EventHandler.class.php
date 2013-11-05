<?php

define("HANDLER_VERSION", "1.0");

require_once("Event.class.php");
require_once("Thread.class.php");
require_once("ShellTable.class.php");

/**
* The main class for eventhandling. It includes self control and a minimal shell interface for monitoring
*
*/
abstract class EventHandler extends Thread
{
    /**
    * @var array $Handlers
    */
    protected $Handlers;

    /**
    * @var int $maxThreads
    */
    protected $maxThreads = 20;

    /**
    * @var int $deadClearMicroSeconds
    */
    protected $deadClearMicroSeconds = 800000;

    /**
    * @var int $timeoutMicroSeconds
    */
    protected $timeoutMicroSeconds = 1000;

    /**
    * @var int $startTime;
    */
    private $startTime;

    /**
    *
    * Returns an array of object of and Event-Descendand. 
    * @return array
    */
    abstract public function getEvents();

    public function __construct()
    {
        parent::__construct();
        $this->startTime = time();
    }

    /**
    * Calls the 'clear' command on the shell
    **/
    private function clearScreen()
    {
        passthru("clear");
    }

    /**
    * Shows the monitor of current registered events
    *
    */
    private function showMonitor()
    {
        $this->clearScreen();

        $colorList = array(
            "black" =>  "\033[30m",
            "blue"  =>  "\033[34m",
            "green" =>  "\033[32m",
            "cyan"  =>  "\033[36m",
            "red"   =>  "\033[31m",
            "purple" => "\033[35m",
            "yellow" => "\033[1;33m",
            "no"        => "\33[0m"
        );

        $runTime = time() - $this->startTime - 3600;

        print $colorList["yellow"]."stytex Eventhandler v" . HANDLER_VERSION . $colorList["no"];
        print str_repeat(PHP_EOL, 5);
        print $colorList["green"]."Runnging Threads: " . $colorList["no"]." ".count($this->Handlers).PHP_EOL;
        print $colorList["green"]."Uptime: " . $colorList["no"]." ".sprintf("%s Days, %s hours, %s minutes, %s seconds",(string)floor(($runTime+3600)/86400),date("H",$runTime),date("i",$runTime),date("s",$runTime)).PHP_EOL;
        print str_repeat(PHP_EOL, 2);
        
        $table = new ShellTable();
        $table->addColumn("ID");
        $table->addColumn("name");
        $table->addColumn("status");        


        foreach ($this->Handlers as $handlerID => $Handler) {
            $status = $Handler['thread']->isAlive() ? "running" : "dead";
            $table->addRow($handlerID, $Handler['event']->getName(), $status);
        }

        $table->render();
        unset($table);
    }

    /**
    * The run method of this master-thread
    *
    */
    public function run()
    {
        while(1)
        {
            $newEvents = $this->getEvents();

            foreach($newEvents as $Event)
            {
                $this->waitEvents();
                if(!$Event instanceof Event)
                    continue;
                $newId = uniqid();
                $this->Handlers[$newId] = array("event" => $Event, "thread" => new Thread(array($Event,"run")));
                $this->Handlers[$newId]['thread']->start();
            }
            $this->showMonitor();
            usleep($this->timeoutMicroSeconds);
        }
    }

    /**
    * Clears dead handlers
    *
    */
    private function cleanUp()
    {
        foreach($this->Handlers as $K => $Handler)
        {
            if(!$Handler['thread']->isAlive())
            {
                unset($this->Handlers[$K]);
                //$this->log("Handler $liK wurde bereinigt","purple");
            }
        }
    }

    /**
    * Blocks the intepreter, while the amount of running handlers equals (or greater than) max allowed threads
    */
    private function waitEvents()
    {
        while(count($this->Handlers) >= $this->maxThreads)
        {
            $this->cleanUp();
            usleep($this->deadClearMicroSeconds);
        }
    }

    /**
    * Entry point for the eventhandler. Includes self controll of beeing alive
    *
    */
    public static function launch()
    {
        $className = get_called_class();

        $mainHandler = new $className();
        $mainHandler->start();

        while(1)
        {
            if(!$mainHandler->isAlive())
            {
                $mainHandler->start();
            }
            sleep(1);
        }
    }
}