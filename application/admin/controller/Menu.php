<?php
/**
 * Created by PhpStorm.
 * User: mrsu
 * Date: 2017/3/24
 * Time: 9:08
 */
namespace app\index\controller;

use think\Request;
use think\Db;
use think\Loader;

class Menu extends Common {
    public function index(){
        $menus = Db::name('UserRule')->order('id asc')->select();
        $menuLevel = array_level($menus);
        $this->assign('menu',$menuLevel);
        return $this->fetch();
    }

    public function add($pid = ''){
        if(Request()->isPost()){
            $data = input('post.');
            $validate = Loader::validate();
            if(!$validate->check($data)){
                return $this->error($validate->getError());
            }
            $addMenu = Db::name('UserRule')->insert($data);
            if($addMenu !== false){
                return $this->success('添加成功',url('index/menu/index'));
            }else{
                return $this->error('添加失败');
            }
        }else{
            $menus = Db::name('UserRule')->order('id asc')->select();
            $menuLevel = array_level($menus);
            $this->assign('menu',$menuLevel);
            return $this->fetch('add',['pid' => $pid]);
        }
    }

    public function edit(){
        $id = input('id');
        if(Request()->isPost()){
            $data = input('post.');
            $hasValue = Db::name('UserRule')->where('id','neq',$id)->where('title',$data['title'])->find();
            if($hasValue){
                return $this->error('菜单名称已存在');
            }
            $addMenu = Db::name('UserRule')->where('id',$id)->update($data);
            if($addMenu){
                return $this->success('修改成功',url('index/menu/index'));
            }else{
                return $this->error('修改失败');
            }

        }else{
            $info = Db::name('UserRule')->where('id',$id)->find();
            $menus = Db::name('UserRule')->order('id asc')->select();
            $menuLevel = array_level($menus);
            $this->assign('menu',$menuLevel);
            $this->assign('id',$id);
            $this->assign('info',$info);
            return $this->fetch();

        }
    }

    public function delete(){
        $id = input('id');
        $menu = Db::name('UserRule')->where('id',$id)->delete();
        if($menu){
            return $this->success('删除成功',url('index/menu/index'));
        }else{
            return $this->error('删除失败');
        }
    }
}