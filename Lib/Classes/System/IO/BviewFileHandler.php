<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewFileHandler.php
// @date: 20240115 10:43:06
namespace igk\bviewParser\System\IO;

use IGK\Controllers\BaseController;
use IGK\Helper\ViewHelper;
use IGK\System\Html\HtmlNodeBuilder;
use IGK\System\IO\FileHandler;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
///<summary></summary>

/**
 * 
 * @package igk\bviewParser\System\IO
 * @author C.A.D. BONDJE DOUE
 */
class BviewFileHandler extends FileHandler
{
    var $option;
    /**
     * transform file context 
     */
    public function transform(string $content, $options = null)
    {
        $n = igk_create_notagnode();
        $builder = new HtmlNodeBuilder($n);
        $tab = BviewParser::ParseFromContent($content);
        $builder($tab->data, null, $options);
        list($ctrl) = igk_extract($options ?? [], 'ctrl');
        if ($tab->directives && ($doc = $ctrl->getCurrentDoc())) {
            // primary media loading 
            list($title, $media, $import) = igk_extract($tab->directives, 'title|media|import');
            if ($import) {
                $this->loadImport($import, $ctrl, $options, false);
            }

            if ($title)
                $doc->setTitle(sprintf($title, igk_configs()->website_domain));
            if ($media) {
            }

            igk_hook('bview://bind_directive', [
                'n' => $n,
                'directives' => $tab->directives,
                'options' => $options,
                'document' => $doc
            ]);
        }
        return $n;
    }
    /**
     * 
     * @param string|string[] $import 
     * @param BaseController $ctrl 
     * @param mixed $options 
     * @param bool $no_cache 
     * @return void 
     */
    public function loadImport($import, BaseController $ctrl, $options, bool $no_cache)
    {
        if (!is_array($import)) {
            $import = [$import];
        }
        $import = array_unique($import);
        $p = ['./' => ViewHelper::Dir(), '@/' => $ctrl->getViewDir()];
        while (count($import) > 0) {
            $f = array_shift($import);
            foreach ($p as $k => $v) {
                if (igk_str_startwith($f, $k)) {
                    $f = Path::Combine($v, substr($f, strlen($k)));
                    break;
                }
            }
            if ($f) {
                igk_include_view_file($ctrl, $f, $no_cache);
            }
        }
    }
    /**
     * 
     * @return null|string 
     */
    public function initDefaultSource(): ?string
    {
        $sb = new StringBuilder();
        $sb->appendLine('/* bview file */');
        $sb->appendLine('# @author ' . IGK_AUTHOR);
        $sb->appendLine('# @version 1.0');
        $sb->appendLine('# @date ' . date('Y-m-d'));
        $sb->appendLine("main.section{}");
        return $sb . '';
    }
}
