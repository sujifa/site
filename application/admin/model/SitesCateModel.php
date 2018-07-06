<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/7/6
 * Time: 20:28
 */

namespace app\admin\model;

use think\Exception;
use think\Model;

class SitesCateModel extends Model
{
    protected $name = 'sites_cate';

    //获取分类列表
    public function getAll()
    {
        try{
            $data = $this->where('status',1)->order('sort')->select();
            return $data;
        }catch (Exception $e){
            return false;
        }
    }

}