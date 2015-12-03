<?php

use Phalcon\Db\Column as Column;
use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class WrnewsController extends ControllerBase
{

	//物融新闻分类
	private static $wrNewsCatType = array(
			1001 => 'medianews',
			1002 => 'brandnews',
			1003 => 'marketnews');

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;

    }

    /**
     * 首页分类浏览 WRXW-01
     */
    public function getHomeNewsAction() {

		$createAt = $this->request->get('createAt', 'int') ?: time();
		$size = $this->request->get('size', 'int') ?: parent::SIZE;
		$conditions = "1";
		$conditions .= " AND Article.createAt < :createAt:";
		if(self::$wrNewsCatType && is_array(self::$wrNewsCatType)) {
			$catTypeKey = array_keys(self::$wrNewsCatType);
			$catTypeKeyStr = "'".implode("', '", $catTypeKey)."'";
			$conditions .= " AND Article.catType IN ({$catTypeKeyStr})";
		}
        $phql = "SELECT Article.id,Article.catId,Article.title,IF(Article.imgurl LIKE 'http://%', Article.imgurl, CONCAT('".$this->get_url()."', Article.imgurl)) imgurl,Article.createAt,Article.catType,Article.brief
        		 FROM Article LEFT JOIN Article a
        		 ON Article.catId=a.catId AND Article.id<a.id
        		 WHERE {$conditions}
        		 GROUP BY Article.catId,Article.id
        		 HAVING COUNT(a.id)<:size:
        		 ORDER BY Article.catId,Article.id DESC";
        $query = $this->modelsManager->createQuery($phql);
        $articles = $query->execute(
        			compact('size', 'createAt'),
					array(Column::BIND_PARAM_INT, Column::BIND_PARAM_STR))->toArray();
        $resultArr = array();
        foreach($articles as $article) {
        	$catTypeName = self::$wrNewsCatType[$article['catType']];
        	unset($article['catType']);
			$resultArr[$catTypeName][] = $article;
        }
        return ResponseApi::send($resultArr);
    }

    /**
     * WRXW-02 获得分类新闻列表
     * @return array
     */
    public function getClassifyNewsAction() {
        $catType = $this->request->get('catType', 'int');
        if(!$catType) {
			$catType = '1001, 1002, 1003';
        }
        $createAt = $this->request->get('createAt', 'int');
        $size = $this->request->get('size', 'int') ?: parent::SIZE;
        $forward = $this->request->get('forward', 'int');
        $result = $this->_query($catType, $createAt, $size, $forward);
        $classifyNewsList = array();
		if(is_object($result) && $result->count()) {
            if ($forward && $forward == 1) {
                if (count($result->toArray())<$size && count($result->toArray())>=1 ) {
                    $result = $this->_query($catType, null, $size, 0);
                    $classifyNewsList = $result -> toArray();
                } else{
                    $classifyNewsList = array_reverse($result -> toArray());
                }

            } else {
                $classifyNewsList = $result -> toArray();
            }
		}
        return ResponseApi::send($classifyNewsList);
    }

    private function _query($catType, $createAt, $size, $forward) {
        $sort = 'DESC';
        if ($forward && $forward==1) $sort = 'ASC';

        $conditions = "1";
        if($catType) {
            $conditions .= " AND catType IN ({$catType})";
        }
        if (isset($createAt)) {
            if ($sort == 'DESC') {
                $conditions .= " AND createAt < " . $createAt;
            } else {
                $conditions .= " AND createAt > " . $createAt;
            }
        }
        $result = Article::query()
            -> columns(array('id', 'title', "IF(imgurl LIKE 'http://%', imgurl, CONCAT('".$this->get_url()."', imgurl)) imgurl", 'createAt', 'catType'))
            -> where($conditions)
            -> orderBy("createAt ".$sort)
            -> limit($size)
            -> execute();
        return $result;
    }

    /**
     * 新闻详情 WRXW-03
     */
    public function viewAction() {
    	$id = $this->request->get('id', 'int');
    	$cache = $this->get_cache();
    	$article = array();
    	$result = Article::findFirst(array(
    			"conditions" => "id=?1",
    			"bind" => array(1 => $id),
    			"columns" => array('title', 'id', 'content', 'createAt', 'source', 'catType'),
    	));
    	if(is_object($result) && $result) {
    		$article = $result -> toArray();
    		$article['content'] = parent::replaceImgUrl($article['content']);
    		$cache->hIncrBy($id, "browserNum");
    		$createAt = $article['createAt'];
    		$catType = $article['catType'];

    		$lastArticle = $nextArticle = array();
    		$nextResult = Article::findFirst(array(
    				'conditions' => 'createAt < :createAt: AND catType = :catType:',
    				'bind' => compact('createAt', 'catType'),
    				'order' => 'createAt DESC',
    				'columns' => 'id, title'
    		));
    		if(is_object($nextResult) && $nextResult) {
    			$nextArticle = $nextResult->toArray();
    		}
    		$lastResult = Article::findFirst(array(
    				'conditions' => 'createAt > :createAt: AND catType = :catType:',
    				'bind' => compact('createAt', 'catType'),
    				'order' => 'createAt',
    				'columns' => 'id, title'
    		));
    		if(is_object($lastResult) && $lastResult) {
    			$lastArticle = $lastResult->toArray();
    		}
    		$article['lastArticle'] = $lastArticle;
    		$article['nextArticle'] = $nextArticle;
    		unset($article['catType']);
    		$article['browserNum'] = $cache->hGet($id, "browserNum");
    	} else {
    		return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '您访问的内容已被删除或者不存在');
    	}

    	return ResponseApi::send($article);
    }

    /**
     * 获得分类置顶的新闻 WRXW-04
     */
    public function getTopNewsAction() {
        $type = $this->request->get('type', 'int');
        if(!$type || !array_key_exists($type, self::$wrNewsCatType)) {
        	return ResponseApi::send(null, Message::$_ERROR_CODING, "公告分类类型不合法！");
        }
		$result = Article::findFirst(array(
				'conditions' => 'catType = :type: AND articleType=1',
				'bind' => compact('type'),
				'columns' => array('id', 'title', 'brief', 'createAt', 'fileUrl'),
		));
		$article = array();
		if(is_object($result) && $result->count()) {
			$article = $result -> toArray();
			$baseUrl = $this->get_url();
			$article['fileUrl'] = array_map(function($file) use($baseUrl) {
				if(preg_match('/^http:\/\//', $file)) {
					return $file;
				}
				return $baseUrl . $file;
			}, array_values(unserialize($article['fileUrl'])));
		}
        return ResponseApi::send($article);
    }
}
