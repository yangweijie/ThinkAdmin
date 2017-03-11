<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\common\builder\form;

use app\common\builder\ZBuilder;

/**
 * 表单构建器
 * @package app\common\builder\type
 * @author 蔡伟明 <314013107@qq.com>
 */
class Builder extends ZBuilder
{
    /**
     * @var string 模板路径
     */
    private $_template = '';

    /**
     * @var array 模板变量
     */
    private $_vars = [
        'page_title'      => '',    // 页面标题
        'page_tips'       => '',    // 页面提示
        'tips_type'       => '',    // 提示类型
        'btn_hide'        => [],    // 要隐藏的按钮
        'btn_title'       => [],    // 按钮标题
        'form_items'      => [],    // 表单项目
        'tab_nav'         => [],    // 页面Tab导航
        'post_url'        => '',    // 表单提交地址
        'form_data'       => [],    // 表单数据
        'extra_html'      => '',    // 额外HTML代码
        'extra_js'        => '',    // 额外JS代码
        'extra_css'       => '',    // 额外CSS代码
        'ajax_submit'     => true,  // 是否ajax提交
        'hide_header'     => false, // 是否隐藏表单头部标题
        'header_title'    => '',    // 表单头部标题
        'js_list'         => [],    // 需要引入的js文件名
        'css_list'        => [],    // 需要引入的css文件名
        'field_triggers'  => [],    // 需要触发的表单项名
        'field_hide'      => '',    // 需要隐藏的表单项
        'field_values'    => '',    // 触发表单项的值
        '_js_files'       => [],    // 需要加载的js（合并输出）
        '_js_init'        => [],    // 初始化的js（合并输出）
        '_css_files'      => [],    // 需要加载的css（合并输出）
    ];

    /**
     * @var bool 是否组合分组
     */
    private $_is_group = false;

    /**
     * 初始化
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function _initialize()
    {
        $this->_template = APP_PATH. 'common/builder/form/layout.html';
        $this->_vars['post_url'] = $this->request->url(true);
    }

    /**
     * 设置页面标题
     * @param string $title 页面标题
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setPageTitle($title = '')
    {
        $this->_vars['page_title'] = trim($title);
        return $this;
    }

    /**
     * 设置表单页提示信息
     * @param string $tips 提示信息
     * @param string $type 提示类型：success,info,danger,warning
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setPageTips($tips = '', $type = 'info')
    {
        $this->_vars['page_tips'] = $tips;
        $this->_vars['tips_type'] = trim($type);
        return $this;
    }

    /**
     * 设置表单提交地址
     * @param string $post_url 提交地址
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setUrl($post_url = '')
    {
        $this->_vars['post_url'] = trim($post_url);
        return $this;
    }

    /**
     * 隐藏按钮
     * @param array|string $btn 要隐藏的按钮，如：['submit']，其中'submit'->确认按钮，'back'->返回按钮
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function hideBtn($btn = [])
    {
        $this->_vars['btn_hide'] = is_array($btn) ? $btn : explode(',', $btn);
        return $this;
    }

    /**
     * 添加按钮
     * @param string $btn 按钮内容
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addBtn($btn = '')
    {
        $this->_vars['btn_extra'] = $btn;
        return $this;
    }

    /**
     * 设置按钮标题
     * @param string|array $btn 按钮名 'submit' -> “提交”，'back' -> “返回”
     * @param string $title 按钮标题
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setBtnTitle($btn = '', $title = '')
    {
        if (is_array($btn)) {
            $this->_vars['btn_title'] = $btn;
        } else {
            $this->_vars['btn_title'][trim($btn)] = trim($title);
        }
        return $this;
    }

    /**
     * 隐藏表单头部标题
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function hideHeaderTitle()
    {
        $this->_vars['hide_header'] = true;
        return $this;
    }

    /**
     * 设置表单头部标题
     * @param string $title 标题
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setHeaderTitle($title = '')
    {
        $this->_vars['header_title'] = trim($title);
        return $this;
    }

    /**
     * 设置触发
     * @param string $trigger 需要触发的表单项名，目前支持select（单选类型）、text、radio三种
     * @param string $values 触发的值
     * @param string $show 触发后要显示的表单项名，目前不支持普通联动、范围、拖动排序、静态文本
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setTrigger($trigger = '', $values = '', $show = '')
    {
        if (is_array($trigger)) {
            foreach ($trigger as $item) {
                $this->_vars['field_hide']   .= $item[2].',';
                $this->_vars['field_values'] .= $item[1].',';
                $this->_vars['field_triggers'][$item[0]][] = [(string)$item[1], $item[2]];
            }
        } else {
            $this->_vars['field_hide']   .= $show.',';
            $this->_vars['field_values'] .= (string)$values.',';
            $this->_vars['field_triggers'][$trigger][] = [(string)$values, $show];
        }
        return $this;
    }

    /**
     * 添加触发
     * @param array $triggers 触发数组
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addTrigger($triggers = [])
    {
        $this->setTrigger($triggers);
        return $this;
    }

    /**
     * 添加数组类型的表单项，基本和Textarea是一样的，但读取的时候会用parse_attr函数转换
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author caiweiming <314013107@qq.com>
     * @return Builder
     */
    public function addArray($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '') {
        return $this->addTextarea($name, $title, $tips, $default, $extra_attr, $extra_class);
    }

