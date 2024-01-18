<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewTokens.php
// @date: 20240115 10:45:59
namespace igk\bviewParser\System\IO;


///<summary></summary>
/**
* 
* @package igk\bviewParser\System\IO
* @author C.A.D. BONDJE DOUE
*/
abstract class BviewTokens{
    const TOKEN_COMMENT=0x1;
    const TOKEN_FUNC= 0x1;
    const TOKEN_VAR = 0x3;
    /**
     * text to apply to node definition
     */
    const TOKEN_TEXT = 0x4; 

    /**
     * value to pass
     */
    const TOKEN_VALUE = 0x5;
    
    /**
     * 
     */
    const TOKEN_LITTERAL = 0x6;

    /**
     * read brank
     */
    const TOKEN_BRANK = 0x7;


    /**
     * read attribute
     */
    const TOKEN_ATTRIBUTE = 0x8;

    /**
     * attribute value
     */
    const TOKEN_ATTRIBUTE_VALUE = 0x9;

    const TOKEN_BEGIN_BRANK_EXPRESSION = 0xa;

    const TOKEN_EXPRESSION = 0xb;
}