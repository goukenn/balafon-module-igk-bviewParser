<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionJsxEngine.php
// @date: 20240116 15:30:55
namespace igk\bviewParser\System\Engines;
use IGK\System\Html\HtmlReader;
use IGKException;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class ExpressionJsxEngine extends ExpressionEngineBase{
    /**
     * eval expression
     * @param string $content 
     * @param mixed $options 
     * @return mixed
     * @throws IGKException 
     */
    public function evalExpression(string $content, $options = null){ 
        $sb = '';
        $n = HtmlReader::Load($content);
         $sb = $n->render();  
        return $sb;
    }
}