<?php
/**
 * 首页
 *
 * @category Controller
 * @author   jay <917647288@qq.com>
 * @license   MIT
 * @link   http://url.com
 * @package category
 *
 */

namespace app\index\controller;

use voku\CssToInlineStyles\CssToInlineStyles;

/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Index extends Home
{

    // 老杨微信公众号文章编辑器——最好的公众号前端编辑器
    public function index()
    {
        return $this->fetch('wechat_editor');
    }

    /**
     * 返回css转行内工具的返回值
     *
     * @param array $param
     * @return string
     */
    public function parse($html, $css, $param='')
    {
        config('trace.type', 'console');
        $default = [
            'cleanup'=>0,
            'useInlineStylesBlock'=>0,
            'stripOriginalStyleTags'=>0,
            'excludeMediaQueries'=>1,
            'excludeConditionalInlineStylesBlock'=>1,
        ];
        $param = $param? array_merge($default, $param): $default;

        // The following properties exists and have set methods available:

        // Property | Default | Description
        // -------|---------|------------
        // cleanup|false|Should the generated HTML be cleaned?
        // useInlineStylesBlock|false|Use inline-styles block as CSS.
        // stripOriginalStyleTags|false|Strip original style tags.
        // excludeMediaQueries||true|Exclude media queries from extra "css" and keep media queries for inline-styles blocks.
        // excludeConditionalInlineStylesBlock |true|Exclude conditional inline-style blocks.

        // config('default_return_type', 'json');
        if (empty($html)) {
            goto render;
        }

        // Convert HTML + CSS to HTML with inlined CSS
        $cssToInlineStyles = new CssToInlineStyles();
        $cssToInlineStyles->setHTML($html);
        $cssToInlineStyles->setCSS($css);
        if ($param['cleanup']) {
            $cssToInlineStyles->setCleanup(true);
        }
        if ($param['useInlineStylesBlock']) {
            $cssToInlineStyles->setUseInlineStylesBlock(true);
        }
        if ($param['stripOriginalStyleTags']) {
            $cssToInlineStyles->setStripOriginalStyleTags(true);
        }
        if (!$param['excludeMediaQueries']) {
            $cssToInlineStyles->setExcludeMediaQueries(false);
        }
        if (!$param['excludeConditionalInlineStylesBlock']) {
            $cssToInlineStyles->setExcludeConditionalInlineStylesBlock(false);
        }
        $html = $cssToInlineStyles->convert();
        render:
        $this->assign('html', $html);
        return $this->fetch('preview');
    }

    public function fb()
    {
        return $this->fetch();
    }
}
