<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionEvalEngine.php
// @date: 20240117 10:14:19
namespace igk\bviewParser\System\Engines;
use igk\bviewParser\System\IO\IBviewParserOptions;
use IGK\System\Templates\BindingExpressionReader;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\Templates\Engine\Traits\ExpressionEvalEngineTrait;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class ExpressionEvalEngine extends ExpressionEngineBase{
    use ExpressionEvalEngineTrait;
    public function evalExpression(string $content, $options = null) { 
        return new EvalExpression($content); 
    }  
}