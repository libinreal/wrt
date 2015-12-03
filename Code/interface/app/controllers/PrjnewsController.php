<?php

use Phalcon\Db\Column as Column;
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class PrjnewsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * 获得最新的工程招标信息 GCZZ-01
     */
    public function getPnewestAction() {
        $size = $this->request->get('size', 'int') ?: parent::SIZE;
		$criteria = Bidding::query();
		$criteria->leftJoin('Region', 'r.id=Bidding.areaId', 'r');
		$criteria->columns('r.name area, Bidding.name');
		$criteria->orderBy('Bidding.createAt DESC');
		$criteria->limit($size);
		$result = $criteria->execute();
		$pnewest = array();
		if(is_object($result) && $result->count()) {
			$pnewest = $result->toArray();
		}
        return ResponseApi::send($pnewest);
    }

    /**
     * 获得商情信息 GCZZ-02
     */
    public function getBnewestAction() {
		$goodsPriceLog = new GoodsPriceLog();
		$sqlStatement = "SELECT
						    T.price, T.vscope, b.brand_name factory, c.cat_name category
						FROM
						    (SELECT
						        *
						    FROM
						        goods_price_log
						    WHERE
						        (SELECT
						                goods_id
						            FROM
						                business_recommend
						            WHERE
						                goods_id = goodsId)
						    GROUP BY LEFT(wcode, 2) , id
						    ORDER BY LEFT(wcode, 2) , price) T
						        LEFT JOIN
						    brand b ON T.brandId = b.brand_id
						        LEFT JOIN
						    category c ON c.code = T.cat_code
						GROUP BY LEFT(T.wcode, 2)";
		$result = $goodsPriceLog->getReadConnection()->query($sqlStatement);
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$bnewest = array();
		if($result->numRows()) {
			$bnewest = $result->fetchAll();
		}
		return ResponseApi::send($bnewest);
    }

    /**
     * 获得全国工程招标信息数统计 GCZZ-03
     */
    public function getPrjTotalAction() {
        $type = $this->request->get('type');
        $builder = $this->modelsManager->createBuilder();
        $builder->from('Bidding');
        $builder->leftJoin('Region', 'r.id=Bidding.areaId', 'r');
        $builder->columns('r.id As areaId,r.name AS area, SUM(Bidding.amount) AS amount, COUNT(Bidding.id) AS number');
        if($type) {
	        $builder->where('type="' . $type . '"');
        }
        $builder->groupBy('Bidding.areaId');
        $result = $builder->getQuery()->execute();
        $Biddings = array();
        if(is_object($result) && $result->count()) {
        	$Biddings = $result->toArray();
        }
        return ResponseApi::send($Biddings);
    }

    /**
     * 获得工程招标的信息列表 GCZZ-04
     */
    public function getBiddingListAction() {
        $type = $this->request->get('type');
        $areaId = $this->request->get('areaId', 'int');
        $createAt = $this->request->get('createAt', 'int');
        $size = $this->request->get('size', 'int') ?: parent::SIZE;
        $forward = $this->request->get('forward', 'int');
        $result = $this->_query($type, $areaId, $createAt, $size, $forward);
     	$list = array();
        if(is_object($result) && $result->count()) {
            if ($forward && $forward==1) {
                if (count($result->toArray())<$size && count($result->toArray())>=1 ) {
                    $result = $this->_query($type, $areaId,null, $size,0);
                    $list = $result -> toArray();
                } else{
                    $list = array_reverse($result -> toArray());
                }
            } else {
                $list = $result -> toArray();
            }
        }


        return ResponseApi::send($list);
    }

    private function _query($type, $areaId, $createAt, $size, $forward) {
        $sort = 'DESC';
        if ($forward && $forward==1) $sort = 'ASC';

        $criteria = Bidding::query();
        $criteria->leftJoin('Region', 'r.id=Bidding.areaId', 'r');
        if($type) {
            $criteria->andWhere('Bidding.type=:type:', compact('type'), Column::BIND_PARAM_STR);
        }
        if($areaId) {
            $criteria->andWhere('Bidding.areaId=:areaId:', compact('areaId'), Column::BIND_PARAM_INT);
        }
        if($createAt) {
            if ($sort=='DESC') {
                $criteria->andWhere('Bidding.createAt < :createAt:', compact('createAt'), Column::BIND_PARAM_INT);
            } else {
                $criteria->andWhere('Bidding.createAt > :createAt:', compact('createAt'), Column::BIND_PARAM_INT);
            }
        }
        $criteria->limit($size);
        $criteria->orderBy('Bidding.createAt '.$sort);
        $criteria->columns('
	        		Bidding.name,
			        Bidding.prjdesc,
			        Bidding.content,
			        Bidding.amount,
			        Bidding.conditions,
			        Bidding.biddingman,
			        Bidding.prjaddress,
			        Bidding.biddingAt,
			        r.name area,
			        Bidding.createAt,
			        Bidding.id');
        return $criteria->execute();

    }
    /**
     * 获得友情链接 GCZZ-05
     */
    public function getLinksAction() {
        $type = $this->request->get('type');
        if(!$type) {
        	return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
        }
        $result = Links::find(array(
        	'columns' => 'title, url, CONCAT("'.$this->get_url().'", icon) icon',
			'conditions' => 'type=:type:',
        	'bind' => compact('type'),
        	'bindTypes' => array(Column::BIND_PARAM_STR),
        ));
        $links = array();
        if(is_object($result) && $result->count()) {
			$links = $result->toArray();
        }
        return ResponseApi::send($links);
    }

    /**
     * 获得指定类别的推荐商品信息 GCZZ-06
     * TODO 规格
     */
    public function getRecommAction() {
		$codes = $this->request->get('categorys');
		$codes = array_map(function($code) {
			return substr(filter_var($code, FILTER_SANITIZE_NUMBER_INT), 0, 4);
		}, explode(',', trim($codes, ' ,')));
		if(!$codes) {
			return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
		}
		$codes = implode(',', $codes);
		$goodsPriceLog = new GoodsPriceLog();
		$sqlStatement = "SELECT
						    T.brandName factory,
						    T.brandId,
						    T.vscope vary,
						    g.goods_name `name`,
						    T.price,
						    T.wcode,
						    GROUP_CONCAT(DISTINCT CONCAT(ab.attr_name, ':', attr.attr_value)) spec,
						    UNIX_TIMESTAMP() `time`,
						    FROM_UNIXTIME(T.createAt, '%Y-%m-%d') `date`
						FROM
						    (SELECT
						        *
						    FROM
						        goods_price_log
						    GROUP BY brandId , wcode , id
						    ORDER BY createAt DESC) T
						        LEFT JOIN
						    goods g ON g.goods_id = T.goodsId
						        LEFT JOIN
						    goods_attr attr ON attr.goods_id = g.goods_id
						        LEFT JOIN
						    attribute ab ON attr.attr_id = ab.attr_id
						        AND ab.attr_name LIKE '%规格%'
						WHERE
						    (FIND_IN_SET(T.brandId,
						            (SELECT
						                    brand_id
						                FROM
						                    business_recommend br
						                WHERE
						                    br.goods_wcode = T.wcode)))
						        AND (LEFT(T.cat_code, 4) IN ({$codes}))
						GROUP BY T.brandId , T.wcode";
		$result = $goodsPriceLog->getReadConnection()->query($sqlStatement);
		$goodsInfo = array();
		if($result->numRows()) {
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$currDate = date('Y-m-d');
			while(($r = $result->fetch()) != false) {
				if($r['date'] != $currDate) {
					$r['vary'] = 0;
				}
				unset($r['date']);
				$goodsInfo[] = $r;
			}
		}
        return ResponseApi::send($goodsInfo);
    }

    /**
     * 获得指定商品最近10次的价格走势数据  GCZZ-07
     */
    public function getPriceLineAction() {
    	$wcode = $this->request->get('wcode', 'int');
    	$brandId = $this->request->get('brandId', 'int');
    	if(!$wcode || !$brandId) {
    		return ResponseApi::send(null, Message::$_ERROR_LOGIC, "数据格式不合法");
    	}
    	$sqlStatement = "SELECT
					    FROM_UNIXTIME(createAt, IF(wcode = '060103020400', '%u', '%Y-%m-%d')) `date`,
					    log.price
					FROM
					    (SELECT
					        *,
					            FROM_UNIXTIME(createAt, IF(wcode = '060103020400', '%u', '%Y-%m-%d')) grouping
					    FROM
					        goods_price_log
					    WHERE
					        wcode = '{$wcode}' AND brandId = '{$brandId}'
					    ORDER BY createAt DESC) `log`
					GROUP BY log.grouping ASC
    				LIMIT 14";
    	$goodsPriceLog = new GoodsPriceLog();
		$result = $goodsPriceLog->getReadConnection()->query($sqlStatement);
		$priceLogs = array();
		if($result->numRows()) {
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$logs = $dates = array();
			while(($r = $result->fetch()) != false) {
				$logs[$r['date']] = $r['price'];
			}
			if(count($logs) != 14) {
				$nowDate = new DateTime();
				if($wcode == '060103020400') {
					$curWeek = $nowDate->format('W');
					end($logs);
					$diffWeek = $curWeek - key($logs);
					$weeks[$nowDate->format('W')] = $nowDate->format('Ymd');
					for($i = 0; $i < $diffWeek; $i ++) {
						$dateObj = $nowDate->sub(DateInterval::createFromDateString("7 days"));
						$weeks[$dateObj->format('W')] = $dateObj->format('Ymd');
					}
					ksort($weeks);
					foreach($weeks as $W => $date) {
						if(array_key_exists($W, $logs)) {
							$logs[$date] = $logs[$W];
							unset($logs[$W]);
						} else {
							$logs[$date] = $logs[$weeks[$W - 1]];
						}
					}
				} else {
					$startDate = new DateTime(key($logs));
					$days = $nowDate->diff($startDate)->format('%d');
					for($i = 0; $i < $days; $i ++) {
						$date = $startDate->add(DateInterval::createFromDateString("1 days"))->format('Y-m-d');
						if(!array_key_exists($date, $logs)) {
							$dateObj = new DateTime($date);
							$beforeDate = $dateObj->sub(DateInterval::createFromDateString("1 days"))->format('Y-m-d');
							$logs[$date] = $logs[$beforeDate];
						}
					}
				}
				$logs = array_slice($logs, -14, null, true);
			}
    		$priceLogs['updates'] = array_map(function($v) {
    			return preg_replace('/(\d{4})(\d{2})(\d{2})/', "$1-$2-$3", $v);
    		}, array_keys($logs));
    		$priceLogs['prices'] = array_values($logs);
		}
    	return ResponseApi::send($priceLogs);
    }

}
