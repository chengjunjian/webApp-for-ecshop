/* Filters */
angular.module('y.filters', []).
filter('reverse', function () {
    return function(input, uppercase) {
		var out = "";
		for (var i = 0; i < input.length; i++) {
			//out放后面实现字符反转
			out = input.charAt(i) +out ;
		}
		if (uppercase) {
			out = out.toUpperCase();
		}
		return out;
    }
});