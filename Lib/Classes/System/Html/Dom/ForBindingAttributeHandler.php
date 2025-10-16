<?php
// @author: C.A.D. BONDJE DOUE
// @file: ForBindingAttributeHandler.php
// @date: 20240118 21:04:04
namespace igk\bviewParser\System\Html\Dom;
use igk\bviewParser\System\IO\BviewDefinition;
use IGK\System\Html\HtmlNodeBuilder;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Html\Dom
* @author C.A.D. BONDJE DOUE
*/
class ForBindingAttributeHandler extends BindingAttributeBase{
    public function bindAttribute(BviewDefinition $definition, $value) {
        $attrib = & $this->getAttributeData($definition);
        $attrib['*for'] = $value; 
    }
}