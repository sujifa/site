<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23
 * Time: 0:26
 */
namespace app\index\validate;

use think\Db;
use think\Validate;

class Member extends Validate{
    protected $rule = [
        'username'  =>  'require|checkHasValue:username|alphaNum',
        'nickname'  =>  'require|checkHasValue:nickname|alphaNum',
        'password'  =>  'require',
        'password2' =>  'require|confirm:password',
        'status' =>  'require',
        'group_id'  =>  'require',
    ];

    protected $message = [
        'username.require'  =>  '用户名不能为空',
        'username.checkHasValue:nickname'  =>  '用户名已存在',
        'username.alphaNum'  =>  '用户名必须是字母、数字',
        'nickname.require'  =>  '昵称不能为空',
        'nickname.checkHasValue:nickname'  =>  '昵称已存在',
        'nickname.alphaNum'  =>  '昵称必须是字母、数字',
        'password.require'  =>  '请输入密码',
        'password2.require' =>  '请输入确认密码',
        'password2.confirm' =>  '请重新输入确认密码',
        'group_id.require' =>  '请选择所属权限组',
    ];

    protected $scene = [
        'edit'  =>  ['username','nickname','status'],
        'editPass'  =>  ['password','password2'],
        'add'   =>  ['username','nickname','password','password2','status','group_id'],
    ];

    protected function checkHasValue($value,$rule){
        $id = input('id');
        switch ($rule){
            case 'username':
                if(empty($id)){
                    $hasValue = Db::name('Users')->where('username',$value)->find();
                    if($hasValue == null){
                        return true;
                    }else{
                        return '用户名已存在';
                    }
                }else{
                    //编辑资料判断
                    $checkValue = Db::name('Users')->where('id','neq',$id)->where('username',$value)->find();
                    if($checkValue == null){
                        return true;
                    }else{
                        return '用户名已存在';
                    }
                }

            case 'nickname':
                if(empty($id)){
                    $hasValue = Db::name('Users')->where('nickname',$value)->find();
                    if($hasValue == null){
                        return true;
                    }else{
                        return '昵称已存在';
                    }
                }else{
                    //更改资料的判断
                    $checkValue = Db::name('Users')->where('id','neq',$id)->where('nickname',$value)->find();
                    if($checkValue == null){
                        return true;
                    }else{
                        return '昵称已存在';
                    }
                }
        }
    }
}