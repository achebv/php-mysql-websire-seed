
var ListMgmt = (function () {


//	public
	var me = {

	};

// inherit
	me = inherit(me, Grid);

	me.init = function () {
		if(AppClient.currentList && AppClient.currentList['key']){
			me.render({
				NoOfCols: 5,
				ItemsPerPage: 'ItemsPerPage',
				GridTpl: 'listValuesTpl',
				GridCt: 'gridCt',
				StartItems: 'startItems',
				EndItems: 'endItems',
				TotalItems: 'totalItems',
				FilterItems: 'FilterItems',
				Filter: {"Name": '', "CurrentList" : AppClient.currentList},
				EmptyMsg: 'Nu exista elemente.'
			});
			if(typeof(AppClient.allList[AppClient.currentList.group]) != 'undefined'){
				var keyGroup = AppClient.allList[AppClient.currentList.group].key;
				/*$.dd({
					id: 'group_' + keyGroup,
					store: {
						url: 'google.com'
					}
				});*/
			}
		}
	}

	return me;

} ());

$(document).ready(function(){
	ListMgmt.init();
});