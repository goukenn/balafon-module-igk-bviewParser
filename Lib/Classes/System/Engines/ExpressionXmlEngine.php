<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionXmlEngine.php
// @date: 20240116 15:58:04
namespace igk\bviewParser\System\Engines;

use IGK\System\Html\HtmlReader;

///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class ExpressionXmlEngine extends ExpressionEngineBase{

    public function evalExpression(string $content, $options = null): ?string {
        $n = HtmlReader::Load($content,null);
        //$n = igk_create_notagnode();
        //$n->load($content);
        return $n->render();
    }

}