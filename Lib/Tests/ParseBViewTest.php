<?php
// @author: C.A.D. BONDJE DOUE
// @file: ParseBViewTest.php
// @date: 20250629 20:21:55
namespace igk\bviewParser\Tests;
use igk\bviewParser\System\IO\BviewParser;
use IGK\System\Html\HtmlNodeBuilder; 
require_once __DIR__ . "/ModuleTestBase.php";
///<summary></summary>
/**
* 
* @package igk\bviewParser\Tests
* @author C.A.D. BONDJE DOUE
*/
class ParseBViewTest extends ModuleTestBase{
    function test_bview_parse_single_top_comment(){
        $b = BviewParser::ParseFromContent(<<<'bview'


// this is special comment 


// definition

div{ 
- hello friend
}
bview        );
        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);
        $builder($b->data);
        $this->assertEquals('<div>hello friend</div>', $n->render());
    }
}