$(document).ready(function(){});

Common = (function(){

   var me = {};
   me = inherit(me, Main);             //  all must inherit this

   me.init = function(){

   };

	me.reload = function(){
		window.location.reload();
	};

	me.Dictio = function(key){
		return AppClient.Dictio[key] || '['+key+']';
	};

   return me;
})();
