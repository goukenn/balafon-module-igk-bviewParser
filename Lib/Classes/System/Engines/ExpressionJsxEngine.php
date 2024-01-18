<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionJsxEngine.php
// @date: 20240116 15:30:55
namespace igk\bviewParser\System\Engines;

use IGK\System\Html\HtmlReader;

///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class ExpressionJsxEngine extends ExpressionEngineBase{

    public function evalExpression(string $content, $options = null): ?string { 

        $sb = '';
        $n = HtmlReader::Load($content);
         $sb = $n->render();  
        return $sb;
    }

}