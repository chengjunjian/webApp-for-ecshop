<?php
class Email
{
	public $smtp_port;		//stmp端口
	public $time_out;		//超时
	public $host_name;		//
	public $log_file;		//日志文件
	public $relay_host;
	public $debug = false;	//true则向屏幕输出信息
	public $auth;
	public $user;
	public $pass;
	public $sock;
	
	public $option = array(
		'SMTP_SERVER' 	=>'smtp.163.com',
		'SMTP_PORT'		=>25,
		'SMTP_USER_EMAIL' =>'test998765@163.com',
		'SMTP_TIME_OUT'	=>30,
		'SMTP_AUTH'		=>true,
		'SMTP_USER'		=>'test998765@163.com',
		'SMTP_PWD'		=>'yy123456',
		'SMTP_MAIL_TYPE'=>'HTML',	
	);

	public function __construct()
	{
		$this->smtp_port = $this->option['SMTP_PORT'];
		$this->relay_host = $this->option['SMTP_SERVER'];
		$this->time_out = $this->option['SMTP_TIME_OUT'];
		$this->auth = $this->option['SMTP_AUTH'];
		$this->user = $this->option['SMTP_USER'];
		$this->pass = $this->option['SMTP_PWD'];
		$this->host_name = "localhost";
		//$this->log_file ="";
		$this->sock = FALSE;
	}

