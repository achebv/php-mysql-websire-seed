/**
 *	Dependency your dependency
 *	Write once - run from everywhere
 */
var Dep = Dependency = (function () {

	var _method, _params, _waitElements = 0, _loadAll = false;

	/**
	*	For callback
	*/
	function runIt() {
		if (typeof (_method) == 'string') {
			if (_method.indexOf('.') >= 0) {
				var obj = eval(_method.substr(0, _method.indexOf('.')));

				if (typeof (_params) != 'undefined') {
					for (var x in _params) {
						obj.set(x, _params[x]);
					}
				}
			}
			eval(_method + '();');
		} else {
			var x = "var a = " + _method + '();';
			eval(x);
		}
	}

	/**
	* Internal load
	*/
	function iLoad(type, method, params) {
		try {
			if (!_loadAll) { _waitElements = 1; }
			_method = method;
			_params = params;
			eval(type + '()');
		} catch (e) {
//			if (console) {
//				console.debug();
//				console.warn(e);
//			}
		}
	}

	
	function form() {	//	submiter, state
		module('web/js/framework/jQuery/ux/jquery.form.js', function () {
			module('web/js/ux/submiter.js', function () {
				_waitElements--;
				if (_waitElements == 0) runIt();
			});
		});
	}
	
	function json() {
		module('web/js/framework/jQuery/ux/jquery.json-2.3.min.js', function () {
			_waitElements--;
			if (_waitElements == 0) runIt();
		});
	}
	
	function win() {
		module('web/css/win.css', function(){
			
		});				
		module('web/js/extend/win.js', function () {
			_waitElements--;
			if (_waitElements == 0) runIt();
		});
	}
	
	function combo() {
		module('web/css/combo.css', function(){
			
		});				
		module('web/js/extend/combo.js', function () {
			_waitElements--;
			if (_waitElements == 0) runIt();
		});
	}
	
	function autocomplete() {
		module('web/css/autocomplete.css', function(){
			
		});				
		module('web/js/extend/autocomplete.js', function () {
			_waitElements--;
			if (_waitElements == 0) runIt();
		});
	}
	
	
	return {
		/**
		*   Run a function with dependency
		*/
		load: function (type, method, params) {
			if (typeof (type) == 'string') {
				iLoad(type, method, params);
			} else {
				_loadAll = true;
				_waitElements = type.length;
				for (var i = 0; i < _waitElements; i++) {
					iLoad(type[i], method, params);
				}
			}
		}
	}
} ());