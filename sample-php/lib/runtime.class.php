<?php
/**
* $Id: runtime.class.php 8 2008-01-23 09:24:34Z zhuzhu $
*/

class runtime
{
    var $StartTime = 0;
    var $StopTime = 0;
 
    function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
 
    function start()
    {
        $this->StartTime = $this->get_microtime();
    }
 
    function stop()
    {
        $this->StopTime = $this->get_microtime();
    }
 
    function spent()
    {
        return round(($this->StopTime - $this->StartTime) * 1000, 1);
    }
 
}
?>