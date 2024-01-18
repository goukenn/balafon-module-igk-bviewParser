<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionEngineBase.php
// @date: 20240116 15:22:46
namespace igk\bviewParser\System\Engines;


///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
abstract class ExpressionEngineBase{
    static $sm_registry;
    private static function _InitRegistry(){
        if (is_null(self::$sm_registry)){
            self::$sm_registry = [];
        }
    }
    public static function Factory(string $name):?static{
        self::_InitRegistry();
        $cl = null;
        if (isset(self::$sm_registry[$name])){
            $cl = self::$sm_registry[$name];
        } else if (class_exists($t = __NAMESPACE__."\\Expression".ucfirst($name)."Engine")){
            $cl = new $t;
            self::$sm_registry[$name] = $cl;
        }
        return $cl;
    }
    public static function Register(string $name, ExpressionEngineBase $engine){
        self::_InitRegistry();
        self::$sm_registry[$name] =$engine;
    }
    public abstract function evalExpression(string $content, $options=null):mixed;
}