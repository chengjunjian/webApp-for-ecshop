'use strict';
/* Controllers */

angular.module('y.controllers',['y.services']).
//首页控制器
controller('indexCtrl', ['$scope','$location','$rootScope','$http','restApi',function($scope,$location,$rootScope,$http,restApi) {
	//alert(8);
    restApi.best.get(function(data){
		console.log(data);
		$scope.goodsList = data.data;
	});
}]).
//分类控制器
controller('categoryCtrl', ['$scope','$location','$rootScope','$http','restApi',function($scope,$location,$rootScope,$http,restApi) {
	restApi.category.get(function(data){
		//console.log(data);
        console.log(data);
		$scope.categories = data.data;

	});

	$scope.show = function(index){
		$(".c"+index).toggle();
	}
}]).
//分类下商品
controller('categoryGoodsCtrl', ['$scope','$location','$rootScope','$http','restApi','$routeParams',function($scope,$location,$rootScope,$http,restApi,$routeParams) {
	var id = $routeParams.id;
	var p = 1;
	restApi.categoryGoods.get({id:id,p:p},function(data){
		console.log(data);
		$scope.goodsList = data.data;
	});

	$scope.getMore = function(){
        p++;
        restApi.categoryGoods.get({id:id,p:p},function(data){

			if(data.data.length==0) {
                alert('没有更多数据');return;
            }
            for(var i=0;i<data.data.length;i++){
               $scope.goodsList.push(data.data[i]);
            }
		});
    }
}]).
//商品控制器
controller('goodsCtrl', ['$scope','$location','$rootScope','$http','restApi','$routeParams',function($scope,$location,$rootScope,$http,restApi,$routeParams) {
	var id = $routeParams.id;
	$scope.title = '';
    restApi.goods.get({id:id},function(data){
		console.log(data);
		$scope.goods= data.data;
        $scope.title = data.data.goods_name;
	});

    $scope.collect = function(){   
    
        if($rootScope.isLogin == false){
            alert('请先登陆');
            $location.url('/login?backUrl=/goods/'+id);return;
        }
        restApi.collect.get({goods_id:id,user_id:$rootScope.user_id},function(data){
            alert(data.msg);
        });
    }

    $scope.addToCart = function(){
        alert('购物模块还没做╮(╯▽╰)╭');
    }
}]).
//商品收藏
controller('collectionCtrl', ['$scope','$location','$rootScope','$http','restApi','$routeParams',function($scope,$location,$rootScope,$http,restApi,$routeParams) {
    if($rootScope.isLogin == false){
        alert('请先登陆');
        $location.url('/login?backUrl=/collection');return;
    }
    $scope.title = '我的收藏';
    restApi.collection.get({user_id:$rootScope.user_id},function(data){
        console.log(data);
        if(data.code == 200){
            $scope.collection = data.data;
        }
    });

    $scope.delete = function(id){   
        restApi.delete_collection.get({collection_id:id},function(data){
            console.log(data);
            if(data.code == 200){
                //$scope.collection = data.data;
                $('.rec'+id).slideUp();
            }
        });
    }
}]).
//search
controller('searchCtrl', ['$scope','$location','$rootScope','$http','restApi','$routeParams',function($scope,$location,$rootScope,$http,restApi,$routeParams) {
    var name = $routeParams.name;
    var page = 1;
    $scope.goodsList = [];
    $scope.title = '搜索中心';
    //console.log(name);
    restApi.search.get({name:name,page:page},function(data){
        console.log(data);
        $scope.goodsList = data.data;
    });

    $scope.getMore = function(){
        page++;
        restApi.search.get({name:name,page:page},function(data){
            if(data.data.length==0) {
                alert('没有更多数据');return;
            }
            if(data.data.length < 10){
                alert('已经是最后一页');return;
            }
            for(var i=0;i<data.data.length;i++){
               $scope.goodsList.push(data.data[i]);
            }
        });
    }
}]).
//登录
controller('loginCtrl', ['$scope','$location','$rootScope','restApi','$routeParams',function($scope,$location,$rootScope,restApi,$routeParams) {
	var backUrl = $routeParams.backUrl ?  $routeParams.backUrl : '/user';
	console.log(backUrl);
	
	$scope.doLogin = function(){
		$("#loginBtn").addClass("disabled");
        
        if($scope.username == undefined || $scope.password == undefined) {
            alert('请正确填写');
            $("#loginBtn").removeClass("disabled");
            return;
        }

        restApi.doLogin.get({username:$scope.username,password:$scope.password},function(data){
        	console.log(data);
        	if(data.code == 200){
        		$rootScope.isLogin = true;
                $rootScope.username = $scope.username;
                $rootScope.password = $scope.password;
                $rootScope.user_id = data.data.user_id;
        		localStorage.userInfo = JSON.stringify(data.data);
        		$location.url(backUrl);
        	}else {
        		alert(data.msg);
                $("#loginBtn").removeClass("disabled");

        	}
        });
	}
}]).
// 用户中心
controller('userCtrl', ['$scope','$rootScope','$location','$http', function ($scope,$rootScope,$location,$http) {

    if($rootScope.isLogin == false ) {
        //alert("请先登陆");
        $location.url('/login?backUrl=/user');
        return;
    }
    
    console.log(localStorage.userInfo);
    var userInfo = JSON.parse(localStorage.userInfo);
    console.log(userInfo);
    $scope.userInfo = userInfo;
                        
  

    $scope.logout = function(){
        $rootScope.isLogin = false;
        localStorage.removeItem("userInfo");
        $location.path('/');
    }

}]).
// about
controller('aboutCtrl', ['$scope','$rootScope','$location','$http', function ($scope,$rootScope,$location,$http) {
    $scope.title = '关于我们';
   
}]).
// 更多
controller('moreCtrl', ['$scope','$rootScope','$location','$http', function ($scope,$rootScope,$location,$http) {

   
}]);