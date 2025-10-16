<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20240115 10:42:02
namespace igk\bviewParser\Tests;
use IGK\Tests\BaseTestCase;
///<summary></summary>
/**
* 
* @package igk\bviewParser\Tests
* @author C.A.D. BONDJE DOUE
*/
abstract class ModuleTestBase extends BaseTestCase{
	public static function setUpBeforeClass(): void{
	   igk_require_module('igk/bviewParser');
	}
}