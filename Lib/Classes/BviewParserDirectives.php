<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewParserDirectives.php
// @date: 20251229 13:21:23
namespace igk\bviewParser;


/**
* store bview parser directives 
* @package igk\bviewParser
* @author C.A.D. BONDJE DOUE
*/
class BviewParserDirectives{
    /**
     * view page title
     * @var ?string
     */
    var $title;
    /**
     * view author
     * @var ?string
     */
    var $author;
    /**
     * view version
     * @var ?string
     */
    var $version;

    /**
     * creation date
     * @var ?string 
     */
    var $date;

    /**
     * default namespace
     * @var ?string
     */
    var $namespace;

    /**
     * 
     * @var mixed
     */
    var $import;
    
    public function __construct()
    {
        throw new \Exception('Not implemented');
    }
}