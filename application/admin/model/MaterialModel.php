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

class MaterialModel extends Model
{
    protected $name = 'material';

    //获取所有素材
    public function getAll($page)
    {
        try{
            $data = $this
                ->order('id desc')
                ->paginate(6,false,[
                    'page'     => $page,
                    'var_page' => 'page',
                ]);
            return $data->items();
        }catch (Exception $e){
            return false;
        }
    }

}