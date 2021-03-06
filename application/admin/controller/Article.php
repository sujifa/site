<?php
namespace app\admin\controller;

use \app\admin\model\ArticleModel;
use app\admin\model\MaterialModel;
use app\admin\model\TagModel;

class Article extends Common
{
    public function index()
    {
        $model = new ArticleModel();
        $article = $model->getAll();
        $a = $model->where('id',2)->find();
        if($article){
            $this->assign('article',$article);
            $this->assign('a',$a);
        }

        return view();
    }

    public function add()
    {
        $data = input('post.');
        if($data){
            $model = new ArticleModel();
            $tag = array_values($data['tag']);
            unset($data['tag']);
            $model->save($data);
            $id = $model->getLastInsID();
            $info = ArticleModel::get($id);
            $result = $info->artTag()->attach($tag);
            if($result){
                return $this->success('添加成功',url('index'));
            }else{
                return $this->error('添加失败');
            }
        }
        //获取标签
        $tagModel = new TagModel();
        $tagInfo = $tagModel->getAll();
        $this->assign('tagInfo',$tagInfo);

        return view();
        
    }

    public function edit()
    {
        $data = input('post.');
        $id = input('id');
        $model = new ArticleModel();
        if($data){
            $tag = array_values($data['tag']);
            unset($data['tag']);
            $result = $model->isUpdate(true)->save($data);
            $info = ArticleModel::get($data['id']);
            $info->artTag()->detach();
            $result = $info->artTag()->attach($tag);
            if($result){
                return $this->success('编辑成功',url('index'));
            }else{
                return $this->error('编辑失败');
            }
        }
        if($id){
            $info = $model->find($id);
            if($info){
                //获取标签
                $tagModel = new TagModel();
                $tagInfo = $tagModel->getAll();
                $this->assign('tagInfo',$tagInfo);
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
        $model = new ArticleModel();
        $result = $model->where('id',$id)->delete();
        if($result){
            return $this->success('删除成功',url('index'));
        }else{
            return $this->error('删除失败');
        }
    }

    //上传图片
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        //先定义返回数据
        $data = [];
        $data['code'] = 0;
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $url = config('site').'/uploads/'.date('Ymd').'/'.($info->getFilename()); //($info->getSaveName());
                $localUrl = '/public/uploads/'.date('Ymd').'/'.($info->getFilename());
                $name = $info->getFilename();
                //将文件保存到素材数据库表
                $id = $this->saveMaterial($url,$localUrl,$name);
                $data['code'] = 1;
                $data['msg']['url'] = $url;
                $data['msg']['id'] = $id;
                $data['msg']['name'] = $name;
                die(json_encode($data));
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getFilename();
            }else{
                die(json_encode($data));
            }
        }
    }

    //保存素材数据到素材表、
    private function saveMaterial($url,$localUrl,$title)
    {
        $data = [];
        $data['url'] = $url;
        $data['local_url'] = $localUrl;
        $data['title'] = $title;
        $model = new MaterialModel();
        $info = $model->saveMaterial($data);
        return $info;
    }


}
