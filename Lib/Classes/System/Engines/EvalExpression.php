<?php
// @author: C.A.D. BONDJE DOUE
// @file: EvalExpression.php
// @date: 20240122 12:55:59
namespace igk\bviewParser\System\Engines;

use Exception;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Traits\NoDebugTrait; 
use IGK\System\Html\IHtmlNodeEvaluableExpression;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
* expression to evaluate on rendering context 
* @package igk\bviewParser\System\Engines
* @author C.A.D. BONDJE DOUE
*/
class EvalExpression implements IHtmlNodeEvaluableExpression{
    use NoDebugTrait;
    private $m_value;
    public function __construct(string $content)
    {
        $this->m_value = $content;
    }
    /**
     * 
     * @return null|string 
     */
    public function getValue(): ?string {
        return $this->m_value;
    }
    /**
     * 
     * @param mixed|array $context 
     * @return mixed 
     * @throws Exception 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function evaluate($context){ 
        return ExpressionEvalEngine::EvalBindingExpression($this->m_value,(array)$context);
    }
}