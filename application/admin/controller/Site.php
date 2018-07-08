<?php
namespace app\admin\controller;


use app\admin\model\SiteModel;
use app\admin\model\SitesCateModel;
use org\Favion;

class Site extends Common
{
    public function index()
    {
        $model = new SiteModel();
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
            //获取站点的ico地址
            $ico = $this->getFavion($data['site_url']);
            $data['ico'] = $ico;
            $model = new SiteModel();
            $info = $model->save($data);
            if($info){
                return $this->success('添加成功',url('index'));
            }else{
                return $this->error('添加失败');
            }
        }

        $cateModel = new SitesCateModel();
        $cate = $cateModel->getAll();
        $this->assign('cate',$cate);
        return view();

    }
    public function edit()
    {
        $data = input('post.');
        $id = input('id');
        $model = new SiteModel();
        if($data){
            //获取站点的ico地址
//            $ico = $this->getFavion($data['site_url']);
//            $data['ico'] = $ico;
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
                return $this->error('没有该站点信息');
            }

            $cateModel = new SitesCateModel();
            $cate = $cateModel->getAll();
            $this->assign('cate',$cate);
            return view();
        }

    }

    public function delete()
    {
        $id = input('id');
        $model = new SiteModel();
        $result = $model->where('id',$id)->delete();
        if($result){
            return $this->success('删除成功',url('index'));
        }else{
            return $this->error('删除失败');
        }
    }

    //获取ico路径
    // https://www.google.com/s2/favicons?domain=url   来自Google的
    // https://favicon.yandex.net/favicon/sina.cn  来自yandex的解析
    public function getFavion($url)
    {
        $favion = new Favion();
        $dir = './ico';//图标缓存目录
        //如果缓存目录不存在则创建
        if (!is_dir($dir)) mkdir($dir,0777,true) or die('创建缓存目录失败！');

//        $url = $favion->getParam('url'); //获取传过来的链接参数
        //没有url参数，输出默认图像
        if(!$url){
            return $favion->echoFav();
        }
        $http = 'http://';
        //如果网页不是http://开头的，就给他加上
        if(substr($url, 0, 4) != 'http')
        {
            $url = 'http://'.$url;
        }
        else if(substr($url, 0, 5) == 'https')
        {
            $http = 'https://'; //如果是https头，传到后面取图标时加上。防止出现302重定向
        }
        //非法域名时调用默认文件
        if($favion->isUrl($url) != '1'){
            return $favion->echoFav();
        }
        $arr = parse_url($url); //分解目标域名
        $domain = $arr['host']; //没有头和尾的裸域名

        $fav = $dir."/".$domain.".ico"; //图标保存的路径和名称
        //调用缓存文件
        if (file_exists($fav)) //有缓存就直接输出缓存
        {
            return $fav;
        }

        //直接尝试站点根目录下的favion.ico文件  (通用方法)
        $result = $favion->getFav($http.$domain."/favicon.ico", $fav);
        if($result != -1){
            return $result;
        }
        //直接请求目标网址并匹配<meta>标签中的favion.ico
        $curl = $favion->get_url_content($url);
        $file = $curl['exec'];
        preg_match('|href\s*=\s*[\"\']([^<>]*?)\.ico[\"\'\?]|i',$file,$a);    //正则匹配
        //没有匹配结果
        if(!(isset($a[1]) && $a[1]))
        {
            $info = $favion->getFav('https://ico.mikelin.cn/'.$domain, $fav);
            if($info == -1){
                return $favion->echoFav($fav);
            }else{
                return $info;
            }
        }
        $a[1] .='.ico'; //加上后缀名
        //判断链接是否完整
        $a[1] = $this->typeOfUrl($a[1]);
        $info = $favion->getFav($a[1], $fav);    //如果favicon自身带有完整链接

        if($info != -1){
            return $info;
        }
        if(substr($a[1], 0, 1) == '/')  //相对路径的处理
        {
            $a[1] = substr($a[1], 1);
        }
        if(substr($a[1], 0, 3) == '../')  //相对路径的处理
        {
            $a[1] = substr($a[1], 3);
        }
        if(substr($a[1], 0, 2) == './')  //相对路径的处理
        {
            $a[1] = substr($a[1], 2);
        }
        $u = $http.$domain.'/'.$a[1];   //手动加上链接再试一次
        $info2 = $favion->getFav($u, $fav);
        if($info2 != -1){
            return $info2;
        }
        //上面的方法都没法获取
        $info3 = $favion->getFav('https://ico.mikelin.cn/'.$domain, $fav);

        if($info3 == -1){
            return $favion->echoFav($fav);
        }else{
            return $info3;
        }

    }

    private function typeOfUrl($url){
        if(empty($url)){
            return '';
        }
        //判断是否http开头
        if(!preg_match("/^http/",$url)){
            //判断图片路径是否以“//”开头
            if(preg_match("/^\/\//",$url)){
                $url = substr($url,2);
            }
            //判断图片路径是否以“/”开头
            if(preg_match("/^\//",$url)){
                $url = substr($url,1);
            }
        }
        return $url;
    }

}