    /**
     * 添加百度地图
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $ak 百度APPKEY
     * @param string $tips 提示
     * @param string $default 默认坐标
     * @param string $address 默认地址
     * @param string $level 地图显示级别
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addBmap($name = '', $title = '', $ak = '', $tips = '', $default = '', $address = '', $level = '', $extra_class = '')
    {
        $item = [
            'type'        => 'bmap',
            'name'        => $name,
            'title'       => $title,
            'ak'          => $ak,
            'tips'        => $tips,
            'value'       => $default,
            'address'     => $address,
            'level'       => $level == '' ? 12 : $level,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加复选框
     * @param string $name 复选框名
     * @param string $title 复选框标题
     * @param string $tips 提示
     * @param array $options 复选框数据
     * @param string $default 默认值
     * @param array $attr 属性，
     *      color-颜色(default/primary/info/success/warning/danger)，默认primary
     *      size-尺寸(sm,nm,lg)，默认sm
     *      shape-形状(rounded,square)，默认rounded
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addCheckbox($name = '', $title = '', $tips = '', $options = [], $default = '', $attr = [], $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'checkbox',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'extra_label_class' => $extra_attr == 'disabled' ? 'css-input-disabled' : '',
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加CKEditor编辑器
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $width 编辑器宽度，默认100%
     * @param integer $height 编辑器高度，默认400px
     * @param string $default 默认值
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addCkeditor($name = '', $title = '', $tips = '', $default = '', $width = '100%', $height = 400, $extra_class = '')
    {
        $item = [
            'type'        => 'ckeditor',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'width'       => $width,
            'height'      => $height,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加取色器
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $mode 模式：默认为rgba(含透明度)，也可以是rgb
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addColorpicker($name = '', $title = '', $tips = '', $default = '', $mode = 'rgba', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'colorpicker',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'mode'        => $mode,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加日期
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $format 日期格式
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addDate($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'date',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'format'      => $format == '' ? 'yyyy-mm-dd' : $format,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加日期范围
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $format 格式
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addDaterange($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '')
    {
        if (strpos($name, ',')) {
            list($name_from, $name_to) = explode(',', $name);
            $id_from = $name_from;
            $id_to   = $name_to;
            $id      = $name_from;
        } else {
            $name_from = $name_to = $name . '[]';
            $id_from = $name . '_from';
            $id_to   = $name . '_to';
            $id      = $name;
        }

        if (strpos($default, ',') !== false) {
            list($value_from, $value_to) = explode(',', $default);
        } else {
            $value_from = $default;
            $value_to   = '';
        }

        $item = [
            'type'        => 'daterange',
            'id'          => $id,
            'name_from'   => $name_from,
            'name_to'     => $name_to,
            'id_from'     => $id_from,
            'id_to'       => $id_to,
            'title'       => $title,
            'tips'        => $tips,
            'value_from'  => $value_from,
            'value_to'    => $value_to,
            'format'      => $format == '' ? 'yyyy-mm-dd' : $format,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加日期时间
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $format 日期时间格式
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addDatetime($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'datetime',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'format'      => $format == '' ? 'YYYY-MM-DD HH:mm' : $format,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加markdown编辑器
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param bool $watch 是否实时预览
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addEditormd($name = '', $title = '', $tips = '', $default = '', $watch = true, $extra_class = '')
    {
        $item = [
            'type'        => 'editormd',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'watch'       => $watch,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单文件上传
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $size 文件大小，单位为kb
     * @param string $ext 文件后缀
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addFile($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = '')
    {
        $size = ($size != '' ? $size : config('upload_file_size')) * 1024;
        $ext  = $ext != '' ? $ext : config('upload_file_ext');

        $item = [
            'type'        => 'file',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'size'        => $size,
            'ext'         => $ext,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多文件上传
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $size 图片大小，单位为kb
     * @param string $ext 文件后缀
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addFiles($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = '')
    {
        $size = ($size != '' ? $size : config('upload_file_size')) * 1024;
        $ext  = $ext != '' ? $ext : config('upload_file_ext');

        $item = [
            'type'        => 'files',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'size'        => $size,
            'ext'         => $ext,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加分组
     * @param array $groups 分组数据
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addGroup($groups = [])
    {
        if (is_array($groups) && !empty($groups)) {
            $this->_is_group = true;
            foreach ($groups as &$group) {
                foreach ($group as $key => $item) {
                    $type = $item[0];
                    $args = $item;
                    array_shift($args);
                    $group[$key] = call_user_func_array([$this, 'add'.ucfirst($type)], $args);
                }
            }
            $this->_is_group = false;
        }

        $item = [
            'type'    => 'group',
            'options' => $groups
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加隐藏表单项
     * @param string $name 表单项名
     * @param string $default 默认值
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addHidden($name = '', $default = '', $extra_class = '')
    {
        $item = [
            'type'        => 'hidden',
            'name'        => $name,
            'value'       => $default,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加图标选择器
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addIcon($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'icon',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单图片上传
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $size 图片大小，单位为kb，0为不限制
     * @param string $ext 文件后缀
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addImage($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = '')
    {
        $size = ($size != '' ? $size : config('upload_image_size')) * 1024;
        $ext  = $ext != '' ? $ext : config('upload_image_ext');

        $item = [
            'type'        => 'image',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'size'        => $size,
            'ext'         => $ext,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多图片上传
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $size 图片大小，单位为kb，0为不限制
     * @param string $ext 文件后缀
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addImages($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = '')
    {
        $size = ($size != '' ? $size : config('upload_image_size')) * 1024;
        $ext  = $ext != '' ? $ext : config('upload_image_ext');

        $item = [
            'type'        => 'images',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'size'        => $size,
            'ext'         => $ext,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 图片裁剪
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param array $options 参数
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addJcrop($name = '', $title = '', $tips = '', $default = '', $options = [], $extra_class = '')
    {
        $item = [
            'type'        => 'jcrop',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'options'     => json_encode($options),
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加普通联动表单项
     * @param string $name 表单项名
     * @param string $title 表单项标题
     * @param string $tips 表单项提示说明
     * @param array $options 表单项options
     * @param string $default 默认值
     * @param string $ajax_url 数据异步请求地址
     *      可以用Url方法生成，返回数据格式必须如下：
     *      $arr['code'] = '1'; //判断状态
     *      $arr['msg'] = '请求成功'; //回传信息
     *      $arr['list'] = [
     *          ['key' => 'gz', 'value' => '广州'],
     *          ['key' => 'sz', 'value' => '深圳'],
     *      ]; //数据
     *      return json($arr);
     *      status用于判断是否请求成功，list将作为$next_items第一个表单名的下拉框的内容
     * @param string $next_items 下一级下拉框的表单名
     *      如果有多个关联关系，必须一同写上，用逗号隔开,
     *      比如学院作为联动的一个下拉框，它的下级是专业，那么这里就写上专业下拉框的表单名，如：'zy'
     *      如果还有班级，那么切换学院的时候，专业和班级应该是一同关联的
     *      所以就必须写上专业和班级的下拉框表单名，如：'zy,bj'
     * @param string $param 指定请求参数的key名称，默认为$name的值
     *      比如$param为“key”
     *      那么请求数据的时候会发送参数key=某个下拉框选项值
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addLinkage($name = '', $title = '', $tips = '', $options = [], $default = '', $ajax_url = '', $next_items = '', $param = '')
    {
        $item = [
            'type'       => 'linkage',
            'name'       => $name,
            'title'      => $title,
            'tips'       => $tips,
            'value'      => $default,
            'options'    => $options,
            'ajax_url'   => $ajax_url,
            'next_items' => $next_items,
            'param'      => $param == '' ? $name : $param,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加快速多级联动
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $table 表名
     * @param int $level 级别
     * @param string $default 默认值
     * @param array $fields 字段名，默认为id,name,pid
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addLinkages($name = '', $title = '', $tips = '', $table = '', $level = 2, $default = '', $fields = [])
    {
        if ($level > 4) {
            halt('目前最多只支持4级联动');
        }

        // 键字段名，也就是下拉菜单的option元素的value值
        $key    = 'id';
        // 值字段名，也就是下拉菜单显示的各项
        $option = 'name';
        // 父级id字段名
        $pid    = 'pid';

        if (!empty($fields)) {
            if (!is_array($fields)) {
                $fields = explode(',', $fields);
                $key    = isset($fields[0]) ? $fields[0] : $key;
                $option = isset($fields[1]) ? $fields[1] : $option;
                $pid    = isset($fields[2]) ? $fields[2] : $pid;
            } else {
                $key    = isset($fields['id'])   ? $fields['id']   : $key;
                $option = isset($fields['name']) ? $fields['name'] : $option;
                $pid    = isset($fields['pid'])  ? $fields['pid']  : $pid;
            }
        }

        $item = [
            'type'   => 'linkages',
            'name'   => $name,
            'title'  => $title,
            'tips'   => $tips,
            'table'  => $table,
            'level'  => $level,
            'key'    => $key,
            'option' => $option,
            'pid'    => $pid,
            'value'  => $default,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加格式文本
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $format 格式
     * @param string $default 默认值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addMasked($name = '', $title = '', $tips = '', $format = '', $default = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'masked',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'format'      => $format,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加数字输入框
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $min 最小值
     * @param string $max 最大值
     * @param string $step 步进值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addNumber($name = '', $title = '', $tips = '', $default = '', $min = '', $max = '', $step = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'number',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default == '' ? 0 : $default,
            'min'         => $min,
            'max'         => $max,
            'step'        => $step,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加密码框
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addPassword($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'password',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单选
     * @param string $name 单选名
     * @param string $title 单选标题
     * @param string $tips 提示
     * @param array $options 单选数据
     * @param string $default 默认值
     * @param array $attr 属性，
     *      color-颜色(default/primary/info/success/warning/danger)，默认primary
     *      size-尺寸(sm,nm,lg)，默认sm
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addRadio($name = '', $title = '', $tips = '', $options = [], $default = '', $attr = [], $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'radio',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'extra_label_class' => $extra_attr == 'disabled' ? 'css-input-disabled' : '',
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加范围
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param array $options 参数
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addRange($name = '', $title = '', $tips = '', $default = '', $options = [], $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'range',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];
        $item = array_merge($item, $options);
        if (isset($item['double']) && $item['double'] == 'true') {
            $item['double'] = 'double';
        }

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加普通下拉菜单
     * @param string $name 下拉菜单名
     * @param string $title 标题
     * @param string $tips 提示
     * @param array $options 选项
     * @param string $default 默认值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addSelect($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '')
    {
        $type = 'select';
        if ($extra_attr != '') {
            if (in_array('multiple', explode(' ', $extra_attr))) {
                $type = 'select2';
            }
        }

        $item = [
            'type'        => $type,
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加拖拽排序
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param array $value 值
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addSort($name = '', $title = '', $tips = '', $value = [], $extra_class = '')
    {
        $content = [];

        if (!empty($value)) {
            $content = $value;
        }

        $item = [
            'type'        => 'sort',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => implode(',', array_keys($value)),
            'content'     => $content,
            'extra_class' => $extra_class
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加静态文本
     * @param string $name 静态表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_class 额外css类
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addStatic($name = '', $title = '', $tips = '', $default = '', $extra_class = '')
    {
        $item = [
            'type'        => 'static',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加Summernote编辑器
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $width 编辑器宽度
     * @param int $height 编辑器高度
     * @param string $extra_class
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addSummernote($name = '', $title = '', $tips = '', $default = '', $width = '100%', $height = 350, $extra_class = '')
    {
        $item = [
            'type'        => 'summernote',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'width'       => $width,
            'height'      => $height,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加开关
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param array $attr 属性，
     *      color-颜色(default/primary/info/success/warning/danger)，默认primary
     *      size-尺寸(sm,nm,lg)，默认sm
     *      shape-形状(rounded,square)，默认rounded
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addSwitch($name = '', $title = '', $tips = '', $default = '', $attr = [], $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'switch',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'extra_label_class' => $extra_attr == 'disabled' ? 'css-input-disabled' : '',
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加标签
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addTags($name = '', $title = '', $tips = '', $default = '', $extra_class = '')
    {
        $item = [
            'type'        => 'tags',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => is_array($default) ? implode(',', $default) : $default,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单行文本框
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param array $group 标签组，可以在文本框前后添加按钮或者文字
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addText($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'text',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => $group,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多行文本框
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addTextarea($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'textarea',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加时间
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $format 日期时间格式
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addTime($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'time',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'format'      => $format == '' ? 'HH:mm:ss' : $format,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加百度编辑器
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addUeditor($name = '', $title = '', $tips = '', $default = '', $extra_class = '')
    {
        $item = [
            'type'        => 'ueditor',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加wang编辑器
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_class 额外css类名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this|array
     */
    public function addWangeditor($name = '', $title = '', $tips = '', $default = '', $extra_class = '')
    {
        $item = [
            'type'        => 'wangeditor',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加表单项
     * 这个是addCheckbox等方法的别名方法，第一个参数传表单项类型，其余参数与各自方法中的参数一致
     * @param string $type 表单项类型
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addFormItem($type = '')
    {
        // 获取所有参数值
        $args = func_get_args();
        array_shift($args);

        if (!empty($type)) {
            $method = 'add'. ucfirst($type);
            call_user_func_array([$this, $method], $args);
        }

        return $this;
    }

    /**
     * 一次性添加多个表单项
     * @param array $items 表单项
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function addFormItems($items = [])
    {
        if (!empty($items)) {
            foreach ($items as $item) {
                call_user_func_array([$this, 'addFormItem'], $item);
            }
        }
        return $this;
    }

    /**
     * 直接设置表单项数据
     * @param array $items 表单项数据
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setFormItems($items = [])
    {
        // 额外已经构造好的表单项目与单个组装的的表单项目进行合并
        $this->_vars['form_items'] = array_merge($this->_vars['form_items'], $items);
        foreach ($items as $item) {
            if ($item['type'] == 'group') {
                foreach ($item['options'] as $group) {
                    foreach ($group as $key => $value) {
                        $this->loadMinify($group[$key]['type']);
                    }
                }
            } else {
                $this->loadMinify($item['type']);
            }
        }
        return $this;
    }

    /**
     * 设置Tab按钮列表
     * @param array $tab_list Tab列表 如：['tab1' => ['title' => '标题', 'url' => 'http://www.dolphinphp.com']]
     * @param string $curr_tab 当前tab名
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setTabNav($tab_list = [], $curr_tab = '')
    {
        if (!empty($tab_list)) {
            $this->_vars['tab_nav'] = [
                'tab_list' => $tab_list,
                'curr_tab' => $curr_tab,
            ];
        }
        return $this;
    }

    /**
     * 设置表单数据
     * @param array $form_data 表单数据
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setFormData($form_data = [])
    {
        $this->_vars['form_data'] = $form_data;
        return $this;
    }

    /**
     * 设置额外HTML代码
     * @param string $extra_html 额外HTML代码
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setExtraHtml($extra_html = '')
    {
        $this->_vars['extra_html'] = $extra_html;
        return $this;
    }

    /**
     * 设置额外JS代码
     * @param string $extra_js 额外JS代码
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setExtraJs($extra_js = '')
    {
        $this->_vars['extra_js'] = $extra_js;
        return $this;
    }

    /**
     * 设置额外CSS代码
     * @param string $extra_css 额外CSS代码
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setExtraCss($extra_css = '')
    {
        $this->_vars['extra_css'] = $extra_css;
        return $this;
    }

    /**
     * 表单项布局
     * @param array $column 布局参数 ['表单项名' => 所占宽度,....]
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function layout($column = [])
    {
        $this->_vars['_layout'] = $column;
        return $this;
    }

    /**
     * 引入模块js文件
     * @param string $files_name js文件名，多个文件用逗号隔开
     * @author caiweiming <314013107@qq.com>
     * @return $this
     */
    public function js($files_name = '')
    {
        $this->loadFile('js', $files_name);
        return $this;
    }

    /**
     * 引入模块css文件
     * @param string $files_name css文件名，多个文件用逗号隔开
     * @author caiweiming <314013107@qq.com>
     * @return $this
     */
    public function css($files_name = '')
    {
        $this->loadFile('css', $files_name);
        return $this;
    }

    /**
     * 引入css或js文件
     * @param string $type 类型：css/js
     * @param string $files_name 文件名，多个用逗号隔开
     * @author caiweiming <314013107@qq.com>
     */
    private function loadFile($type = '', $files_name = '')
    {
        if ($files_name != '') {
            if (!is_array($files_name)) {
                $files_name = explode(',', $files_name);
            }
            foreach ($files_name as $item) {
                $this->_vars[$type.'_list'][] = $item;
            }
        }
    }

    /**
     * 设置ajax方式提交
     * @param bool $ajax_submit 默认true，false为关闭ajax方式提交
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function isAjax($ajax_submit = true)
    {
        $this->_vars['ajax_submit'] = $ajax_submit;
        return $this;
    }

    /**
     * 设置模版路径
     * @param string $template 模板路径
     * @author 蔡伟明 <314013107@qq.com>
     * @return $this
     */
    public function setTemplate($template = '')
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * 根据表单项类型，加载不同js和css文件，并合并
     * @param string $type 表单项类型
     * @author 蔡伟明 <314013107@qq.com>
     */
    private function loadMinify($type = '')
    {
        if ($type != '') {
            switch ($type) {
                case 'colorpicker':
                    $this->_vars['_js_files'][]  = 'colorpicker_js';
                    $this->_vars['_css_files'][] = 'colorpicker_css';
                    $this->_vars['_js_init'][]   = 'colorpicker';
                    break;
                case 'ckeditor':
                    $this->_vars['_ckeditor']  = '1';
                    $this->_vars['_js_init'][] = 'ckeditor';
                    break;
                case 'date':
                case 'daterange':
                    $this->_vars['_js_files'][]  = 'datepicker_js';
                    $this->_vars['_css_files'][] = 'datepicker_css';
                    $this->_vars['_js_init'][]   = 'datepicker';
                    break;
                case 'datetime':
                case 'time':
                    $this->_vars['_js_files'][]  = 'datetimepicker_js';
                    $this->_vars['_css_files'][] = 'datetimepicker_css';
                    $this->_vars['_js_init'][]   = 'datetimepicker';
                    break;
                case 'editormd':
                    $this->_vars['_js_files'][] = 'editormd_js';
                    $this->_vars['_editormd']   = '1';
                    break;
                case 'file':
                case 'files':
                case 'image':
                case 'images':
                    $this->_vars['_js_files'][]  = 'webuploader_js';
                    $this->_vars['_css_files'][] = 'webuploader_css';
                    break;
                case 'icon':
                    $this->_vars['_icon'] = '1';
                    break;
                case 'jcrop':
                    $this->_vars['_js_files'][]  = 'jcrop_js';
                    $this->_vars['_css_files'][] = 'jcrop_css';
                    break;
                case 'linkage':
                case 'linkages':
                case 'select':
                case 'select2':
                    $this->_vars['_js_files'][]  = 'select2_js';
                    $this->_vars['_css_files'][] = 'select2_css';
                    $this->_vars['_js_init'][]   = 'select2';
                    break;
                case 'masked':
                    $this->_vars['_js_files'][] = 'masked_inputs_js';
                    break;
                case 'range':
                    $this->_vars['_js_files'][]  = 'rangeslider_js';
                    $this->_vars['_css_files'][] = 'rangeslider_css';
                    $this->_vars['_js_init'][]   = 'rangeslider';
                    break;
                case 'sort':
                    $this->_vars['_js_files'][]  = 'nestable_js';
                    $this->_vars['_css_files'][] = 'nestable_css';
                    break;
                case 'tags':
                    $this->_vars['_js_files'][]  = 'tags_js';
                    $this->_vars['_css_files'][] = 'tags_css';
                    $this->_vars['_js_init'][]   = 'tags-inputs';
                    break;
                case 'ueditor':
                    $this->_vars['_ueditor'] = '1';
                    break;
                case 'wangeditor':
                    $this->_vars['_js_files'][]  = 'wangeditor_js';
                    $this->_vars['_css_files'][] = 'wangeditor_css';
                    break;
                case 'summernote':
                    $this->_vars['_js_files'][]  = 'summernote_js';
                    $this->_vars['_css_files'][] = 'summernote_css';
                    $this->_vars['_js_init'][]   = 'summernote';
                    break;
            }
        } else {
            if ($this->_vars['form_items']) {
                foreach ($this->_vars['form_items'] as &$item) {
                    // 判断是否为分组
                    if ($item['type'] == 'group') {
                        foreach ($item['options'] as &$group) {
                            foreach ($group as $key => $value) {
                                if ($group[$key]['type'] != '') {
                                    $this->loadMinify($group[$key]['type']);
                                }
                            }
                        }
                    } else {
                        if ($item['type'] != '') {
                            $this->loadMinify($item['type']);
                        }
                    }
                }
            }
        }
    }

    /**
     * 设置表单项的值
     * @author 蔡伟明 <314013107@qq.com>
     */
    private function setFormValue()
    {
        if ($this->_vars['form_data']) {
            foreach ($this->_vars['form_items'] as &$item) {
                // 判断是否为分组
                if ($item['type'] == 'group') {
                    foreach ($item['options'] as &$group) {
                        foreach ($group as $key => $value) {
                            if (isset($this->_vars['form_data'][$value['name']])) {
                                $group[$key]['value'] = $this->_vars['form_data'][$value['name']];
                            } else {
                                $group[$key]['value'] = '';
                            }
                        }
                    }
                } else {
                    // 针对日期范围特殊处理
                    if ($item['type'] == 'daterange') {
                        if ($item['name_from'] == $item['name_to']) {
                            list($item['value_from'], $item['value_to']) = $this->_vars['form_data'][$item['id']];
                        } else {
                            $item['value_from'] = $this->_vars['form_data'][$item['name_from']];
                            $item['value_to']   = $this->_vars['form_data'][$item['name_to']];
                        }
                    } else {
                        if (isset($this->_vars['form_data'][$item['name']])) {
                            $item['value'] = $this->_vars['form_data'][$item['name']];
                        } else {
                            $item['value'] = isset($item['value']) ? $item['value'] : '';
                        }
                    }
                }
            }
        }
    }

    /**
     * 加载模板输出
     * @param string $template 模板文件名
     * @param array  $vars     模板输出变量
     * @param array  $replace  模板替换
     * @param array  $config   模板参数
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        if (!empty($vars)) {
            $this->_vars['form_data'] = $vars;
        }

        // 设置表单值
        $this->setFormValue();

        // 处理不同表单类型加载不同js和css
        $this->loadMinify();

        // 处理页面标题
        if ($this->_vars['page_title'] == '' && defined('ENTRANCE') && ENTRANCE == 'admin') {
            $location = get_location();
            $curr_location = end($location);
            $this->_vars['page_title'] = $curr_location['title'];
        }

        // 另外设置模板
        if ($template != '') {
            $this->_template = $template;
        }

        // 处理需要隐藏的表单项，去除最后一个逗号
        if ($this->_vars['field_hide'] != '') {
            $this->_vars['field_hide'] = rtrim($this->_vars['field_hide'], ',');
        }
        if ($this->_vars['field_values'] != '') {
            $this->_vars['field_values'] = explode(',', $this->_vars['field_values']);
            $this->_vars['field_values'] = array_filter($this->_vars['field_values'], 'strlen');
            $this->_vars['field_values'] = implode(',', array_unique($this->_vars['field_values']));
        }

        // 处理js和css合并的参数
        if (!empty($this->_vars['_js_files'])) {
            $this->_vars['_js_files'] = array_unique($this->_vars['_js_files']);
            sort($this->_vars['_js_files']);
        }
        if (!empty($this->_vars['_css_files'])) {
            $this->_vars['_css_files'] = array_unique($this->_vars['_css_files']);
            sort($this->_vars['_css_files']);
        }
        if (!empty($this->_vars['_js_init'])) {
            $this->_vars['_js_init'] = array_unique($this->_vars['_js_init']);
            sort($this->_vars['_js_init']);
            $this->_vars['_js_init'] = json_encode($this->_vars['_js_init']);
        }

        // 实例化视图并渲染
        return parent::fetch($this->_template, $this->_vars, $replace, $config);
    }
}
