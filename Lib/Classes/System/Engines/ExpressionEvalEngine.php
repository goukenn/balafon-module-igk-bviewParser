<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionEvalEngine.php
// @date: 20240117 10:14:19
namespace igk\bviewParser\System\Engines;

use igk\bviewParser\System\IO\IBviewParserOptions;

///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class ExpressionEvalEngine  extends ExpressionEngineBase{

    public function evalExpression(string $content, $options = null): mixed { 
        if ($options instanceof IBviewParserOptions){
            $data = (array)$options;
            if ($c = preg_match_all('/(?P<escape>(\\\)?\')?\{\{(?P<value>.+)\}\}/', $content, $matches)){
                $tab = [];
                for($i = 0; $i < $c; $i++){
                    $v_escape = $matches['escape'][$i];
                    if ($v_escape=='\''){
                        continue;
                    }
                    $v_v = $matches[$i][0];
                    if (!key_exists($v_v, $tab)){
                        $v_ts = substr($v_v, strlen($v_escape));
                        $v = igk_engine_eval_pipe($v_ts, 0, $data);
                        if ($v_escape=='\\\''){
                            $v_escape='\'';
                        }
                        $content = str_replace($v_v, $v_escape.$v, $content);
                        $tab[$v_v] = 1;
                    }
                }
            }
         }
        return igk_str_remove_quote($content);
    }

}