	public function send($data)
	{
		if(is_array($data))
		{
			$from = empty($data['mailfrom'])? $this->option['SMTP_USER_EMAIL']:$data['mailfrom'];
			$subject = empty($data['subject'])? 'no subject':$data['subject'];
			$body = empty($data['body']) ? 'no title':$data['body'];
			$mailtype = empty($data['mailtype'])? $this->option['SMTP_MAIL_TYPE']: $data['mailtype'];
			$to = $data['mailto'];
		}

		$mail_from = $this->get_address($from);
		
		//header
		$header = '';
		$header .= "MIME-Version:1.0\r\n";
		if($mailtype=="HTML")
		{
			$header .= "Content-Type:text/html\r\n";
		}
		$header .= "To: ".$to."\r\n";
		$header .= "From: $from<".$from.">\r\n";
		$header .= "Subject: ".$subject."\r\n";
		$header .= "Date: ".date("r")."\r\n";
		$header .= "X-Mailer:By Redhat (PHP/".phpversion().")\r\n";
		list($msec, $sec) = explode(" ", microtime());
		$header .= "Message-ID: <".date("YmdHis", $sec).".".($msec*1000000).".".$mail_from.">\r\n";
		
		$TO = explode(",", $to);
			 
		$sent = TRUE;
		foreach ($TO as $rcpt_to) {
			$rcpt_to = $this->get_address($rcpt_to);
			
			if (!$this->smtp_sockopen_relay()) 
			{
				$this->log_write("Error: Cannot send email to ".$rcpt_to."\n");
				$sent = FALSE;
				continue;
			}
			if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $body)) 
			{
				$this->log_write("E-mail has been sent to <".$rcpt_to.">\n");
			} 
			else 
			{
				$this->log_write("Error: Cannot send email to <".$rcpt_to.">\n");
				$sent = FALSE;
			}
			fclose($this->sock);
				$this->log_write("Disconnected from remote host\n");
		}
		
		if($this->debug)
		{
			echo "<br>";
			echo $header;
		}
		return $sent;
	}
	
	//邮件发送
	private function smtp_send($helo, $from, $to, $header, $body = "")
	{	
		//发送HELO命令用来标识自己的身份
		if (!$this->smtp_putcmd("HELO", $helo)) 
		{
			return $this->_error("sending HELO command");
		}
		//认证
		if($this->auth){
			if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user))) 
			{
				return $this->_error("sending HELO command");
			}
		 
			if (!$this->smtp_putcmd("", base64_encode($this->pass))) 
			{
				return $this->_error("sending HELO command");
			}
		}
		//邮件发送者
		if (!$this->smtp_putcmd("MAIL", "FROM:<".$from.">")) 
		{
			return $this->_error("sending MAIL FROM command");
		}
	 
		//邮件接收者
		if (!$this->smtp_putcmd("RCPT", "TO:<".$to.">")) 
		{
			return $this->_error("sending RCPT TO command");
		}
	 	
		//指示开始实际的邮件内容传输
		if (!$this->smtp_putcmd("DATA")) 
		{
			return $this->_error("sending DATA command");
		}
	 	
		//开始传输
		if (!$this->smtp_message($header, $body)) 
		{
			return $this->_error("sending message");
		}
	 	
		//信息结束
		if (!$this->smtp_eom()) 
		{
			return $this->_error("sending <CR><LF>.<CR><LF> [EOM]");
		}
	 
		//退出
		if (!$this->smtp_putcmd("QUIT")) 
		{
			return $this->_error("sending QUIT command");
		}
			return TRUE;
	}


 	
	private function smtp_sockopen_relay(){
		
		$this->log_write("Trying to ".$this->relay_host.":".$this->smtp_port."\n");
		//
		$this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
		if (!($this->sock && $this->smtp_ok())) {
			$this->log_write("Error: Cannot connenct to relay host ".$this->relay_host."\n");
			$this->log_write("Error: ".$errstr." (".$errno.")\n");
			return FALSE;
		}
		$this->log_write("Connected to relay host ".$this->relay_host."\n");
		return TRUE;;
	}
 
	
	private function smtp_message($header, $body){
		
		fputs($this->sock, $header."\r\n".$body);
		$this->display_info("> ".str_replace("\r\n", "\n"."> ", $header."\n> ".$body."\n> "));
		return TRUE;
	}
 
	private function smtp_eom(){
		
		fputs($this->sock, "\r\n.\r\n");
		$this->display_info(". [EOM]\n"); 
		return $this->smtp_ok();
	}
 	
	//返回
	private function smtp_ok(){
	
		$response = str_replace("\r\n", "", fgets($this->sock, 512));
		$this->display_info($response."\n");
		return TRUE;
	}
 
	private function smtp_putcmd($cmd, $arg = ""){	
		
		if ($arg != "")
		{
			if($cmd=="") $cmd = $arg;
			else $cmd = $cmd." ".$arg;
		} 
		fputs($this->sock, $cmd."\r\n");
		$this->display_info("> ".$cmd."\n");
		return $this->smtp_ok();
	}
 
	private function _error($string){
		
		$this->log_write("Error: Error occurred while ".$string.".\n");
		return FALSE;
	}
    
	//保存日志
	private function log_write($message){
		
		$this->display_info($message);
	 	
		if ($this->log_file == "") 
		{
			return TRUE;
		}
	 	
		$message = date("M d H:i:s ").get_current_user()."[".getmypid()."]: ".$message;
	
		if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a"))) 
		{
			$this->display_info("Warning: Cannot open log file \"".$this->log_file."\"\n");
			return FALSE;
		}
		
		flock($fp, LOCK_EX);
		fputs($fp, $message);
		fclose($fp);
		return TRUE;
	}
 
	private function get_address($address){
		$address = preg_replace("/([ \t\r\n])+/", "", $address);
		return $address;
	}
 
	private function display_info($message){
	 	if ($this->debug) 
		{
			echo $message."<br>";
		}
	}
 
	function get_attach_type($image_tag) { 
	 
		$filedata = array();
		 
		$img_file_con=fopen($image_tag,"r");
		unset($image_data);
		while ($tem_buffer=AddSlashes(fread($img_file_con,filesize($image_tag))))
		$image_data.=$tem_buffer;
		fclose($img_file_con);
		$filedata['context'] = $image_data;
		$filedata['filename']= basename($image_tag);
		$extension=substr($image_tag,strrpos($image_tag,"."),strlen($image_tag)-strrpos($image_tag,"."));
		switch($extension)
		{
			case ".gif":
			$filedata['type'] = "image/gif";
			break;
			case ".gz":
			$filedata['type'] = "application/x-gzip";
			break;
			case ".htm":
			$filedata['type'] = "text/html";
			break;
			case ".html":
			$filedata['type'] = "text/html";
			break;
			case ".jpg":
			$filedata['type'] = "image/jpeg";
			break;
			case ".tar":
			$filedata['type'] = "application/x-tar";
			break;
			case ".txt":
			$filedata['type'] = "text/plain";
			break;
			case ".zip":
			$filedata['type'] = "application/zip";
			break;
			default:
			$filedata['type'] = "application/octet-stream";
			break;
		}
		return $filedata;
	}
 }
 
 //$data['mailto'] 	='379395979@qq.com'; //收件人
 //$data['subject'] =	'nihao';    //邮件标题
 //$data['body'] 	=	'<a href="163.com" >adsf</a>';    //邮件正文内容
 //$email = new Email();
 //$email->send($data);
?>