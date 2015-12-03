<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class MockController extends ControllerBase{
    const BASE_URL = "http://192.168.29.17:8090/mock/";

    public function getCreAmtAction() {
        $cusFnum = $this->request->get("cusFnum");
        $creamt = array();
        $creamt['restoreAmtTotal'] = 230000;
        $creamt['creAmtTot'] = 230000;
        $creamt['cusFnum']="100003444";
        $creamt['restoreAmt']=2888888;
        $creamt['isSuccess'] = 0;//0:成功 1 失败
        $creamt['resInfo'] = "成功";//描述
        $creamt['banks'] = array();
        array_push($creamt['banks'],array(
            "bankFnum"=>"29111333",
            "bankName"=>"浙商银行",
            "strDate"=>"20140405",
            "endDate"=>"20141205",
            "creAmt"=>200000,
            "spendAmt"=>30000,
            "lastAmt"=> 170000,
        ));
        return ResponseApi::send($creamt);
    }

    /**
     *
     */
    public function creRec_saveAction() {
        $customerNo = $this->get_user()->customNo;
        $url =$this::BASE_URL."creRec_save";

        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $post_data = array("chanFnum"=>"134",
            "chanDate"=>"20140812",
            "cusFnum"=>"11111",
            "billFnum"=>"124",
            "billNO"=>"111",
            "bankFnum"=>"222",
            "chanKind"=>"1",
            "chanAmt"=>234214,
            "remark"=>"111");
       	curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $response = curl_exec($ch);
        curl_close($ch);
        if ($response) {
            $this->get_cache()->_redis->set("API_CACHE_".$customerNo, json_encode($response));
            return ResponseApi::send(json_decode($response));
        } else {
            return ResponseApi::send($this->get_cache()->_redis->get("API_CACHE_".$customerNo));
        }
    }
}