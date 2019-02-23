/*!
MODAL jquery plugin
@author slym
*/

/*
DEPENDENCIES:
- jQuery 1.0+
  
USAGE:
- apply         : jQuery(selector).modal({config}*)
- define & use  : jQuery.win({config}*)
- use           : jQuery.win('alias')

NOTES:
* jQuery(selector) is the modal trigger
* {config} is optional
	-- if not specified, APPLIED modal will be empty with default size [it will not be destroyed on close]
	-- if not specified, DEFINED modal will be empty with default size and will be shown upon definition and destroyed on close
* IF {config}.id not specified THEN defined modal will show upon definition and destroyed on close
  
RETURNS:
- jQuery(window) if modal is defined
- jQuery(selector) if modal is applied
- jQuery(trigger) if modal exists
- FALSE if modal does not exist
*/

/*!
@CONFIG
	@id				- modal unique name
	@html			- html content
	@contentEl		- dom element to display in modal
	@xdom			- *//* TRUE | FALSE *//*! if true fetches external content using YQL
	@url			- url to external content *//* see @xdom */ /*!
	@preload		- *//* TRUE | FALSE *//*! if TRUE loads external content upon definition, if FALSE loads external content on show *//* see @url *//*!
	@display		- show modal upon definition
	@width			- *//* NUMBER *//*! modal content width
	@height			- *//* NUMBER *//*! modal content height
	@visibility		- *//* TRUE | FALSE *//*! if true hides/shows modal modal using offset method
	@closeButton	- *//* TRUE | FALSE *//*! if false hides close button
    
@METHODS
	@content(t)		- get/set modal content *//* USAGE: jQuery.win(*).content() returns content string || jQuery.win(*).content('content') sets content *//*!  
	@preload(u,c)	- loads url and displays as modal content, executes callback on success
	@refresh()		- forces reload on defined url and triggers defined callback and shows modal
	@destroy()		- removes modal from dom
	@visible()		- check if modal is visible
      
@EVENTS
	@show(c)		- attaches callback on/triggers modal show
	@hide(c)		- attaches callback on/triggers modal hide
*/

