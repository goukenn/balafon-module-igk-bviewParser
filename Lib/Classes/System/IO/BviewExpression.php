<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewExpression.php
// @date: 20240116 13:27:42
namespace igk\bviewParser\System\IO;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\IO
* @author C.A.D. BONDJE DOUE
*/
class BviewExpression{
    var $name;
    var $value;
    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;    
    }
}