<?php
/**
 * Created by PhpStorm.
 * User: mrsu
 * Date: 2017/3/22
 * Time: 13:42
 */
namespace app\admin\controller;

use \think\Controller;
use think\Cache;
use \think\Session;
use \org\Auth;
use \think\Request;
use \think\Db;

class Common extends Controller{

    protected function _initialize()
    {
        parent::_initialize();
        $this->checkAuth();
        $this->menus();
    }

    //退出登录
    public function logout(){
        session('user',null);
        return $this->redirect(url('admin/base/login'));
    }
    //清楚缓存
    public function clearCache(){
        Cache::clear();
        $path = dirname(APP_PATH)."/runtime/temp";
        delDirAndFile($path,false);
        return $this->success('清除缓存成功',url('admin/index/index'));
    }

    //权限检查
    protected function checkAuth(){
//        if(!Session::has('uid')){
//            $this->redirect('admin/base/login');
//        }

        $uid = session('uid');
        $uid = 1;
        $request        = Request::instance();
        //获取当前模块
        $module     = strtolower($request->module());
        //获取当前控制器
        $controller     = strtolower($request->controller());
        //获取当前方法
        $action         = strtolower($request->action());
        //组合url
        $url            = $module."/".$controller."/".$action;

        if($uid==1){
            //超级管理员，直接返回
            return true;
        }
        $not_check = ['admin/index/index','admin/base/login','admin/index/main','admin/common/logout'];
        //获取菜单的id
        $ruleId = Db::name('UserRule')->field('id')->where('name',$url)->find();
        //如果不存在，说明权限不受限，可以访问
        if($ruleId == null){
            return true;
        }
        //获取当前登录用户所在的用户组
        $group = Db::name('UserGroupAccess')->field('group_id')->where('uid',$uid)->find();
//        var_dump($group);die;
        if(!$group){
            return $this->error("没有权限");
        }
        //所有权限数组
        $rules_array = [];
        $rules = Db::name('UserGroup')->where('id',$group['group_id'])->where('status',1)->value('rules');
        if($rules){
            $rules_array = explode(',',$rules);
        }

        $ruleId = (string)$ruleId['id'];

        //权限判断
        if(!in_array($url,$not_check)){
            if(!in_array((string)$ruleId,$rules_array)){
                return $this->error("没有权限",url('admin/index/main'));
            }
        }

    }
    //根据权限生成菜单信息
    protected function menus(){
        $uid = session('uid');
        $uid = 1;
        if($uid == 1){
            $menus = Db::name('UserRule')->order('id asc')->select();
        }else{
            //获取当前登录用户所在的用户组
            $group = Db::name('UserGroupAccess')->field('group_id')->where('uid',$uid)->find();
            //获取用户所有权限数组
            $rules = Db::name('UserGroup')->where('id',$group['group_id'])->where('status',1)->value('rules');
            if($rules){
                $rules = explode(',',$rules);
                $menus = Db::name('UserRule')->order('id asc')->where('id','in',$rules)->select();
            }
        }


        $menuLevel = menu_level($menus);
        static $list = [];
//        static $pid;
//        static $key;
        foreach($menuLevel as $k=>$v){
            if($v['pid'] ==0){
                $list[$k] = $v;
                foreach($menuLevel as $kk=>$vv){
                    if($vv['pid'] == $v['id']){
                        $list[$k]['child'][] = $vv;
                    }
                }
            }

        }

        $this->assign('menuLevel',$list);
    }
}