<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewParserTest.php
// @date: 20240115 10:57:41
namespace igk\bviewParser\Tests\System\IO;

use igk\bviewParser\System\IO\BviewParser;
use igk\bviewParser\System\IO\BviewParserOptions;
use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlLoadingContext;
use IGK\System\Html\HtmlNodeBuilder;
use IGK\System\Html\HtmlReader;
use IGK\Tests\Controllers\ModuleBaseTestCase;

///<summary></summary>
/**
 * 
 * @package igk\bviewParser\Tests\System\IO
 * @author C.A.D. BONDJE DOUE
 */
class BviewParserTest extends ModuleBaseTestCase
{
    private function _load(string $asset)
    {
        $file = igk_current_module()->getDeclaredDir() . "/Lib/Tests/Data/" . $asset;
        return file_get_contents($file);
    }

    public function test_read_comment()
    {
        $this->expectException(\IGKException::class);
        BviewParser::ParseFromContent("/* hello");
    }

    public function test_read_valid_comment()
    {
        $b = BviewParser::ParseFromContent("/* hello comment */");
        $this->assertEquals([], $b->data);
    }
    public function test_parse()
    {
        $b = BviewParser::ParseFromContent("a{}");
        $this->assertEquals(['a' => []], $b->data);


        $b = BviewParser::ParseFromContent("a#id.btn{}");
        $this->assertEquals(['a#id.btn' => []], $b->data);

        $b = BviewParser::ParseFromContent("a#id.btn > span.local{}");
        $this->assertEquals(['a#id.btn > span.local' => []], $b->data);


        $b = BviewParser::ParseFromContent("a#id.btn    >    span.local{}");
        $this->assertEquals(['a#id.btn > span.local' => []], $b->data, 'cannot format and skip file');
    }
    public function test_parse_litteral()
    {

        $b = BviewParser::ParseFromContent(<<<EOF
        a#id.btn{
            - hello world
        }
EOF);
        $this->assertEquals(['a#id.btn' => 'hello world'], $b->data, 'cannot format and skip file');


        $b = BviewParser::ParseFromContent(<<<EOF
        a#id.btn{
            - hello world
            - cadinfo
        }
EOF);
        $this->assertEquals(['a#id.btn' => ['hello world', 'cadinfo']], $b->data, 'canot read litteral string multiple');
    }

    public function test_parse_sub_items()
    {

        $b = BviewParser::ParseFromContent(<<<EOF
        a#id.btn{
            span{
                - hello
            }
        }
EOF);
        $this->assertEquals(['a#id.btn' => ['span' => 'hello']], $b->data, 'canot read litteral string multiple');
    }

    public function test_parse_attribute()
    {

        $b = BviewParser::ParseFromContent(<<<EOF
        a#id.btn{
            span{
                - hello
                title:current span
            }
        }
EOF);
        $this->assertEquals(['a#id.btn' => ['span' => [
            'hello',
            '_' => [
                'title' => 'current span'
            ]
        ]]], $b->data, 'canot read litteral string multiple');

        $b = BviewParser::ParseFromContent(<<<EOF
        a#id.btn{
            span{
                - hello
                title:current span
                className:btn
            }
        }
EOF);
        $this->assertEquals(['a#id.btn' => ['span' => [
            'hello',
            '_' => [
                'title' => 'current span',
                'className' => 'btn'
            ]
        ]]], $b->data, 'canot read litteral string multiple');
    }

    public function test_parse_html_bhtml_content()
    {

        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                - html(
                    <li>information</li>
                )
                
            }
        }
JSX);
        $this->assertEquals(['a#id.btn' => [
            'span' =>
            '<li>information</li>',
        ]], $b->data, 'canot bind html expression');


        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                - bhtml(
                    <igk:ajxabutton igk:args="sample">information</igk:ajxabutton>
                )
                
            }
        }
JSX);
        $this->assertEquals(['a#id.btn' => [
            'span' =>
            '<a class="igk-btn" href="sample" igk-ajx-lnk="1">information</a>',
        ]], $b->data, 'canot bind html expression');
    }

    public function test_parse_json_classname()
    {

        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                - information
                class: json(
                    {
                        "underline":true,
                        "litteral":false
                    }
                )
                
            }
        }
