<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23
 * Time: 23:03
 */
namespace app\admin\validate;
use think\Db;
use think\Validate;

class Auths extends Validate{
    protected $rule = [
        'title' =>  'require|checkName:name',
    ];
    protected $message = [
        'title.require' =>  '用户组名称不能为空',
    ];
    protected function checkName($value){
        $info = Db::name('UserGroup')->where('title',$value)->find();
        if($info){
            return '用户组已存在';
        }else{
            return true;
        }
    }
}