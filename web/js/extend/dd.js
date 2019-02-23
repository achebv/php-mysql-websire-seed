/**
 * dd plugin
 */


(function ($) {

	$.dd = function (o) {

		var _crtCbo = "";
		var _refererOnly = false, _bodyHandler = true, navDownCbo = 0, navUpCbo = 0, canCloseCbo = true, _isElClickCbo = false;

		//	private
		function dd() {

			var _oldVal = '', _fullData = [], _oldValInput, _dataLoaded = false;

			// Create and add dd template
			function _buildCombo(o) {

				var items = '',
					id = 'id',
					text = 'text',
					cls = "";
				if (o.store.reader) {
					id = o.store.reader[0].name || o.store.reader[0];
					text = o.store.reader[1].name || o.store.reader[1];
					if (typeof (o.store.reader[2]) != 'undefined') {
						cls = o.store.reader[2].name || o.store.reader[2] || "";
					}
				}
				if (o.store.data) {
					var d = typeof (o.store['readerRoot']) != 'undefined' ? o.store.data[o.store['readerRoot']] : o.store.data;
					d.map(function (e) {
						if (e[id].length < 1) { } else {
							items += '<li id="opt_' + e[id] + '" class="' + ((e[id] == o.value) ? 'selected' : e.cls) + '" ><a href="javascript:;">' + e[text] + '</a></li>';
						}
					});
				}

				if ($('#fake_' + o.id)) {	//	 this helps for rebind
					$('#fake_' + o.id).remove();
				}
				if ($('ul#list_' + o.id)) {	//	 this helps for rebind
					$('ul#list_' + o.id).parent().remove();
				}

				var tpl =
					'<div class="dd">' +
						'<div class="dd-ddown">' +
							'<ul id="list_' + o.id + '">' +
								items +
							'</ul>' +
						'</div>' +
					'</div>';
				
				var requiredCls		= (typeof o.required != "undefined" && o.required ? "required" : ""),
					ddownContext	= $('#' + o.id).parents('.dd-container').length > 0 ? $('#' + o.id).parents('.dd-container') : 'body';
					
				$(ddownContext).append(tpl);
				return '<span id="fake_' + o.id + '" class="dd">' +
					'<input type="text" style="width:' + (o.width ? o.width - 18 : "") + 'px;"' + (o.readOnly ? ' readonly="readonly" ' : '') + ' class="' + requiredCls + ' rColInput" autocomplete="off" />' +
					'<a href="javascript:;" class="tool"></a>' +
				'</span>';
			}


			function _rebindData(o) {
				var sel_val = o.value;
				if (!o.value) {
					sel_val = $('#' + o.id + ' option:selected').val();
				}
				var items = itemsOpt = '';
				var id = 'id';
				var text = 'text';
				var cls = "";
				if (o.store.reader) {
					id = o.store.reader[0].name || o.store.reader[0];
					text = o.store.reader[1].name || o.store.reader[1];
					if (typeof (o.store.reader[2]) != 'undefined') {
						cls = o.store.reader[2].name || o.store.reader[2] || "";
					}
				}

				if (o.store.data) {
					var d = typeof (o.store['readerRoot']) != 'undefined' ? o.store.data[o.store['readerRoot']] : o.store.data;
					itemsOpt += '<option value="">' + o.emptyText + '</option>';
					d.map(function (e) {
						e.cls = e.cls || '';
						if (e[id].length < 1) { } else {
							items += '<li id="opt_' + e[id] + '" class="' + ((e[id] == sel_val) ? 'selected' : e[cls]) + '" ><a href="javascript:;">' + e[text] + '</a></li>';
							itemsOpt += '<option value="' + e[id] + '" class="' + e[cls] + '" ' + ((e[id] == sel_val) ? 'selected="selected"' : '') + ' >' + e[text] + '</option>';
						}
					});
				}
				$('#list_' + o.id).html('');
				$('#list_' + o.id).html(items);
				$('#' + o.id).html('');
				$('#' + o.id).html(itemsOpt);
			}

			function _mapListCbo() {
				//	click on list element
				$('ul#list_' + o.id + ' li').map(function (i, el) {
					if ($(el).attr('class') != "disabled" && $(el).attr('class') != "selected") {
						var x = el;
						$(el).unbind('click').click(function () {
							if ($(x).attr('class') != "disabled" && $(x).attr('class') != "selected") {
								var newV = _oldVal = $(el).attr('id').replace('opt_', '');
								$('#' + o.id).val(newV).change();
								$('#list_' + o.id).hide();
								/*	if (typeof (o.onChange) == 'function') {
								var obj = _getObjectByKey(o.store, optionS.val());
								o.onChange(obj);
								}*/
							}
						}).mouseenter(function () { _isElClickCbo = true; }).mouseleave(function () { _isElClickCbo = false; });
					}
				});
				if (o.listHeight > 0) {
					$('ul#list_' + o.id).css("max-height", o.listHeight);
				}
			}

			// toggle dd dropdown
			function _showListCombo(o, force) {

				var ddown = $('#list_' + o.id),
					fakeTpl = $('#fake_' + o.id),

				// list width
					listW =
								(
									fakeTpl.width() - 2 < ddown.children('li:first a').outerWidth(true) ?
										ddown.children('li:first a').outerWidth(true)
										:
										(fakeTpl.width() - 2)
								);

				if (!ddown.is(":visible") || force == true) {
					
					var container	= fakeTpl.parents('.dd-container'),
						top 		= container.length > 0 ? fakeTpl.offset().top - (container.offset().top + 1 - container.scrollTop()) + 'px' : fakeTpl.offset().top + 'px',
						left 		= container.length > 0 ? fakeTpl.offset().left - (container.offset().left + 1) + 'px' : fakeTpl.offset().left + 'px';


					$('.dd #list_' + o.id)
						.show()
						.parent('.dd-ddown').css({
							top: top,
							left: left,
							minWidth: listW + 'px',
							zIndex: 2147483647
						});
				}

				ddown.css({
					border: '1px solid #999999',
					display: 'block'
				});

				if ($('#list_' + o.id + ' li:visible').length < 1) {

					ddown.css({
						border: '0px solid #999999',
						display: 'none'
					});
				}

				// resize page to fit dd list (if bigger than page)
				totalHeight = ddown.height() + ddown.offset().top;
				totalHeight = totalHeight > $('body').height() ? totalHeight : 0;
				offsetHeight = $.dd.offsetHeight = totalHeight != 0 ? totalHeight - $('body').height() : false;

				if (offsetHeight) {
					setHeight(totalHeight);
					$('[data-modal] .overlay').height(totalHeight);
				}
			}

			function _applyAction(o) {
				$('#list_' + o.id).mouseenter(function () { _isElClickCbo = true; }).mouseleave(function () {
					_isElClickCbo = false;
					$('#fake_' + o.id + ' input').focus();
				}).mousemove(function () { _isElClickCbo = true; });
				//	trigger click
				$('#fake_' + o.id + ' a.tool').unbind('click').click(function (e) {
					if ($('#fake_' + o.id + ' input').attr("disabled") == "disabled") { return; } // stop it - now I am disabled !
					$('#list_' + o.id + ' li').show();
					$('#list_' + o.id).show();
					$('#fake_' + o.id + ' input').focus();
					if ($(this).hasClass('disable')) { return; } //	stop it - I am disabled !
					_bodyHandler = false;
					_mapListCbo();
					$('ul[id^=list_]').hide();
					e.stopPropagation();
					_oldVal = $('#' + o.id).val();
					_oldValInput = $('#fake_' + o.id + ' input').val();
					if (_fullData) {
						_fullData = _fullData.length < 1 ? o.store.data : _fullData;
					}
					if (o.store.mode == 'remote') {
						o.store.params.q = '';
						_req(o, function (d) {
							o.store.data = d;
							_rebindData(o);
							_applyAction(o);
							_mapListCbo();
							_showListCombo(o);
						});
						return;
					}
					_showListCombo(o);
					$('#fake_' + o.id + ' input').focus();
					_markSelectedCbo(o);
				});

				//	input element events
				$('#fake_' + o.id + ' input').unbind('blur').blur(function () {
					if ($(this).val().length < 1) {
						$(this).val(o.emptyText).addClass('wm');
						$('#' + o.id).val('');
						if (o.store.mode == 'remote') {
							$('#' + o.id).html('');
						}
					}

					if (typeof (o.forceSelect) != 'undefined' && o.forceSelect) {
						if ($('#list_' + o.id + ' li').length == 0) {
							$(this).val(o.emptyText).addClass('wm');
							$('#' + o.id).val('');
						}
					}

					canCloseCbo = _isElClickCbo ? false : true;
					setTimeout(function () {
						if (canCloseCbo) {
							if ($('#list_' + o.id).is(":visible")) {
								$('#list_' + o.id).hide();
								$('#list_' + o.id + ' li').removeClass('selected');
								$('#list_' + o.id + ' li').show();
								// resize page back to normal
								setHeight($('body').height() - $.dd.offsetHeight);
								$('[data-modal] .overlay').height($('body').height() - $.dd.offsetHeight);
							}
						}
					}, 300);
				}).unbind('click').click(function () {
					if ($(this).val() == o.emptyText) {
						$(this).val('');
					}
				}).unbind('focus').focus(function () {
					if (_fullData) {
						_fullData = _fullData.length < 1 ? o.store.data : _fullData;
					}
					canCloseCbo = false;
				}).unbind('keyup').keyup(function (e) {
					if (o.readOnly) { } else {
						//	letters	
						if ((e.keyCode >= 48 && e.keyCode <= 90) || e.keyCode == 8 || e.keyCode == 46) {
							var v = $(this).val();
							$('ul#list_' + o.id).hide();
							$('ul#list_' + o.id + ' li').show();
							var _self = this;
							setTimeout(function () {
								if ($(_self).val() == v) {
									if (v.length >= o.minChr) {
										if (o.store.mode == 'remote') {
											o.store.params.q = v;
											_req(o, function (d) {
												o.store.data = d;
												_rebindData(o);
												_applyAction(o);
												_mapListCbo();
												_showListCombo(o);
											});
											return;
										}
										_mapListCbo();
										_showListCombo(o);
										//	local search
										_filterOptions(v, o);
									}
								}
							}, 500);
						} else {
							//	log(e.keyCode);
						}
					}

					if (e.keyCode == 40) {	//down
						var first = false;
						if (!$('#list_' + o.id).is(":visible")) {
							$('#fake_' + o.id + ' a.tool').click();
							first = true;
						}
						var listItem = $('#list_' + o.id + ' li.selected');
						var nextIndex = $('#list_' + o.id + ' li:visible').index(listItem) + 1;
						nextIndex = ($('#list_' + o.id + ' li:visible').length - 1 <= nextIndex) ? ($('#list_' + o.id + ' li:visible').length - 1) : nextIndex;
						if (first) { nextIndex--; }
						$('#list_' + o.id + ' li').removeClass('selected');
						setActiveElement(nextIndex, o, 1);
						navDownCbo++;
					} else if (e.keyCode == 38) {	//up
						var listItem = $('#list_' + o.id + ' li.selected');
						var nextIndex = $('#list_' + o.id + ' li:visible').index(listItem) - 1;
						nextIndex = nextIndex < 0 ? 0 : nextIndex;
						$('#list_' + o.id + ' li').removeClass('selected');
						setActiveElement(nextIndex, o, -1);
						navUpCbo++;
					} else if (e.keyCode == 13) {	//enter
						$('#list_' + o.id + ' li.selected').click();
						navDownCbo = 0;
						navUpCbo = 0;
					} else if (e.keyCode == 27) {
						$('body').click();
					} else {

					}
				});


				setActiveElement = function (nextIndex, o, dir) {
					var suspectElement = $('#list_' + o.id + ' li:visible')[nextIndex];
					if (dir > 0) {
						if ($(suspectElement).hasClass('disabled')) {
							nextIndex++;
							setActiveElement(nextIndex, o, dir);
							return;
						}
					} else {
						if ($(suspectElement).hasClass('disabled')) {
							nextIndex--;
							setActiveElement(nextIndex, o, dir);
							return;
						}
					}
					$(suspectElement).addClass('selected');
					$('a', $(suspectElement)).focus();
					$(suspectElement).focus();
					$('#fake_' + o.id + ' input').focus();
				};

				//	select change
				$('#' + o.id).unbind('change').change(function (e) {
					var optionS = $('#' + o.id + ' option:selected'),
						comboV = $('#fake_' + o.id + ' input');

					comboV.val(optionS.text() || o.emptyText);
					if (optionS.text() == o.emptyText) comboV.addClass('wm'); else comboV.removeClass('wm');
					//	comboV.removeClass('wm');
					_markSelectedCbo(o);
					if (typeof (o.onChange) == 'function') {
						var obj = _getObjectByKey(o.store, optionS.val());
						o.onChange(obj);
					}
				});
				//	add selected to input
				//	$('#' + o.id).val(o.value).change();

				function setWatermark() {
					$this = $(this)
					if (o.emptyText == $this.val()) {
						$this.addClass('wm');
					} else {
						$this.removeClass('wm');
					}
				}

				$('#fake_' + o.id + ' input').keyup(setWatermark).change(setWatermark);
			}

			//	utils
			function _getObjectByKey(store, key) {
				if (typeof store.data != "undefined") {
					var id = "Key";
					if (o.store.reader) {
						id = o.store.reader[0].name || o.store.reader[0];
					}
					for (var index in store.data.Data) {
						if (store.data.Data.hasOwnProperty(index)) {
							var item = store.data.Data[index];
							if (item[id] == key) {
								return item;
							}
						}
					}
				}
				return {};
			};

			function _filterOptions(v, o) {
				if (v == o.emptyText) { v = ''; }
				v = v.replace(/\"/g, '').replace(/\//g, '').replace(/\(/g, '').replace(/\)/g, '');
				var pattern = "/^" + v + "/i";
				pattern = eval(pattern);
				$('ul#list_' + o.id + ' li').map(function (i, el) {
					if (!pattern.test($(el).text())) {
						$(el).hide();
					}
				});
				$('#list_' + o.id).css({ border: '1px solid #999999' });
				if ($('#list_' + o.id + ' li:visible').length < 1) {
					$('#list_' + o.id).css({ border: '0px solid #999999' });
				}
			}

			function _markSelectedCbo(o) {
				$('ul#list_' + o.id + ' li').removeClass('selected');
				$('ul#list_' + o.id + ' li').each(function (i, el) {
					if ($(el).attr('id') == 'opt_' + _oldVal) {
						$(el).attr('class', 'selected');
						return false;
					}
				});
			}


			return {
				create: function (o) {
					return _buildCombo(o);
				},
				bind: function (o) {
					_applyAction(o);
				} /*,
				getData: function (o, seek) {
					_getData(o, seek);
				}*/,
				dataLoaded: _dataLoaded,
				reBuildCombo: function (o) {
					_rebindData(o);
				}
			}
		}

		function _startRequest(o) {
			$('#fake_' + o.id + ' input').removeClass('error');
			_reqStart = true;
			var v = $('#fake_' + o.id + ' input').val();
			if (v.length < 1 || v == o.emptyText) {
				//$('#fake_' + o.id + ' input').val('loading...');
			}
		}

		_reqStart = false;

		_reqCnt = 0;

		function _req(o, callback) {
			_reqCnt++;
			//if (_reqStart) return;
			callback = callback || function () { };
			var v = $('#fake_' + o.id + ' input').val();
			//$('#fake_' + o.id + ' input').attr('readonly', 'readonly');
			$('#fake_' + o.id + ' a').attr('class', 'tool disable');
			$('#fake_' + o.id + ' a').addClass('tool');
			$('#fake_' + o.id + ' a').addClass('wait');

			if (typeof o.store.beforeReload == 'function') {
				o.store.beforeReload();
			}

			$.ajax({
				dataType: 'json',
				data: o.store.params || {},
				type: 'post',
				url: o.store.url,
				beforeSend: function (xhr) { _startRequest(o); },
				error: function (jqXHR, textStatus, errorThrown) {
					//	log(jqXHR.status + ' ' + jqXHR.statusText);
					_reqStart = false;
					$('#fake_' + o.id + ' input').val(jqXHR.statusText + ' (' + jqXHR.status + ') ');
					$('#fake_' + o.id + ' input').attr('class', 'error');
					//$('#fake_' + o.id + ' input').removeAttr('readonly');
					$('#fake_' + o.id + ' a').removeClass('disable');
					$('#fake_' + o.id + ' a').removeClass('wait');
					$('#fake_' + o.id + ' a').addClass('tool');
				},
				success: function (d) {
					_reqCnt--;
					if (_reqCnt > 0) return;
					//$('#fake_' + o.id + ' input').val(v).focus();
					//$('#fake_' + o.id + ' input').removeAttr('readonly');
					$('#fake_' + o.id + ' a').removeClass('disable');
					$('#fake_' + o.id + ' a').removeClass('wait');
					$('#fake_' + o.id + ' a').addClass('tool');

					callback(d);
					$('#list_' + o.id + ' li').css({ width: 'auto' }); //	fix ie7 width

					//	var dd = callback(d);
					//	dd.val(o.value);
					if (typeof (o.store.success) == 'function') {
						o.store.success(d);
					}
				}
			});
		}

		if (typeof (o) == 'string') {
			$('select').each(function (i, e) {
				if ($(e).data('config')) {
					if ($(e).data('config').id == o) {
						_refererOnly = true;
						_crtCbo = e;
					}
				}
			});
		} else if (typeof (o) == 'object') {
			o.id = o.id || o.applyTo || new Date().getTime();
			if (typeof (o.readOnly) != 'undefined') { } else {
				o.readOnly = false;
			}
			o.listHeight = o.listHeight || 0;
			o.minChr = o.minChr || 1;
			var node = $('#' + o.id)[0].nodeName.toLowerCase();
			if (node != 'select') {
				//alert("For a Combobox transform only a 'select' HTML tag. Error in tag with id: " + o.id);
				return;
			}
			_crtCbo = $('#' + o.id);
		} else {
			//alert("wrong config");
			return;
		}

		if (typeof (_crtCbo) == 'string') {
			//alert("wrong config. No 'select' element with ID: " + o);
			return;
		}
		var defSel = '';
		if (!_refererOnly) {
			if ($("#" + o.id + " option").length > 0 && $($("#" + o.id + " option")[0]).val().length < 1) {
				o.emptyText = o.emptyText || $($("#" + o.id + " option")[0]).text();
			} else {
				if (!o.emptyText) { o.emptyText = ""; }
				if (typeof (o.store) != 'undefined' && o.store.mode == "remote") {
					$('#' + o.id).html('<option value="">' + o.emptyText + '</option>');
				}
				$('<option value="">' + o.emptyText + '</option>').insertBefore($('#' + o.id + ' option')[0]);
				$('#' + o.id).val('');
			}
			//	get default selected class="selected"
			$("#" + o.id + ' option').map(function (i, opt1) {
				if ($(opt1).attr('class') == 'selected') {
					defSel = $(opt1).val();
				}
			});

			var configWithStoreLocal = false;
			var hasOpt2 = false;
			if (!o.store) {
				var dataCbo = [];
				$('#' + o.id + ' option').each(function (i, opt2) {
					hasOpt2 = true;
					dataCbo.push({ id: $(opt2).attr('value'), text: $(opt2).text(), cls: $(opt2).attr('class') || $(opt2).attr('disabled') || "" });
				});
				o.store = {
					mode: 'local',
					reader: [{ name: 'id' }, { name: 'text'}],
					data: dataCbo
				}
			} else {
				if (o.store.mode == 'local') {
					configWithStoreLocal = true;
					$('#' + o.id).html('');
					var newdata = [];
					o.store.data.map(function (d, i) {
						var id = o.store.reader[0].name || o.store.reader[0];
						var text = o.store.reader[1].name || o.store.reader[1];
						var cls = "";
						if (typeof (o.store.reader[2]) != 'undefined') {
							cls = o.store.reader[2].name || o.store.reader[2] || "";
						}
						$('#' + o.id).append('<option value="' + d[id] + '" class="' + cls + '">' + d[text] + '</option>');
						newdata.push({ id: d[id], text: d[text], cls: hasOpt2 ? ($(opt2).attr('class') || $(opt2).attr('disabled') || "") : "" });
					});
					o.store.data = newdata;
				}
			}
			$('#' + o.id).data("config", o);
			$(dd().create(o)).insertBefore('#' + o.id);
			dd().bind(o);
		} else {
			var o = $('#' + o).data('config');
			dd().bind(o);
		}
		$('#' + o.id).hide();


		$('#' + o.id).val('').change();
		if (defSel.length > 0) {
			$('#' + o.id).val(defSel).change();
		}
		if (o.value) {
			$('#' + o.id).val(o.value).change();
		} else if (defSel.length > 0) {
			o.value = defSel;
		}


		return {

			config: $('#' + o.id).data("config"),

			widget: $('#fake_' + o.id),

			select: $('#' + o.id),

			reset: function () {
				$('#' + o.id).val('').change();
				$("#" + o.id + " option:selected").removeAttr("selected");
				$("#fake_" + o.id + " input").val(o.emptyText);
			},

			enable: function (d) {
				if (typeof (d) == 'undefined' || d == true) {
					$('#fake_' + o.id + ' input').removeAttr('disabled');
					$('#fake_' + o.id + ' a').attr('class', 'tool');
				} else {
					$('#fake_' + o.id + ' input').attr('disabled', 'disabled');
					$('#fake_' + o.id + ' a').attr('class', 'tool disable');
				}
			},

			reload: function (p) {
				var options = this.config;
				if (p) {
					for (var pr in p) {
						options.store[pr] = p[pr];
					}
				}
				if (!options.dataLoaded) {
					var self = this;
					_req(options, function (d) {
						options.store.dataLoaded = true;
						options.store.data = d;
						dd().reBuildCombo(options);
						//	options.store.success();
						return self;
					});
				}
			},

			val: function (v) {
				var old = $(this.select).val();
				return (typeof (v) == 'undefined') ? $(this.select).val() : (old == v ? $(this.select).val(v) : $(this.select).val(v).change());
			},

			// set dd value and display text WHEN store not loaded
			setRawValue: function (key, value) {
				$('<option value="' + key + '">' + value + '</option>').appendTo('#' + o.id);
				this.val(key);
				$('#fake_' + o.id + ' input').val(value).removeClass('wm');
			},

			// reset dd value and display test WHEN store not loaded
			reset: function (v) {
				this.setRawValue(v || '', o.emptyText);
				$('#fake_' + o.id + ' input').addClass('wm');
			},

			text: function () {
				return $('#' + this.select.attr('id') + ' option:selected').text();
			},

			disable: function (d) {
				this.enable(typeof (d) == 'undefined' ? false : !d);
			}
		}
	};

})(jQuery);


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