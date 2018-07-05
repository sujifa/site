<?php
/**
 * Created by PhpStorm.
 * User: mrsu
 * Date: 2017/3/24
 * Time: 10:35
 */
namespace app\index\validate;

use think\Validate;

class Menu extends Validate{
    protected $rule = [
        'pid'   =>  'require',
        'title'   =>  'require',
        'name'   =>  '',
        'sort'   =>  'require|number',
    ];
    protected $message = [
        'pid.require'   =>  '请选择上级菜单',
        'title.require'   =>  '请输入菜单名称',
//        'name.require'   =>  '请输入控制器名称',
        'sort.require'   =>  '请输入排序',
        'sort.number'   =>  '排序只能是数字',
    ];
}