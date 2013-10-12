<?php

/**
 * 常用函数库
 *
 * @author ykq
 */

class Fun
{

    /**
     * 对变量进行转义
     */
    public static function addslashes_deep($param)
    {
        if (is_array($param))
        {
            foreach ($param as $k => $v)
            {
                $param[$k] = self::addslashes_deep($v);
            }
            return $param;
        }
        else
        {
            return addslashes($param);
        }
    }

    /**
     * 字符转码方法
     * @param string $in_charst 输入字符集
     * @param string $out_charset 输出字符集
     * @param string|array $param 转换数据
     * @return string|array
     * @author Icehu
     */
    public static function iconv($in_charst, $out_charset, $param)
    {
        if (is_array($param))
        {
            foreach ($param as $_key => $_var)
            {
                $param[$_key] = self::iconv($in_charst, $out_charset, $_var);
            }
            return $param;
        }
        else
        {
            if (function_exists('iconv'))
            {
                return @iconv($in_charst, $out_charset, $param);
            }
            elseif (function_exists('mb_convert_encoding'))
            {
                return @mb_convert_encoding($param, $out_charset, $in_charst);
            }
            else
            {
                return $param;
            }
        }
    }

    /**
     * 获取上一次访问的地址
     *
     * @return string
     * @todo 考虑登陆、退出地址、SEO转换等问题
     */
    public static function getReffer()
    {
        return $_SERVER['HTTP_REFERER'];
    }



    /**
     * 获取客户端IP
     * 目前是取带来IP，是否取真实IP？如果取真实IP，可能被伪造
     * @return string
     * @todo 取真实IP？
     */
    public static function getClientIp()
    {
        if (isset($_SERVER['REMOTE_ADDR']))
        {
            return $_SERVER['REMOTE_ADDR'];
        }
        else if ($_tmp = getenv('REMOTE_ADDR'))
        {
            return $_tmp;
        }
        return 'unknow';
    }





    /**
     * 将数组转换为JSON格式
     * 所有格式的数组都将转换为json对象，而不会转换为js array
     * @param array $array
     * @param bool $_s
     * @return string
     */
    public static function array2json($array=array(), $_s = false)
    {
        $r = array();
        foreach ((array) $array as $key => $val)
        {
            if (is_array($val))
            {
                $r[$key] = "\"$key\": " . self::array2json($val, $_s);
            }
            else
            {
                if ($_s && $key == '_s')
                {
                    $r[$key] = "\"$key\": " . $val;
                }
                else
                {
                    if (is_numeric($val))
                    {
                        $r[$key] = "\"$key\": " . $val;
                    }
                    else if (is_bool($val))
                    {
                        $r[$key] = "\"$key\": " . ($val ? 'true' : 'false');
                    }
                    else if (is_null($val))
                    {
                        $r[$key] = "\"$key\": null";
                    }
                    else
                    {
                        $r[$key] = "\"$key\": \"" . str_replace(array("\r\n", "\n", "\""), array("\\n", "\\n", "\\\""), $val) . '"';
                    }
                }
            }
        }
        return '{' . implode(',', $r) . '}';
    }


    

    public static $data = null;

    /**
     * 获得webroot
     * 以 / 结尾
     * @return string
     */
    public static function getWebroot()
    {
        return Front::getWebRoot();
    }



    /**
     * 获得Urlroot
     * @return string
     */
    public static function getUrlroot()
    {
        $webroot = self::getWebroot();
        $http = $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http';
        return $http . '://' . $_SERVER['HTTP_HOST'] . $webroot;
    }


    /**
     * 检查文件是否可读
     * @param string $filename
     * @return bool
     * @author Icehu
     */
    public static function isReadable($filename)
    {
        if (!$fh = @fopen($filename, 'r', true))
        {
            return false;
        }
        @fclose($fh);
        return true;
    }

