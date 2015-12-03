<?php

use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher;

/**
 * Security
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class Security extends Plugin
{
	public function __construct($dependencyInjector)
	{
		$this->_dependencyInjector = $dependencyInjector;
	}

	public function getAcl()
	{
		//if (!isset($this->persistent->acl)) {

			$acl = new PhpRudder\Security\Acl();

			$acl->setDefaultAction(Phalcon\Acl::DENY);

			//Register roles
			$roles = array(
				'Users'  => new Phalcon\Acl\Role('Users'),
                'Vip'  => new Phalcon\Acl\Role('Vip'),
                'Vip2'  => new Phalcon\Acl\Role('Vip2'),
				'Guests' => new Phalcon\Acl\Role('Guests')
			);
			foreach ($roles as $role) {
				$acl->addRole($role);
			}

            //Private area resources
            $vip2Resources = array(
                'credit' => array(
                    'mycreamt',
                    'applyxyed',
                    'applycged',
                    'getrestorehistory',
                    'getbillnotice'
                ),
            	'personcenter' => array(
            		'creditsorder'
                ),
            	'order' => array(
            		'getlist',
            		'getdetail',
            		'getbatchs',
            		'getlogistics',
            		'ustatus',
            		'pay',
            		'check',
            		'requestproc'
            	),
            	'project' => array(
            		'getlist',
            		'getdetail',
            		'addcart'
            	),
                /* 'bank' => array(
                    'submitcontract',
                    'zjwccheck',
                    'cancelcontract',
                    'getsubmitdata',
                    'getzjwrdata',
                    'getcanceldata'
                ), */
            );

            foreach ($vip2Resources as $resource => $actions) {
            	$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
            }

            //Private area resources
            $vipResources = array(
                'credit' => array(
                    'mycreamt',
                    'applyxyed',
                    'applycged',
                    'getrestorehistory',
                    'getbillnotice',
                    'getcged'

                ),
            	'personcenter' => array(
            		'creditsorder',
            		'creditslog'
                ),
            	'api' => array('getpurchase'),
            );

            foreach ($vipResources as $resource => $actions) {
                $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
            }

			//Private area resources
			$privateResources = array(
				'customize'    => array('index', 'search', 'create', 'get', 'append', 'myapplys', 'getintrinfo', 'createproject', 'myproject', 'myprojectdetail'),
                'credit' => array('getevaluationinfo', 'createevaluation', 'getevaluationlist','getintrinfo', 'mycreamt', 'getcged','getintrinfo','getbuyamt','creditdetail'),
                'account' => array('uinfo', 'upwd', 'uphone', 'uicon', 'get', 'logout'),
				'address' => array('save', 'delete', 'getlist', 'setdefault', 'detail'),
                'goods' => array(
		                    'getsearchcondition',
		                    'searchdz',
		                    'search',
		                    'detail',
		                    'historylist',
		                    'getcommonlist',
		                    'addcommon',
		                    'getcartlist',
		                    'addcart',
		                    'getcontracts',
                			'getcarttotal',
                			'updatecart',
		                    'addorder',
		                    'getsimilar',
                			'find',
                			'findbp',
                			'finddz',
                			'getcartlast',
                			'getinv'),
				'gift' => array(
							'getlist',
							'getcategorys',
							'getdetail',
							'exchange',
					),
				'favorites' => array('getlist', 'save', 'delete'),
				'helpcenter' => array('appointment', 'omplaint'),
				'project' => array('getlist', 'getdetail', 'addcart', 'getlistbak')
			);

			foreach ($privateResources as $resource => $actions) {
				$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
			}

			//Public area resources
			$publicResources = array(
				'index'   => array('index'),
				'session' => array('index', 'register', 'start', 'end'),
                'prjnews' => array('index', 'getpnewest', 'getbnewest', 'getprjtotal', 'getbiddinglist', 'getlinks', 'getpriceline', 'getrecomm'),
                'account' => array('index', 'register', 'login', 'findpwd', 'checkname', 'createcode', 'checkmobile', 'createvcode'),
                'goods' => array('index', 'getallcategory', 'getrdgoods','getrdbrands',),
                'api' => array('getcreamt', 'crerec_save', 'sendbill', 'sendcontracts', 'order','saveorder', 'sendplyorder', 'sendcheckorder', 'saveorderaction'),
                'mock' => array('getcreamt', 'crerec_save'),
				'global' => array('index', 'upload', 'getprovincelist', 'get', 'getfactorylist', 'sendsms'),
				'wrnews' => array('gethomenews', 'getclassifynews', 'view', 'gettopnews'),
				'notice' => array('view', 'gettopnews', 'getannouncelist'),
				'helpcenter' => array('gethelps', 'viewhelp'),
				'credit' => array('getallapply'),
			    'bank' => array(
			        'submitcontract',
			        'submitcontractadmin',
			        'getsubmitdata',
			        'cancelcontract',
			        'cancelcontractadmin',
			        'getcanceldata',
			        'confirmcontract',
			        'confirmcontractadmin',
			        'getconfirmdata',
			    ),
			);
			foreach ($publicResources as $resource => $actions) {
				$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
			}

			//Grant access to public areas to both users and guests
			foreach ($roles as $role) {
				foreach ($publicResources as $resource => $actions) {
                    foreach ($actions as $action){
                        $acl->allow($role->getName(), $resource, $action);
                    }
				}
			}


			//Grant acess to private area to role Users
			foreach ($privateResources as $resource => $actions) {
				foreach ($actions as $action){
					$acl->allow('Users', $resource, $action);
                    $acl->allow('Vip', $resource, $action);
                    $acl->allow('Vip2', $resource, $action);
				}
			}

            //Grant acess to private area to role Users
            foreach ($vipResources as $resource => $actions) {
                foreach ($actions as $action){
                    $acl->allow('Vip', $resource, $action);
                    $acl->allow('Vip2', $resource, $action);
                }
            }

            //Grant acess to private area to role Users
            foreach ($vip2Resources as $resource => $actions) {
                foreach ($actions as $action){
                    $acl->allow('Vip2', $resource, $action);
                }
            }

			//The acl is stored in session, APC would be useful here too
			$this->persistent->acl = $acl;
		//}

		return $this->persistent->acl;
	}

	/**
	 * This action is executed before execute any action in the application
	 *
	 * @param Event $event
	 * @param Dispatcher $dispatcher
	 */
	public function beforeDispatch(Event $event, Dispatcher $dispatcher) {
 		$auth = $this->session->get('auth');
  		if (!isset($auth)){
             $role = 'Guests';
  		} else if ($auth->customLevel == Users::LEVEL_MEMBER) {
 			$role = 'Users';
		} else if ($auth->customLevel == Users::LEVEL_VIP1MEMBER) {
            $role = 'Vip';
        } else if ($auth->customLevel == Users::LEVEL_VIP2MEMBER) {
        	$role = 'Vip2';
        }else {
        	$role = 'Guests';
        }
		$controller = $dispatcher -> getControllerName();
 		$action = $dispatcher->getActionName();
        if(!$controller||!$action) {
            echo json_encode(\PhpRudder\Http\ResponseApi::send(null, Message::$_ERROR_NOFOUND,"您访问的资源不存在。"));
            exit;
        }
  		$acl = $this->getAcl();
        $allowed = $acl->isAllowed($role, strtolower($controller), strtolower($action));
  		if ($allowed != Phalcon\Acl::ALLOW) {
  			$this->response->setHeader('Content-Type', 'application/json;charset=UTF-8');
  			$this->response->send();
            if (isset($auth)) {
               echo json_encode(\PhpRudder\Http\ResponseApi::send(null, Message::$_ERROR_AUTHORIZATION, "您的权限不允许进行本次操作。"));
            } else {
                echo json_encode(\PhpRudder\Http\ResponseApi::send(null, Message::$_ERROR_UNLOGIN, "您尚未登录，请登录后重新再试。"));
             }
             exit();
  	    } else {
  	    	// 记录流量信息
  	    	Stats::visitStats($this->request);
  	    }
	}

}
