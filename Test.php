<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/27
 * Time: 12:54
 */
class Test{

    public static function __callStatic($name, $arguments)
    {
       return call_user_func_array([new Db(strtolower(__CLASS__)),$name],$arguments);
    }


}