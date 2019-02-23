// Production steps of ECMA-262, Edition 5, 15.4.4.19
// Reference: http://es5.github.com/#x15.4.4.19
if (!Array.prototype.map) {
  Array.prototype.map = function(callback, thisArg) {
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
    while(k < len) {
      var kValue, mappedValue;
      if (k in O) {
        kValue = O[ k ];
        mappedValue = callback.call(T, kValue, k, O);
        A[ k ] = mappedValue;
      }
      k++;
    }
    return A;
  };
}

String.prototype.startsWith = function(str) {
    return ( str === this.substr( 0, str.length ) );
}


Array.prototype.inArray = function (search) {
	for (var i = 0; i < this.length; i++)
		if (this[i] == search) return true;
	return false;
};

function log(o){
	try{
		if(console) console.log(o);
	}catch(e){

	}
}

sstJS = (function(){
    return {
    	storeData : function (key, value){
    		sessionStorage.setItem(key, JSON.stringify(value));
    	},

    	getData : function (key, objectOrString){
    		var toObject = (typeof objectOrString == 'undefined' || objectOrString == false) ? false : true;
    		return sessionStorage.getItem(key) ? ( toObject == true ? JSON.parse(sessionStorage.getItem(key)) : sessionStorage.getItem(key) ) : null;
    	},

    	clean : function(){
    		sessionStorage.clear();
    	}
    };

}());

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
