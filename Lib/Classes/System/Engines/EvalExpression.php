<?php
// @author: C.A.D. BONDJE DOUE
// @file: EvalExpression.php
// @date: 20240122 12:55:59
namespace igk\bviewParser\System\Engines;
use IGK\System\Traits\NoDebugTrait; 
use IGK\System\Html\IHtmlNodeEvaluableExpression;
///<summary></summary>
/**
* 
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
    public function getValue(): ?string {
        return $this->m_value;
    }
    public function evaluate($context){ 
        return ExpressionEvalEngine::EvalBindingExpression($this->m_value,(array)$context);
    }
}