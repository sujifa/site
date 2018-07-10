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

class TagModel extends Model
{
    protected $name = 'tag';

    //获取文章
    public function getAll()
    {
        try{
            $data = $this->where('status',1)
                ->where('delete_time',0)
                ->order('id desc')
                ->select();
            return $data;
        }catch (Exception $e){
            return false;
        }
    }

}