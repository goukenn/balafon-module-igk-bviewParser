<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewParserStateInfo.php
// @date: 20240115 10:51:36
namespace igk\bviewParser\System\IO;


///<summary></summary>
/**
* 
* @package igk\bviewParser\System\IO
* @author C.A.D. BONDJE DOUE
*/
class BviewParserStateInfo{
    const MODE_READ_GLOBAL = 0;
    const MODE_READ_EXPRESSION = 1; // mustache expression
    
    /**
     * depth
     * @var int
     */
    var $depth= 0;

    var $line = 0;
    var $column = 0;

    /**
     * 
     * @var int
     */
    var $mode = 0;

    /**
     * expression mode
     * @var ?bool
     */
    var $expression;

    /**
     * expression info definition
     * @var mixed
     */
    var $expressionInfo; 

    /**
     * 
     * @var mixed
     */
    var $selection;
    /**
     * store array key
     * @var mixed
     */
    var $key;

    /**
     * represent state definition
     * @var mixed
     */
    var $definition;

    /**
     * store flag
     * @var mixed
     */
    var $flag;

    /**
     * store attribute
     * @var mixed
     */
    var $attribute; 
}