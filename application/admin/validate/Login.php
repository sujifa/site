<?php
/**
 * Created by PhpStorm.
 * User: mrsu
 * Date: 2017/3/21
 * Time: 10:52
 */
namespace app\admin\validate;

use think\Validate;

class Login extends Validate{
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
        'captcha' => 'require|checkCaptcha:null',
    ];

    protected $message = [
        'username.require' => '用户名或密码错误！',
        'password.require' => '用户名或密码错误！',
        'captcha.require' => '验证码错误',
    ];

    protected function checkCaptcha($value){
        $captcha = new \org\Captcha();
        if($captcha->check($value)){
            return true;
        }else{
            return '验证码错误！';
        }
    }


}