JSX);

        $s = ['a#id.btn' => ['span' => [
            'information',
            '_' => [
                'class' => (object)['underline' => true, 'litteral' => false]
            ]
        ]]];

        $d = $b->data;
        $this->assertEquals($s, $d,  'cannot bind json data expression');

        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);
        $builder($d);

        $this->assertEquals('<a class="btn" href="#" id="id"><span class="underline">information</span></a>', $n->render());

        //var_dump($s);
    }

    public function test_read_a_file()
    {
        $g = $this->_load('a.bview');
        $q = BviewParser::ParseFromContent($g);
        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);
        $builder($q->data);
        $this->assertEquals(["a" => ["span" => "hello", "Toujours"]], $q->data);
        $this->assertEquals('<a href="#"><span>hello</span>Toujours</a>', $n->render());
    }

    public function test_parse_multi_level()
    {
        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                - information
            }
            span{
                - hello
            }
        }
JSX);
        $this->assertEquals(["a#id.btn" => ["span" => "information", ['@_t:span' => 'hello']]], $b->data);

        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);
        $builder($b->data);

        $this->assertEquals('<a class="btn" href="#" id="id"><span>information</span><span>hello</span></a>', $n->render(), 'tag block reference missing');
    }

    public function test_arg_resolution()
    {
        $options = Activator::CreateNewInstance(BviewParserOptions::class, [
            'ctrl' => BviewDummyCtrl::ctrl(),
            'raw' => [
                'x' => 1,
                'y' => 4
            ]
        ]);
        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                - {{ $raw["x"] }}
            }
        }
JSX,  $options);

        $this->assertEquals(['a#id.btn'=>['span'=>1]], $b->data);
    }
    public function test_arg_unescape_resolution()
    {
        $options = Activator::CreateNewInstance(BviewParserOptions::class, [
            'ctrl' => BviewDummyCtrl::ctrl(),
            'raw' => [
                'x' => 1,
                'y' => 4
            ]
        ]);
        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                - "data \'{{ $raw["x"] }}"
            }
        }
JSX,  $options);

        $this->assertEquals(['a#id.btn'=>['span'=>"data '1"]], $b->data);
    }
    public function test_arg_evaluable_expression()
    {
        $options = Activator::CreateNewInstance(BviewParserOptions::class, [
            'ctrl' => BviewDummyCtrl::ctrl(),
            'raw' => [
                'x' => 1,
                'y' => 4
            ]
        ]);
        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                - item {{ $raw["x"] }}
            }
        }
JSX,  $options);

        $this->assertEquals(['a#id.btn'=>['span'=>'item 1']], $b->data);
    }

    public function test_arg_evaluable_article_expression()
    {
        $options = Activator::CreateNewInstance(BviewParserOptions::class, [
            'ctrl' => BviewDummyCtrl::ctrl(),
            'raw' => [
                'x' => 1,
                'y' => 4
            ]
        ]);
        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                - bhtml(<igk:a>item: {{ $raw["x"] }}</igk:a>)
            }
        }
JSX,  $options);

        $this->assertEquals(['a#id.btn'=>['span'=>'<a href="#">item: 1</a>']], $b->data);
    }

    /**
     * test array value
     */
    public function test_array_object_value()
    {
        $options = Activator::CreateNewInstance(BviewParserOptions::class, [
            'ctrl' => BviewDummyCtrl::ctrl(),
            'raw' => [
                'x' => 1,
                'y' => 4
            ]
        ]);
        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                class:{
                    "basic":true,
                    "info":false
                }
                - hello world
            }
        }
