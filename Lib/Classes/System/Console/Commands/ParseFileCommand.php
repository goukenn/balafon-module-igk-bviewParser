<?php
// @author: C.A.D. BONDJE DOUE
// @file: ParseFileCommand.php
// @date: 20240210 20:37:29
namespace igk\bviewParser\System\Console\Commands;
use igk\bviewParser\System\IO\BviewParser;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class ParseFileCommand extends AppExecCommand{
	var $command="--bview:parse";
	var $desc="parse bview file to html";
	var $category="bview";
	var $options=[];
	var $usage='filename [option]';
	public function exec($command, ?string $filename=null) { 
		$d = file_get_contents($filename);
		$g = BviewParser::ParseFromContent($d);
		Logger::print($g);
	}
}