(function (jQuery) {

	// filter YQL response xml
	function filterData(data) {
		// no body tags
		data = data.replace(/<?\/body[^>]*>/g, '');
		// no linebreaks
		data = data.replace(/[\r|\n]+/g, '');
		// no comments
		data = data.replace(/<--[\S\s]*?-->/g, '');
		// no noscript blocks
		data = data.replace(/<noscript[^>]*>[\S\s]*?<\/noscript>/g, '');
		// no script blocks
		data = data.replace(/<script[^>]*>[\S\s]*?<\/script>/g, '');
		// no self closing scripts
		data = data.replace(/<script.*\/>/, '');
		return data;
	};

	// get params helper
	function getParameterByName(s) {
		var params = {},
      e,
      a = /\+/g,
      r = /([^&=]+)=?([^&]*)/g,
      d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
      q = s;
		while (e = r.exec(q))
			params[d(e[1])] = d(e[2]);
		return params;
	};

	// alias for easy access
	jQuery.win = function (o) {
		return jQuery().modal(o);
	};

	jQuery.confirm = function () {
		var o = {};
		var args = Array.prototype.slice.call(arguments);
		var tArgs = args.length;
		if (tArgs < 1) { o = ""; } //	alert with no option.
		var buttons = [], CustomHandler = function () { return true; }, o = args[0];
		if (typeof (o) == 'object') {
			o = {
				title: o.title || '',
				html: o.msg || '',
				btnActive: o.btnActive,
				btnPassive: o.btnPassive,
				handler: o.handler || function () { },
				show: o.show || function () { },
				hide: o.hide || function () { }
			}
			CustomHandler = o.handler;
		} else if (typeof (o) == 'string') {
			o = {};
			if (tArgs == 3) {
				o.title = args[0];
				o.html = args[1];
				CustomHandler = args[2];
			} else if (tArgs == 2) {
				if (typeof (args[1]) == 'string') {	//	'', ''
					o.title = args[0];
					o.html = args[1];
				} else {							//	'', handler
					o.html = args[0];
					CustomHandler = args[1];
				}
			} else if (tArgs == 1) {
				o = {};
				o.html = args[0];
			}
		}
		//	o.height = o.height || 50;
		o.buttons = [{
			text: o.btnActive || 'Ok',
			active: true,
			confirm: true,
			handler: function () {
				var r = CustomHandler(this);
				if (r === false) {
					return;
				}
				w.hide();
			}
		}, {
			text: o.btnPassive || 'Cancel',
			confirm: false,
			handler: function () {
				var r = CustomHandler(this);
				if (r === false) {
					return;
				}
				w.hide();
			}
		}];
		var w = jQuery.win(o);
		return w.show();
	}

	jQuery.alert = function () {
		var o = {};
		var args = Array.prototype.slice.call(arguments);
		var tArgs = args.length;
		if (tArgs < 1) { o = ""; } //	alert with no option.
		var buttons = [], CustomHandler = function () { return true; }, o = args[0];
		if (typeof (o) == 'object') {
			var customWidth = o.width;
			o = {
				title: o.title || '',
				html: o.msg || '',
				handler: o.handler || function () { },
				show: o.show || function () { },
				hide: o.hide || function () { },
				onTop: o.onTop || false
			}
			if (typeof customWidth != 'undefined' && customWidth > 0) o.width = customWidth;
			CustomHandler = o.handler;
		} else if (typeof (o) == 'string') {
			o = {};
			if (tArgs == 3) {
				o.title = args[0];
				o.html = args[1];
				CustomHandler = args[2];
			} else if (tArgs == 2) {
				if (typeof (args[1]) == 'string') {	//	'', ''
					o.title = args[0];
					o.html = args[1];
				} else {							//	'', handler
					o.html = args[0];
					CustomHandler = args[1];
				}
			} else if (tArgs == 1) {
				o = {};
				o.html = args[0];
			}
		}
		//	o.height = o.height || 50;
		o.buttons = [{
			text: o.btnName || 'Close',
			active: true,
			handler: function () {
				CustomHandler(this);
				w.hide();
			}
		}];
		var w = jQuery.win(o);
		return w.show();
	};

	jQuery.fn.modal = function (options) {

		// return modal if only one string parameter specified
		if (typeof (options) == 'string') {
			var modal = jQuery('[data-modal=' + options + '] span#trg_' + options);
			if (modal.data('modal-config')) {
				modal.data('modal-config')['preload'] = false; // reset preload
				return modal.data('modal', true).modal(modal.data('modal-config'));
			} else return false; //'modal '+options+' does not exist';
		};

		var opts = jQuery.extend({}, jQuery.fn.modal.defaults, options),
        modal = this.length ? this : jQuery(document); // reference to modal object

		function _adjustPos(tpl) {
			var currentWin = jQuery('.modal .overlay:visible > label').last();
			jQuery('.modal .overlay').unbind('resize').resize(function () {
				setTimeout(function () {
					/*
					if (typeof (jQuery.win.cp) != 'undefined') {
						if (jQuery.win.cp.height() > currentWin.outerHeight(true)) {
							jQuery('body').height(jQuery('.modal .overlay:visible').height());
						};
					};
					*/
					jQuery('body').height(jQuery('.modal .overlay:visible').height());
				}, 100);
			});

			var scrollPos = 0,
				visibleModals = jQuery('.modal[data-modal]:visible').length,
			// compute document content height
				mh = Math.max(
						Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
						Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
						Math.max(document.body.clientHeight, document.documentElement.clientHeight)
						),
			// get scroll offset
				so = scrollPos || (document.documentElement) ? (document.documentElement.scrollTop || (window.pageYOffset ? window.pageYOffset : 0)) : 0;

			// bring modal into view [if postMessage supported query parent for scroll offset]
			if (self === parent) {
				setTimeout(function () {
					if (jQuery('.content', tpl).height() < (window.innerHeight ? window.innerHeight : document.documentElement.offsetHeight)) jQuery('.overlay', tpl).height(mh); else jQuery('.overlay', tpl).height('auto');
				}, 100);
				jQuery('.overlay>label', tpl).css('padding-top', (so < 200 ? 60 : so + 50) + 'px');
				if (jQuery('.overlay>label', tpl).outerHeight(true) < mh && visibleModals == 1) jQuery('.overlay', tpl).css({ height: mh + 'px' });

			} else {
				if (typeof jQuery.postMessage != 'undefined') {

					jQuery.postMessage({
						queryScroll: 'true',
						myUrl: window.location.toString()
					}, getCookie('docRef'), parent);

					jQuery.receiveMessage(function (e) {

						if (e.data.indexOf('scrollPos') == -1) return;
						//if ((jQuery('.overlay', tpl)[0] === jQuery('.overlay')[0]) && (jQuery('.overlay').length > 1)) return;
						jQuery('.overlay', tpl).height('auto'); // reset overlay height

						var scrollPos = +getParameterByName(e.data)['scrollPos'], 										// parent scroll position
							innerH = +getParameterByName(e.data)['innerH'], 											// parent viewport height
							cp = jQuery.win.cp = jQuery('.content', tpl), 														// current modal

						// update mask to tallest modal
							updateMask = function () {
								//if ( (jQuery('.overlay', tpl)[0] === jQuery('.overlay')[0]) && (jQuery('.overlay').length > 1) ) return;

								//if (cp.height() < innerH - 80) jQuery('.overlay', tpl).height(mh); else jQuery('.overlay', tpl).height('auto');
								if (overlayLabel.outerHeight(true) <= mh) jQuery('.modal .overlay').css({ height: mh + 'px' });

								// get tallest overlay
								var h = Math.max.apply(null, jQuery('.modal .overlay').filter(':visible').map(function () {
									return jQuery(this).outerHeight(true);
								}).get());

								if (cp.height() < innerH - 80) jQuery('.overlay', tpl).height(Math.max(h, mh)); else jQuery('.overlay', tpl).height('auto');

								// update overlay & body height
								jQuery('.overlay', tpl).last().height(h);
								jQuery('body').height(h);
							};

						overlayLabel = jQuery('.overlay>label', tpl);
						overlayLabel.css('padding-top', scrollPos > 150 ? scrollPos - 110 : 15 + 'px'); 				// bring modal into view

						if (jQuery.win.cp.height() > 0) {

							var ho = jQuery.win.ho = overlayLabel.outerHeight(true) - (jQuery('body').outerHeight(true)); 		// compute height difference
							if (visibleModals <= 1) setTimeout(updateMask, 100); else updateMask();
							if (visibleModals == 2) setTimeout(updateMask, 100);
						};
					});
				};
			}

		}

		return modal.each(function () {

			// inits
			var jQuerythis = jQuery(this),
				tpl = jQuerythis.parents('.modal'),
				 o = jQuery.meta ? jQuery.extend({}, opts, jQuerythis.data()) : opts;

			// code here //

			// only inject modal into markup if not already created
			if (!jQuerythis.data('modal') && !jQuerythis.attr('data-modal') && !jQuery('[data-modal=' + o.id + '] span.trigger').data('modal')) {
				var id = Math.floor(Math.random() * 999), // set random id
				target = jQuerythis,
				// modal template
				tpl = jQuery(
				  '<ul class="modal">' +
					'<li>' +
					  '<label for="modal' + id + '">' +
						'<span id="trg_' + (o.id || 0) + '" class="trigger">modal-' + id + '</span>' +
					  '</label>' +
					  '<input type="radio" id="modal' + id + '" name="modals' + id + '" />' + '\n' +
					  '<div class="overlay" style="' + (o.visibility ? 'display:block;visibility:hidden;' : '') + (o.onTop ? 'z-index:9999999;"' : '') + '">' +
						'<label for="">' +
						  '<input type="hidden" name="modals' + id + '" />' +
						  '<span class="content" style="width:' + o.width + 'px;">' +
							'<strong class="closebutton" style="display:' + (o.closeButton ? 'block' : 'none') + '">' +
							  '<label for="close' + id + '"></label>' +
							'</strong>' +
							((o.title) ? '<h1>' + o.title + '</h1>' : '') +
							'<span id="c_' + id + '" class="inner_content" style="height:' + ((o.height) ? o.height + 'px' : 'auto') + ';' + ((o.height) ? 'overflow-y:auto' : '') + '">' +
							  (o.html || '') +
							'</span>' +
							(o.buttons.length ? '<div class="btnCt" style="text-align:' + o.buttonAlign + ';"></div><br style="clear:both;" />' : '') +
						  '</span>' +
						'</label>' +
					  '</div>' +
					  '<input type="radio" id="close' + id + '" name="modals' + id + '" />' +
					'</li>' +
				  '</ul>'
				); // end template

				tpl.attr('data-modal', o.id);

				// if no target specified create modal and append to body
				if (modal.selector == '') {
					tpl.appendTo('body');
					// ie 7/8 mask fix
					jQuery('.overlay', tpl).prepend('<div style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;-moz-opacity:0;-khtml-opacity:0;background:#fff;filter:alpha(opacity=0);">');
					jQuery('.trigger', tpl).data('modal-config', o);
					// force show & destroy on close if no alias specified
					if (!o.id) {
						jQuery('input[id=modal' + id + ']', tpl).attr('checked', 'true');
						jQuery('input[id=close' + id + ']', tpl).unbind('click').click(function (e) {
							e.preventDefault();
							//modal.hide();
							modal.destroy();
						});
					} else { jQuery('.trigger', tpl).data('modal', true); }

					if (o.buttons) {
						o.buttons.map(function (eb) {
							var cls = eb['cls'] ? eb['cls'] : (eb['active'] ? 'def' : '');
							jQuery('<button class="' + cls + '">' + eb.text + '</button>').appendTo(jQuery('.btnCt', tpl)).click(function () {
								eb.handler = eb.handler || function () { };
								eb.handler();
							});
						});
					}
					_adjustPos(tpl);
				} else {
					// if target specified replace existing markup element with modal trigger
					//var url = jQuerythis.attr('href');
					jQuerythis.attr('href', 'javascript:;');
					jQuerythis.replaceWith(tpl);
					//target.wrap('<span id="' + (o.id || 0) + '"></span>');
					//jQuery('label[for=modal' + id + ']', tpl).html('<span id="trg_' + (o.id || 0) + '">' + target + '</span>');
					jQuery('label[for=modal' + id + ']', tpl).html(target);
					target.data('modal', true); // know if modal is created
					//target.parent('span').data('modal', true); // know if modal is created
					//target.parent('span').data('modal-config')['url'] = url;
				}
			};

			// show modal util function
			modal.show = function (c) {
				_adjustPos(tpl);
				if (typeof (c) == 'function') {
					jQuery('.trigger', tpl).data('modal-config')['show'] = c;
					jQuery('input[id^=modal]', tpl).unbind('click').click(function (e) {
						c();
					});
					return modal;
				} else {

					var ct = jQuery('#' + o.contentEl);
					jQuery('<p id="placeOf_' + o.contentEl + '" class="x-hidden"></p>').insertBefore(ct);
					ct.removeClass('x-hidden').appendTo(jQuery('.inner_content', tpl));

					// autosize iframe
					jQuery('iframe', tpl).width('100%').css('overflow-x', 'hidden').height(jQuery('.inner_content', tpl).height() - 20);

					jQuery('input[id^=modal]', tpl).attr('checked', 'true');
					if (!o.preload && o.url && !tpl.hasClass('loaded') && !tpl.hasClass('loading')) modal.preload(o.url, o.success);
				};

				if (o.visibility) {
					jQuery('.overlay', tpl).css({
						display: 'block',
						visibility: 'visible'
					});
				} else {
					jQuery('.overlay', tpl).show();
				};

				o.show();

				setTimeout(function () {
					if (!jQuery('iframe', tpl).length) _adjustPos(tpl);
				}, 120);

				if (typeof jQuery.postMessage != 'undefined') {
					setTimeout(function () {
						jQuery.postMessage({
							showOverlay: 'true'
						}, getCookie('docRef'), parent);
					}, 250);
				};

				return modal;
			};

			// hide modal util function
			modal.hide = function (c) {

				var visibleModals = jQuery('ul.modal').length;

				//ho = jQuery.win.ho = overlayLabel.outerHeight(true) - (jQuery('body').outerHeight(true));

				jQuery('.overlay', tpl).css('height', 0);
				if (visibleModals < 2) jQuery('body').css('height', 0);

				//	if postMessage is supported unmask parent
				if (typeof jQuery.postMessage != 'undefined') {
					// set expanded page height back when closing modal ONLY IF another modal not already displayed

					if (visibleModals == 1) {
						if (jQuery.win.ho > 0) {
							jQuery('body').css('height', jQuery('body').outerHeight() - jQuery.win.ho + 'px');
						};

						/*if (jQuery.win.ho > 1) */jQuery('body').css('height', 'auto');
					} else {
						if (jQuery.win.ho == 0) {
							//jQuery('.overlay,body').height('auto');
							//jQuery('body').height(jQuery('body').height() + 120);
						}
					}
					//
					if (o.id)
						jQuery.postMessage({
							showOverlay: 'false'
						}, getCookie('docRef'), parent);
				};

				jQuery('#' + o.contentEl).addClass('x-hidden').insertAfter(jQuery('#placeOf_' + o.contentEl));
				jQuery('#placeOf_' + o.contentEl).remove();

				var rez = o.hide(); //	do user hide

				if (typeof (c) == 'function') {
					jQuery('.trigger', tpl).data('modal-config')['hide'] = c;
					jQuery('input[id^=close]', tpl).unbind('click').click(function (e) {
						c();
					});
					return modal;
				} else {
					if (!o.id && modal.selector == '') modal.destroy();
					//	jQuery('input[id^=close]',tpl).attr('checked','true');
				};

				if (rez === false) { } else {
					if (jQuery.win.ho < 0 && visibleModals == 1) {
						jQuery('.overlay', tpl).css({ height: 0 });
						jQuery('.overlay>label', tpl).css('padding-top', 0);
					}
					if (o.visibility) {
						jQuery('.overlay', tpl).css({
							display: 'block',
							visibility: 'hidden',
							height: 0
						});
					} else {
						jQuery('.overlay', tpl).hide();
					}
					return;
				}
				return modal;
			};

			// bind close event
			jQuery('label[for^=close]', tpl).unbind('click').click(function (e) {
				modal.hide();
			});

			// if configured to initially show then show on create
			if (o.display) modal.show();

			// set/get modal content
			modal.content = function (t) {
				return jQuery('.inner_content', tpl).html(t);
			};

			modal.title = function (t) {
				return jQuery('.content h1', tpl).html(t);
			}

			// remove modal from dom
			modal.destroy = function () {
				if (o.id) this.hide();
				if (jQuery('.overlay', tpl).is(':visible')) {
					if (typeof jQuery.postMessage != 'undefined') {
						jQuery.postMessage({
							showOverlay: 'false'
						}, getCookie('docRef'), parent);
					};
				}
				return 'destroyed modal ' + o.id + (tpl.remove() ? '' : '');
			};

			// preload util function
			modal.preload = function (u, c) {
				o.url = u || o.url;
				c = c || o.success || function () { };
				tpl.addClass('loading');
				if (o.url) {
					// if crossdomain request use YQL
					if (o.xdom) {
						jQuery.getJSON((o.https ? 'https:' : 'http:') +
							"//query.yahooapis.com/v1/public/yql?" +
							"q=select%20*%20from%20html%20where%20url%3D%22" +
							encodeURIComponent(o.url) +
							"%22&format=xml'&callback=?",
						function (data) {
							if (data.results[0]) {
								var data = filterData(data.results[0]);
								jQuery('.inner_content', tpl).html(data);
								tpl.removeClass('loading');
								tpl.addClass('loaded');
								if (typeof (c) == 'function') c();
							};
						});
					} else
					// inbound request
						jQuery.ajax({
							cache: false,
							data: o.params || {},
							type: (o.reqType) ? o.reqType : 'post',
							url: o.url,
							beforeSend: function (xhr) {
								xhr.callback = c;
							},
							success: function (r, a, xhr) {
								jQuery('.inner_content', tpl).html(r);
								tpl.removeClass('loading');
								tpl.addClass('loaded');
								_adjustPos(tpl);
								if (typeof (xhr.callback) == 'function') xhr.callback();
							}
						});
				};
				return modal;
			};

			// refresh modal alias
			modal.refresh = function () {
				return modal.preload(o.url, o.success).show();
			};

			// check if window is visible
			modal.visible = function () {
				return $('.overlay', tpl).is(':visible');
			}

			// handle preload
			if (!o.preload && o.url) {
				jQuery('label[for=modal' + id + ']', tpl).click(function (e) {
					if (!tpl.hasClass('loaded') && !tpl.hasClass('loading')) modal.preload(o.url, o.success);
				});
			} else if (o.url) modal.preload(o.url, o.success);

			// end code //

			return modal;
		});
	};

	// default config
	jQuery.fn.modal.defaults = {
		buttons: [],
		buttonAlign: 'right',
		closeButton: true,
		https: false,
		xdom: false,
		id: '',
		preload: false,
		display: false,
		visibility: false,
		contentEl: '',
		html: '',
		url: '',
		width: 360,
		show: function () { },
		hide: function () { }
	};
})(jQuery);

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