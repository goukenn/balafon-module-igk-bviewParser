<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionJsonEngine.php
// @date: 20240116 16:36:50
namespace igk\bviewParser\System\Engines;

use igk\bviewParser\System\Engines\ExpressionEngineBase;

///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class ExpressionJsonEngine extends ExpressionEngineBase{

    public function evalExpression(string $content, $options = null): mixed { 
        if ($data = json_decode($content)){
            return $data;
        }
        return '';
    }

}