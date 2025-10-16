<?php
// @author: C.A.D. BONDJE DOUE
// @file: BindingAttributeBase.php
// @date: 20240118 20:55:48
namespace igk\bviewParser\System\Html\Dom;
use igk\bviewParser\System\IO\BviewDefinition;
use IGK\System\Html\HtmlNodeBuilder;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Html\Dom
* @author C.A.D. BONDJE DOUE
*/
abstract class BindingAttributeBase{
    /**
     * retrieve attribute data from definition
     * @param BviewDefinition $definition 
     * @return mixed 
     */
    final protected function & getAttributeData(BviewDefinition $definition){
        return $definition->getAttributeData();
    }
    abstract function bindAttribute(BviewDefinition $definition, $value);
}