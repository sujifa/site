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

class SiteModel extends Model
{
    protected $name = 'site';

    //获取分类列表
    public function getAll()
    {
        try{
            $data = $this->where('status',1)->select();
            return $data;
        }catch (Exception $e){
            return false;
        }
    }

    public function siteAndCate()
    {
        return $this->belongsTo('SitesCateModel','cate_id','id');
    }

}