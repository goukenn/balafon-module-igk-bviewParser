<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewFileHandler.php
// @date: 20240115 10:43:06
namespace igk\bviewParser\System\IO;

use IGK\System\Html\HtmlNodeBuilder;
use IGK\System\IO\FileHandler;

///<summary></summary>
/**
* 
* @package igk\bviewParser\System\IO
* @author C.A.D. BONDJE DOUE
*/
class BviewFileHandler extends FileHandler{
    var $option;
    public function transform(string $content) { 
        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);
        $tab = BviewParser::ParseFromContent($content);

        $builder->build($tab->data);
        return $n;
    }

}