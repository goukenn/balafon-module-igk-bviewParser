<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionVueEngine.php
// @date: 20250717 16:09:57
namespace igk\bviewParser\System\Engines;

use igk\js\Vue3\Compiler\VueSFCCompiler;
use igk\jsBuild\JsTreatImport;
use IGK\System\Console\Logger;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\HtmlReader;

/**
 * 
 * @package igk\bviewParser\System\Engines
 * @author C.A.D. BONDJE DOUE
 */
class ExpressionVueEngine extends ExpressionEngineBase
{
    public function evalExpression(string $content, $options = null)
    {
        $tn = HtmlReader::Load($content, null);
        $converter = igk_app()->getService('vue::sfc') ?? new SFCVueConverter;
        $template = $tn->getElementsByTagName('template');
        $tscript = $tn->getElementsByTagName('script');
        $n = igk_create_notagnode();
        $scripts = [];
        $renders = [];
        if ($template) {
            $childs = $template[0]->getChilds()->to_array();
            $app = igk_html_host('div#app');
            $l = igk_create_notagnode();
            foreach ($childs as $child) {
                $l->add($child);
            }
            $renders[] = $converter->convertToRender($l);
            $n->add($app);
        }

        $src = '';
        if ($tscript) {
            $q = array_shift($tscript);
            if ($q['setup']) {
                $src = $q->getContent();
                $g = JsTreatImport::Treat($src);
                // Logger::print(json_encode($g));
                // exit;
                $g = implode(
                    "\n",
                    [
                        '... await(async function(){ ',
                        'const ',
                        ' HelloProgram  = await import(\'/testapi/envato/components/helloProgram\');',
                        '/**',
                        ' * declaring data',
                        ' */',
                        'const i = 8888, j = 128,m =45;',
                        'let sample = function(){',
                        '    var x = 8;',
                        '};',
                        '// done', 
                        'return {components:{HelloProgram}, data(){ return {i,j,m,sample, HelloProgram}}}})()',
                    ]
                );



                $src = $g.","; 
            }
        }

        // for vue.runtime.global.prod.js
        //     $scripts[] = 'const { createApp, h } = Vue; createApp({render(){'.
        //  'return h("div", "hello world"); '.   
        // "}}).mount('#app');";
        // for vue.global.js
        $scripts[] = ' const { createApp, h, defineAsyncComponent } = Vue; createApp({' . $src . $renders[0] .
            ", props:{ title: { required:true, default:'undef-title', type: String } }}".
            ",{title: 'basouka'}".
            ").mount('#app');";
 


        $n->balafonjs()
            ->activate('async')
            ->content = implode("\n", $scripts);
        return $n->render();
    }
}

class SFCVueConverter
{
    public function convertToRender(HtmlItemBase $item)
    {
        return VueSFCCompiler::ConvertToVueRenderMethod($item);
    }
}
