<?php

/**
* Helper class for rendering basic tables in the shell
*
* How to use:
* $table = new ShellTable();
* $table->addColumn("Employee");
* $table->addColumn("EMail");
*
* $table->addRow("John Doe", "john.doe@company");
* $table->addRow("Chuck Norris", "chuck.norris@company");
*
* $table->render();
*/
class ShellTable
{
    /**
    * An array of columns
    * @var array $columns
    */    
    private $columns = array();

    /**
    * An array of rows
    * @var array $rows
    */
    private $rows = array();

    /**
    * The default table length in spaces
    * @var array $columns
    */
    private $length = 80;

    /**
    * Adds a new column
    *
    * @param string $columnName
    * @throws RowsInTableException
    */
    public function addColumn($columnName)
    {
        if(count($this->rows) > 0)
            throw new RowsInTableException("You cannot add new columns, when there are already rows", 1);
            
        $this->columns[] = $columnName;
    }

    /**
    * Adds a new row. The number and order of arguments must be equal to the configured columns.
    */
    public function addRow()
    {
        $args = func_get_args();
        if(count($args) != count($this->columns))
           return;

        $this->rows[] = $args;
    }

    /**
    * Prints the table to the shell
    */
    public function render()
    {
        
        $columnCount    = count($this->columns);
        $columnLength   = $this->length / $columnCount;

        //computing dimensions
        if(($columnLength - floor($columnLength)) > 0)
        {
            $lastColumn = $columnLength;
            $columnLength = ceil($columnLength);
        }
        else
        {
            $lastColumn = $columnLength;
        }

        //print the header
        print str_repeat("-", $this->length) . PHP_EOL;
        print "|";
        for($i = 0; $i < $columnCount; $i++)
        {
            print " ";
            print $this->columns[$i];
            $currentLength = ($i < ($columnCount-1)) ? $columnLength  : $lastColumn - 1;
            print str_repeat(" ", $currentLength - 2 - strlen($this->columns[$i]));
            print "|";
        }
        print PHP_EOL;
        print str_repeat("=", $this->length) . PHP_EOL;
        //print the body
        foreach ($this->rows as $row) {
            print "|";
            for($i = 0; $i < $columnCount; $i++)
            {
                print " ";
                print $row[$i];
                $currentLength = ($i < ($columnCount-1)) ? $columnLength  : $lastColumn - 1;
                print str_repeat(" ", $currentLength - 2 - strlen((string)$row[$i]));
                print "|";
            }
            print PHP_EOL;
            print str_repeat("-", $this->length) . PHP_EOL;
        }

    }
}

class RowsInTableException extends Exception {}