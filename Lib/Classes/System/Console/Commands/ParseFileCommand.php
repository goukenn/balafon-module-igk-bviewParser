<?php
// @author: C.A.D. BONDJE DOUE
// @file: ParseFileCommand.php
// @date: 20240210 20:37:29
namespace igk\bviewParser\System\Console\Commands;
use igk\bviewParser\System\IO\BviewParser;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\HtmlNodeBuilder;

///<summary></summary>
/**
* parsing bview file 
* @package igk\bviewParser\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class ParseFileCommand extends AppExecCommand{
	var $command="--bview:parse";
	var $desc="parse bview file to html";
	var $category="bview";
	var $options=[
		'-f'=>'json data filename'
	];
	var $usage='filename json_data*  [option]';
	public function exec($command, ?string $filename=null, $data=null) { 
		$filename || igk_die('required filename');
		if (is_null($data) && property_exists($command->options, '-f')){
			$cf = igk_getv($command->options, '-f');
			if (file_exists($cf)){
				$data = file_get_contents($cf);
			}
		}

		igk_reg_component_package('igk-com', function(string $name){
			igk_wln_e("create domp : ", $name);
		});
		$d = file_get_contents($filename);
		$data = json_decode($data ?? '[]', true);
		if ($g = BviewParser::ParseFromContent($d)){
			$n = igk_create_notagnode();
			$builder = new HtmlNodeBuilder($n);
			$builder($g->data, null, $data);
			Logger::print($n->render());
		}
	}
}