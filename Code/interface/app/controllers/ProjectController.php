<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class ProjectController extends ControllerBase
{

    public function indexAction()
    {
    	$this->persistent->parameters = null;
    }

    /**
     * 查看我的项目列表 WDXM-01
     */
	public function getListAction() {
        $customNo = $this->get_user()->customNo;
        $sqlstatement = "select
        distinct contract.contract_id as id,
        contract.customer_id as prjNo,
        contract.contract_name as prjName,
        contract.contract_amount as price,
        contract.end_time as deadline,
        (
            select count(order_recommend.id)
            from order_recommend
            where order_recommend.conFnum = contract.customer_id and order_recommend.cusFnum= '".$customNo."')
        as recommand
        from contract
        where contract.customer_id= '".$customNo."'";

        $result1 = $this->get_db()->fetchAll($sqlstatement);

        $sqlstatement2= "select
        distinct contract.contract_id as id,
        contract.customer_id as prjNo,
        contract.contract_name as prjName,
        contract.contract_amount as price,
        contract.end_time as deadline,
        order_recommend.id as recommand
        from contract join order_recommend on contract.customer_id = order_recommend.conFnum
         and contract.customer_id!= '".$customNo."' and order_recommend.cusFnum= '".$customNo."'";

        $result2 = $this->get_db()->fetchAll($sqlstatement2);
        $arr = array_merge($result1,$result2);
        foreach($arr as $key=>&$val) {
           foreach($val as $k=>&$v) {
               if(intval($k) || $k == '0') {
                   unset($val[$k]);
               }
           }
           $val['companyName'] = $this->get_user()->companyName;
        }
        return ResponseApi::send($arr);
	}


	/**
	 * 查看分期推荐订单 WDXM-02
	 */
	public function getDetailAction() {
        $customNo = $this->get_user()->customNo;
        $customerId = $this->get_user()->id;
        $cno = $this->request->get('constractSn');
        if (!$cno) {
        	return ResponseApi::send(null, -1, 'doesn\'t give `constractSn`');
        }
        $sql1= "select contract.contract_id as id,
                            contract.contract_num as prjNo,
                            contract.contract_name as prjName,
                            contract.contract_amount as price,
                            contract.end_time as deadline
                        from contract
                        where contract.contract_num = '".$cno."' and contract.customer_id = '".$customerId."'";

        $result = $this->get_db()->fetchOne($sql1);
        if ($result) {
            unset($result[0]);
            unset($result[1]);
            unset($result[2]);
            unset($result[3]);
            unset($result[4]);
            unset($result[5]);
            $result['companyName'] = $this->get_user()->companyName;
            $result['prjs'] = array();
        }

        $sql2= "select prjName,endDate,goodsid,conChildFnum,conCount
                        from order_recommend
                        where order_recommend.conFnum = '".$cno."' and order_recommend.cusFnum = '".$customNo."'";
        $items = $this->get_db()->fetchAll($sql2);

        foreach($items as $key=>&$val) {
            foreach($val as $k=>&$v) {
                if(intval($k) || $k == '0') {
                    unset($val[$k]);
                }
            }
            $goodsid = $val['goodsid'];
            unset($val['goodsid']);
            $goods  = $this->getRecommandGoods($goodsid);
            $val['goodsItem']=$goods;
            array_push($result['prjs'],$val);
        }
        return ResponseApi::send($result);
	}

    /**
     * @param $ids
     * @return array
     */
    private function getRecommandGoods($ids) {
        $builder = $this->modelsManager->createBuilder();
        $builder->from('Goods');
        $builder->leftJoin('GoodsAttr', 'a.goodsId=Goods.id', 'a');
        $builder->leftJoin('Attribute', 'ab.id=a.attrId', 'ab');
        $builder->leftJoin('Category', 'cat.code=Goods.code', 'cat');
        $builder->columns("Goods.id as id,
				Goods.name as goodsName,
				Goods.price as goodsPrice,
				IF(Goods.thumb LIKE 'http://%', Goods.thumb,
				CONCAT('" . $this->get_url() . "', Goods.thumb)) thumb,
				cat.unit as goodsUnit,
				GROUP_CONCAT(DISTINCT CONCAT(
							IF(ab.name IS NULL, '', ab.name),':',
							IF(a.attr_value IS NULL, '', a.attr_value),':',
							IF(a.attrId IS NULL, '', a.attrId),':',
							IF(ab.sort IS NULL, '', ab.sort
							))) attr");
        $builder->having('Goods.id in ('.$ids.')');
        $builder->groupBy('Goods.id');

        $result = $builder->getQuery()->execute();
        $goods = array();
        $goodsIds = array();
        if(is_object($result) && $result->count()) {
            $result  = $result->toArray();
            foreach($result as $r) {
                $goodsIds[] = $r['id'];
                if($r['attr']) {
                    $sort = array();
                    $r['attr'] = array_map(function($a) use (&$sort) {
                        $_a = array_combine(array('name', 'value', 'pid', 'sort'), explode(':', $a));
                        $sort[] = $_a['sort'];
                        return $_a;
                    }, array_values(explode(',', $r['attr'])));
                    array_multisort($sort, SORT_ASC, SORT_NUMERIC, $r['attr']);
                    $r['attr'] = array_map(function($v) {
                        unset($v['sort']);
                        return $v;
                    }, $r['attr']);
                } else {
                    $r['attr'] = array();
                }
                $goods[] = $r;
            }
        }
        return $goods;
    }

    /**
     * 添加商品到购物车
     * JJSC-12
     *
     */
    public function addCartAction() {
        if (!$this->request->isPost()) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "只支持POST请求");
        }
        $goodsId = $this->request->getPost('ids');
        if(!$goodsId) {
            return ResponseApi::send(null, Message::$_ERROR_CODING, "数据格式错误");
        }
        $result = Goods::find(array(
            'conditions' => 'id in ('.$goodsId.')',
            'columns' => 'id as goodsId,name goodsName, vipPrice price, goodsSn',
        ));
        if ($result) {
            foreach ($result as $item) {
                $goods = $item->toArray();
                $userId = $this->get_user()->id;
                $nums = 1;
                extract($goods);
                $cart = Cart::findFirst('goodsId=' . $goods['goodsId'] . 'AND userId = '.$userId);
                if(is_object($cart) && $cart) {
                    $cart->nums +=  1;
                } else {
                    $cart = new Cart();
                    foreach(array('nums', 'userId', 'goodsId', 'goodsSn', 'goodsName', 'price') as $v) {
                        $cart->$v = $$v;
                    }
                }
                if(!$cart->save()) {
                    foreach($cart->getMessages() as $message) {
                        return ResponseApi::send(null, Message::$_ERROR_LOGIC, $message);
                    }
                }
            }
        } else {
            return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '源请求错误，试图获取已经过期或则并不存在的资源');
        }
        //计算购物车商品总数
        $total = $this->getCartTotal();
        return ResponseApi::send(compact('total'));
    }

    /**
     * 获得购物车商品数量
     * @return number
     */
    private function getCartTotal() {
        $userId = $this->get_user()->id;
        $total = Cart::sum(array(
                'conditions' => 'userId = :userId:',
                'bind' => compact('userId'),
                'column' => 'nums'
        ));
        return $total;
    }

}

