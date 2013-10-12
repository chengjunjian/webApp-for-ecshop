<?php
/**
 * 所有Controller继承自此类
 */
class Controller
{

    protected $_classMethods;
    protected $_params;

    /**
     * 构造函数
     * @param array $params Pathinfo中附带的请求参数
     */
    public function __construct($params)
    {   
        $this->_params = $params; 

    }
	
    /**
     * 获取请求参数
     * 优先级 Pathinfo => $_GET => $_POST
     * @param string $key    不解释
     * @param string $default    默认值
     * @return mix
     * @author Icehu
     */
    public function getParam ($key, $default = null)
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
     * 获取所有请求参数
     * Pathinfo解析 + $_GET + $_POST
     * @return array
     */
    public function getParams ()
    {
        $return = $this->_params;
        if (isset ($_GET) && is_array ($_GET)) {
            $return += $_GET;
        }
        if (isset ($_POST) && is_array ($_POST)) {
            $return += $_POST;
        }
        return $return;
    }

    /**
     * 设置一个请求参数
     * @param string $key	参数key
     * @param mix $value	参数值
     */
    public function setParam ($key, $value)
    {
        $key = (string)$key;

        if ((null === $value) && isset ($this->_params[$key])) {
            unset ($this->_params[$key]);
        } elseif (null !== $value) {
            $this->_params[$key] = $value;
        }

        return $this;
    }

    /**
     * 设置一组请求参数
     *
     * @param array $params 请求参数数组
     */
    public function setParams ($params=array ())
    {
        foreach ($params as $key => $value)
        {
            $this->setParam ($key, $value);
        }
        return $this;
    }

    /**
     * 转发请求到其它控制器
     * @param string $action	转发的Action
     * @param string $controller	转发的Controller
     * @param string $model	转发的Model
     * @param array $params 附带的参数
     */
    public function forward ($action, $controller=null, $model=null, $params=null)
    {
        $front = Front::getInstance ();
        $front->setdparams ($action, $controller, $model, $params);
        $front->dispatch ();
    }

    protected $_front = null;



    //获得Model Name
    protected function getModuleName ()
    {
        return Front::getInstance ()->getModuleName ();
    }


    // 获得Controller Name
    protected function getControllerName ()
    {
        return Front::getInstance ()->getControllerName ();
    }

    //获得Action Name
    protected function getActionName ()
    {
        return Front::getInstance ()->getActionName ();
    }

    /**
     * 分发前执行的操作
     * 如有需要请重载
     */
    public function preDispatch ()
    {	
		//code..
    }

    /**
     * 分发完成后执行的操作
     * 如有需要请重载
     */
    public function postDispatch ()
    {

    }

    /**
     * 分发请求到Action
     * @param string $action
     * @author Icehu
     */
    public function dispatch ($action)
    {   
        $this->preDispatch ();

        if (null === $this->_classMethods) {
            $this->_classMethods = get_class_methods ($this);	//返回由该类的方法所组成的数组
        }

        //__call 方法兼容
        if (in_array ($action, $this->_classMethods)) {
            $this->$action ();
        } else {
            $this->__call ($action, array ());	//魔术方法，如果你试着调用一个对象中不存在或被权限控制中的方法，__call方法将会被自动调用。
        }
        $this->postDispatch ();
    }

    /**
     * __call 魔术方法，在Action不存在时运行
     * 子类继承可以用来做个性化Url [index.php/p/12345]
     * @param string $methodName 调用的成员函数名称
     * @param array $args 调用函数时传入的参数
	 */
    public function __call ($methodName, $args)
    {	
        echo 'Method ' . $methodName . ' does not exist and was not trapped in __call()'; 
		exit();
    }
	
	
    /**
     * 设置一个模板变量
     * @param string $key
     * @param mix $val
     */
    public function assign ($key, $val)
    {
        Tpl::assignvar ($key, $val);
    }

    /**
     *
     * 调用一个模板并显示
     * @param string $tpl
     * @author Icehu
     */
    public function display ($tpl = null)
    {   
        Tpl::display ($tpl);
    }

    /**
     * showmsg 方法
     *
     * @param string $msg	提示信息内容
     * @param string $gourl 跳转地址
     * @param number $time	跳转等待时间
     */
    public function showmsg ( $msg , $url=-1 , $time = null )
    {
        if ($time == null) $time = 5;
        if ($url == -1) $url = $_SERVER['HTTP_REFERER'];
		
		$this->assign('msg',$msg);
		$this->assign('url',$url);
		$this->assign('time',$time);
        $this->display('showMsg.html');
		
		exit;
    }
	
    /**
     * 分页
     */
    public static function page ($total)
    {
		//code
    }



}


