<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewParser.php
// @date: 20240115 10:45:51
namespace igk\bviewParser\System\IO;

use Exception;
use igk\bviewParser\System\Engines\EngineExpressionReaderFactory;
use igk\bviewParser\System\Engines\ExpressionEngineBase;
use igk\bviewParser\System\Exception\BviewSyntaxException;
use igk\bviewParser\System\Html\Dom\BindingAttributeBase;
use IGK\System\Console\Logger;
use IGK\System\Html\HtmlNodeBuilder;
use IGK\System\Html\IHtmlNodeEvaluableExpression;
use IGK\System\Html\Traits\HtmlNodeTagExplosionTrait;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;
use function igk_str_startwith as str_starts_with;
///<summary></summary>
/**
 * 
 * @package igk\bviewParser\System\IO
 * @author C.A.D. BONDJE DOUE
 */
class BviewParser
{
    use HtmlNodeTagExplosionTrait;
    protected $split = '>';
    protected static $sm_errors = [];
    /**
     * 
     * @var ?BviewParserStateInfo
     */
    protected $m_state;
    protected $m_source;
    var $data;
    var $option;
    /**
     * bview special directive 
     * @var ?array
     */
    var $directives;
    /**
     * special declarations
     * @var mixed
     */
    var $specials;
    /**
     * 
     * @var mixed
     */
    private $m_listener;
    private $m_tokens = [];
    public function setListener(?IBviewListener $listener)
    {
        $this->m_listener = $listener;
    }
    protected function __construct()
    {
        $this->data = [];
    }
    /**
     * parse bview 
     * @param string $content 
     * @param null|IBviewParserOptions $option 
     * @return static
     */
    public static function ParseFromContent(string $content, ?IBviewParserOptions $option = null)
    {
        $g = new static;
        $g->option = $option;
        $g->parse($content);
        return $g;
    }
    public static function GetErrors()
    {
        $tab = self::$sm_errors;
        return $tab;
    }
    protected function getState()
    {
        return $this->m_state;
    }
    private function _ReadVar(string $content, &$pos): string
    {
        throw new \Exception('not implement');
        $s = '';
        return $s;
    }
    private function _ReadFlags(string $content, &$pos, &$v_ltoken)
    {
        switch ($this->m_state->flag) {
            case BviewFlags::READ_ATTRIBUTE:
                $v_v = self::_ReadExpression($content, $pos, $this->m_state);
                if (in_array($v_v, ['nil', 'null'])) {
                    $v_v = null;
                } else if (in_array($v_v, ['true', 'false'])) {
                    $v_v = igk_bool_val($v_v); // null;
                }
                $v_ltoken = [BviewTokens::TOKEN_ATTRIBUTE_VALUE, $v_v];
                $this->m_state->flag = 0;
                break;
        }
    }
    /**
     * read attribute expression
     * @param string $content 
     * @param mixed $pos 
     * @param mixed $state 
     * @return string 
     */
    private static function _ReadExpression(string $content, &$pos, $state)
    {
        $ln = strlen($content);
        $v_litteral = false;
        $v_end = false;
        $v_expression = false;
        $v = '';
        while (!$v_end && ($pos < $ln)) {
            $ch = $content[$pos];
            switch ($ch) {
                case '"':
                case '\'':
                    $v_litteral = true;
                    if (empty(trim($v))) {
                        $v = '';
                    }
                    $v .= igk_str_read_brank($content, $pos, $ch, $ch, null, true);
                    $ch = '';
                    # code...
                    break;
                case "\n":
                    if (($v_litteral) || ($v_expression) || !empty($v = trim($v))) {
                        $v_end = true;
                        $ch = '';
                        $pos--;
                    }
                    $state->line++;
                    break;
                case '(':
                    $v_name = trim($v);
                    if (empty($v_name)) {
                        $v_name = 'html';
                    }
                    $expression_reader = EngineExpressionReaderFactory::Create($v_name);
                    $v = $expression_reader->read($content, $pos, ')', '(');
                    $v = new BviewExpression($v_name, $v);
                    $v_expression = true;
                    return $v;
                case '[':
                    $ts = trim($v);
                    if (empty($ts)) {
                        $ch = '';
                        // parse array 
                        $bs = igk_str_read_brank($content, $pos, ']', '[');
                        $tab = json_decode($bs);
                        $v_end = true;
                        $v = $tab;
                        return $v;
                    }
                    break;
                case '{':
                    $ts = trim($v);
                    if (empty($ts)) {
                        if (($pos + 1 < $ln) && ($content[$pos + 1] != '{')) {
                            $ch = '';
                            // parse array 
                            $bs = igk_str_read_brank($content, $pos, '}', '{');
                            $tab = json_decode($bs);
                            $v_end = true;
                            $v = $tab;
                            return $v;
                        }
                    } else {
                        $v .= igk_str_read_brank($content, $pos, '}', '{');
                        $ch = '';
                    }
                    break;
            }
            $v .= $ch;
            $pos++;
        }
        if (is_string($v) && preg_match("/\{\{(?P<data>.+)\}\}/", $v)) {
            //+ | contain mustache expression
            $v = new BviewExpression('eval', $v); //match['data']); 
        }
        return $v;
    }
    /**
     * regex bview container
     * @return RegexMatcherContainer 
     * @throws IGKException 
     * @throws Exception 
     */
    protected function _globalRegexDetector(): RegexMatcherContainer
    {
        $r = new RegexMatcherContainer;
        $r->begin('\/\*', '\*\/', 'multi-comment');
        $r->match("# +@([a-zA-Z_][a-zA-Z0-9_\-:]*) (.+)", 'g-expression');
        return $r;
    }
    protected function _fchandleToken($v_ltoken, &$v)
    {
        if (!empty($v = trim($v))) {
            $this->_handleToken([BviewTokens::TOKEN_VALUE, $v]);
        }
        $this->_handleToken($v_ltoken);
        $v = '';
    }
    /**
     * parse content
     */
    public function parse(string $content)
    {
        $ln = strlen($content);
        $pos = 0;
        $ch = $v = '';
        $this->m_state = new BviewParserStateInfo;
        $this->m_source = $content;
        $v_ltoken = null;
        $v_skip = false;
        // + | --------------------------------------------------------------------
        // + | remove special single comment meaning
        // + |
        $loop = 1;
        $specials = [];
        while ($loop && (false !== ($pos = strpos($content, "\n")))) {
            $line = trim(substr($content, 0, $pos));
            if (empty($line) || str_starts_with($line, '// ')) {
                if (!empty($line)) {
                    $specials[] = $line;
                }
                $content = substr($content, $pos + 1);
            } else {
                $loop = 0;
            }
        }
        if ($specials) {
            $this->specials = $specials;
        }
        $v_global_regex = $this->_globalRegexDetector();
        $pos = 0;
        $ln = strlen($content);
        $coffset = 0;
        while ($pos < $ln) {
            $v_ltoken = null;
            if ($this->m_state->mode == BviewParserStateInfo::MODE_READ_GLOBAL) {
                $lopos = $pos;
                if ($g = $v_global_regex->detect($content, $pos)) {
                    if ($e = $v_global_regex->end($g, $content, $pos)) {
                        if (false === empty(trim(substr($content, $coffset, $e->from - $coffset)))) {
                            $pos = $lopos;
                        } else {
                            switch ($e->tokenID) {
                                case 'multi-comment':
                                    if (is_null($e->info->endType)){
                                        throw new IGKException('invalid comment');
                                    }
                                    $v_ltoken = [BviewTokens::TOKEN_COMMENT, $e->value];
                                    break;
                                case 'g-expression':
                                    if (!$this->directives) {
                                        $this->directives = [];
                                    }
                                    $this->directives[$e->beginCaptures[1][0]] = $e->beginCaptures[2][0];
                                    break;
                            }
                            if ($v_ltoken) {
                                $this->_fchandleToken($v_ltoken, $v);
                            }
                            continue;
                        }
                    }
                } else {
                    $pos = $lopos;
                }
            }
            $ch = $content[$pos];
            $v_ltoken = null;
            if ($this->m_state->mode != BviewParserStateInfo::MODE_READ_GLOBAL) {
                $this->m_state->expressionInfo->handle($content, $pos, $v_ltoken);
            } else if ($this->m_state->flag) {
                $this->_ReadFlags($content, $pos, $v_ltoken);
            } else {
                switch ($ch) {
                    case ' ':
                        if (!$v_skip) {
                            $v_skip = true;
                            $v .= $ch;
                        }
                        $ch = '';
                        break;
                    case "\n":
                        $this->m_state->line++;
                        $this->m_state->column = 0;
                        if ($this->m_state->definition && !empty($attrs = trim($v))) {
                            if ($attrs[0] == '@') {
                                $v_activate = &$this->m_state->definition->data;
                                if (!isset($v_activate[HtmlNodeBuilder::KEY_ATTRIBS_ACTIVATION])) {
                                    $v_activate[HtmlNodeBuilder::KEY_ATTRIBS_ACTIVATION] = [];
                                }
                                $m = &$v_activate[HtmlNodeBuilder::KEY_ATTRIBS_ACTIVATION];
                                array_map(function ($attr) use (&$m) {
                                    $m[] = substr($attr, 1);
                                }, explode(' ', $attrs));
                                $ch = '';
                                $v = '';
                            }
                        }
                        break;
                    case '$':
                        /// TODO: READ VAR 
                        $pos++;
                        $v_var = self::_ReadVar($content, $pos);
                        break;
                    case '{':
                    case '}':
                        if ($ch == '{') {
                            $v_next = $pos + 1;
                            if (($v_next < $ln) && ($content[$v_next] == '{')) {
                                // + | mustache
                                $v_ltoken = [BviewTokens::TOKEN_BEGIN_BRANK_EXPRESSION, $ch . $ch];
                                $pos++;
                                $this->m_state->expression = true;
                            }
                        }
                        if ($ch == '{') {
                            $this->m_state->depth++;
                        } else {
                            $this->m_state->depth--;
                        }
                        $v_ltoken = [BviewTokens::TOKEN_BRANK, $ch];
                        $ch = '';
                        break;
                    case '-':
                        // + | binding text definition or call expression
                        if (empty(trim($v)) && ($content[$pos + 1] == ' ')) {
                            $pos += 2;
                            $v = $this->_readTextExpression($content, $pos, $this->m_state);
                            // $v = self::_ReadExpression($content, $pos, $this->m_state);
                            // $sub = strpos($content, "\n", $pos + 1);
                            // if ($sub === false){
                            //     // + | read to end
                            //     $sub = $ln;
                            // }
                            // $data = trim(substr($content, $pos + 1, $sub - $pos + 1));
                            // $v_ltoken = [BviewTokens::TOKEN_TEXT, $data];
                            $v_ltoken = [
                                $v instanceof BviewExpression ?
                                    BviewTokens::TOKEN_EXPRESSION :  BviewTokens::TOKEN_TEXT,
                                $v
                            ];
                            $ch = '';
                            $v = '';
                        }
                        break;
                    case '+':
                        // + | consider it as a node expression event not found
                        if (empty(trim($v)) && ($content[$pos + 1] == ' ')) {
                            $pos += 2;
                            $v = self::_ReadSelectorExpression($content, $pos, $this->m_state);
                            $v_ltoken = [BviewTokens::TOKEN_ADD_SELECTOR, $v];
                            $ch = '';
                            $v = '';
                        }
                        break;
                    case ':':
                        if (!self::_EscapeSelector($content, $pos, $v) && (!$this->m_state->flag)) {
                            if (!empty($v = trim($v))) {
                                $v_ltoken = [BviewTokens::TOKEN_ATTRIBUTE, $v];
                                $ch = '';
                                $v = '';
                                $this->m_state->flag = BviewFlags::READ_ATTRIBUTE;
                            }
                        }
                        break;
                    case '/':
                        if (($pos + 1) < $ln && ($content[$pos + 1] == '*')) {
                            // read multiline comment
                            if (($v_epos = strpos($content, "*/", $pos + 1)) !== false) {
                                $v_d = substr($content, $pos, $v_epos - $pos + 2);
                                $v_ltoken = [BviewTokens::TOKEN_COMMENT, $v_d];
                                $ch = '';
                                $pos = $v_epos + 2;
                            } else {
                                igk_die('unterminated comment');
                            }
                        }
                        break;
                    case '"':
                    case '\'':
                        $v_d = igk_str_read_brank($content, $pos, $ch, $ch);
                        self::_UpdateColumnAndLineState($this->m_state, $v_d);
                        if (empty($v)) {
                            $v_ltoken = [BviewTokens::TOKEN_LITTERAL, $v_d];
                        } else {
                            $v .= $v_d;
                        }
                        $ch = '';
                        break;
                    case '`':
                        // read multiline expression
                        $v_d = igk_str_read_brank($content, $pos, $ch, $ch);
                        $v_lines = count($lines = explode("\n", $v_d));
                        if ($v_lines > 1){
                            $this->m_state->line += $v_lines - 1;
                        }
                        $this->m_state->column = strlen(igk_array_last($lines));
                        $ch = '';
                        break;
                    case '[':
                        //     if (!$this->m_state->definition){
                        //         throw new BviewSyntaxException($this->m_state, 'Invalid array declaration');
                        //     }
                        //+ | skip reading attribute var so stat ':' seperator will be skipped
                        $v_d = igk_str_read_brank($content, $pos, ']', '[');
                        self::_UpdateColumnAndLineState($this->m_state, $v_d);
                        $v .= $v_d;
                        $ch = '';
                        break;
                    case '(':
                        if (!self::_EscapeSelector($content, $pos, $v)){
                          $v_d = igk_str_read_brank($content, $pos, ')', '(');
                          $v.= $v_d;
                          $ch='';
                        }
                        //igk_wln_e(__FILE__.":".__LINE__ , 'start read brank');
                        break;
                }
            }
            if ($v_ltoken) {
                $this->_fchandleToken($v_ltoken, $v);
            } else {
                $v .= $ch;
            }
            if (($v_skip) && ($ch != '')) {
                $v_skip = false;
            }
            $pos++;
            $this->m_state->column++;
        }
        if (!empty($v = trim($v))) {
            $this->data[$v] = [];
        }
    }
    private $m_textExpression;

