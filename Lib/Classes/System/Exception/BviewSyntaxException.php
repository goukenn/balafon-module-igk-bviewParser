<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewSyntaxException.php
// @date: 20240118 16:30:22
namespace igk\bviewParser\System\Exception;
use IGKException;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Exception
* @author C.A.D. BONDJE DOUE
*/
class BviewSyntaxException extends IGKException{
    var $syntaxLine;
    var $syntaxColumn;
    public function __construct(IBviewSyntaxInfo $info, string $message, int $code=500, ?\Throwable $throwable=null)
    {
        parent::__construct(sprintf('%s At %s:%s', $message, $info->line, $info->column), $code, $throwable);
        $this->syntaxLine = $info->line;
        $this->syntaxColumn = $info->column;
    }
}