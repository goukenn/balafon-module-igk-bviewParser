<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExpressionBHtmlEngine.php
// @date: 20240116 15:56:48
namespace igk\bviewParser\System\Engines;
use IGKException;
use Exception;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;
///<summary></summary>
/**
 * 
 * @package igk\bviewParser\System\Engines
 * @author C.A.D. BONDJE DOUE
 */
class ExpressionBHtmlEngine extends ExpressionEngineBase
{
    /**
     * eval expression
     * @param string $content 
     * @param mixed $options 
     * @return null|string 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function evalExpression(string $content, $options = null): ?string
    {
        $n = igk_create_notagnode(); 
        $n->load($content, (array)$options); 
        return $n->render();
    }
}