<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewFileHandler.php
// @date: 20240115 10:43:06
namespace igk\bviewParser\System\IO;
use IGK\System\Html\HtmlNodeBuilder;
use IGK\System\IO\FileHandler;
use IGK\System\IO\StringBuilder;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\IO
* @author C.A.D. BONDJE DOUE
*/
class BviewFileHandler extends FileHandler{
    var $option;
    /**
     * transform file context 
     */
    public function transform(string $content, $options=null) { 
        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);
        $tab = BviewParser::ParseFromContent($content);
        $builder($tab->data, null, $options);
        list($ctrl) = igk_extract($options ?? [], 'ctrl');
        if ($tab->directives && ($doc = $ctrl->getCurrentDoc())){
            list($title,$media) = igk_extract($tab->directives, 'title|media');
            if ($title)
                $doc->setTitle(sprintf($title, igk_configs()->website_domain));
 

            igk_hook('bview://bind_directive', ['n'=>$n, 'directives'=>$tab->directives, 'options'=>$options]);
        }
        return $n;
    }
    public function initDefaultSource():?string{
        $sb = new StringBuilder();
        $sb->appendLine('/* bview file */');
        $sb->appendLine("main.section{}");
        return $sb.'';
    }
}