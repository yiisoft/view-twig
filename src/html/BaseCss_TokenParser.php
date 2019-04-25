<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Twig\Html;

use Twig_Token;

abstract class BaseCss_TokenParser extends \Twig_TokenParser
{
    public function parse(\Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();
        $name = $stream->expect(Twig_Token::NAME_TYPE);
        $operator = $stream->expect(Twig_Token::OPERATOR_TYPE);
        $value = $parser->getExpressionParser()->parseExpression();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $nodeClass = $this->getNodeClass();

        return new $nodeClass($name, $value, $operator, $token->getLine(), $this->getTag());
    }

    abstract public function getNodeClass();
}
