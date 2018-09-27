# PDO数据库操作类

## 可用方法列表

where 

order

limit

get

all

first

create

update

del

toArray

### where

```
where($array)方法接收一个数组[$one,$two,$three],
$one  字段名(列名) string
$two  关系 (= ,!= ...) string
$three 值  string number

其后可链式调用(也可继续调用where方法)
```

示例 `Test::where(['id','>',1])->where(['user','=','zhangsan'])->get();`

### order

```
order($lie,$order='desc')方法默认接收一个参数字段名(列名)，可选参数$order为排序方式，默认降序，尚未支持多次链式调用order方法
```

### limit

```
该方法忘了写，有空再完善
```

### get、all

```
get()、all()方法效果是一样的，只是看使用习惯，得到集合对象，能取二维数组，后可跟toArray()方法转换成纯数组
```

### first

```
first($id=null,$key='id')方法可支持两个参数，当不传参数的时候，默认取结果的第一条
只传一个参数时，只会取出主键等于$id的数据，第二个参数是主键列名，默认为id
```

### create

```
create([]);
Test::create([
    ''=>'',
    ...
]);
```

### update

```
参数和create方法类似
如果前面有where等条件闲置，只修改条件范围内的数据
如果Test::update([
    ''=>'',
    ...
]);
将修改整张表
```

### del

```
del($id=null)
如果不传$id，看是否有条件限制，没有清空表数据，有的话删除条件里面的
如果传了，删除主键id对应的值
```

### toArray

```
不接收参数，跟在get、all后面得到数组数据
```



## 文件结构

### 数据库配置文件

mysqlconfig.php->数据库配置文件

```
[
"dbms"=>'mysql',     //数据库类型
"host"=>'localhost', //数据库主机名
"dbName"=>'djz',   //使用的数据库
"user"=>'root',      //数据库连接用户名
"pass"=>'root',         //对应的密码
]
```

### 操作数据库核心类文件

Db.php->操作数据库核心类文件

### 模型类

Test.php->模型类文件，类名代表表名，不区分大小写

初始格式

```
class Test{


    public static function __callStatic($name, $arguments)
    {
       return call_user_func_array([new Db(strtolower(__CLASS__)),$name],$arguments);
    }


}
```

### 引入示例

yunxing.php->示例引入核心数据库操作类文件和模型文件，其他引入方式均可

原理

外部通过模型类Test静态调用核心类的对外开放方法

如 `Test::get();`

Test这个模型类找不到get这个静态方法，触发__callStatic魔术方法

```
 public static function __callStatic($name, $arguments)
    {
       return call_user_func_array([new Db(strtolower(__CLASS__)),$name],$arguments);
    }
```

通过回调函数实例化Db类对象，调用get方法

​	Db类在被实例化的时候触发构造方法__construct,在这里引入配置文件并链接数据库，把相关数据存入受保护的属性里面，为什么这里存受保护的属性?

​	最重要的是为了我们在foreach遍历的时候只会循环到开放的arr属性(调用get等方法时把结果赋值给到的arr属性)；

当取数据时，操作完成后方法内调用storage方法把查询结果存入arr开放属性中；

**ArrayAccess接口是为了数组方式调用数据**

## 对象形式调用

### 调用示例

Test::first(1)->user;

​	将会得到数据表中id为1的数据里面的user这个字段的值，

当然，如果获取数据是多条的二维数组，那不能这样调用，

这样的数据用数组形式调用也没有意义，支持foreach循环。

### 原理

​	通过Db类里面的魔术方法__get，调用不存在或者不开放的属性时触发，返回当前对象开放属性arr数组下标对应的数据

## 数组形式调用

### 调用示例

Test::first(1)['user'];

​	将得到数据表中id为1的数据里面的user这个字段的值

### 原理

通过重写PHP内置接口ArrayAccess里面的抽象方法

```
//直接用对象数组方式取值检测是否存在时调用
offsetExists
返回值是否存在

//直接用对象数组方式取值时调用
offsetGet
返回调取数据

//直接用对象数组方式设置值时调用
offsetSet
//添加值

//直接用对象数组方式取值删除时调用
offsetUnset
//删除对应值
```

