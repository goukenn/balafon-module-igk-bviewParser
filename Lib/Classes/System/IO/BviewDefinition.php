<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewDefinition.php
// @date: 20240116 09:23:50
namespace igk\bviewParser\System\IO;


///<summary></summary>
/**
* view section definition
* @package igk\bviewParser\System\IO
* @author C.A.D. BONDJE DOUE
*/
class BviewDefinition{
    /**
     * bview definition parent
     * @var ?static
     */
    var $parent;

    /**
     * bview key definition
     * @var ?string
     */
    var $key;
    /**
     * bview definition data
     * @var array
     */
    var $data = [];
}