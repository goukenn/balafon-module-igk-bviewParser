<?php
// @author: C.A.D. BONDJE DOUE
// @file: %modules%/igk/bviewParser/.global.php
// @date: 20240115 10:42:02
use IGK\Helper\ViewHelper;
use IGK\System\IO\Path;
use igk\bviewParser\System\IO\BviewFileHandler;
use IGK\Controllers\BaseController;
// + module entry file 
if (!function_exists('igk_html_node_bview')){
    /**
     * import bview context
     * @param string $file bview file
     * @param ?BaseController base controller
     * @param ?array $args extra params 
     */
    function igk_html_node_bview(string $file, ?BaseController $source=null, $args=null){
        $source = $source ?? ViewHelper::CurrentCtrl();
        $t = Path::Combine(ViewHelper::Dir(), $file);
        $args = $args ?? ViewHelper::GetViewArgs('data'); 
        $handler = new BviewFileHandler; 
        $src = file_get_contents($t); 
        return $handler->transform($src, (object)['ctrl'=>$source, 'raw'=>$args]);
    }
}
// + module entry file 
if (!function_exists('igk_html_node_ibview')){
    /**
     * import inline bview 
     * @param string $file bview file
     * @param ?BaseController base controller
     * @param ?array $args extra params 
     */
    function igk_html_node_ibview(string $source, ?BaseController $ctrl=null, $args=null){
        $ctrl = $ctrl ?? ViewHelper::CurrentCtrl();
        $args = $args ?? ViewHelper::GetViewArgs('data'); 
        $handler = new BviewFileHandler; 
        return $handler->transform($source, (object)['ctrl'=>$ctrl, 'raw'=>$args]);
    }
}