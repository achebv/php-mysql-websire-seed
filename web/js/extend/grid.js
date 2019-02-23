
Grid = (function(){

	var Config = {};

	var ItemsPerPage = 0;

	var Page = 1;

	var SortIndex = 1;

	var SortDir = 'asc';

	var IsGridStart = false;

	var LastSeenRows = 0;

	function beforeLoadGrid(){
		var htmlRows = '<tr><td colspan="' + Config.NoOfCols + '">(se incarca...)</td></tr>';
		$("#" + Config.GridCt).html('').html(htmlRows);
		$('#'+Config.FilterItems+', #' + Config.ItemsPerPage).attr('disabled', 'disabled');
		IsGridStart = true;
	}

	function afterLoadGrid(){
		$('#'+Config.FilterItems+', #' + Config.ItemsPerPage).removeAttr('disabled');
		IsGridStart = false;
	}

	function applySort(entity){
		$('th[class^="sort"]').unbind('click').click(function(){
			var crt_class = $(this).attr('class');
			var dir = ((crt_class=='sorting') || (crt_class=='sorting_desc')) ? 'asc' : 'desc';
			$('th[class^="sort"]').attr('class', 'sorting');
			$(this).attr('class', 'sorting_' + dir);
			var index = $(this).index() + 1;
//        var obj = eval('(' + entity + ')');
			entity.sortGrid(index, dir);
		});
	}


	function updateItems(){
		if(!Config.Url){
			Config.Url = request + 'updateGrid/';
		}
		$.ajax({
			type: "POST",
			url: Config.Url,
			beforeSend: function(){
				beforeLoadGrid();
			},
			data: {
				ItemsPerPage: ItemsPerPage,
				Page: Page,
				SortDir: SortDir,
				Filter: $.toJSON(Config.Filter),
				GridTpl: Config.GridTpl,
				SortIndex: SortIndex
			},
			success: function(res){
				res = eval('(' + res + ')');
				if(res.html.length<1){
					$("#" + Config.GridCt).html('').html('<tr><td colspan="' + Config.NoOfCols + '">' + Config.EmptyMsg + '</td></tr>');
					$('.dataTables_info', $("#" + Config.GridCt).parent().parent()).attr('style', 'visibility: hidden');
					$('.dataTables_paginate', $("#" + Config.GridCt).parent().parent()).attr('style', 'visibility: hidden');
				}else{
					$('#' + Config.GridCt).html('').html(res.html);
					$('#' + Config.StartItems).html('').html(res.startItems);
					$('#' + Config.EndItems).html('').html(res.endItems);
					$('#' + Config.TotalItems).html('').html(res.totalItems);
					$('.dataTables_info', $("#" + Config.GridCt).parent().parent()).attr('style', 'visibility: visible');
					$('.dataTables_paginate', $("#" + Config.GridCt).parent().parent()).attr('style', 'visibility: visible');
					$('.paginate_enabled_next, .paginate_enabled_previous').attr('style', 'visibility: visible');
					if(!res.hasNext){
						$('.paginate_enabled_next').attr('style', 'visibility: hidden');
					}
					if(!res.hasPrev){
						$('.paginate_enabled_previous').attr('style', 'visibility: hidden');
					}
				}
				afterLoadGrid();
			}
		});
	}

	var me = {};
	me = inherit(me, Main);             //  all must inherit this
	me = inherit(me, Dependency);     	//  all must inherit this

	//	public usage of class
	me.render = function (cfg) {
		Config = cfg;
		ItemsPerPage = $('#' + cfg.ItemsPerPage).val();
		Page = 1;
		updateItems();
		$('#' + cfg.ItemsPerPage).change(function(){
			ItemsPerPage = $(this).val();
//         log(ItemsPerPage);
			Page = 1;
			updateItems();
		});
		applySort(this);
		return this;
	};

	me.reload = function(){
		updateItems();
	};

	me.sortGrid = function(index, dir){
		if(IsGridStart) return;
		SortIndex = index;
		SortDir = dir;
		updateItems();
	};

	me.prevPage = function(){
		if(IsGridStart) return;
		Page = Page - 1;
		updateItems();
	};


	me.nextPage = function(){
		if(IsGridStart) return;
		Page = Page+1;
		updateItems();
	};

	me.filter = function(){
		if(IsGridStart) return;
		SortIndex = 1;
		SortDir = 'asc';
		Page = 1;
		var FilterParam = {};
		for(var f in Config.Filter){
			FilterParam[f] = $('#' + f).val();
		}
		Config.Filter = FilterParam;
		updateItems();
	};

	me.del = function(id){
		log('delete: ' + id);
	};

	return me;

});