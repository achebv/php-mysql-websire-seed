$(document).ready(function(){
	loadForms();
});

var formsWithError = new Array();

function loadSpecificForm(elId){
	loadForm(elId);
}

function loadForms(){
	for(var x=0; x<document.forms.length; x++){
		var id = $(document.forms[x]).attr('id');
		loadForm(id);
	}
}


function loadForm(id){
    console.log('#' + id);
	$('#' + id).unbind('submit').submit(function(e) {
		$(this).ajaxSubmit({
			beforeSubmit: fFormBefore, 
			success:      fFormAfter, 
			dataType: 'json' ,
			error: function (request, status, error) {
				$('#' + id + ' input:submit, #' + id + ' button:submit').removeAttr('disabled');
				var msg = "#msg" + id;
				$(msg).html('=====Eroare la procesarea formularului. Va rugam reveniti. (Cod Eroare: INVALID_JSON_RESPONSE)');
			}
		});
		return false; 
	});
}

function fFormBefore(formData, jqForm, options) {
	return;
	var msg = "#msg" + jqForm[0].id;
	if($(msg)){
		$(msg).attr('class', '').addClass('alert').addClass('alert-info').html('va rugam asteptati...');
	}
	//$('#' + jqForm[0].id + ' input:submit, #' + jqForm[0].id + ' button:submit, ').attr('disabled', 'disabled');
} 


function callBkEnd(responseText){
	if(responseText['callFn']){
    	var y = responseText['callFn'][1];
    	var x;
    	switch (typeof(y)) {
		case 'object':
			var a = $.toJSON(y);
			x = responseText['callFn'][0]+'(' + a + ')';
			break;
		case 'string':
			var a = $.toJSON(y);
    		x = responseText['callFn'][0]+'("' + y + '")';
			break;
		case 'number':
			x = responseText['callFn'][0]+'(' + y + ')';
			break;
		default:
			x = responseText['callFn'][0]+'()';
			break;
		}
    	eval(x);
    }
}

function fFormAfter(responseText, statusText, req, form){
	$('#' + form[0].id + ' input:submit, #' + form[0].id + ' button:submit').removeAttr('disabled');
	var msg = "#msg" + form[0].id;
	//try{
		if(responseText['msg']){
	    	$(msg).attr('class', '').addClass('alert').addClass(responseText['err'] ? 'alert-error' : 'alert-success').html('').html('<span>&nbsp;</span>'+responseText['msg']);
	    }else{
	    	$(msg).html('---Eroare la procesarea formularului. Va rugam reveniti.');
	    }
		
		$('#' + form[0].id + ' input, ' + '#' + form[0].id + ' select').removeClass('error');
		if(responseText['errFields']){
			var fs = responseText['errFields'];
			var m = $(msg).html();
			m += '<ul>';
			for(var i=0; i<fs.length; i++){
				log(fs[i]);
				var l = $('label[for='+fs[i]+'] span').html();
				var t = $('#' + fs[i]).attr('title') || '';
				m += '<li>'+l+ ' ' + t +'</li>';
				$('#' + fs[i]).addClass('error');
			}
			m += '</ul>';
			$(msg).html(m);
			$('#' + fs[0]).focus();
		}
		formsWithError = new Array();
		if(responseText['res']){
			formError = true;
			var text = '';
			var localText = "";
			for(var section in responseText['res']){
				localText = responseText['res'][section]['msg'];
				if(responseText['res'][section]['err'] && responseText['res'][section]['err'] == true){
					if(!formsWithError.inArray(responseText['res'][section]['fn'])){
						formsWithError.push(responseText['res'][section]['fn']);
					}
					var inputs = document.getElementById(responseText['res'][section]['fn']).getElementsByTagName('label');
					for(var i = inputs.length-1 ; i >= 0 ; i--){
						localText = localText.replace($(inputs[i]).attr('for'), $(inputs[i]).html().toLowerCase().capitalize());
					}
					text+= localText;
				}
			}
	    	$(msg).attr('class', '').addClass('alert').addClass(responseText['error'] ? 'alert-error' : 'alert-success').html('').html(text);
	    }
	    if(responseText['url']){
	    	//de vazut acest redirect;
	    	setTimeout("window.location.href='"+ responseText['url']+"';", 500); 
	    	return;
	    }
	    callBkEnd(responseText);    
	/*}catch(e){
		$(msg).html('++++Eroare la procesarea formularului. Va rugam reveniti.');
		log(e);
	}*/
}

