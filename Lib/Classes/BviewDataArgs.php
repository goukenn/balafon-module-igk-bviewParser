<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewDataArgs.php
// @date: 20251021 21:02:31
namespace igk\bviewParser;

use IGK\System\DataArgs;

/**
* 
* @package igk\bviewParser
* @author C.A.D. BONDJE DOUE
*/
class BviewDataArgs extends DataArgs{
    public function __construct($tab)
    {
        $raw = igk_getv($tab, 'raw');
        if (!($raw instanceof DataArgs)){
            $raw = new DataArgs($raw);
            $tab['raw'] = $raw;
        }
        parent::__construct($tab);
    }
    public function __get($name){
        if ($name=='context'){
            return $this;
        }
        return parent::__get($name);
    }
}