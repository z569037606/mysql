<?php
include_once 'Db.php';
include_once 'Test.php';
echo '<pre>';
print_r(Test::get());
//foreach (Test::get() as $v){
//    print_r($v);
//    echo '<pre>';
//}
//print_r((new Db('test'))->where(['id','>',1])->order('id','asc')->all());
//print_r((new Db('test'))->first(1));
//print_r((new Db('test'))->query('select * from test'));
//var_dump((new Db('test'))->create([
//    'id'=>5,
//    'user'=>'zhangsan'
//
//]));
//var_dump((new Db('test'))->where(['id','=',3])->del());
//var_dump((new Db('test'))->del(5));
//$arr=Test::where(['id','>',1])->get();
//print_r($arr);
//echo Test::where(['id','=',1])->update([
//    'user'=>'好'
//]);
?>