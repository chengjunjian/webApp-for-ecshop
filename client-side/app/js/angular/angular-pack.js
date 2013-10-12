/*
 ng-route v1.2.0rc1 
 (c) 2010-2012 Google, Inc. http://angularjs.org
 License: MIT
*/
(function(p,b,E){'use strict';function u(b,d){return l(new (l(function(){},{prototype:b})),d)}var v=b.copy,A=b.equals,l=b.extend,t=b.forEach,n=b.isDefined,w=b.isFunction,x=b.isString,B=b.element;p=b.module("ngRoute",["ng"]).provider("$route",function(){function b(c,q){var d=q.caseInsensitiveMatch,m={originalPath:c,regexp:c},l=m.keys=[];c=c.replace(/([().])/g,"\\$1").replace(/(\/)?:(\w+)([\?|\*])?/g,function(c,b,q,d){c="?"===d?d:null;d="*"===d?d:null;l.push({name:q,optional:!!c});b=b||"";return""+
(c?"":b)+"(?:"+(c?b:"")+(d&&"(.+)?"||"([^/]+)?")+")"+(c||"")}).replace(/([\/$\*])/g,"\\$1");m.regexp=RegExp("^"+c+"$",d?"i":"");return m}var d={};this.when=function(c,q){d[c]=l({reloadOnSearch:!0},q,c&&b(c,q));if(c){var h="/"==c[c.length-1]?c.substr(0,c.length-1):c+"/";d[h]=l({redirectTo:c},b(h,q))}return this};this.otherwise=function(c){this.when(null,c);return this};this.$get=["$rootScope","$location","$routeParams","$q","$injector","$http","$templateCache","$sce",function(c,b,h,m,y,p,C,D){function r(){var a=
s(),e=k.current;if(a&&e&&a.$$route===e.$$route&&A(a.pathParams,e.pathParams)&&!a.reloadOnSearch&&!f)e.params=a.params,v(e.params,h),c.$broadcast("$routeUpdate",e);else if(a||e)f=!1,c.$broadcast("$routeChangeStart",a,e),(k.current=a)&&a.redirectTo&&(x(a.redirectTo)?b.path(g(a.redirectTo,a.params)).search(a.params).replace():b.url(a.redirectTo(a.pathParams,b.path(),b.search())).replace()),m.when(a).then(function(){if(a){var c=l({},a.resolve),b,e;t(c,function(a,b){c[b]=x(a)?y.get(a):y.invoke(a)});n(b=
a.template)?w(b)&&(b=b(a.params)):n(e=a.templateUrl)&&(w(e)&&(e=e(a.params)),e=D.getTrustedResourceUrl(e),n(e)&&(a.loadedTemplateUrl=e,b=p.get(e,{cache:C}).then(function(a){return a.data})));n(b)&&(c.$template=b);return m.all(c)}}).then(function(b){a==k.current&&(a&&(a.locals=b,v(a.params,h)),c.$broadcast("$routeChangeSuccess",a,e))},function(b){a==k.current&&c.$broadcast("$routeChangeError",a,e,b)})}function s(){var a,c;t(d,function(d,k){var f;if(f=!c){var g=b.path();f=d.keys;var m={};if(d.regexp)if(g=
d.regexp.exec(g)){for(var h=1,p=g.length;h<p;++h){var r=f[h-1],n="string"==typeof g[h]?decodeURIComponent(g[h]):g[h];r&&n&&(m[r.name]=n)}f=m}else f=null;else f=null;f=a=f}f&&(c=u(d,{params:l({},b.search(),a),pathParams:a}),c.$$route=d)});return c||d[null]&&u(d[null],{params:{},pathParams:{}})}function g(a,c){var b=[];t((a||"").split(":"),function(a,d){if(0==d)b.push(a);else{var f=a.match(/(\w+)(.*)/),g=f[1];b.push(c[g]);b.push(f[2]||"");delete c[g]}});return b.join("")}var f=!1,k={routes:d,reload:function(){f=
!0;c.$evalAsync(r)}};c.$on("$locationChangeSuccess",r);return k}]});p.provider("$routeParams",function(){this.$get=function(){return{}}});var z=500;p.directive("ngView",["$route","$anchorScroll","$compile","$controller","$animate",function(b,d,c,q,h){return{restrict:"ECA",terminal:!0,priority:z,compile:function(m,l){var p=l.onload||"";m.html("");var n=B(document.createComment(" ngView "));m.replaceWith(n);return function(l){function r(){g&&(g.$destroy(),g=null);f&&(h.leave(f),f=null)}function s(){var k=
b.current&&b.current.locals,a=k&&k.$template;if(a){r();g=l.$new();f=m.clone();f.html(a);h.enter(f,null,n);var a=c(f,!1,z-1),e=b.current;e.controller&&(k.$scope=g,k=q(e.controller,k),e.controllerAs&&(g[e.controllerAs]=k),f.data("$ngControllerController",k),f.children().data("$ngControllerController",k));e.scope=g;a(g);g.$emit("$viewContentLoaded");g.$eval(p);d()}else r()}var g,f;l.$on("$routeChangeSuccess",s);s()}}}}])})(window,window.angular);

