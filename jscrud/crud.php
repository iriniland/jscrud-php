<?php


require_once("MysqlDB.php"); //引入数据库操作文件
$db = new MysqlDB(); //创建数据库操作对象
do_before(); //先执行自定义动作

$table = $_POST['table']; //表名
$action = $_POST['a']; //要执行的操作，create/delete/update/find
echo $action($db, $table, $_POST);



/**
 * 新增记录
 * @param type $db mysql操作对象
 * @param type $table 表名
 * @param type $post_args post请求参数数组
 * @return type json字符串
 */
function create($db, $table, $post_args){
    if(empty($table)){
        $ret = array('ret'=>'err');
    }else{
        $data = array(); //数组，存放新增数据
        
        foreach ($post_args as $key => $value) {
            //table和a 这两个参数之外的参数，是要添加的数据
            if(equal($key, 'a') || equal($key, 'table')){
                continue;
            }
            $data[$key] = $value;
        }
        
        //执行插入操作
        $newid = $db->create($table, $data);
        
        $ret_msg = $newid === false ? 'err' : 'ok';
        $ret = array('ret'=>$ret_msg, 'id'=>$newid);
    }
    
    return json_encode($ret);
}

/**
 * 更新记录
 * @param type $db mysql操作对象
 * @param type $table 表名
 * @param type $post_args post请求参数数组
 * @return type json字符串
 */
function update($db, $table, $post_args){
    if(empty($table)){
        $ret = array('ret'=>'err');
    }else{
        $pre_cdt = 'cdt_'; //更新条件的参数名前缀
        $pre_set = 'set_'; //更新值的参数名前缀
        $data_conditoin = array(); //数组，更新的查询条件
        $data_set = array(); //数组，更新的值
        foreach ($post_args as $key => $value) {
            if(strpos($key, $pre_cdt) === 0){
                $col_name = str_replace($pre_cdt, '', $key);
                $data_conditoin[$col_name] = $value;
            }
            elseif(strpos($key, $pre_set) === 0){
                $col_name = str_replace($pre_set, '', $key);
                $data_set[$col_name] = $value;
            }
        }
        $ret_update = $db->update($table, $data_conditoin, $data_set);
        $ret_msg = $ret_update === false ? 'err' : 'ok';
        $ret = array('ret'=>$ret_msg);
    }
    return json_encode($ret);
}

/**
 * 删除记录
 * @param type $db mysql操作对象
 * @param type $table 表名
 * @param type $post_args post请求参数数组
 * @return type json字符串
 */
function delete($db, $table, $post_args){
    if(empty($table)){
        $ret = array('ret'=>'err');
    }else{
        $data = array(); //数组，要删除的数据的查询条件
        
        foreach ($post_args as $key => $value) {
            //table和a 这两个参数之外的参数，是条件数据
            if(equal($key, 'a') || equal($key, 'table')){
                continue;
            }
            $data[$key] = $value;
        }
        
        //执行插入操作
        $ret_del = $db->delete($table, $data);
        
        $ret_msg = $ret_del === false ? 'err' : 'ok';
        $ret = array('ret'=>$ret_msg);
    }
    
    return json_encode($ret);
}

function find($db, $table, $post_args){
if(empty($table)){
        $ret = array('ret'=>'err');
    }else{
        $pre_cdt = 'cdt_'; //查询条件的参数名前缀
        $pre_order = 'order_'; //排序的参数名前缀
        $data_conditoin = array(); //数组，查询条件
        $str_order = ''; //排序字符串
        foreach ($post_args as $key => $value) {
            if(strpos($key, $pre_cdt) === 0){
                $col_name = str_replace($pre_cdt, '', $key);
                $data_conditoin[$col_name] = $value;
            }
            elseif(strpos($key, $pre_order) === 0){
                $col_name = str_replace($pre_order, '', $key);
                $str_order .= $col_name . ' ' . $value . ',';
            }
        }
        $str_order = substr($str_order, 0, strlen($str_order)-1);
        
        $li = $db->findAll($table, $data_conditoin, $str_order);
        $ret = $li;
    }
    return json_encode($ret);    
}

function ss_log($message) {
	$fd = fopen ( '/alidata/www/xinglong/task/log.html', "a+" );
	fputs ( $fd, "<p style='border-bottom:1px solid #000; padding:5px;'>"
                . "[" . date ( "Y-m-d H:i" ,strtotime("+8 hour")) . "] - " . $message . 
                "</p>" );
	fclose ( $fd );
}
function equal($str1, $str2){
	if (strcmp($str1,$str2) == 0) {
		return true;
	}
	return false;
}