    /**
     * init expression used to read text
     * @return RegexMatcherContainer 
     * @throws IGKException 
     * @throws Exception 
     */
    protected function _initTextExpression()
    {
        $rg = new RegexMatcherContainer;
        $brank = $rg->createPattern(['begin' => '\(', 'end' => '\)', 'brank']);
        $args = $rg->createPattern(['begin' => '(?<=,)', 'end' => '(?=\))', 'args']);
        $brank->patterns = [$args, $brank];
        $expression = $rg->createPattern(['begin' => '\{\{', 'end' => '\}\}', 'tokenID' => 'expression']);
        $type = $rg->begin('([a-zA-Z]+)\\s*(?=\()', '(?<=\))', 'data-type')->last();
        $type->patterns = [
            $brank
        ];
        // $m = $rg->begin('(?=.)', '(?=\n)|(?<=\\})', 'rf')->last();
        $m = $rg->begin('(?=.)', '(?=\n)', 'rf')->last();
        $end_expresss = $rg->match("(?=\\})", "end_express" )->last();
        $m->patterns = [
            $expression,
            $end_expresss
        ];
        $rg->append($expression);
        return $rg;
    }
    /**
     * read text expression 
     * @param string $source 
     * @param int &$pos 
     * @param mixed $state 
     * @return string 
     * @throws IGKException 
     * @throws Exception 
     */
    protected function _readTextExpression(string $source, int &$pos, $state)
    {
        $texp = $this->m_textExpression = $this->m_textExpression ?? $this->_initTextExpression();
        $lpos = $pos;
        $v = '';
        $expressions = [];
        $args = [];
        $end_express = false;
        $end_pos = -1;
        while ($g = $texp->detect($source, $pos)) {
            if ($e = $texp->end($g, $source, $pos)) {
                igk_is_debug() && Logger::info('token: '.$e->tokenID);
                if (is_null($e->parentInfo)) {
                    $lpos = $pos;
                    $v = $e->value;
                    if ($e->tokenID == 'data-type') {
                        $v_name = $e->beginCaptures[0][0];
                        $expression_reader = EngineExpressionReaderFactory::Create($v_name);
                        $content = substr($v, strpos($v, '('));
                        $tpos = 0;
                        $v = $expression_reader->read($content, $tpos, ')', '(');
                        $v = new BviewExpression($v_name, $v);
                        $args = [];
                    }
                    if ($e->tokenID == 'rf') {
                        $v = trim($v);
                        if ($end_express){
                            $v = substr($source, $e->from, ($end_pos -$e->from)-1);
                            $v = rtrim($v, '}');
                            $lpos = $pos = $end_pos - 1;
                        }
                    }
                    break;
                } else {
                    if (($e->tokenID == 'end_express') && !$end_express){
                        $end_express = true;
                        $end_pos = $e->from;
                    }
                    if ($e->tokenID == 'expression') {
                        $expressions[trim($e->value . '')] = 1; // null;
                    } else if ($e->tokenID == 'args') {
                        $args[] = $e->value;
                    }
                }
            }
        }
        $pos = $lpos;
        if ($expressions) {
            $v = new BviewExpression('eval', $v);
        }
        return $v;
    }
    private static function _ReadSelectorExpression(string $content, &$pos, $state)
    {
        $ln = strlen($content);
        $v = '';
        $end = false;
        while (!$end && ($pos < $ln)) {
            $ch = $content[$pos];
            if ($ch == '{') igk_die("invalid syntaxe");
            if ($ch == "\n") {
                $end = true;
                $ch = '';
                break;
            }
            switch ($ch) {
                case ':':
                    self::_EscapeSelector($content, $pos, $v, $ch);
                    break;
                case '(':
                    $v .= igk_str_read_brank($content, $pos, ')', '(');
                    $ch = '';
                    break;
                case '[':
                    $v .= igk_str_read_brank($content, $pos, ']', '[');
                    $ch = '';
                    break;
                case '"':
                case "'":
                    $v .= igk_str_read_brank($content, $pos, $ch, $ch);
                    $ch = '';
                    break;
            }
            $v .= $ch;
            $pos++;
        }
        return trim($v);
    }
    private static function _EscapeSelector(string $content, int &$pos, &$v)
    {
        if (($pos > 0) && ($content[$pos - 1] == '\\')) {
            $v = rtrim($v, '\\');
            return true;
        }
        return false;
    }
    /**
     * update state line info
     * @param mixed $state 
     * @param mixed $v 
     * @return void 
     */
    protected function _UpdateColumnAndLineState(BviewParserStateInfo $state, string $v, $column_offset = -1)
    {
        $v_line = explode("\n", $v);
        $v_tcount = count($v_line);
        if ($v_tcount > 1) {
            $state->line += ($v_tcount - 1);
            $state->column = strlen($v_line[$v_tcount - 1]) - $column_offset;
        } else {
            $state->column += strlen($v) - $column_offset;
        }
    }
    protected function _handleToken($token)
    {
        $this->m_tokens[] = $token;
        $this->handleToken($token);
    }
    /**
     * get bindint attribute handler
     * @param string $attribute 
     * @return null|BindingAttributeBase 
     * @throws IGKException 
     */
    public function getBindingAttributeHandler(string $attribute)
    {
        $cl = \igk\bviewParser\System\Html\Dom\BindingAttributeFactory::GetBindingAttributeHandler($attribute);
        return $cl;
    }
    /**
     * handle token
     */
    protected function handleToken($token)
    {
        $e = $token;
        $v_listener = $this->m_listener;
        $v_data = null;
        $v_def = $this->m_state->definition;
        if ($v_def && $v_def->parent)
            $v_data =  &$v_def->parent->data;
        else
            $v_data =  &$this->data;
        switch ($e[0]) {
            case BviewTokens::TOKEN_COMMENT:
                break;
            case BviewTokens::TOKEN_FUNC:
                break;
            case BviewTokens::TOKEN_VAR:
                break;
            case BviewTokens::TOKEN_ATTRIBUTE:
                $this->m_state->attribute = $e[1];
                break;
            case BviewTokens::TOKEN_ATTRIBUTE_VALUE:
                if (!isset($this->m_state->definition->data['_'])) {
                    $this->m_state->definition->data['_'] = [];
                }
                $attrib = &$this->m_state->definition->data['_'];
                $v_attr_key = $this->m_state->attribute;
                $v_attr_value = $this->evalAttributeExpression($e[1]);
                if (strpos($v_attr_key, "*") === 0) {
                    // special attribute expression 
                    if ($v_attr_key == '*if') {
                        // + | conditional key binding - transform to attribute key definition 
                        if ($v_attr_value instanceof IHtmlNodeEvaluableExpression) {
                            $v_attr_value = $v_attr_value->getValue();
                        }
                        $this->m_state->definition->data[HtmlNodeBuilder::KEY_CONDITION] =
                            new BviewContextAttributeExpression($v_attr_value);
                    } else {
                        $binding_attr = $this->getBindingAttributeHandler(substr($v_attr_key, 1));
                        // key binding 
                        if (is_null($binding_attr)) {
                            throw new BviewSyntaxException($this->m_state, sprintf('%s not allowed', $v_attr_key));
                        } else {
                            $binding_attr->bindAttribute($this->m_state->definition, $v_attr_value);
                        }
                    }
                } else {
                    $attrib[$v_attr_key] = $v_attr_value; //this->evalAttributeExpression($e[1]);
                }
                $this->m_state->attribute = null;
                break;
            case BviewTokens::TOKEN_EXPRESSION:
                $this->m_state->definition->data[] = $this->evalAttributeExpression($e[1]);
                break;
            case BviewTokens::TOKEN_VALUE:
                $this->m_state->key = $e[1];
                break;
            case BviewTokens::TOKEN_BRANK:
                if ($e[1] == '}') {
                    $v_def = $this->m_state->definition;
                    if ($v_def && $this->m_state->key) {
                        $v_d = $v_def->data;
                        $v_key = $this->m_state->key;
                        // + | convert to single entry value string
                        if (!empty($v_d) && ((count($v_d) == 1) && (key($v_d) === 0))) {
                            $v_d = $v_d[0];
                        }
                        if (isset($v_data[$this->m_state->key])) {
                            // + | append tag block
                            $v_data[] = ['@_t:' . $v_key => $v_d];
                        } else
                            $v_data[$v_key] = $v_d;
                    }
                    $this->_moveTop();
                    if ($this->m_state->depth === 0) {
                        $this->m_state->key = null;
                    }
                } else {
                    // start token
                    if ($this->m_state->key) {
                        // sub token start 
                        $v_parent = $this->m_state->definition; //? $this->m_state->definition->parent : null;
                        $this->m_state->definition = new BviewDefinition;
                        $this->m_state->definition->key = $this->m_state->key;
                        $this->m_state->definition->parent = $v_parent;
                        // if (igk_is_debug())
                        // {
                        //     $defs = [];
                        //     $path = $this->m_state->getFullSelectorPath();
                        //     if ($path){
                        //         $v_tv = '';
                        //         $this->explodeTagDefinition($path, $defs, $v_tv);
                        //         if ($defs){
                        //             // detect in template definition
                        //         }
                        //         igk_debug_wln($path);
                        //     }
                        // }  
                    }
                }
                break;
            case BviewTokens::TOKEN_TEXT:
                if ($this->m_state->definition) {
                    $this->m_state->definition->data[] = $e[1]; // this->m_state->definition->data; 
                } else {
                    igk_die("missing definition");
                }
                //$this->m_state->definition->data[] = $e[1];
                break;
            case BviewTokens::TOKEN_ADD_SELECTOR:
                if ($this->m_state->definition) {
                    $v_v = $e[1];
                    if (isset($this->m_state->definition->data[$v_v])) {
                        $v_v = [$v_v];
                        $this->m_state->definition->data[] = $v_v;
                    } else {
                        $this->m_state->definition->data[$v_v] = [];
                    }
                } else {
                    igk_die("missing definition");
                }
                break;
        }
        if ($v_listener) {
            $v_listener->handleToken($token);
        }
    }
    private function _moveTop()
    {
        if ($this->m_state->definition) {
            $this->m_state->definition = $this->m_state->definition->parent;
            $this->m_state->key = $this->m_state->definition ? $this->m_state->definition->key : null;
        }
    }
    /**
     * evaluate attribute expression
     * @param mixed $expression 
     * @return string|array|object|void 
     */
    public function evalAttributeExpression($expression)
    {
        if (is_string($expression))
            return $expression;
        if ($expression instanceof BviewExpression) {
            $engine = ExpressionEngineBase::Factory($expression->name) ?? igk_die('factory BviewExpression missing .(' . $expression->name . ')');
            return $engine->evalExpression($expression->value, $this->getExpressionOptions());
        }
        return $expression;
    }
    /**
     * Bview expression option 
     * @return mixed 
     */
    public function getExpressionOptions()
    {
        return $this->option;
    }
}