/*
 ng-resource v1.2.0rc1
 (c) 2010-2012 Google, Inc. http://angularjs.org
 License: MIT
*/
(function(H,g,C){'use strict';var A=g.$$minErr("$resource");g.module("ngResource",["ng"]).factory("$resource",["$http","$parse","$q",function(D,y,E){function n(g,h){this.template=g;this.defaults=h||{};this.urlParams={}}function u(l,h,d){function p(c,b){var e={};b=v({},h,b);q(b,function(a,b){t(a)&&(a=a());var m;a&&a.charAt&&"@"==a.charAt(0)?(m=a.substr(1),m=y(m)(c)):m=a;e[b]=m});return e}function b(c){return c.resource}function e(c){z(c||{},this)}var F=new n(l);d=v({},G,d);q(d,function(c,f){var h=
/^(POST|PUT|PATCH)$/i.test(c.method);e[f]=function(a,f,m,l){var d={},r,s,w;switch(arguments.length){case 4:w=l,s=m;case 3:case 2:if(t(f)){if(t(a)){s=a;w=f;break}s=f;w=m}else{d=a;r=f;s=m;break}case 1:t(a)?s=a:h?r=a:d=a;break;case 0:break;default:throw A("badargs",arguments.length);}var n=r instanceof e,k=n?r:c.isArray?[]:new e(r),x={},u=c.interceptor&&c.interceptor.response||b,y=c.interceptor&&c.interceptor.responseError||C;q(c,function(a,c){"params"!=c&&("isArray"!=c&&"interceptor"!=c)&&(x[c]=z(a))});
x.data=r;F.setUrlParams(x,v({},p(r,c.params||{}),d),c.url);d=D(x).then(function(a){var b=a.data,f=k.$promise;if(b){if(g.isArray(b)!=!!c.isArray)throw A("badcfg",c.isArray?"array":"object",g.isArray(b)?"array":"object");c.isArray?(k.length=0,q(b,function(a){k.push(new e(a))})):(z(b,k),k.$promise=f)}k.$resolved=!0;(s||B)(k,a.headers);a.resource=k;return a},function(a){k.$resolved=!0;(w||B)(a);return E.reject(a)}).then(u,y);return n?d:(k.$promise=d,k.$resolved=!1,k)};e.prototype["$"+f]=function(a,c,
b){t(a)&&(b=c,c=a,a={});a=e[f](a,this,c,b);return a.$promise||a}});e.bind=function(c){return u(l,v({},h,c),d)};return e}var G={get:{method:"GET"},save:{method:"POST"},query:{method:"GET",isArray:!0},remove:{method:"DELETE"},"delete":{method:"DELETE"}},B=g.noop,q=g.forEach,v=g.extend,z=g.copy,t=g.isFunction;n.prototype={setUrlParams:function(l,h,d){var p=this,b=d||p.template,e,n,c=p.urlParams={};q(b.split(/\W/),function(f){!/^\d+$/.test(f)&&(f&&RegExp("(^|[^\\\\]):"+f+"(\\W|$)").test(b))&&(c[f]=!0)});
b=b.replace(/\\:/g,":");h=h||{};q(p.urlParams,function(c,d){e=h.hasOwnProperty(d)?h[d]:p.defaults[d];g.isDefined(e)&&null!==e?(n=encodeURIComponent(e).replace(/%40/gi,"@").replace(/%3A/gi,":").replace(/%24/g,"$").replace(/%2C/gi,",").replace(/%20/g,"%20").replace(/%26/gi,"&").replace(/%3D/gi,"=").replace(/%2B/gi,"+"),b=b.replace(RegExp(":"+d+"(\\W|$)","g"),n+"$1")):b=b.replace(RegExp("(/?):"+d+"(\\W|$)","g"),function(a,c,b){return"/"==b.charAt(0)?b:c+b})});b=b.replace(/\/+$/,"");b=b.replace(/\/\.(?=\w+($|\?))/,
".");l.url=b.replace(/\/\\\./,"/.");q(h,function(c,b){p.urlParams[b]||(l.params=l.params||{},l.params[b]=c)})}};return u}])})(window,window.angular);


