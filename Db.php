<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/27
 * Time: 9:04
 */
class Db implements ArrayAccess{
protected $dbms; //数据库类型 mysql
protected $host; //主机地址
protected $dbName; //数据库名
protected $user; //用户名
protected $pass; //密码
protected $pdo; // PDO对象
protected $model; //表名
protected $where=''; //where语句
protected $order=''; //排序语句
protected $limit=''; //截取语句
public function __construct($model)
{
    //引入配置文件
    $config=include "mysqlconfig.php";
    $this->dbms=$config['dbms'];
    $this->host=$config['host'];
    $this->dbName=$config['dbName'];
    $this->user=$config['user'];
    $this->pass=$config['pass'];
    $this->model=$model;
    $dsn="{$this->dbms}:host={$this->host};dbname={$this->dbName}";
if(!($this->pdo)){
    try {
        $dbh = new PDO($dsn, $this->user, $this->pass); //初始化一个PDO对象
        $this->pdo=$dbh;
//        echo "连接成功<br/>";
//        $dbh = null;
    } catch (PDOException $e) {
        die ("Error!: " . $e->getMessage() . "<br/>");
    }
}
}

//此方法为了对象调用形式获取值
    public function __get($name)
    {
        return $this->arr["$name"];
    }


//下面几个方法是数组取值接口抽象方法的重写，为了数组形式获取值
//存储数组
    public $arr=[];
//直接用对象数组方式取值检测是否存在时调用
    public function offsetExists($key){
        return array_key_exists($key, $this->arr);
    }
//直接用对象数组方式取值时调用
    public function offsetGet($key){
        return isset($this->arr[$key]) ? $this->arr[$key] : '';
    }
//直接用对象数组方式设置值时调用
    public function offsetSet($key, $value){
        $this->arr[$key] = $value;
    }
//直接用对象数组方式取值删除时调用
    public function offsetUnset($key){
        unset($this->arr[$key]);
    }
//重写结束

//存储数据方法
protected function storge($arr){
    $this->arr=$arr;
    return $this;
}


//get之后不能继续操作
public function get(){
    $pdo=$this->pdo;
    $model=$this->model;
    $where=$this->where;
    $order=$this->order;
    $limit=$this->limit;
    $sql="select * from $model".' '. $where.$order.$limit;
//    echo $sql;die;
    //关联数组数据
    $result=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    return $this->storge($result);
}

//all方法和get一样，只是看习惯
    public function all(){
        $pdo=$this->pdo;
        $model=$this->model;
        $where=$this->where;
        $order=$this->order;
        $limit=$this->limit;
        $sql="select * from $model".' '. $where.$order.$limit;
//    echo $sql;die;
        $result=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $this->storge($result);
    }

    //all方法和get一样，只是看习惯
    public function first($id=null,$key='id'){
        $pdo=$this->pdo;
        $model=$this->model;
        if(!$id){
            $where=$this->where;
            $order=$this->order;
            $limit=$this->limit;
            $sql="select * from $model".' '. $where.$order.$limit;
        //    echo $sql;die;
            $result=$pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        }else{
            $sql="select * from $model where $key = $id";
//            echo $sql;
            $result=$pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        }
        return $this->storge($result);
    }

//where查询
public function where($arr){
    $oldsql=$this->where;
    if($oldsql){
        $where=' and ';
    }else{
        $where='where';
    }
    if(gettype($arr[2])=='string'){
        $sql=$where." {$arr[0]} {$arr[1]} '{$arr[2]}'";
    }else{
        $sql="$where {$arr[0]} {$arr[1]} {$arr[2]}";
    }

    $this->where=$oldsql.$sql;
    return $this;
}
//排序方法,默认降序
public function order($lie,$order='desc'){
$sql=" order by $lie $order";
$this->order=$sql;
return $this;
}

//原生sql语句执行
public function query($sql){
    $pdo=$this->pdo;
    $result=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

//create 添加数据方法

    /**
     * @param $arr  接收数组参数
     */
public function create($arr){
    $pdo=$this->pdo;
    $model=$this->model;
    $keys=array_keys($arr);
    $keys=implode(',',$keys);
    $val=array_values($arr);
   $str='';
   foreach($val as $k=>$v){
       if(gettype($v)=='string'){
           $str.="'$v',";
       }else{
           $str.=$v.',';
       }
   }
    $str=rtrim($str,',');
   $sql="insert into $model ($keys) values($str)";
   $result =$pdo->exec($sql);
   return $result;
}

//del 删除方法
public function del($id=null){
        $pdo=$this->pdo;
        $model=$this->model;
        if(!$id){
            $where=$this->where;
            $order=$this->order;
            $limit=$this->limit;
            $sql="delete from $model".' '. $where.$order.$limit;
        }else{
            $sql="delete from $model where id = $id";
        }
        $result=$pdo->exec($sql);
        return $result;
        }

//修改方法
public function update($arr){
    $str='';
    foreach ($arr as $k=>$v){
        if(gettype($v)=='string'){
            $v="'$v'";
        }
        $str.="$k=$v,";
    }
    $str=rtrim($str,',');
    $pdo=$this->pdo;
    $model=$this->model;
    $where=$this->where;
    $order=$this->order;
    $limit=$this->limit;
    $sql="update $model set $str".' '. $where.$order.$limit;
    $result=$pdo->exec($sql);
    return $result;
}
//返回数组
public function toArray(){
    return $this->arr;
}
//返回调取方法错误
public function __call($name, $arguments)
{
    echo $name."方法不存在";
}

//返回调取方法错误
public static function __callStatic($name, $arguments)
{
    echo $name."方法不存在";
}
}


/*
 * 模型类的定义
 */

//class User{
//
//    public static function __callStatic($name, $arguments)
//    {
//        return call_user_func_array([new Db(strtolower(__CLASS__)),$name],$arguments);
//    }
//
//
//}