<?php
namespace app\admin\controller;

use \app\admin\model\MaterialModel;
class Material extends Common
{
    public function index()
    {
        $model = new MaterialModel();
        $info = $model->selectMaterial();
        if($info){
            $this->assign('info',$info);
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
                }
                $result = [];
                $result['data'] = $data;
                $result['pages'] = (int)ceil($total/6);
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
        $model = new MaterialModel();
        $info = $model->find($id);
        $a = delFile(dirname(APP_PATH).$info['local_url']);
        $result = $model->where('id',$id)->delete();
        if($result){
            return $this->success('删除成功',url('index'));
        }else{
            return $this->error('删除失败');
        }
    }


}
