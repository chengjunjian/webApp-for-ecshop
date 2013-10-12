<?php

/**
 * Tfk前端控制器
 */
class Front
{

	protected $_pathInfo = null;
	protected $_baseUrl = null;
	protected $_requestUri = null;
	protected $_params = array();
	protected $_moduleKey = '__Module';
	protected $_controllerKey = '__Controller';
	protected $_actionKey = '__Action';
	protected $_module = 'index';				//默认module
	protected $_controller = 'index';			//默认controller
	protected $_action = 'index';    			//默认action
	protected $_controllerpath = '';			//控制器目录，需要实例化后设置

	protected $_modules = array();
	
	const URI_DELIMITER = '/';

	private static $_instance;

	/**
	 *
	 * 单例模式
	 * 获取前端控制器对象
	 * @return Core_Controller_Front
	 */
	public static function getInstance()
	{
		if (self::$_instance)
		{
			return self::$_instance;
		}
		self::$_instance = new self();
		return self::$_instance;
	}


	//私有的构造函数，不允许new
	private function __construct(){}

	//设置控制器的目录	
	public function setControllerPath($path)
	{
		$this->_controllerpath = $path;
	}



	//设置允许的module列表
	public function registerModules($modules)	//array('admin','example','api','plugin','wap','mobile')
	{
		$this->_modules = $modules;
	}

	//设置框架的module/Controller/Action以及请求参数用于重新分发
	public function setdparams($action, $controller=null, $module=null, $params=null)
	{
		$action && $this->_action = $action;
		$controller && $this->_controller = $controller;
		$module && $this->_module = $module;
		$params && $this->_params += $params;
	}


	// 分发请求到控制器
	public function dispatch()
	{
		/* 简单的说$pathinfo就是先获取 baseUrl /xx/index.php
		 * 在获取requestUri $u=parse_url($_SERVER['REQUEST_URI']) =>$u['path']
		 * requestUri - baseUrl(相减) 就是pathinfo
		 */ 
		$pathinfo = $this->getPathInfo();
		
		//分析pathinfo
		$this->match($pathinfo);
	
		$controller = ucfirst($this->_controller);
		
		$fileName = $this->_controllerpath . ucfirst($this->_module) . '/' .  $controller . 'Controller.class.php';
		
		if (file_exists($fileName)){	
			include_once $fileName;
		}else{	
			echo 'Controller '.$controller.' is not exists';exit();	
		}
		
		//类名是这样的 IndexController
		$className = $controller.'Controller';
		
       	if (class_exists($className)){	
			
       		$class = new $className($this->_params);		//如果类名存在，则new它并传入参数
			
       		//所有类一定要继承Controller基类
			if (!$class instanceof Controller)
			{	
				echo 'Controller "' . $className . '" is not an instance of Controller';exit();
			}
			
			$method = strtolower($this->_action) . 'Action';	//方法后面都连接一个Action
            $class->dispatch($method);
		
       	}else{	
			echo 'Controller "' . $className . '" is not correct';exit();
		}

	}

	/**
	 * 匹配Pathinfo，分析module/Controller/Action 和请求参数
	 * @param string $path	Pathinfo
	 */
	public function match($path)
	{
		
		$path = trim($path, self::URI_DELIMITER);	
		//忽略 后缀
		$hasExt = strrpos($path, '.');		//查找字符串在另一个字符串中最后一次出现的位置

		$params = array();
		
		if ($path)
		{
			$path = explode(self::URI_DELIMITER, $path);
			
			
			if (count($path) && !empty($path[0]) && in_array($path[0], $this->_modules))
			{	
				$this->_module = $params[$this->_moduleKey] = array_shift($path);
			}

			if (count($path) && !empty($path[0]))
			{	
				$this->_controller = $params[$this->_controllerKey] = array_shift($path);
			}

			if (count($path) && !empty($path[0]))
			{	
				$this->_action = $params[$this->_actionKey] = array_shift($path);
			}
	
			
			//剩下的作为参数
			if ($numSegs = count($path))
			{
				for ($i = 0; $i < $numSegs; $i = $i + 2)
				{
					$key = urldecode($path[$i]);									//键 如果有用encode编码则解码 
					$val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;	//值 有的话还原
					
					$params[$key] = (isset($params[$key]) ? (array_merge((array) $params[$key], array($val))) : $val);
				}
			}
		}

		$this->setParams($params);
		return $this;
	}


	//获取当前module名称
	public function getModuleName()
	{
		return $this->_module;
	}


	//获取当前Controller名称
	public function getControllerName()
	{
		return $this->_controller;
	}

	
	//获取当前Action名称
	public function getActionName()
	{
		return $this->_action;
	}


	//设置请求参数
	public function setParams($params)
	{
		$this->_params = $params;
	}


	//获取请求参数
	public function getParam($name ,$default = null)
	{
		if (isset($this->_params[$key]))
		{
			return $this->_params[$key];
		}
		elseif (isset($_GET[$key]))
		{
			return $_GET[$key];
		}
		elseif (isset($_POST[$key]))
		{
			return $_POST[$key];
		}
		return $default;
	}