var FormState = (function () {

    var _dirty = false;

    var _initObject = {};

    var _changeSet = [];

    var acceptedTags = ['input', 'select', 'textarea'];

    var acceptedTypes = ['checkbox', 'radio', 'file', 'text'];

    var _autoLog = false;   //  display info when triggered

    var _selector = '';

    var _triggerFn = function () { };

    /**
    *    get ANY control value
    */
    function getControlValue(el) {
        switch ($(el)[0].nodeName.toLowerCase()) {
            case 'input':
                if ($(el).attr('type') == 'checkbox' || $(el).attr('type') == 'radio') {
                    return $(el).is(':checked');
                } else {
                    return $(el).val();
                }
                break;
            case 'select':
                return $('#' + $(el).attr('id') + ' option:selected').val();
                break;
            case 'textarea':
                return $(el).val();
                break;
        }
    }

    /**
    *   Bind ANY Control events    
    */
    function bindEvents(el) {
        // console.log($(el));
        switch ($(el)[0].nodeName.toLowerCase()) {
            case 'input':
                if ($(el).attr('type') == 'checkbox' || $(el).attr('type') == 'radio' || $(el).attr('type') == 'file') {
                    $(el).change(function () {
                        checkForDirty(el);
                    });
                } else {
                    $(el).unbind('keyup');
                    $(el).keyup(function () {
                        checkForDirty(el);
                    });
                    $(el).unbind('blur');
                    $(el).blur(function () {
                        checkForDirty(el);
                    });
                    $(el).bind('paste', function () {
                        $(el).unbind('blur');
                        $(el).blur(function () {
                            checkForDirty(el);
                        });
                    });
                }
                break;
            case 'select':
                $(el).change(function () {
                    checkForDirty(el);
                });
                break;
            case 'textarea':
                $(el).unbind('keyup');
                $(el).keyup(function () {
                    checkForDirty(el);
                });
                $(el).unbind('blur');
                $(el).blur(function () {
                    checkForDirty(el);
                });
                $(el).bind('paste', function () {
                    $(el).unbind('blur');
                    $(el).blur(function () {
                        checkForDirty(el);
                    });
                });
                break;
        }
    }

    /**
    *   This function Know If I made any changes
    */
    function checkForDirty(el) {
        if (!in_array($(el).attr('id'), _changeSet)) {
            _changeSet.push($(el).attr('id'));
        }
        if (_initObject[$(el).attr('id')] == getControlValue(el)) {
            for (var x = 0; x < _changeSet.length; x++) {
                if (_changeSet[x] == $(el).attr('id')) {
                    _changeSet.splice(x, 1);
                    break;
                }
            }
        }
        _dirty = !(_changeSet.length < 1);
        /*if (_autoLog && console) {
            console.log("Form State Logs - BEGIN");
            console.log(_dirty);
            console.log(_changeSet);
            console.log("Form State Logs - END");
        }*/
        _triggerFn();
    }


    /**
    * Like in PHP scripting I need to know if "needle" is in "haystack"
    */
    function in_array(needle, haystack, argStrict) {
        var key = '', strict = !!argStrict;
        if (strict) {
            for (key in haystack) {
                if (haystack[key] === needle) {
                    return true;
                }
            }
        } else {
            for (key in haystack) {
                if (haystack[key] == needle) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
    *   Enter to private
    */
    function CreateConfirm(selector) {
        if (selector.length < 1) { selector = "body"; }
        $(selector).each(function (index, el) {
            if (!in_array($(el)[0].nodeName.toLowerCase(), acceptedTags)) {
                /*if (console) {
                    console.warn("Sorry I don't accept this tag: " + $(el)[0].nodeName.toLowerCase() + " Just only: ");
                    console.log(acceptedTags);
                }*/
                return;
            }
            if ($(el)[0].nodeName.toLowerCase() == "input") {
                if (!in_array($(el).attr('type'), acceptedTypes)) {
                    /*if (console) {
                        console.warn("Sorry I don't accept for an INPUT this type: " + $(el).attr('type') + " Just only: ");
                        console.log(acceptedTypes);
                    }*/
                    return;
                }
            }
            //  copy initial values;
            _initObject[$(el).attr('id')] = getControlValue(el);
            //  bind events
            bindEvents(el);
        });
    }

    return {

        init: function (selector) {
            // CreateConfirm(selector);
            _selector = selector + " :input:not(input[type=hidden],input[type=button],input[type=submit]):not(button), " + selector + " textarea, " + selector + " select";
            CreateConfirm(_selector);
        },

        getObj: function () {
            return _initObject;
        },

        isDirty: function () {
            return _dirty;
        },

        getDiff: function () {
            return _changeSet;
        },

        autoLog: function (v) {
            _autoLog = v;
        },

        checkForChanges: function () {
            $(_selector).keyup();
        },

        triggerFn: function (f) {
            _triggerFn = f;
        }
    }

})();
