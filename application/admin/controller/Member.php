<?php
/**
 * Created by PhpStorm.
 * User: mrsu
 * Date: 2017/3/22
 * Time: 16:56
 */
namespace app\admin\controller;

use think\Db;
use think\Loader;
use think\Request;
class Member extends Common {
    //会员列表
    public function index(){
        $userList = Db::name('Users')->order("id desc")->paginate(10);
        $this->assign('userList',$userList);
        return $this->fetch('index');
    }
    //编辑会员信息
    public function edit($id){
        if(request()->isPost()){
            $data = input('post.');
            $validate = Loader::validate("Member");
            if(!$validate->scene('edit')->check($data)){
                return $this->error($validate->getError());
            }
            $userInfo['username'] = $data['username'];
            $userInfo['nickname'] = $data['nickname'];
            $userInfo['status'] = $data['status'];
            $updateUser = Db::name('Users')->where('id',$data['id'])->update($userInfo);
            if($updateUser !== false){
                $userGroupAccess['group_id'] = $data['group_id'];
                $updateUser2 = Db::name('userGroupAccess')->where('uid',$data['id'])->update($userGroupAccess);
                if($updateUser2 !== false){
                    return $this->success('编辑成功',url('index'));
                }else{
                    return $this->error('编辑失败');
                }
            }else{
                return $this->error('编辑失败');
            }
        }else{
            //查询数据
            if(empty($id)){
               return $this->error('请选择有效数据');
            }
            $user['id'] = $id;
            $userInfo = Db::name('Users')->alias('u')->join('UserGroupAccess ua','u.id=ua.uid','LEFT')->where('u.id',$id)->find();
            $this->assign('userInfo',$userInfo);

            $userGroup = Db::name('UserGroup')->select();
            $this->assign('userGroup',$userGroup);
            return $this->fetch('edit');
        }
    }

    public function editPass($id){
        if(request()->isPost()){
            $data = input('post.');
            $validate = Loader::validate("Member");
            if(!$validate->scene('editPass')->check($data)){
                return $this->error($validate->getError());
            }
            $password = md5($data['password'].config('MD5_KEY'));
            $updatePass = Db::name('Users')->where('id',$id)->update(['password'=>$password]);
            if($updatePass !== false){
                return $this->success('编辑成功',url('index'));
            }else{
                return $this->error('编辑失败');
            }
        }else{
            if(empty($id)){
                return $this->error('数据错误');
            }
            $user['id'] = $id;
            $userInfo = Db::name('Users')->where($user)->find();
            $this->assign('userInfo',$userInfo);
            return $this->fetch('editPass');
        }
    }

    public function delete(){
        $id = input('id');
        $userDel = Db::name('Users')->where('id',$id)->delete();
        if($userDel !== false){
            return $this->success('用户删除成功',url('index'));
        }else{
            return $this->error('删除失败');
        }
    }

    public function add(){
        if(request()->isPost()){
            $data = input('post.');
            $validate = Loader::validate("Member");
            if(!$validate->scene('add')->check($data)){
                return $this->error($validate->getError());
            }
            $info['username'] = $data['username'];
            $info['nickname'] = $data['nickname'];
            $info['password'] = md5($data['password'].config('MD5_KEY'));
            $info['status'] = $data['status'];
            $info['add_time'] = time();
            $addUser = Db::name('Users')->insert($info);
            if($addUser){
                $info2['group_id'] = $data['group_id'];
                $info2['uid'] = Db::name('Users')->getLastInsID();
                $addUserGroupAccess = Db::name('UserGroupAccess')->insert($info2);
                if($addUserGroupAccess !== false){
                    return $this->success('添加成功',url('index'));
                }else{
                    return $this->error('添加失败');
                }
            }else{
                return $this->error('添加失败');
            }
        }else{
            $userGroup = Db::name('UserGroup')->select();
            $this->assign('userGroup',$userGroup);
            return $this->fetch('add');
        }
    }
}