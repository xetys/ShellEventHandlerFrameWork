<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 05.11.13
 * Time: 15:17
 * To change this template use File | Settings | File Templates.
 */

class SharedMemory {

    public static function set($name,$value)
    {
        $allVars = self::readMemory();

        $allVars[$name] = $value;

        self::writeMemory($allVars);
    }

    /**
     * @param string $name
     * @throws VariableNotDefinedException
     */
    public static function get($name)
    {
        $allVars = self::readMemory();

        if(!isset($allVars[$name]))
            throw new VariableNotDefinedException($name." is not defined in shared memory",1);

        return $allVars[$name];
    }
    public static function reset()
    {
        $f = fopen("tmp/vars","w");
        $emptyVars = array();
        fwrite($f,serialize($emptyVars));
        fclose($f);
    }

    private static function init()
    {
        if(!file_exists("tmp/vars"))
        {
            $f = fopen("tmp/vars","w");
            $emptyVars = array();
            fwrite($f,serialize($emptyVars));
            fclose($f);
        }
    }

    private static function readMemory()
    {
        self::init();
        $f = fopen("tmp/vars",'r');
        $content = '';
        while(!feof($f))
        {
            $content .= fread($f,1024);
        }
        fclose($f);

        return unserialize($content);
    }

    private static function writeMemory($allVars)
    {
        self::init();
        $f = fopen("tmp/vars","w");
        $serialized = serialize($allVars);
        fwrite($f,$serialized);
        fclose($f);
    }
}


class VariableNotDefinedException extends Exception {}