	/**
	 * 获取BaseUrl
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		if (null === $this->_baseUrl)
		{
			$this->setBaseUrl();
		}

		return $this->_baseUrl;
	}

	
	/**
	 * 设置BaseUrl
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl = null){
		if ($baseUrl === null)
		{
			$filename = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']) : '';

			if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename)
			{
				$baseUrl = $_SERVER['SCRIPT_NAME'];
			}
			elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename)
			{
				$baseUrl = $_SERVER['PHP_SELF'];
			}
			elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename)
			{
				$baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
			}
			else
			{
				// Backtrack up the script_filename to find the portion matching
				// php_self
				$path = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
				$file = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
				$segs = explode('/', trim($file, '/'));
				$segs = array_reverse($segs);
				$index = 0;
				$last = count($segs);
				$baseUrl = '';
				do
				{
					$seg = $segs[$index];
					$baseUrl = '/' . $seg . $baseUrl;
					++$index;
				}
				while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
			}

			// Does the baseUrl have anything in common with the request_uri?
			$requestUri = $this->getRequestUri();
			if (0 === strpos($requestUri, $baseUrl))
			{
				// full $baseUrl matches
				$this->_baseUrl = $baseUrl;
				return $this;
			}
			$dirname = str_replace(DIRECTORY_SEPARATOR , '/', dirname($baseUrl));
			if (0 === strpos($requestUri, $dirname))
			{
				// directory portion of $baseUrl matches
				$this->_baseUrl = rtrim($dirname, '/');
				return $this;
			}

			if (!strpos($requestUri, basename($baseUrl)))
			{
				// no match whatsoever; set it blank
				$this->_baseUrl = '';
				return $this;
			}

			// If using mod_rewrite or ISAPI_Rewrite strip the script filename
			// out of baseUrl. $pos !== 0 makes sure it is not matching a value
			// from PATH_INFO or QUERY_STRING
			if ((strlen($requestUri) >= strlen($baseUrl))
					&& ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0)))
			{
				$baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
			}
		}

		$this->_baseUrl = rtrim($baseUrl, '/');
		return $this;
	}

	/**
	 * 获取REQUEST_URI
	 * 兼容Apache 和 IIS
	 */
	public function getRequestUri()
	{
		if (empty($this->_requestUri))
		{
			$this->setRequestUri();
		}

		return $this->_requestUri;
	}

	/**
	 * 设置REQUEST_URI
	 *
	 * @param string $requestUri
	 * @return 
	 */
	public function setRequestUri($requestUri = null)
	{
		$parseUriGetVars = false;
		if ($requestUri === null)
		{
			if (isset($_SERVER['HTTP_X_REWRITE_URL']))	
			{ 	// check this first so IIS will catch 
				$requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
			}
			// 
			elseif (isset($_SERVER['REQUEST_URI']))		
			{
				$requestUri = $_SERVER['REQUEST_URI'];
			}
			elseif (isset($_SERVER['ORIG_PATH_INFO']))	
			{ 	// IIS 5.0, PHP as CGI
				$requestUri = $_SERVER['ORIG_PATH_INFO'];
				if (!empty($_SERVER['QUERY_STRING']))
				{
					$requestUri .= '?' . $_SERVER['QUERY_STRING'];
				}
			}
			else
			{
				return $this;
			}
		}

		$_url = parse_url($requestUri);	//解析url 返回其组成部分
		$requestUri = $_url['path'];

		$this->_requestUri = $requestUri;
		return $this;
	
	}

	/**
	 * 获取Pathinfo
	 * @return string
	 */
	public function getPathInfo()
	{
		if (empty($this->_pathInfo))
		{
			$this->setPathInfo();
		}
		return $this->_pathInfo;
	}

	/**
	 * 设置Pathinfo
	 * @param string $pathInfo
	 * @return Core_Controller_Front
	 * @author Icehu
	 */
	public function setPathInfo($pathInfo = null)
	{
		if ($pathInfo === null)
		{
			$baseUrl = $this->getBaseUrl();		// /xxx/index.php
			
			//$requestUri = /xxx/index.php/login
			if (null === ($requestUri = $this->getRequestUri()))
			{
				return $this;
			}
			
			// Remove the query string from REQUEST_URI,去掉?及后面的所有
			if ($pos = strpos($requestUri, '?'))
			{
				$requestUri = substr($requestUri, 0, $pos);	//
			}
			
			//这里把$pathinfo = $requestUri - $baseUrl
			if ((null !== $baseUrl)
					&& (false === ($pathInfo = substr($requestUri, strlen($baseUrl)))))
			{
				// If substr() returns false then PATH_INFO is set to an empty string
				$pathInfo = '';
			}
			elseif (null === $baseUrl)
			{
				$pathInfo = $requestUri;
			}
			
			
		}
		//fix iis url gbk
		if(isset($_SERVER['SERVER_SOFTWARE']) && FALSE!==strpos($_SERVER['SERVER_SOFTWARE'],"Microsoft-IIS"))
		{
			$pathInfo = iconv("GBK","UTF-8",$pathInfo);
		}
		$this->_pathInfo = (string) $pathInfo;
		return $this;
	}

	/**
	 * 全局Cache 变量
	 * @var array
	 */
	private static $data = null;

	
	/**
	 * 获取网站根目录
	 * @return string
	 */
	public static function getWebRoot()
	{
		if (!isset(self::$data['webroot']))
		{
			$front = self::getInstance();
			self::$data['webroot'] =
					preg_match('!index\.php$!i', $_tmp = $front->getBaseUrl()) ? substr($_tmp, 0, -9) : $_tmp . '/';
		}
		return self::$data['webroot'];
	}

}