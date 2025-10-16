<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionXmlEngine.php
// @date: 20240116 15:58:04
namespace igk\bviewParser\System\Engines;
use IGK\System\Html\HtmlReader;
use IGKException;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class ExpressionXmlEngine extends ExpressionEngineBase{
    /**
     * 
     * @param string $content 
     * @param mixed $options 
     * @return mixed 
     * @throws IGKException 
     */
    public function evalExpression(string $content, $options = null) {
        $n = HtmlReader::Load($content,null); 
        return $n->render();
    }
}