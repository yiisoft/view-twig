<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Twig\Html;

class CssClass_TokenParser extends BaseCss_TokenParser
{
    public function getNodeClass()
    {
        return '\Yiisoft\Yii\Twig\Html\CssClassNode';
    }

    public function getTag()
    {
        return 'css_class';
    }
}
