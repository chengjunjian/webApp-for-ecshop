webApp-for-ecshop
=================

ecshop的手机网页版及安卓版及ios版。  
采用php + angular.js + bootstrap [restful Api + angular]     
在此基础上可以做其他商城的二次开发              
具有精品推荐、搜索、分类列表、商品列表、登录、用户中心、商品详情、收藏商品、关于我们等功能      
功能完善中。	      

![Alt text](http://iscript.github.io/data/images/ecshop_demo1.jpeg)   

![Alt text](http://iscript.github.io/data/images/ecshop_demo2.png)        

![Alt text](http://iscript.github.io/data/images/ecshop_demo3.png)
    





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