JSX,  $options);

        $this->assertEquals(['a#id.btn'=>['span'=>['hello world',
        '_'=>[
            "class"=>(object)["basic"=>true, "info"=>false]
        ]]]], $b->data);
    }

    public function test_array_value()
    {
        $options = Activator::CreateNewInstance(BviewParserOptions::class, [
            'ctrl' => BviewDummyCtrl::ctrl(),
            'raw' => [
                'x' => 1,
                'y' => 4
            ]
        ]);
        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn{
            span{
                class:[
                    "basic",
                    "info"
                ] 
                - hello world
            }
        }
JSX,  $options);

        $this->assertEquals(['a#id.btn'=>['span'=>['hello world',
        '_'=>[
            "class"=>["basic", "info"]
        ]]]], $b->data);

        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);
        $builder($b->data);
        $this->assertEquals(
            '<a class="btn" href="#" id="id"><span class="basic info">hello world</span></a>',
            $n->render()
        );
    }   
    public function test_selector()
    {
        
        $b = BviewParser::ParseFromContent(<<<'JSX'
        a#id.btn[title:osaka] > span{ 
            - hello world 
        }
JSX);

        $this->assertEquals(['a#id.btn[title:osaka] > span'=>'hello world'], $b->data); 
        $this->assertEquals(
            '<a class="btn" href="#" id="id" title="osaka"><span>hello world</span></a>',
            self::renderData($b->data)
        );
    }   


    public function test_declaration_selector_with_arg()
    {
        
        $b = BviewParser::ParseFromContent(<<<'JSX'
        div#id.btn[title:osaka] > ajxa.link(./presentation){ 
            - hello world 
        }
JSX);

        $this->assertEquals(['div#id.btn[title:osaka] > ajxa.link(./presentation)'=>'hello world'], $b->data); 
        $this->assertEquals(
            '<div class="btn" id="id" title="osaka"><a class="link" href="./presentation" igk-ajx-lnk="1">hello world</a></div>',
            self::renderData($b->data)
        );
    }   

    public function test_loop()
    {
        
        $b = BviewParser::ParseFromContent(<<<'JSX'
        div#id.container > loop([1,2,3]){ 
            li{
            - hello world 
            }
        }
JSX);

        $this->assertEquals(['div#id.container > loop([1,2,3])'=>['li'=>'hello world']], $b->data); 

        $n = igk_create_notagnode();
        $n->div()->loop([1,2,3])->li()->Content = 'hello world';
        $this->assertEquals('<div><li>hello world</li><li>hello world</li><li>hello world</li></div>', $n->render());



        $this->assertEquals(
            '<div class="container" id="id"><li>hello world</li><li>hello world</li><li>hello world</li></div>',
            self::renderData($b->data)
        );
    }   

    public function test_loop_with_context()
    {
        $context = new BviewParserOptions();
        $context->ctrl = BviewDummyCtrl::ctrl();
        $context->raw = (object)['list'=>[1,4,5]]; 
        $b = BviewParser::ParseFromContent(<<<'JSX'
        div#id.container > loop([[:@raw->list]]){ 
            li{
            - hello world 
            }
        }
JSX, $context);

        $this->assertEquals(['div#id.container > loop([[:@raw->list]])'=>['li'=>'hello world']], $b->data); 

        $this->assertEquals('<div class="container" id="id"><li>hello world</li><li>hello world</li><li>hello world</li></div>', $this->renderData($b->data, $context));
    }


    public function test_active_attribute()
    {
        $context = new BviewParserOptions();
        $context->ctrl = BviewDummyCtrl::ctrl(); 
        $b = BviewParser::ParseFromContent(<<<'JSX'
        div#id.container{ 
            input{
                type:text
                @disabled @readonly
            }
        }
JSX, $context);

        $this->assertEquals(['div#id.container'=>['input'=>['_'=>["type"=>"text"], 
        HtmlNodeBuilder::KEY_ATTRIBS_ACTIVATION=>[
            'disabled',
            'readonly'
        ]
        ]]], $b->data); 

        $this->assertEquals('<div class="container" id="id"><input class="cltext" type="text" disabled readonly/></div>', $this->renderData($b->data, $context));
    }
    public function test_conditional_attribute()
    {
        $context = new BviewParserOptions();
        $context->ctrl = BviewDummyCtrl::ctrl(); 
        $context->raw = (object)[
            'active'=>false
        ]; 

        $b = BviewParser::ParseFromContent(<<<'JSX'
        div#id.container{ 
            input{
                *if: {{ $raw->active }}
                type:text
                @disabled @readonly
            }
        }
JSX, $context);

        $this->assertEquals(['div#id.container'=>['input'=>['_'=>[
            "type"=>"text", 
        ], 
        HtmlNodeBuilder::KEY_CONDITION=>false,
        HtmlNodeBuilder::KEY_ATTRIBS_ACTIVATION=>[
            'disabled',
            'readonly'
        ]
        ]]], $b->data); 

        $c = HtmlReader::LoadExpression('<div class="container" id="id"><input *if="false" class="cltext" type="text" disabled readonly/></div>');
        $this->assertEquals('<div class="container" id="id"></div>', $c->render());

        $this->assertEquals('<div class="container" id="id"></div>', $this->renderData($b->data, $context));
    }

    public function _test_not_binding_attribute(){
        $b = BviewParser::ParseFromContent(<<<'JSX'
        div#id.container{ 
            div{
                *for: {{ $raw->list }}
                ul{
                    li{
                        - {{ $raw->item }}
                    }
                }
            }
        }
JSX);

        $this->assertEquals('', $this->renderData($b->data));
    }

    public function renderData(array $data, $context=null):?string{
        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);
        $builder($data, null, $context);
        return $n->render();
    }

}



class BviewDummyCtrl extends BaseController
{
}
