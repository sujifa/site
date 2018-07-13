<?php
namespace app\admin\controller;

use \app\admin\model\TagModel;
class SitesCate extends Common
{
    public function index()
    {
        $model = new TagModel();
        $cate = $model->getAll();
        if($cate){
            $this->assign('cate',$cate);
        }

        return view();
    }

    public function add()
    {
        $data = input('post.');
        if($data){
            $model = new TagModel();
            $info = $model->save($data);
            if($info){
                return $this->success('添加成功',url('index'));
            }else{
                return $this->error('添加失败');
            }
        }

        return view();
        
    }

    public function edit()
    {
        $data = input('post.');
        $id = input('id');
        $model = new TagModel();
        if($data){
            $result = $model->isUpdate(true)->save($data);
            if($result){
                return $this->success('编辑成功',url('index'));
            }else{
                return $this->error('编辑失败');
            }
        }
        if($id){
            $info = $model->find($id);
            if($info){
                $this->assign('info',$info);
            }else{
                return $this->error('没有该分类信息');
            }
            return view();
        }
    }

    public function delete()
    {
        $id = input('id');
        $model = new TagModel();
        $result = $model->where('id',$id)->delete();
        if($result){
            return $this->success('删除成功',url('index'));
        }else{
            return $this->error('删除失败');
        }
    }


}
