<?php
// @author: C.A.D. BONDJE DOUE
// @file: IBviewListener.php
// @date: 20240115 15:51:56
namespace igk\bviewParser\System\IO;


///<summary></summary>
/**
* 
* @package igk\bviewParser\System\IO
* @author C.A.D. BONDJE DOUE
*/
interface IBviewListener{
    /**
     * handle token
     * @param array $token 
     * @return mixed 
     */
    function handleToken(array $token);
}