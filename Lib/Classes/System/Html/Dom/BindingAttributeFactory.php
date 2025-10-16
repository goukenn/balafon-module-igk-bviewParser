<?php
// @author: C.A.D. BONDJE DOUE
// @file: BindingAttributeFactory.php
// @date: 20240118 20:55:13
namespace igk\bviewParser\System\Html\Dom;
use IGKException;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Html\Dom
* @author C.A.D. BONDJE DOUE
*/
abstract class BindingAttributeFactory{
    private static $sm_registry;
    private static function _InitRegistry(){
        if (is_null(self::$sm_registry)){
            self::$sm_registry = [];
        }
    }
    /**
     * Get binding attribute handler
     * @param string $attribute 
     * @return null|BindingAttributeBase 
     * @throws IGKException 
     */
    public static function GetBindingAttributeHandler(string $attribute):?BindingAttributeBase{
        self::_InitRegistry();
        if (!is_null($l = igk_getv(self::$sm_registry, $attribute))){
            return $l;
        }
        $g = null;
        $cl = __NAMESPACE__."\\".ucfirst($attribute)."BindingAttributeHandler";
        if (class_exists($cl)){
            $g = new $cl;
            self::$sm_registry[$attribute] = $g;
        }
        return $g;
    }
    /**
     * register attrib handler 
     */
    public static function RegisterAttribute(string $attrib_key, BindingAttributeBase $handler){
        empty($attrib_key) && igk_die('empty attrib key not allowed');
        self::_InitRegistry();
        self::$sm_registry[$attrib_key] = $handler;
    }
}