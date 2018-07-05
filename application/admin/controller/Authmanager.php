<?php
/**
 * Created by PhpStorm.
 * User: mrsu
 * Date: 2017/3/23
 * Time: 16:50
 */
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Loader;

class Authmanager extends Common {
    /**
        权限管理器首页
     */
    public function index(){
        $userGroup = Db::name('UserGroup')->order('id desc')->paginate(10);
//        var_dump($userGroup);die;
        $this->assign('userGroups',$userGroup);
        return $this->fetch('index');
    }

    public function add(){
    if(request()->isPost()){
        $data = input('post.');
        $validate = Loader::validate('Auths');
        if(!$validate->check($data)){
            return $this->error($validate->getError());
        }
        //插入数据库
        $result = Db::name('UserGroup')->insert($data);
        if($result){
            return $this->success('添加成功',url('index'));
        }else{
            return $this->error('添加失败');
        }
    }else{
        return $this->fetch();
    }
        
    }

    public function edit(){
        $id = input('id');
        if(request()->isPost()){
            $data = input('post.');
            $hasValue = Db::name('UserGroup')->where('title',$data['title'])->where('id','neq',$data['id'])->find();
            if($hasValue){
                return $this->error('权限组名称不能重复');
            }
            $result = Db::name('UserGroup')->update($data);
            if($result){
                return $this->success('编辑成功',url('index'));
            }else{
                return $this->error('编辑失败');
            }
        }else{
            $userGroup = Db::name('UserGroup')->where('id',$id)->find();
            $this->assign('group',$userGroup);
            return $this->fetch();
        }
    }
    
    public function delete(){
        $id = input('id');
        $result = Db::name('UserGroup')->where('id',$id)->delete();
        if($result !== false){
            return $this->success('用户组删除成功',url('index'));
        }else{
            return $this->error('删除失败');
        }
    }

    public function auth(){
        //获取用户组id
        $gid = input('id');
        // Menu列表数据
        $menuList = Db::name('UserRule')->select();
        //获取已有权限
        $rules   = Db::name('UserGroup')->where('id',$gid)->value('rules');
        //查询当前用户组的名称
        $group  = Db::name('UserGroup')->where('id',$gid)->value('title');
        // Select列表数据转换成树
        $menuListTree = list_to_tree($menuList);
//         var_dump($menuListTree);die;
        // die();
        // 输出赋值
        $this->assign('rules',$rules);
        $this->assign('gid',$gid);
        $this->assign('group',$group);
        $this->assign('menuListTree',$menuListTree);
        return $this->fetch();
    }

    public function setAccess()
    {
        //用户组id
        $gid = input('post.gid');
        //权限id数组
        $ids = input('post.ids/a');
        //转换数组为字符串
        if(is_array($ids)){
            $ids = implode(',',$ids);
        }
        $info = Db::name('UserGroup')->where('id',$gid)->setField('rules',$ids);
        if($info){
            $result = 1;
            echo json_encode($result);
        }else{
            return $this->error('设置失败');
        }
    }

}