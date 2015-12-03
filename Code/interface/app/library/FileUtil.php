<?php
/**
 * Created by PhpStorm.
 * User: wliu
 * Date: 14-8-26
 * Time: 上午12:49
 */

class FileUtil {
// 生成随机文件名
    function GetID($prefix) {
        //第一步:初始化种子
        //microtime(); 是个数组
        $seedstr = split(" ",microtime(),5);
        $seed = $seedstr[0]*10000;
        //第二步:使用种子初始化随机数发生器
        srand($seed);
        //第三步:生成指定范围内的随机数
        $random =rand(1000, 10000);

        $filename = date("YmdHis", time()).$random.$prefix;

        return $filename;
    }

    function isImg() {

    }
}