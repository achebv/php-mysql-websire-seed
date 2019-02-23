
///	<reference path="vswd-ext_2.2.js" />


var AllScripts = [];    //  all scripts
var Modules = {};       //  all modules

/**
* Main Module : This can be the parent for all modules
*/
Main = (function () {
	/**
	* All information that I set goes here
	*/
	var _params = {};
	var _ts = Date.now();
	return {
		set: function (pName, pValue) {
			_params[pName] = pValue;
		},
		get: function (pName) {
			return _params[pName];
		},
		getTs: function () {
			return _ts;
		},
		IsIE: (document.all && !window.opera && window.XMLHttpRequest) ? true : false
	}
} ());

/**
Inherit to object from another object
*/
function inherit(to, from) { for (var key in from) { if (from.hasOwnProperty(key)) { to[key] = from[key]; } } return to; }

/*
Test if module is loaded
*/
function if_module(name) { return !(typeof (Modules[name]) == 'undefined'); }

/**
* Include module in head and append in DOM with alias
* @todo: check the main path
*/
function include_module(src, asName, useCdnPath) {
	if (!asName) {
		asName = src;
	}

	var ext = src.substring(src.lastIndexOf('.'));
	
	var srcF = web_path;
	var head = document.getElementsByTagName("head")[0];
	var fileref;
	var useCdnPath = typeof (useCdnPath) == 'undefined' ? true : useCdnPath;
	
	if (ext == '.js') {
		fileref = document.createElement('script');
		fileref.setAttribute("type", "text/javascript");
		fileref.setAttribute("src", srcF + src + '?_' + Main.getTs());
	} else if (ext == '.css') {
		fileref = document.createElement('link');
		fileref.setAttribute("rel", "stylesheet");
		fileref.setAttribute("href", srcF + src + '?_' + Main.getTs());
	} else {
		//		if (console) {
		//			console.warn('Invalid module name: ' + src);
		//		}
		return;
	}

	head.appendChild(fileref);
	AllScripts.push(src);
	fileref.onreadystatechange = function () {
		if (fileref.readyState == 'complete' || fileref.readyState == 'loaded') {
			Modules[asName] = true;
		}
	}
	fileref.onload = function () {
		Modules[asName] = true;
	};
}

/**
src = path to file from CDN or from base SITE   
callback = scope function
asName = usual module name
*/
function module(src, callback, asName, useCdnPath) {
	if (!asName) {
		asName = src;
	}
	if (!AllScripts.inArray(src)) {
		include_module(src, asName, useCdnPath);
	}
	module = function (src, callback, asName, useCdnPath) {
		if (!asName) {
			asName = src;
		}
		if (!AllScripts.inArray(src)) {
			include_module(src, asName, useCdnPath);
		}
		var i = setInterval(function () {
			if (Modules[asName]) {
				callback();
				clearInterval(i);
				if (asName == src) { } else {
					try {
						return eval(asName);
					} catch (e) {
					/*	if (console) {
							console.log('No "' + asName + '" found as object in file: "' + src + '"');
						}*/
					}
				}
			}
		}, 100);
	}
	return module(src, callback, asName);
}

//	 internal tools

/**
* Let us to inspect an object in firefox console
* @param {object} obj The object what you want to inspect
*/
function log(obj, type) {
//	if (typeof (DEV_MODE) != 'undefined' && DEV_MODE) {
		try { if (console) { switch (type) { case "log": console.log(obj); break; case "warn": console.warn(obj); break; case "error": console.error(obj); break; default: console.log(obj); break; } } } catch (er) { }
//	}
}

// Production steps of ECMA-262, Edition 5, 15.4.4.19  
// Reference: http://es5.github.com/#x15.4.4.19  
if (!Array.prototype.map) {
	Array.prototype.map = function (callback, thisArg) {
		var T, A, k;
		if (this == null) {
			throw new TypeError(" this is null or not defined");
		}
		var O = Object(this);
		var len = O.length >>> 0;
		if ({}.toString.call(callback) != "[object Function]") {
			throw new TypeError(callback + " is not a function");
		}
		if (thisArg) {
			T = thisArg;
		}
		A = new Array(len);
		k = 0;
		while (k < len) {
			var kValue, mappedValue;
			if (k in O) {
				kValue = O[k];
				mappedValue = callback.call(T, kValue, k, O);
				A[k] = mappedValue;
			}
			k++;
		}
		return A;
	};
}

Array.prototype.inArray = function (search) {
	for (var i = 0; i < this.length; i++)
		if (this[i] == search) return true;
	return false;
};

/**
* simple bootstrap
*
(ux = function j(u, c, e) { if (!u) return j; var d = document, s = 'script', f = d.getElementsByTagName(s)[0], t = d.createElement(s); t.src = u; f.parentNode.insertBefore(t, f); if (e) t.onerror = e; if (c) { t.onreadystatechange = function () { if (t.readyState == 'loaded' || t.readyState == 'complete') c(j) }; t.onload = function () { c(j) } }; return j }
("//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min1.js")
("//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min2.js")
("//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min4.js")
("//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min3.js")
);
*/