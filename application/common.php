<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//获取用户状态
function get_user_status($userStatus){
    switch ($userStatus){
        case '0':
            $result = "禁用";
            break;
        case "1":
            $result = "正常";
            break;
        default:
            $result = "无";
            break;
    }
    return $result;
}
/**
 * 删除目录及目录下所有文件或删除指定文件
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态
 */
function delDirAndFile($path, $delDir = FALSE) {
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}

/**
 * 数组数据层级缩进
 */
function array_level($array,$pid=0,$level=1){
    static $list = [];
    foreach($array as $v){
        if($v['pid'] == $pid){
            $v['level'] = $level;
            $list[] = $v;
            array_level($array,$v['id'],$level+1);
        }
    }
    return $list;
}

function menu_level($array,$pid=0,$level=1){
    static $list = [];
    foreach($array as $v){
        if($v['pid'] == $pid && $level<3){
            $v['level'] = $level;
            $list[] = $v;
            menu_level($array,$v['id'],$level+1);
        }
    }
    return $list;
}


/**
 * 返回权限是否被选中
 * @author vaey
 * @param  [type]  $id [description]
 * @return boolean     [description]
 */
function is_checked($id,$rules)
{
    $rules = explode(',',$rules);
    if(is_array($rules)){
        if(in_array($id,$rules)){
            return "checked";
        }else{
            return "";
        }
    }else{
        return "";
    }
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}



/**
 * tree转换为auth_view
 * @author  vaey
 */
function tree_to_auth_access($list,$rules,$level = 0,$repeat = "&nbsp;&nbsp;&nbsp;&nbsp;")
{
    $data = '';
    if($level>=2){
        $data = '<dd>';
    }
    foreach ($list as $key => $value) {
        if($level>=2){
            $data = $data.str_repeat($repeat, $level)."<label><input type='checkbox' name='ids' value='".$value['id']."'" .is_checked($value['id'],$rules)."> ".$value['title']."</label>";
            if (!empty($value['_child'])) {
                $data = $data.tree_to_auth_access($value['_child'],$rules,$level+1);
            }
        }else{
            if($level == 0){
                $str = "<dd class='dd1'>";
            }else{
                $str = "<dd>";
            }
            $data = $data.$str.str_repeat($repeat, $level)."<label><input type='checkbox' name='ids' value='".$value['id']."'" .is_checked($value['id'],$rules)."> ".$value['title']."</label></dd>";
            if (!empty($value['_child'])) {
                $data = $data.tree_to_auth_access($value['_child'],$rules,$level+1);
            }
        }
    }
    if($level>=2){
        return $data.'</dd>';
    }else{
        return $data;
    }
}

