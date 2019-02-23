function nextTab(selector){
	var _noOfTabs = $(selector + ' .tabHeader ul li').length;
	if(getCrtTabIndex(selector) < _noOfTabs){
		$(selector + ' .tabHeader ul li.active').next().click();
	}
	_changeRegisterStep3Form();
}
	
function prevTab(selector){
	if(getCrtTabIndex(selector) > 1){
		$(selector + ' .tabHeader ul li.active').prev().click();
	}
}

function getCrtTabIndex(selector){
	return ($(selector + ' .tabHeader ul li.active').index() + 1);
}


function initTab(selector, o){
	
	if(!o){o = {}};
	var _noOfTabs = $(selector + ' .tabHeader ul li').length;
	$(selector + ' .tabHeader ul li').each(function(){
		
		var tab = $(this);
		tab.click(function(){
		//	if(tab.hasClass('active')) return;
			for(var i=1; i <= _noOfTabs; i++){
				
				$(selector + ' .tabCt .tab'+i).addClass('x-hidden');
			}
			$(selector + ' .tabHeader ul li').removeClass('active');
			tab.addClass('active');
			$(selector + ' .tabCt .'+tab.attr('data-index')).removeClass('x-hidden');
		});
	});
	
	//	 height
	// @ todo: make it dinamic 
	if(o.autoHeight && o.autoHeight==true){
		var h = Math.max($($('.tabCt form').children()[0]).height(), Math.max($($('.tabCt form').children()[1]).height(),$($('.tabCt form').children()[2]).height()));
		$($('.tabCt form').children()[0]).height(h);
		$($('.tabCt form').children()[1]).height(h);
		$($('.tabCt form').children()[2]).height(h);
	}
}