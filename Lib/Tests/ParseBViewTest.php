<?php
// @author: C.A.D. BONDJE DOUE
// @file: ParseBViewTest.php
// @date: 20250629 20:21:55
namespace igk\bviewParser\Tests;

use Exception;
use igk\bviewParser\System\Exception\BviewSyntaxException;
use igk\bviewParser\System\IO\BviewParser;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlNodeBuilder;
use IGKException;
use ReflectionException;

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

    public function test_bview_compact_script(){
        // check inline compact script 
        $this->assertEquals('<div>top action</div><span>info</span>',
        $this->_bview_parse('div{ - top action }   span{ - info }'),
        'inline compact script failed');
    }
    public function test_bview_arg(){
        // check inline compact script 
        $this->assertEquals('<div>top : action</div>',
        $this->_bview_parse('div{ - top : {{ $x }} } ', (object)[
            'raw'=>[
                'x'=>'action'
            ]
        ]),
        'arg failed');
    }
    public function test_bview_arg2(){
        // check inline compact script 
        $this->assertEquals('<div>top : action</div>',
        $this->_bview_parse('div{ - top : {{ $raw->x }} } ', (object)[
            'raw'=>[
                'x'=>'action'
            ]
        ]),
        'arg failed');
    }
    public function test_bview_constant(){
        // check inline compact script 
        $this->assertEquals('<div>top '.IGK_VERSION.' bunny</div><champions></champions>',
        $this->_bview_parse('div{ - top {{ IGK_VERSION }} bunny }  champions'),
        'constant failed');
    }
    /**
     * 
     * @param string $content 
     * @param ?rawdata $data {raw: data to pass , ctrl: controller to pass}
     * @return null|string 
     * @throws IGKException 
     * @throws Exception 
     * @throws BviewSyntaxException 
     * @throws EnvironmentArrayException 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private function _bview_parse($content, $data=null){
        $b = BviewParser::ParseFromContent($content);
        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);                 
        $builder($b->data, null, $data);
        return $n->render();
    }
}