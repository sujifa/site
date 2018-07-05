<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Loader;
use think\Request;

class Login extends Controller{
    //用户登录方法
    public function login(){

        if(Request::instance()->isPost()){
            $username = input('post.username');
            $password = input('post.password');
            $captcha = input('post.captcha');

            $validate = Loader::validate('Login');
            $data = ['username'=>$username,'password'=>$password,'captcha'=>$captcha];
            //验证
            if(!$validate->check($data)){
                return $this->error($validate->getError());
            }
            //查询数据库
            $where['username'] = $username;
            $where['status'] = 1;
            $userInfo = Db::name('Users')->where($where)->find();
            $returnData = [];
            $returnData['code'] = 1;
            $returnData['info'] = '登录成功';
            if($userInfo && $userInfo['password'] === $password ){
                session('uid',$userInfo['id']);
                session('username',$userInfo['username']);
                die(json_encode($returnData));
            }else{
                $returnData['code'] = 0;
                $returnData['info'] = '登录失败';
                die(json_encode($returnData));
            }

        }else{
            return $this->fetch('login');
        }
    }

}