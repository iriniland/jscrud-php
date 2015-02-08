在后台的开发中，有大量简单的增删改查操作

例如添加信息，通常的做法是：页面提交数据给后台的相应方法，后台获取提交的参数，对各个参数进行安全验证，有问题就返回给页面，没有问题就提交到数据库

修改和删除信息也是同样的流程，而且大多数情况都是单表操作

所以，在后台的开发中，单表的增加、修改、删除操作占了很大的比重

jscrud-php希望简化这些简单重复的操作

例如添加信息，做法变为：页面中用js调用jscrud的db_create方法，参数中指明要操作的表名、要提交的数据（字段名和值）、回调函数，这样就省掉了php和数据库的开发工作

适用的场景条件：

1. 后台系统中的单表操作

2. 没有业务逻辑，只是单纯的增加、修改、删除、查询一个表里的数据

安全性问题：

可以自定义是否登录的验证

对于提交参数的格式验证，此版本中没有处理，后续新版本中会根据数据表中定义的数据项来逐个验证

js代码中直接暴露表名和字段名也不安全，后续版本会增加处理

使用步骤：

1. 把下载的jscrud-php目录放到项目根目录

2. 编辑config.mysqldb.php

修改数据库连接信息

在do_before函数中添加自定义操作逻辑，例如登录验证

3. 页面中引用crud.js

4. 定义url_db变量，指定crud.php的位置，然后调用js方法执行数据库操作

方法说明：

1.新增记录

db_create(params, callback)

参数 params json对象，形式：

{table: 'test', data: {name: 'aaa', age:3, create_time: db_time()}}

table - 表名

data - json对象，指定字段名和值

参数 callback 自定义回调函数

示例：

db_create({table: 'test', data: {name: 'aaa', age:3, create_time: db_time()}}, function (data) {

    //data的格式：

    //{ret:'err'} 操作失败的返回信息

    //{ret:'ok', 'id':3} 操作成功的返回信息，id为新增记录的ID

});
            
2. 修改记录

db_update(params, callback)

参数 params json对象，形式：

{table:'test', condition:{name:'aaa'}, set:{age:10}}

table - 表名

condition - json对象，指定做为查询条件的字段名和值

set - json对象，指定要修改的字段名和值

参数 callback 自定义回调函数

示例：

db_update({table:'test', condition:{name:'aaa'}, set:{age:10}}, function (data){

    //data的格式：

    //{ret:'err'} 操作失败的返回信息

    //{ret:'ok'} 操作成功的返回信息

})
            
3. 删除记录

db_delete(params, callback)

参数 params json对象，形式：

{table:'test', condition:{id:1}}

table - 表名

condition - json对象，指定做为查询条件的字段名和值

参数 callback 自定义回调函数

示例：

db_delete({table:'test', condition:{id:1}}, function (data){

    //data的格式：

    //{ret:'err'} 操作失败的返回信息

    //{ret:'ok'} 操作成功的返回信息

});
            
4. 查找记录

db_find(params, callback)

参数 params json对象，形式：

{table:'test', condition:{name:'aaa'}, orderby:{create_time:'desc', name:'asc'}}

table - 表名

condition - json对象，指定做为查询条件的字段名和值

orderby - json对象，指定做为排序的字段，值只能为 asc(升序)、desc(降序)

参数 callback 自定义回调函数

示例：

db_find({table:'test', condition:{name:'aaa'}, orderby:{create_time:'desc', name:'asc'}}, function (data){

    //data的格式：

    //{ret:'err'} 操作失败的返回信息

    //{查询结果集合} 操作成功的返回信息
});

5. 获得当前日期

db_time(is_data)

工具方法，返回当前日期 或 当前日期与时间

is_data 是否需要返回日期格式，true/false
