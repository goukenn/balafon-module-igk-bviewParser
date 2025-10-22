<?php
// @author: C.A.D. BONDJE DOUE
// @file: Configs.php
// @date: 20251022 00:05:07
namespace igk\bviewParser\System;

use IGK\Helper\Activator;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;

/**
* 
* @package igk\bviewParser\System
* @author C.A.D. BONDJE DOUE
*/
class Configs{
    private static $sm_instance; 
    var $info = '1.0';
    var $debug = false;    
    private function __construct()
    {
        
    }
    public function __toString(){
        return json_encode($this);
    }
    public function to_array(){
        return (array)$this;
    }
    /**
     * 
     * @return static|mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function getInstance(){
        if (is_null(self::$sm_instance)){
            $g = new static;
            $n = igk_current_module()->getName();
            $c = igk_configs()->get($n) ?? [];            
            Activator::BindProperties($g, $c);
            self::$sm_instance = $g;
            igk_configs()->setConfig($n, $g); 
        }
        return $g;
    }
}