    /**
     * 经过重写的SESSION_START
     *
     * @author Icehu
     */
    public static function session_start()
    {
        if (!defined('SESSION_START'))
        {	
            if(is_writeable(SESSION_PATH) && is_readable(SESSION_PATH)){
				session_save_path(SESSION_PATH);
			}
			
			
			//$cookie_pre = Core_Config::get('cookiepre', 'basic', 't_');	// t_
            //$domain = Core_Config::get('cookiedomain', 'basic', null);	// 
            //ini_set('session.name', $cookie_pre . 'skey');				//echo session_name();	t_skey
         	
		    /* void session_set_cookie_params ( int $lifetime [, string $path [, string $domain [, bool $secure = false [, bool $httponly = false ]]]] )
		     *  设置session_cookie的参数，即：
		     * 	session.cookie_lifetime
			 *	session.cookie_path
			 *	session.cookie_domain
			 *	session.cookie_secure
			 *	session.cookie_httponly
			 */	
        	/*	
            session_set_cookie_params(0, '/', $domain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0, true);
			
            //session_set_save_handler ( string open, string close, string read, string write, string destroy, string gc )
            //参数为数组表示类里面的方法
            session_set_save_handler(	
                array('Core_Lib_Session', 'open'),		//运行session_start()时执行
                array('Core_Lib_Session', 'close'),		//session_write_close() session_destroy()时执行
                array('Core_Lib_Session', 'read'),		//session_start() 读取session数据到$_SESSION[] 
                array('Core_Lib_Session', 'write'),		//结束时和session_write_close()强制提交session数据时执行即$_SESSION['aa']='aa'
                array('Core_Lib_Session', 'destroy'),	//session_destroy()时执行
                array('Core_Lib_Session', 'gc')			//open read session_start 执行垃圾回收
            );
            $cookieTime = Core_Config::get('cookietime', 'basic', 30);
            session_cache_expire($cookieTime > 0 ? $cookieTime : 30);
            */
            session_start();
            define('SESSION_START', true);
          
        }
    }

    /**
     * UTF-8数据的中文截字
     *
     * @param string $content 需要截字的原文
     * @param number $length 截取的长度
     * @param string $add 末尾添加的字符串
     * @return string
     */
    public static function cn_substr($content, $length, $add='')
    {
        if ($length && strlen($content) > $length)
        {
            $str = substr($content, 0, $length);
            $len = strlen($str);
            for ($i = strlen($str) - 1; $i >= 0; $i-=1)
            {
                $hex .= ' ' . ord($str[$i]);
                $ch = ord($str[$i]);
                if (($ch & 128) == 0)
                    return substr($str, 0, $i) . $add;
                if (($ch & 192) == 192)
                    return substr($str, 0, $i) . $add;
            }
            return($str . $hex . $add);
        }
        return $content;
    }

    /**
     * 格式化字节
     * @param $size - 大小(字节)
     * @return 返回格式化后的文本
     * @author Icehu
     */
    public static function formatBytes($size)
    {
        if ($size >= 1073741824)
        {
            $size = round($size / 1073741824 * 100) / 100 . ' GB';
        }
        elseif ($size >= 1048576)
        {
            $size = round($size / 1048576 * 100) / 100 . ' MB';
        }
        elseif ($size >= 1024)
        {
            $size = round($size / 1024 * 100) / 100 . ' KB';
        }
        else
        {
            $size = $size . ' Bytes';
        }
        return $size;
    }




    //弹窗
    static public function alert($info){
    	echo "<script type='text/javascript'>alert('$info');</script>";
    	exit();
    }
     
    //弹窗跳转
    static public function alertLocation($info,$url){
	    if(!empty($info)){
		    echo "<script type='text/javascript'>alert('$info');location.href='$url';</script>";
		    exit();
	    }else{
		    header("Location:$url");
		    exit();
	    }
    }
     
    //弹窗返回
    static public function alertBack($info){
	    echo "<script type='text/javascript'>alert('$info');history.back();</script>";
	    exit();
    }
    
    //弹窗关闭
    static public function alertClose($info){
	    echo "<script type='text/javascript'>alert('$info');close();</script>";
	    exit();
    }
    
    //将以对象形式的数组转化为关联数组
    static public function changeArray($data,$key,$value){
    	$items = array();
    	if(is_array($data)){
    		foreach($data as $v){
    			$items[$v[$key]] = $v[$value];
    		}
    	}
    	return $items;
    }
    
    // 创建项目目录结构
    static public function build_app_dir() {
        // 没有创建项目目录的话自动创建
        if(!is_dir(APP_PATH)) mkdir(APP_PATH,0755,true);
        if(is_writeable(APP_PATH)) {
            $dirs  = array(
                MODEL_PATH,
                VIEW_PATH,
                CONTROLLER_PATH,
                RUNTIME_PATH,
                CONF_PATH,
                LANG_PATH,
                CACHE_PATH,
                COMPILE_PATH,
				SESSION_PATH,
                CONTROLLER_PATH."Index/",
                );
            foreach ($dirs as $dir){
                if(!is_dir($dir)) mkdir($dir,0755,true);
            }
           
            // 写入测试Action
            if(!is_file(LIB_PATH.'Action/IndexAction.class.php')){
                $content = file_get_contents(Y_PATH."Tpl/default_index.html");
                file_put_contents(CONTROLLER_PATH.'Index/IndexController.class.php',$content);
            }    
        }else{
            exit('项目目录不可写，目录无法自动生成！<BR>请使用项目生成器或者手动生成项目目录~');
        }
    }
    
}
