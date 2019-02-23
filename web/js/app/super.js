
var Super = (function () {


    function _login(){
        $("#loginArea").jqxExpander({ toggleMode: 'none', width: '336px', showArrow: false});
        $('.text-input').jqxInput();
        $('.sendBtn').jqxButton();
    }


	return {
		/**
		 *   Run a function with dependency
		 */
		init: function () {
			try{
				eval('(_' + AppClient.renderBox  + '())');
			}catch(e){
			//	log('Unable to process super: ' + renderBox + ' method.');
			//	log(e);
			}
		}
	}

} ());

$(document).ready(function(){
    //$.jqx.theme = 'classic';
    Super.init();
});