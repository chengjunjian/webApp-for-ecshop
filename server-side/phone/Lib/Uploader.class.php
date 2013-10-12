<?php
/**
 * php上传类
 */
class Uploader
{
    private $fileField;            	//文件域名
    private $file;                 	//文件上传对象
    private $config;               	//配置信息
    private $oriName;              	//原始文件名
    private $fileName;             	//新文件名
    private $fullName;             	//完整文件名,即从当前配置目录开始的URL
    private $fileSize;             	//文件大小
    private $fileType;             	//文件类型
    private $stateInfo;            	//上传状态信息,
    private $stateMap = array(    	//上传状态映射表
        "SUCCESS" ,                	//上传成功标记
        "文件大小超出 upload_max_filesize 限制" ,
        "文件大小超出 MAX_FILE_SIZE 限制" ,
        "文件未被完整上传" ,
        "没有文件被上传" ,
        "上传文件为空" ,
        "POST" => "文件大小超出 post_max_size 限制" ,
        "SIZE" => "文件大小超出config[]自定义限制" ,
        "TYPE" => "不允许的文件类型" ,
        "DIR" => "目录创建失败" ,
        "IO" => "输入输出错误" ,
        "UNKNOWN" => "未知错误" ,
        "MOVE" => "文件保存时出错"
    );

    /**
     * 构造函数
     * @param string $fileField 表单名称
     * @param array $config  配置项
     */
    public function __construct( $fileField , $config )
    {
        $this->fileField = $fileField;
        $this->config = $config;
        $this->stateInfo = $this->stateMap[ 0 ];
        $this->upFile( );
    }

    /**
     * 上传文件的主处理方法
     */
    private function upFile()
    {

        // $_FILES['file'][] 获得相关数组
        $file = $this->file = $_FILES[ $this->fileField ];
		
		// 如果数组为空，表示大小超出了php.ini的post_max_size限制 
        if ( !$file ) {
            $this->stateInfo = $this->getStateInfo( 'POST' );
            return;
        }
		//如果有错误 (error为0表示上传成功)
        if ( $this->file[ 'error' ] ) {
            $this->stateInfo = $this->getStateInfo( $file[ 'error' ] );
            return;
        }
        // is_uploaded_file 判断文件是否是通过 HTTP POST 上传的, 指定 $_FILES['file']['tmp_name']
		if ( !is_uploaded_file( $file[ 'tmp_name' ] ) ) {
            $this->stateInfo = $this->getStateInfo( "UNKNOWN" );
            return;
        }

        $this->oriName = $file[ 'name' ];		//文件名
        $this->fileSize = $file[ 'size' ];		//文件大小
        $this->fileType = $this->getFileExt();	//文件后缀
		
		//如果大小超出配置信息中自定义限制
        if ( !$this->checkSize() ) {
            $this->stateInfo = $this->getStateInfo( "SIZE" );
            return;
        }
		
		//如果文件类型不是config[]中定义的类型
        if ( !$this->checkType() ) {
            $this->stateInfo = $this->getStateInfo( "TYPE" );
            return;
        }
		
		//设置上传后文件的路径及名称
        $this->fullName = $this->getFolder() . '/' . $this->getName();
        
		//如果没有任何错误
		if ( $this->stateInfo == $this->stateMap[ 0 ] ) {
            if ( !move_uploaded_file( $file[ "tmp_name" ] , $this->fullName ) ) {
                $this->stateInfo = $this->getStateInfo( "MOVE" );
            }
        }
    }


    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            "originalName" => $this->oriName ,
            "name" => $this->fileName ,
            "url" => $this->fullName ,
            "size" => $this->fileSize ,
            "type" => $this->fileType ,
            "state" => $this->stateInfo
        );
		
		/**
		 * 得到上传文件所对应的各个参数,数组结构
		 * array(
		 *     "originalName" => "",   //原始文件名
		 *     "name" => "",           //新文件名
		 *     "url" => "",            //返回的地址
		 *     "size" => "",           //文件大小
		 *     "type" => "" ,          //文件类型
		 *     "state" => ""           //上传状态，上传成功时必须返回"SUCCESS"
		 * )
		 */
		
		
		
    }

    /**
     * 上传错误检查
     * @param $errCode
     * @return string
     */
    private function getStateInfo( $errCode )
    {	
		// 如果stateMap数组里没有就是unknow，有则返回
        return !$this->stateMap[ $errCode ] ? $this->stateMap[ "UNKNOWN" ] : $this->stateMap[ $errCode ];
    }

    /**
     * 重命名文件
     * @return string
     */
    private function getName()
    {
        return $this->fileName = time() . rand( 1 , 10000 ) . $this->getFileExt();
    }

    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType()
    {
        return in_array( $this->getFileExt() , $this->config[ "allowFiles" ] );
    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function  checkSize()
    {
        return $this->fileSize <= ( $this->config[ "maxSize" ] * 1024 );
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        //strrchr() 函数查找字符串在另一个字符串中最后一次出现的位置，并返回从该位置到字符串结尾的所有字符。
		return strtolower( strrchr( $this->file[ "name" ] , '.' ) );
    }

    /**
     * 按照日期自动创建存储文件夹
     * @return string
     */
    private function getFolder()
    {
        $pathStr = $this->config[ "savePath" ];
        if ( strrchr( $pathStr , "/" ) != "/" ) {
            $pathStr .= "/";
        }
        $pathStr .= date( "Ymd" );
        if ( !file_exists( $pathStr ) ) {
            if ( !mkdir( $pathStr , 0777 , true ) ) {
                return false;
            }
        }
        return $pathStr;
    }
}

/*	
	该类的使用方法：
	上传表单要提交的php文件 x.php
	include "Uploader.class.php";
	$config = array(
        //"savePath" =>$_SERVER['DOCUMENT_ROOT'].'/upload/',     //设置一个绝对路径
		"savePath" =>'upload/',		//表示当前目录下的upload 
        "maxSize" => 3000 , 		//单位KB
        "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp"  )
    );
	$up = new Uploader( "pic" , $config );
	
	$info = $up->getFileInfo(); //获取上传或文件的信息,路径如果要插入数据库，要用php处理成 /xx/xx/xx 的形式


	<form> 标签的 enctype 属性规定了在提交表单时要使用哪种内容类型。在表单需要二进制数据时，比如文件内容，使用 "multipart/form-data"。
	<form action="upload_file.php" method="post" enctype="multipart/form-data">

    $_FILES["file"]["name"] - 被上传文件的名称
    $_FILES["file"]["type"] - 被上传文件的类型
    $_FILES["file"]["size"] - 被上传文件的大小，以字节计
    $_FILES["file"]["tmp_name"] - 存储在服务器的文件的临时副本的名称
    $_FILES["file"]["error"] - 由文件上传导致的错误代码
	
	0： 没有错误发生，文件上传成功。
	1： 上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的。
	2： 上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的。
	3： 文件只有部分被上传。
	4： 没有文件被上传。
	
*/
