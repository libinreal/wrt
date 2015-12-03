<?php

use PhpRudder\Mvc\ControllerBase;
use PhpRudder\Http\ResponseApi;

class NoticeController extends ControllerBase
{
	//公告分类
	private static $NoticeCatType = array(
			2002 => '商城公告',
			2003 => '定制专区公告',
			2004 => '积分公告');

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * 获得模块首页公告 GG-01
     */
    public function getAnnounceListAction() {
        $type = $this->request->get('type', 'int');
        $createAt = $this->request->get('createAt');
        $size = $this->request->get('size', 'int') ?: parent::SIZE;
        $forward = $this->request->get('forward', 'int');
        $announces = $this->_query($type, $createAt, $size, $forward);
        $resturnarr = array();
        if(is_object($announces) && $announces) {
            if ($forward && $forward==1) {
                if (count($announces->toArray())<$size && count($announces->toArray())>=1 ) {
                    $announces = $this->_query($type,null, $size,0);
                    $resturnarr = $announces -> toArray();
                } else {
                    $resturnarr = array_reverse($announces -> toArray());
                }
            } else {
                $resturnarr = $announces -> toArray();
            }
        }
        return ResponseApi::send($resturnarr);
    }

    private function _query($type, $createAt, $size, $forward) {
        $sort = 'DESC';
        if ($forward && $forward==1) $sort = 'ASC';

        $query = Article::query();
        if($type) {
            $query->where('catType='.$type);
        } else {
            $query->inWhere('catType', array_keys(self::$NoticeCatType));
        }
        if($createAt) {
            if ($sort=='DESC') {
                $query->andWhere('createAt<:createAt:', compact('createAt'));
            } else {
                $query->andWhere('createAt>:createAt:', compact('createAt'));
            }
        }
        $query->orderBy('createAt '.$sort);
        $query->limit($size);
        $query->columns(array('id', 'catType', 'title', 'createAt'));
        return  $query->execute();
    }
    /**
     * 得到公告详情 GG-02
     */
    public function viewAction() {
        $id = $this->request->get('id', 'int');
        $cache = $this->get_cache();
        $article = array();
        $result = Article::findFirst(array(
        		"conditions" => "id=?1",
        		"bind" => array(1 => $id),
        		"columns" => array('title', 'id', 'content', 'createAt', 'catType'),
        ));
        if(is_object($result) && $result) {
			$article = $result->toArray();
			$article['content'] = parent::replaceImgUrl($article['content']);
	        $cache->hIncrBy($id, "browserNum");
	        $article['browserNum'] = $cache->hGet($id, "browserNum");
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
        } else {
        	return ResponseApi::send(null, Message::$_ERROR_NOFOUND, '您访问的内容已被删除或者不存在');
        }
        return ResponseApi::send($article);
    }

    /**
     * 获得分类置顶的公告  GG-03
     */
    public function getTopNewsAction() {
        $type = $this->request->get('type', 'int');
        if($type) {
        	$result = Article::findFirst(array(
        			'conditions' => 'catType = :type: AND articleType=1',
        			'bind' => compact('type'),
        			'columns' => array('id', 'title', 'brief', 'createAt', 'fileUrl'),
        	));
        } else {
        	$result = Article::findFirst(array(
        			'conditions' => 'articleType=1',
        			'order' => 'createAt DESC',
        			'columns' => array('id', 'title', 'brief', 'createAt', 'fileUrl'),
        	));
        }
		$article = array();
		if(is_object($result) && $result->count()) {
			$article = $result->toArray();
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
