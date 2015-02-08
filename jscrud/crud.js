/**
 * 新增记录
 * @param params json对象，形式：
 * {table: 'test', data: {name: 'aaa', age:3, create_time: db_time()}}
 * table - 表名
 * data - json对象，指定字段名和值
 * @param callback 自定义回调函数
 */
function db_create(params, callback) {
    var arg = 'a=create&table=' + params.table;
    for (var key in params.data) {
        var val = params.data[key];
        arg += '&' + key + '=' + val;
    }
    db_post(url_db, arg, callback);
}

/**
 * 修改记录
 * @param params json对象，形式：
 * {table:'test', condition:{name:'aaa'}, set:{age:10}}
 * table - 表名
 * condition - json对象，指定做为查询条件的字段名和值
 * set - json对象，指定要修改的字段名和值
 * @param callback 自定义回调函数
 */
function db_update(params, callback) {
    var arg = 'a=update&table=' + params.table;

    //更新条件的字段名和值
    for (var key in params.condition) {
        var val = params.condition[key];
        arg += '&cdt_' + key + '=' + val;
    }

    //要更新的字段名和值
    for (var key in params.set) {
        var val = params.set[key];
        arg += '&set_' + key + '=' + val;
    }

    db_post(url_db, arg, callback);
}

/**
 * 删除记录
 * @param params json对象，形式：
 * {table:'test', condition:{id:1}}
 * table - 表名
 * condition - json对象，指定做为查询条件的字段名和值
 * @param callback 自定义回调函数
 */
function db_delete(params, callback) {
    var arg = 'a=delete&table=' + params.table;
    for (var key in params.condition) {
        var val = params.condition[key];
        arg += '&' + key + '=' + val;
    }
    db_post(url_db, arg, callback);
}

/**
 * 查询记录
 * @param params json对象，形式：
 * {table:'test', condition:{name:'aaa'}, orderby:{create_time:'desc', name:'asc'}}
 * table - 表名
 * condition - json对象，指定做为查询条件的字段名和值
 * orderby - json对象，指定做为排序的字段，值只能为 asc(升序)、desc(降序)
 * @param callback 自定义回调函数
 */
function db_find(params, callback) {
    var arg = 'a=find&table=' + params.table;

    //查询条件的字段名和值
    for (var key in params.condition) {
        var val = params.condition[key];
        arg += '&cdt_' + key + '=' + val;
    }

    //排序条件的字段名和值
    for (var key in params.orderby) {
        var val = params.orderby[key];
        arg += '&order_' + key + '=' + val;
    }

    alert(arg);
    db_post(url_db, arg, callback);
}

/**
 * 发送post请求
 * @param {type} is_data
 * @returns {String}
 */
function db_post(url, data, callback) {
    var xmlHttpRequest = null;

    if (window.ActiveXObject)
    {
        xmlHttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }
    else if (window.XMLHttpRequest)
    {
        xmlHttpRequest = new XMLHttpRequest();
    }
    if (null != xmlHttpRequest)
    {
        xmlHttpRequest.open("POST", url, true);
        xmlHttpRequest.onreadystatechange = function ()
        {
            if (xmlHttpRequest.readyState == 4)
            {
                if (xmlHttpRequest.status == 200)
                {
                    var content = xmlHttpRequest.responseText;
                    callback(eval('(' + content + ')'));
                }
            }
        };
        xmlHttpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlHttpRequest.send(data);    //形式："v1=" + v1 + "&v2=" + v2
    }
}

/**
 * 工具方法，返回当前日期 或 当前日期与时间
 * @param {boolean} is_data 是否需要返回日期格式，true/false
 * @returns {String} 
 */
function db_time(is_data) {
    var myDate = new Date();
    var y = myDate.getFullYear();    //获取完整的年份(4位,1970-????) 
    var m = myDate.getMonth();       //获取当前月份(0-11,0代表1月) 
    var d = myDate.getDate();        //获取当前日(1-31) 
    var h = myDate.getHours();       //获取当前小时数(0-23) 
    var min = myDate.getMinutes();     //获取当前分钟数(0-59) 
    var s = myDate.getSeconds();     //获取当前秒数(0-59)  

    if (is_data === true) { //返回当前日期
        return y + '-' + m + '-' + d;
    } else { //返回当前时间
        return y + '-' + m + '-' + d + ' ' + h + ':' + min + ':' + s;
    }
}