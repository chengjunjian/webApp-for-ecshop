webApp-for-ecshop
=================

ecshop的手机网页版及安卓版及ios版。
采用php + angular.js + bootstrap 



如何部署？
-----------------

###服务端
将server-side 里面的内容拷贝到网站服务器ecshop的目录下， 完成

###网页客户端
将client-side 里面的内容部署到网站服务器，并修改app/index.html
var api_path = 'http://192.168.1.222/ecshop/yApi.php'; 	//这里指向ecshop目录下的yApi.php 
var server_path = 'http://192.168.1.222/ecshop/';		//这里指向ecshop的根目录


###手机app端
同上，修改app/index.html 里面的 api_path 和 server_path
然后用phonegap打包成安卓app或苹果的app (不知道phonegap是什么的请百度~)  


