<?php
namespace app\admin\controller;

use \app\admin\model\MaterialModel;
class Material extends Common
{
    public function index()
    {
        $model = new MaterialModel();
        $cate = $model->getAll();
        if($cate){
            $this->assign('cate',$cate);
        }

        return view();
    }

    public function all()
    {
        $page = input("get.page");
        if($page){
            $model = new MaterialModel();
            $info = $model->getAll($page);
            $total = $model->count('id');
            if($info){
                $data = [];
                foreach($info as $k=>$v){
                    $data[] = $v->toArray();
                }header("Content-Type:text/html; charset=utf-8");
                $result = [];
                $result['data'] = $data;
                $result['pages'] = (int)ceil($total/20);
                die(json_encode($result));
            }else{
                echo '没有更多素材';
            }
        }

        return view();
    }

    public function delete()
    {
        $id = input('id');
        $model = new SitesCateModel();
        $result = $model->where('id',$id)->delete();
        if($result){
            return $this->success('删除成功',url('index'));
        }else{
            return $this->error('删除失败');
        }
    }


}
