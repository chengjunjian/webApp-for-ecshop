'use strict';

/* Services */
angular.module('y.services', ['ngResource']).
factory('restApi' ,function($resource){
	return {
        category :  	$resource(api_path +'/category'),
        categoryGoods : $resource(api_path +'/category/:id'),
        doLogin : 		$resource(api_path +'/user/doLogin'),
		search : 		$resource(api_path +'/search/:name'),
		goods: 			$resource(api_path +'/goods/:id'),
		best : 			$resource(api_path +'/index/best'),
		collect: 		$resource(api_path +'/user/collect'),
		collection: 	$resource(api_path +'/user/collection'),
		delete_collection:$resource(api_path+'/user/delete_collection')
	};
});
