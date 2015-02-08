<?php

$db_config["hostname"] = "localhost"; //服务器地址
$db_config["username"] = "root"; //数据库用户名
$db_config["password"] = "6ba5"; //数据库密码
$db_config["database"] = "test"; //数据库名称
$db_config["charset"] = "utf8";//数据库编码
$db_config["pconnect"] = 0;//开启持久连接 1-是，0-否

session_start();

/**
 * 做增删改查操作前，先执行此方法，根据自身需求定义此方法，例如验证是否登录
 */
function do_before(){
    /*
    //示例
    $id = intval($_SESSION['user_id']);
    if($id < 1){
        echo json_encode(array('ret'=>'no login'));
        exit(); //退出，不再之后后续操作
    }
    */
}