'use strict';

angular.module('y',['ngRoute','chieffancypants.loadingBar', 'ngAnimate','y.controllers']).
config(['$routeProvider','$locationProvider','$sceProvider', function($routeProvider,$locationProvider,$sceProvider) {
  	$routeProvider.
  	when('/', {
	    templateUrl: 'templates/index.html',
	    controller: 'indexCtrl'
  	}).
  	when('/login', {
	    templateUrl: 'templates/login.html',
	    controller: 'loginCtrl'
  	}).
  	when('/user', {
	    templateUrl: 'templates/user.html',
	    controller: 'userCtrl'
  	}).
  	when('/category', {
	    templateUrl: 'templates/category.html',
	    controller: 'categoryCtrl'
  	}).
  	when('/category/:id', {
	    templateUrl: 'templates/goodsList.html',
	    controller: 'categoryGoodsCtrl'
  	}).
  	when('/goods/:id', {
	    templateUrl: 'templates/goods.html',
	    controller: 'goodsCtrl'
  	}).
  	when('/search/:name', {
	    templateUrl: 'templates/search.html',
	    controller: 'searchCtrl'
  	}).
  	when('/collection', {
	    templateUrl: 'templates/collection.html',
	    controller: 'collectionCtrl'
  	}).
  	when('/more', {
	    templateUrl: 'templates/more.html',
	    controller: 'moreCtrl'
  	}).
  	when('/about', {
	    templateUrl: 'templates/about.html',
	    controller: 'aboutCtrl'
  	}).
  	otherwise({
		redirectTo: '/'
	});
 

  	//$locationProvider.html5Mode(true);
  	$sceProvider.enabled(false);
}]).	
value('version','0.1').
run(function($rootScope,$http,$location){
	
	$rootScope.api_path = api_path;
	$rootScope.server_path = server_path;
	$rootScope.isLogin = false;
	$rootScope.user_id = 0;
	$rootScope.username = '';
	$rootScope.password = '';

	if(localStorage.userInfo != undefined) {
		var u = JSON.parse(localStorage.userInfo);
		console.log(u);
		$rootScope.isLogin = true;
		$rootScope.user_id = u.user_id;
	}

	$rootScope.search = function(){
		var name = $("#search_text").val();
		if(name == '' || name == undefined){
			alert('请输入关键字');return;
		}
		$location.url('/search/'+name);
	}

});


