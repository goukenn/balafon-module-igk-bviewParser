<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionHtmlEngine.php
// @date: 20240116 15:30:33
namespace igk\bviewParser\System\Engines;

use IGK\System\Html\HtmlReader;

///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class ExpressionHtmlEngine extends ExpressionEngineBase{

    public function evalExpression(string $content, $options = null): ?string {
        $n = HtmlReader::LoadExpression($content,null, $options); 
        return $n->render();
    }

}