/*
animate v1.2.0-rc.2
 (c) 2010-2012 Google, Inc. http://angularjs.org
 License: MIT
*/
(function(C,n,D){'use strict';n.module("ngAnimate",["ng"]).config(["$provide","$animateProvider",function(A,t){var u=n.noop,v=n.forEach,B=t.$$selectors,p="$$ngAnimateState",z={running:!0};A.decorator("$animate",["$delegate","$injector","$sniffer","$rootElement","$timeout","$rootScope",function(r,n,t,g,m,h){function w(a){if(a){var e=[],d={};a=a.substr(1).split(".");a.push("");for(var b=0;b<a.length;b++){var s=a[b],f=B[s];f&&!d[s]&&(e.push(n.get(f)),d[s]=!0)}return e}}function f(a,e,d,b,f,h){function n(a){v(a,
function(a){(a.endFn||u)(!0)})}function c(){c.hasBeenRun||(c.hasBeenRun=!0,d.removeData(p),(h||u)())}var l=(" "+((d.attr("class")||"")+" "+e)).replace(/\s+/g,"."),k=[];v(w(l),function(c,d){k.push({start:c[a]})});b||(b=f?f.parent():d.parent());f={running:!0};(b.inheritedData(p)||f).running?m(h||u,0,!1):(b=d.data(p)||{},b.running&&(n(b.animations),b.done()),d.data(p,{running:!0,animations:k,done:c}),v(k,function(b,f){var l=function(){a:{k[f].done=!0;(k[f].endFn||u)();for(var a=0;a<k.length;a++)if(!k[a].done)break a;
c()}};b.start?b.endFn="addClass"==a||"removeClass"==a?b.start(d,e,l):b.start(d,l):l()}))}g.data(p,z);return{enter:function(a,e,d,b){r.enter(a,e,d);h.$$postDigest(function(){f("enter","ng-enter",a,e,d,function(){b&&m(b,0,!1)})})},leave:function(a,e){h.$$postDigest(function(){f("leave","ng-leave",a,null,null,function(){r.leave(a,e)})})},move:function(a,e,d,b){r.move(a,e,d);h.$$postDigest(function(){f("move","ng-move",a,null,null,function(){b&&m(b,0,!1)})})},addClass:function(a,e,d){f("addClass",e,a,
null,null,function(){r.addClass(a,e,d)})},removeClass:function(a,e,d){f("removeClass",e,a,null,null,function(){r.removeClass(a,e,d)})},enabled:function(a){arguments.length&&(z.running=!a);return!z.running}}}]);t.register("",["$window","$sniffer","$timeout",function(r,p,u){function g(c,l,k){function q(a){var c=0;a=n.isString(a)?a.split(/\s*,\s*/):[];h(a,function(a){c=Math.max(parseFloat(a)||0,c)});return c}if(p.transitions||p.animations){if(-1==["ng-enter","ng-leave","ng-move"].indexOf(l)){var g=0;
h(c,function(a){a.nodeType==v&&(a=r.getComputedStyle(a)||{},g=Math.max(q(a[f+d]),q(a[e+d]),g))});if(0<g){k();return}}c.addClass(l);var m=0;h(c,function(c){if(c.nodeType==v){c=r.getComputedStyle(c)||{};var l=Math.max(q(c[f+b]),q(c[e+b])),k=Math.max(q(c[w+b]),q(c[a+b])),h=Math.max(q(c[f+d]),q(c[e+d])),g=Math.max(q(c[w+d]),q(c[a+d]));0<g&&(g*=Math.max(parseInt(c[w+t])||0,parseInt(c[a+t])||0,1));m=Math.max(k+g,l+h,m)}});if(0<m){var x=c[0];x.style[f+s]="none";x.style[e+s]="none";var y="";h(l.split(" "),
function(a,c){y+=(0<c?" ":"")+a+"-active"});c.prop("clientWidth");x.style[f+s]="";x.style[e+s]="";c.addClass(y);u(k,1E3*m,!1);return function(a){c.removeClass(l);c.removeClass(y);a&&k()}}c.removeClass(l)}k()}function m(a,d){var b="";a=n.isArray(a)?a:a.split(/\s+/);h(a,function(a,c){a&&0<a.length&&(b+=(0<c?" ":"")+a+d)});return b}var h=n.forEach,w="animation",f="transition",a=p.vendorPrefix+"Animation",e=p.vendorPrefix+"Transition",d="Duration",b="Delay",s="Property",t="IterationCount",v=1;return{enter:function(a,
b){return g(a,"ng-enter",b)},leave:function(a,b){return g(a,"ng-leave",b)},move:function(a,b){return g(a,"ng-move",b)},addClass:function(a,b,d){return g(a,m(b,"-add"),d)},removeClass:function(a,b,d){return g(a,m(b,"-remove"),d)}}}])}])})(window,window.angular);