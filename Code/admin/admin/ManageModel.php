<?php
class ManageModel 
{
    protected static $_instance;
	protected $params;
	protected $diff;
    
    protected function __construct() {}
    protected function __clone() {}
    
    public static function getIns() 
    {
        if (empty(static::$_instance)) {
            static::$_instance = new static;
        }
        return static::$_instance;
    }
    
    public function run($json) 
    {
        $command      = $json['command'];
        $entity       = $json['entity'];
        $parameters   = $json['parameters'];
        $this->params = $parameters;
        static::$command($entity, $parameters);
    }
    
    
    /**
     * 检查传参
     * @param array $has
     * @desc  just check a part of parameters,params.not check where,another part of params
     */
    protected function checkParams($has) 
    {
    	$request = array();
    	$params  = $this->params;
    	foreach ($params as $k=>$v) {
    		if ($k != 'params') {
    			array_push($request, $k);
    		} elseif ($k == 'params' && is_array($v) && $v) {
    			foreach ($v as $pk=>$pv) {
    				if ($pk != 'where') {
    					array_push($request, $pk);
    				}
    			}
    		}
    	}
    	
    	$diff = array_diff($has, $request);
    	$this->diff = $diff;
    	if ($diff) {
    		return failed_json('传参错误');
    	}
    }
    
    
    /**
     * init
     * @param string $entity
     * @param string $tableName
     */
    protected function init($entity, $tableName) 
    {
        if ( $entity != $tableName ) {
            failed_json('table `'.$entity.'` is not found');
        }
        $this->table = $GLOBALS['ecs']->table($entity);
        $this->db = $GLOBALS['db'];
        return ;
    }
    
    
    
    /**
     * select sql
     * @param array $params
     * array(
     *      'fields' => array(), 
     *      'as'     => '', 
     *      'join'   => '', 
     *      'where'  => '', 
     *      'extend' => ''
     * );
     */
    protected function selectSql($params) 
    {
        if ( is_array($params['fields']) ) {
            $params['fields'] = implode(',', $params['fields']);
        }
        if ( !empty($params['as']) ) {
            $params['as'] = ' AS '.$params['as'];
        }
        if ( !empty($params['where']) ) {
            $params['where'] = ' WHERE '.$params['where'];
        }
        $this->sql = 'SELECT '.$params['fields'].' FROM '.$this->table.' '
                    .$params['as'].' '.$params['join'].' '
                    .$params['where'].' '.$params['extend'];
        return ;
    }
    
    
}

function failed_json($msg) 
{
    make_json_response('', -1, $msg);
}