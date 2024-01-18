<?php
// @author: C.A.D. BONDJE DOUE
// @file: BviewParser.php
// @date: 20240115 10:45:51
namespace igk\bviewParser\System\IO;

use igk\bviewParser\System\Engines\ExpressionEngineBase;
use igk\bviewParser\System\Exception\BviewSyntaxException;
use IGK\System\Html\HtmlNodeBuilder;

///<summary></summary>
/**
 * 
 * @package igk\bviewParser\System\IO
 * @author C.A.D. BONDJE DOUE
 */
class BviewParser
{
    protected static $sm_errors = [];
    protected $m_state;
    protected $m_source;
    var $data;
    var $option;
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
     * 
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
    public static function GetErrors(){
        $tab = self::$sm_errors;
        return $tab;
    }
    protected function getState()
    {
        return $this->m_state;
    }
    private function _ReadVar(string $content, &$pos): string
    {
        $s = '';
        return $s;
    }
    private function _ReadFlags(string $content, & $pos, & $v_ltoken){
        switch($this->m_state->flag){
            case BviewFlags::READ_ATTRIBUTE:
                $v_v = self::_ReadExpression($content, $pos, $this->m_state);
                if (in_array($v_v, ['nil','null'])){
                    $v_v = null;
                }else if (in_array($v_v, ['true','false'])){
                    $v_v = igk_bool_val($v_v);// null;
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
    private static function _ReadExpression(string $content, & $pos, $state){
        $ln = strlen($content);
        $v_litteral = false;
        $v_end = false;
        $v_expression = false;
        $v = '';
        while( !$v_end && ($pos < $ln)){
            $ch = $content[$pos];
            switch ($ch) {
                case '"':
                case '\'':
                    $v_litteral = true;
                    $v .= igk_str_read_brank($content, $pos, $ch, $ch);
                    $ch = '';
                    # code...
                    break;
                case "\n":
                    if (($v_litteral)||($v_expression) || !empty( $v = trim($v))){
                        $v_end = true;
                        $ch = '';
                        $pos--;
                    }
                    $state->line++;
                    break;
                case '(':
                    $v_name = trim($v);
                    $v = igk_str_read_brank($content, $pos, ')', $ch);
                    if (empty($v_name)){
                        $v_name = 'html';
                    }
                    $v = igk_str_rm_last(igk_str_rm_start($v, '('), ')');

                    $v = new BviewExpression($v_name, $v);
                    $v_expression = true;
                    return $v; 
                case '[':
                    $ts = trim($v);
                    if (empty($ts)){
                        $ch = '';
                        // parse array 
                        $bs = igk_str_read_brank($content, $pos, ']','[');
                        $tab = json_decode($bs);
                        $v_end = true;
                        $v = $tab;
                        return $v;
                    }
                    break;
                case '{':
                    $ts = trim($v);
                    if (empty($ts)){
                        if (($pos+1<$ln) && ($content[$pos+1]!='{')){
                            $ch = '';
                            // parse array 
                            $bs = igk_str_read_brank($content, $pos, '}','{');
                            $tab = json_decode($bs);
                            $v_end = true;
                            $v = $tab;
                            return $v;
                        }
                    }
                    break;
            }
            $v .= $ch;
            $pos++;
        }

        if (is_string($v)&& preg_match("/\{\{(?P<data>.+)\}\}/", $v)){
            //+ | contain mustache expression
            $v = new BviewExpression('eval', $v); //match['data']); 
        }
        return $v;
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
        while ($pos < $ln) {
            $ch = $content[$pos];
            $v_ltoken = null;

            if ($this->m_state->mode != BviewParserStateInfo::MODE_READ_GLOBAL) {
                $this->m_state->expressionInfo->handle($content, $pos, $v_ltoken);
            } else if ($this->m_state->flag){
                    $this->_ReadFlags($content, $pos, $v_ltoken);
                
            } else {

                switch ($ch) {
                    case ' ':
                            if (!$v_skip){ 
                                $v_skip = true;
                                $v .= $ch; 
                            }
                            $ch = '';
                        break;
                    case "\n":
                        $this->m_state->line++;
                        $this->m_state->column = 0;
                        if ($this->m_state->definition && !empty($attrs = trim($v))){
                            if ($attrs[0] == '@'){
                                $v_activate = & $this->m_state->definition->data;
                                if (!isset($v_activate[HtmlNodeBuilder::KEY_ATTRIBS_ACTIVATION])){
                                    $v_activate[HtmlNodeBuilder::KEY_ATTRIBS_ACTIVATION] = [];
                                }
                                $m = & $v_activate[HtmlNodeBuilder::KEY_ATTRIBS_ACTIVATION];
                                array_map(function($attr)use( & $m){
                                    $m[] = substr($attr, 1);
                                },explode(' ', $attrs));
                               $ch = '';
                               $v = '';
                            }

                        }
                        break;
                    case '$':
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
                    case '@':
                        break;
                    case '-':
                        // + | binding text definition or call expression
                        if (empty(trim($v)) && ($content[$pos + 1] == ' ')) {
                            $pos+=2;
                            $v = self::_ReadExpression($content, $pos, $this->m_state);

                            // $sub = strpos($content, "\n", $pos + 1);
                            // if ($sub === false){
                            //     // + | read to end
                            //     $sub = $ln;
                            // }
                            // $data = trim(substr($content, $pos + 1, $sub - $pos + 1));
                            // $v_ltoken = [BviewTokens::TOKEN_TEXT, $data];
                            $v_ltoken = [
                                $v instanceof BviewExpression ? 
                                BviewTokens::TOKEN_EXPRESSION :  BviewTokens::TOKEN_TEXT , $v];
                            $ch = '';
                            $v = '';
                            // $pos = $sub;
                        }
                        break;
                    case ':':
                        if (!$this->m_state->flag){
                            if (!empty($v = trim($v))){ 
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

                                $v_d = substr($content, $pos, $v_epos - $pos+2);
                                $v_ltoken = [BviewTokens::TOKEN_COMMENT, $v_d];
                                $ch = '';
                                $pos = $v_epos+2;
                            } else {
                                igk_die('unterminated comment');
                            }
                        }
                        break;
                    case '"':
                    case '\'':
                        $v_d = igk_str_read_brank($content, $pos, $ch, $ch);
                        $this->m_state->column += strlen($v_d);
                        $v_ltoken = [BviewTokens::TOKEN_LITTERAL, $v_d];
                        $ch = '';
                        break;
                    case '`':
                        // read multiline expression
                        $v_d = igk_str_read_brank($content, $pos, $ch, $ch);
                        $this->m_state->column += strlen($v_d);
                        $v_lines = count(explode("\n", $v_d));
                        if ($v_lines > 1) {
                            $this->m_state->line += $v_lines - 1;
                        }
                        $ch = '';
                        break;
                    case '[':
                        $v_d = igk_str_read_brank($content, $pos, ']', '[');
                        self::_UpdateState($this->m_state, $v_d);
                        $v .= $v_d;
                        $ch = '';
                        break;
                }
            }
            if ($v_ltoken) {
                if (!empty($v = trim($v))) {
                    $this->_handleToken([BviewTokens::TOKEN_VALUE, $v]);
                }
                $this->_handleToken($v_ltoken);
                $v = '';
            } else {
                $v .= $ch;
            }
            if (($v_skip) && ($ch!='')){
                $v_skip = false;
            }
            $pos++;
            $this->m_state->column++;
        }
        if (!empty($v=trim($v))) {
            $this->data[$v] = [];
        }
    }
    /**
     * update state line info
     * @param mixed $state 
     * @param mixed $v 
     * @return void 
     */
    protected function _UpdateState(BviewParserStateInfo $state, string $v, $column_offset=-1){
        $v_line = explode("\n", $v);
        $v_tcount = count($v_line);
        if ($v_tcount>1){
            $state->line += ($v_tcount-1);
            $state->column = strlen($v_line[$v_tcount-1])-$column_offset;
        }else{
            $state->column+= strlen($v)-$column_offset;
        }
    }
    protected function _handleToken($token)
    {
        $this->m_tokens[] = $token;
        $this->handleToken($token);
    }
    /**
     * handle token
     */
    protected function handleToken($token)
    {
        $e = $token;
        $v_listener = $this->m_listener;
        $v_data = null;
        if ($this->m_state->definition && $this->m_state->definition->parent)
            $v_data =  & $this->m_state->definition->parent->data;
        else
            $v_data =  & $this->data;
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
                if (!isset($this->m_state->definition->data['_'])){
                    $this->m_state->definition->data['_'] = []; 
                }
                $attrib = & $this->m_state->definition->data['_'];
                $v_attr_key = $this->m_state->attribute;
                $v_attr_value = $this->evalAttributeExpression($e[1]);
                if (strpos($v_attr_key,"*")===0){
                    // special attribute expression 
                    if ($v_attr_key=='*if'){
                        // + | conditional key binding
                        $this->m_state->definition->data[HtmlNodeBuilder::KEY_CONDITION] = $v_attr_value; 
                    } else {
                        // key binding 
                        throw new BviewSyntaxException(sprintf('%s not allowed', $v_attr_key));
                    }
                }else {

                    $attrib[$v_attr_key] = $v_attr_value; //this->evalAttributeExpression($e[1]);
                }
                $this->m_state->attribute = null; 
                break;
            case BviewTokens::TOKEN_EXPRESSION :
                $this->m_state->definition->data[] = $this->evalAttributeExpression($e[1]);
 
                

                break;
            case BviewTokens::TOKEN_VALUE:
                $this->m_state->key = $e[1];
                // $this->m_state->definition = new BviewDefinition;
                // $this->m_state->definition->key = $e[1];
                break;
            case BviewTokens::TOKEN_BRANK:
                if ($e[1]=='}'){
                    if ($this->m_state->key){
                        $v_d = $this->m_state->definition->data;
                        $v_key = $this->m_state->key;
                        // + | convert to single entry value string
                        if (!empty($v_d) && ((count($v_d)==1) && (key($v_d)===0))){
                            $v_d = $v_d[0]; 
                        }
                        if (isset($v_data[$this->m_state->key])){
                            // + | append tag block
                            $v_data[] = ['@_t:'.$v_key=>$v_d];  
                        }else 
                            $v_data[$v_key] = $v_d;
                    }
                    $this->_moveTop();
                    if ($this->m_state->depth===0){
                        $this->m_state->key = null;
                    }
                } else {
                    // start token
                    if ($this->m_state->key){
                        // sub token start 
                        $v_parent = $this->m_state->definition;//? $this->m_state->definition->parent : null;
                        $this->m_state->definition = new BviewDefinition;
                        $this->m_state->definition->key =$this->m_state->key;
                        $this->m_state->definition->parent = $v_parent;
                    }
                }
                break;
            case BviewTokens::TOKEN_TEXT:
                if ($this->m_state->definition){
                    $this->m_state->definition->data[] = $e[1]; // this->m_state->definition->data; 
                }else{
                    igk_die("missing definition");
                }
                //$this->m_state->definition->data[] = $e[1];
                break;
        }
        if ($v_listener) {
            $v_listener->handleToken($token);
        }
    }
    private function _moveTop(){
        if($this->m_state->definition){
            $this->m_state->definition = $this->m_state->definition->parent; 
            $this->m_state->key =$this->m_state->definition? $this->m_state->definition->key :null;
        }
    }

    /**
     * evaluate attribute expression
     * @param mixed $expression 
     * @return string|array|object|void 
     */
    public function evalAttributeExpression($expression){
        if (is_string($expression))
            return $expression;
        if ($expression instanceof BviewExpression){ 
            $engine = ExpressionEngineBase::Factory($expression->name);
            return $engine->evalExpression($expression->value, $this->getExpressionOptions());
        }
        return $expression;

    }
    /**
     * Bview expression option 
     * @return mixed 
     */
    public function getExpressionOptions(){
        return $this->option;
    }
}
