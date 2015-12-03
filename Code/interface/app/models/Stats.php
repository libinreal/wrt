<?php

class Stats extends \PhpRudder\Mvc\ModelBase
{

    /**
     *
     * @var integer
     */
    public $access_time;

    /**
     *
     * @var string
     */
    public $ip_address;

    /**
     *
     * @var integer
     */
    public $visit_times;

    /**
     *
     * @var string
     */
    public $browser;

    /**
     *
     * @var string
     */
    public $system;

    /**
     *
     * @var string
     */
    public $language;

    /**
     *
     * @var string
     */
    public $area;

    /**
     *
     * @var string
     */
    public $referer_domain;

    /**
     *
     * @var string
     */
    public $referer_path;

    /**
     *
     * @var string
     */
    public $access_url;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'access_time' => 'access_time',
            'ip_address' => 'ipAddress',
            'visit_times' => 'visitTimes',
            'browser' => 'browser',
            'system' => 'system',
            'language' => 'language',
            'area' => 'area',
            'referer_domain' => 'domain',
            'referer_path' => 'path',
            'access_url' => 'accessUrl'
        );
    }

    public function initialize() {
    	$this->access_time = time();
    	$attributes = array(
    		'referer_domain',
    		'referer_path'
    	);
    	$this->skipAttributesOnUpdate($attributes);
    }

    /**
    * 获得浏览器名称和版本
    * @access  public
    * @return  string
    */
    public function getUserBrowser($agent){
    	if (empty($agent)) {
    		return '';
    	}
    	$browser = '';
    	$browser_ver = '';

    	if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
    		$browser = 'Internet Explorer';
    		$browser_ver = $regs[1];
    	} else if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
    		$browser = 'FireFox';
    		$browser_ver = $regs[1];
    	} else if (preg_match('/Maxthon/i', $agent, $regs)) {
    		$browser = '(Internet Explorer ' . $browser_ver . ') Maxthon';
    		$browser_ver = '';
    	} else if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
    		$browser = 'Opera';
    		$browser_ver = $regs[1];
    	} else if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
    		$browser = 'OmniWeb';
    		$browser_ver = $regs[2];
    	} else if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
    		$browser = 'Netscape';
    		$browser_ver = $regs[2];
    	} else if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
    		$browser = 'Safari';
    		$browser_ver = $regs[1];
    	} else if (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
    		$browser = '(Internet Explorer ' . $browser_ver . ') NetCaptor';
    		$browser_ver = $regs[1];
    	} else if (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
    		$browser = 'Lynx';
    		$browser_ver = $regs[1];
    	}

    	if (!empty($browser)) {
    		return addslashes($browser . ' ' . $browser_ver);
    	} else {
    		return 'Unknow browser';
    	}
    }

    /**
     * 获得客户端的操作系统
     * @access private
     * @return void
     */
    public function getOs($agent){
    	if ($agent) {
    		return 'Unknown';
    	}

    	$agent = strtolower($agent);
    	$os = '';

    	if (strpos($agent, 'win') !== false) {
    		if (strpos($agent, 'nt 5.1') !== false) {
    			$os = 'Windows XP';
    		} else if (strpos($agent, 'nt 5.2') !== false) {
    			$os = 'Windows 2003';
    		} else if (strpos($agent, 'nt 5.0') !== false) {
    			$os = 'Windows 2000';
    		} else if (strpos($agent, 'nt 6.0') !== false) {
    			$os = 'Windows Vista';
    		} else if (strpos($agent, 'nt') !== false) {
    			$os = 'Windows NT';
    		} else if (strpos($agent, 'win 9x') !== false && strpos($agent, '4.90') !== false) {
    			$os = 'Windows ME';
    		} else if (strpos($agent, '98') !== false) {
    			$os = 'Windows 98';
    		} else if (strpos($agent, '95') !== false) {
    			$os = 'Windows 95';
    		} else if (strpos($agent, '32') !== false) {
    			$os = 'Windows 32';
    		} else if (strpos($agent, 'ce') !== false) {
    			$os = 'Windows CE';
    		}
    	} else if (strpos($agent, 'linux') !== false) {
    		$os = 'Linux';
    	} else if (strpos($agent, 'unix') !== false) {
    		$os = 'Unix';
    	} else if (strpos($agent, 'sun') !== false && strpos($agent, 'os') !== false) {
    		$os = 'SunOS';
    	} else if (strpos($agent, 'ibm') !== false && strpos($agent, 'os') !== false) {
    		$os = 'IBM OS/2';
    	} else if (strpos($agent, 'mac') !== false && strpos($agent, 'pc') !== false) {
    		$os = 'Macintosh';
    	} else if (strpos($agent, 'powerpc') !== false) {
    		$os = 'PowerPC';
    	} else if (strpos($agent, 'aix') !== false) {
    		$os = 'AIX';
    	} else if (strpos($agent, 'hpux') !== false) {
    		$os = 'HPUX';
    	} else if (strpos($agent, 'netbsd') !== false) {
    		$os = 'NetBSD';
    	} else if (strpos($agent, 'bsd') !== false) {
    		$os = 'BSD';
    	} else if (strpos($agent, 'osf1') !== false) {
    		$os = 'OSF1';
    	} else if (strpos($agent, 'irix') !== false) {
    		$os = 'IRIX';
    	} else if (strpos($agent, 'freebsd') !== false) {
    		$os = 'FreeBSD';
    	} else if (strpos($agent, 'teleport') !== false) {
    		$os = 'teleport';
    	} else if (strpos($agent, 'flashget') !== false) {
    		$os = 'flashget';
    	} else if (strpos($agent, 'webzip') !== false) {
    		$os = 'webzip';
    	} else if (strpos($agent, 'offline') !== false) {
    		$os = 'offline';
    	} else {
    		$os = 'Unknown';
    	}

    	return $os;
    }

    /**
     * 统计访问信息
     *
     * @access public
     * @return void
     */
    public static function visitStats($request){
    	$time = time();
    	/* 检查客户端是否存在访问统计的cookie */
    	$visit_times = (!empty($_COOKIE['ECS']['visit_times'])) ? intval($_COOKIE['ECS']['visit_times']) + 1 : 1;
    	setcookie('ECS[visit_times]', $visit_times, $time + 86400 * 365, '/');
    	$agent = $request->getUserAgent();
    	$browser = self::getUserBrowser($agent);
    	$os = self::getOs($agent);
     	$ip = self::realIp();
    	$area = self::geoIp($ip);
    	/* 语言 */
     	$langs = array_slice($request->getLanguages(), 0, 2);
     	$langStr = array();
    	if (is_array($langs)) {
    		foreach($langs as $lang) {
    			if(array_key_exists('language', $lang)) {
    				$langStr[] = $lang['language'];
    			}
    		}
    	}
    	$language = implode(', ', $langStr);
    	$accessUrl = $request->get('_url');
    	/* 来源 */
    	$httpReferer = $request->getHTTPReferer();
    	if (!empty($httpReferer) && strlen($httpReferer) > 9) {
    		$pos = strpos($httpReferer, '/', 9);
    		if ($pos !== false) {
    			$domain = substr($httpReferer, 0, $pos);
    			$path = substr($httpReferer, $pos);

    			/* 来源关键字 */
//     			if (!empty($domain) && !empty($path)) {
//     				save_searchengine_keyword($domain, $path);
//     			}
    		} else {
    			$domain = $path = '';
    		}
    	} else {
    		$domain = $path = '';
    	}
		$stat = new self();
		$stat->ipAddress = $ip;
		$stat->visitTimes = $visit_times;
		$stat->browser = $browser;
		$stat->system = $os;
		$stat->language = $language;
		$stat->area = $area;
		$stat->domain = $domain;
		$stat->path = $path;
		$stat->accessUrl = $accessUrl;
		$stat->save();

    }

    /**
     * 判断是否为搜索引擎蜘蛛
     *
     * @access public
     * @return string
     */
    public function is_spider($record = true){
    	static $spider = NULL;

    	if ($spider !== NULL) {
    		return $spider;
    	}

    	if (empty($_SERVER['HTTP_USER_AGENT'])) {
    		$spider = '';

    		return '';
    	}

    	$searchengine_bot = array (
    			'googlebot',
    			'mediapartners-google',
    			'baiduspider+',
    			'msnbot',
    			'yodaobot',
    			'yahoo! slurp;',
    			'yahoo! slurp china;',
    			'iaskspider',
    			'sogou web spider',
    			'sogou push spider'
    	);

    	$searchengine_name = array (
    			'GOOGLE',
    			'GOOGLE ADSENSE',
    			'BAIDU',
    			'MSN',
    			'YODAO',
    			'YAHOO',
    			'Yahoo China',
    			'IASK',
    			'SOGOU',
    			'SOGOU'
    	);

    	$spider = strtolower($_SERVER['HTTP_USER_AGENT']);

    	foreach ($searchengine_bot as $key => $value) {
    		if (strpos($spider, $value) !== false) {
    			$spider = $searchengine_name[$key];

    			if ($record === true) {
    				$GLOBALS['db']->autoReplace($GLOBALS['ecs']->table('searchengine'), array (
    						'date' => date('Y-m-d'),
    						'searchengine' => $spider,
    						'count' => 1
    				), array (
    						'count' => 1
    				));
    			}

    			return $spider;
    		}
    	}
    	$spider = '';
    	return '';
    }

	public function geoIp($ip){
		static $fp = NULL, $offset = array (), $index = NULL;

		$ip = gethostbyname($ip);
		$ipdot = explode('.', $ip);
		$ip = pack('N', ip2long($ip));

		$ipdot[0] = (int) $ipdot[0];
		$ipdot[1] = (int) $ipdot[1];
		if ($ipdot[0] == 10 || $ipdot[0] == 127 || ($ipdot[0] == 192 && $ipdot[1] == 168) || ($ipdot[0] == 172 && ($ipdot[1] >= 16 && $ipdot[1] <= 31))) {
			return 'LAN';
		}

		if ($fp === NULL) {
			$fp = fopen(realpath(__DIR__ . '/../config/ipdata.dat'), 'rb');
			if ($fp === false) {
				return 'Invalid IP data file';
			}
			$offset = unpack('Nlen', fread($fp, 4));
			if ($offset['len'] < 4) {
				return 'Invalid IP data file';
			}
			$index = fread($fp, $offset['len'] - 4);
		}

		$length = $offset['len'] - 1028;
		$start = unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);
		for($start = $start['len'] * 8 + 1024; $start < $length; $start += 8) {
			if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip) {
				$index_offset = unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
				$index_length = unpack('Clen', $index{$start + 7});
				break;
    		}
    	}
    	fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
    	$area = fread($fp, $index_length['len']);
    	$area = iconv('GBK', 'UTF-8', $area);
    	fclose($fp);
    	$fp = NULL;

    	return $area;
    }

    /**
     * 获取真实ip
     * @return string
     */
	public function realIp(){
		static $realip = NULL;

		if ($realip !== NULL) {
			return $realip;
		}

		if (isset($_SERVER)) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

				foreach ($arr as $ip) {
					$ip = trim($ip);
					if ($ip != 'unknown') {
						$realip = $ip;
						break;
					}
				}
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$realip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				if (isset($_SERVER['REMOTE_ADDR'])) {
					$realip = $_SERVER['REMOTE_ADDR'];
				} else {
					$realip = '0.0.0.0';
				}
			}
		} else {
			if (getenv('HTTP_X_FORWARDED_FOR')) {
				$realip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif (getenv('HTTP_CLIENT_IP')) {
				$realip = getenv('HTTP_CLIENT_IP');
			} else {
				$realip = getenv('REMOTE_ADDR');
			}
		}

		preg_match('/[\d\.]{7,15}/', $realip, $onlineip);
		$realip = ! empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
		return $realip;
	}

}
