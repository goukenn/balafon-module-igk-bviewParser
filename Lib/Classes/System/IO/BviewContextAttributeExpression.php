<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewContextAttributeExpression.php
// @date: 20240122 11:16:43
namespace igk\bviewParser\System\IO;
use igk\bviewParser\System\Engines\ExpressionEvalEngine;
use IGK\System\Html\IHtmlNodeConditionEvaluableAttribute;
use IGK\System\Traits\NoDebugTrait;
///<summary></summary>
/**
* 
* @package igk\bviewParser\System\IO
* @author C.A.D. BONDJE DOUE
*/
class BviewContextAttributeExpression implements IHtmlNodeConditionEvaluableAttribute{
    use NoDebugTrait;
    private $m_value;
    public function __construct(string $value)
    {
        $this->m_value = $value;
    }
    public function evaluate($context): bool { 
        return ExpressionEvalEngine::EvalBindingExpression($this->m_value, (array)$context);
    }
    public function __toString()
    {
        return $this->m_value;
    } 
}