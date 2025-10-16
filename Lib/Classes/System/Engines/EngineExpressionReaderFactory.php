<?php
// @author: C.A.D. BONDJE DOUE
// @file: EngineExpressionReaderFactory.php
// @date: 20240202 15:29:25
namespace igk\bviewParser\System\Engines;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
abstract class EngineExpressionReaderFactory{
    /**
     * 
     * @param string $name 
     * @return ?static 
     */
    public static function Create(string $name){
        $cl = __NAMESPACE__."\\EngineExpression".ucfirst($name)."Reader";
        if (class_exists($cl)){
            return new $cl();
        } 
        return new EngineHtmlExpressionReader();
    }
    public function read(string $content, & $pos, $brank_end=')', $brank_start='('){
        $v = igk_str_read_brank($content, $pos, $brank_end, $brank_start);
        $v = igk_str_rm_last(igk_str_rm_start($v, '('), ')');  
        return $v;
    }
}