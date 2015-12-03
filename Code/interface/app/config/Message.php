<?php
/**
 * Created by PhpStorm.
 * User: wliu
 * Date: 14-8-22
 * Time: 下午11:21
 */

class Message {
    /**
     * 系统错误（所有未被主动识别的错误。）
     * @var int
     */
    public static $_ERROR_SYSTEM = -1;

    /**
     * 提交的数据格式错误（客户端提交的数据不合法）
     * @var int
     */
    public static $_ERROR_LOGIC = -2;

    /**
     * 权限错误，试图访问未经允许的资源
     * @var int
     */
    public static $_ERROR_AUTHORIZATION = -3;

    /**
     * 资源请求错误，试图获取已经过期或则并不存在的资源。
     * @var int
     */
    public static $_ERROR_NOFOUND = -4;

    /**
     * 远程请求方法调用错误
     * @var int
     */
    public static $_ERROR_CODING = -5;


    /**
     * 请登录后再试
     * @var int
     */
    public static $_ERROR_UNLOGIN = -6;
    
    /**
     * 基建商城提交订单，采购额度不足
     * @var int
     */
    public static $_ERROR_CREDITS = -7;
    /**
     * 成功确认（无返回内容仅仅用作操作成功后的确认标志）
     * @var int
     */
    public static $_OK = 0;

    /**
     * 成功但无数据获取
     * @var int
     */
    public static $_OK_NOFOUND = 1;

    /**
     * @var int
     */
    public static $_OK_Null = 1;

    public static function getMessages() {
        $message = array();
        $message['101'] = "请登录后访问。";
        $message['102'] = "数据格式不合法。";
        $message['103'] = "您的权限不够，不能访问。";
        $message['104'] = "您请求的数据不存在或者已过期。";
        //业务编码
        $message['201']= "您输入的账号已经被使用，请重新输入。";
        $message['202']= "您输入的手机号码已被注册，请重新输入。";
        $message['203']= "您的账号不存在或者密码错误，请重新登录。";
        $message['204']= "您输入的手机号码尚未注册，请确定后再试。";
        $message['205']= "您上传的图像过大，请重新上传。";
        $message['206']= "只允许上传JPG.JPEG，PNG格式的图片。";
        $message['207']= "您输入的密码错误，请重新再试。";
        //信用池相关
        $message['210']= "您的申请尚未结束。";
        //定制专区
        $message['220']= "定制申请已过期，不允许继续追加。";
        //积分商城
        $message['230']= "您的积分不足以完成本次兑换。";
        return $message;
    }

    /**
     * @return Array
     */
    public static function getBanks() {
        return array (
				"01" => "浙商银行",
				"02" => "工商银行",
				"03" => "招商银行",
				"04" => "浦发银行",
				"05" => "中信银行",
				"06" => "中交银行",
				"07" => "华夏银行"
		);
    }
}