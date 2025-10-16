<?php
// @author: C.A.D. BONDJE DOUE
// @file: EngineExpressionJsReader.php
// @date: 20240202 15:37:08
namespace igk\bviewParser\System\Engines;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class EngineExpressionJsReader extends EngineExpressionReaderFactory{
    public function read(string $content, & $pos, $brank_end=')', $brank_start='('){
        $ln = strlen($content);
        $depth = 0;
        $v = '';
        if (($brank_end!=")")||($brank_start!="(")){
            igk_die("brank not valid");
        }
        $end = false;
        while((!$end) && $pos < $ln){
            $ch = $content[$pos];
            switch($ch){
                case $brank_start:
                    $depth++;
                    break;
                case $brank_end:
                    $depth--;
                    if ($depth==0){
                        $end = true;
                        $pos--;
                    }
                    break;
                case '/':
                    if ((($pos+1) < $ln) && ($content[$pos+1]=='/')){
                        // skip single line comment
                        $rpos  = strpos($content, "\n", $pos);
                        if ($rpos !== false){
                            $v.= substr($content, $pos, $rpos-$pos);
                            $ch = '';
                            $pos = $rpos-1;
                        }
                    }
                    break;
            }
            $v.= $ch;
            $pos++;
        } 
        $v = igk_str_rm_last(igk_str_rm_start($v, '('), ')');  
        // igk_wln_e("the result : ", $v, $depth);
        return $v;
    }
}