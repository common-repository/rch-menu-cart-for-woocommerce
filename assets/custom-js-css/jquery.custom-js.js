(function(jQuery) {
var CLOSE_EVENT 		= 'Close',
	BEFORE_CLOSE_EVENT 	= 'BeforeClose',
	AFTER_CLOSE_EVENT 	= 'AfterClose',
	BEFORE_APPEND_EVENT = 'BeforeAppend',
	MARKUP_PARSE_EVENT 	= 'MarkupParse',
	OPEN_EVENT 			= 'Open',
	CHANGE_EVENT 		= 'Change',
	NS 					= 'rllc',
	EVENT_NS 			= '.' + NS,
	READY_CLASS 		= 'rllc-ready',
	REMOVING_CLASS 		= 'rllc-removing',
	PREVENT_CLOSE_CLASS = 'rllc-prevent-close';

var rllc, 
	CustomJs = function(){},
	_isJQ = !!(window.jQuery),
	_prevStatus,
	_window = jQuery(window),
	_body,
	_document,
	_prevContentType,
	_wrapClasses,
	_currPopupType;

var _rllcOn = function(name, f) {
		rllc.ev.on(NS + name + EVENT_NS, f);
	},
	_getEl = function(className, appendTo, html, raw) {
		var el = document.createElement('div');
		el.className = 'rllc-'+className;
		if(html) {
			el.innerHTML = html;
		}
		if(!raw) {
			el = jQuery(el);
			if(appendTo) {
				el.appendTo(appendTo);
			}
		} else if(appendTo) {
			appendTo.appendChild(el);
		}
		return el;
	},
	_rllcTrigger = function(e, data) {
		rllc.ev.triggerHandler(NS + e, data);
		if(rllc.st.callbacks) {
			e = e.charAt(0).toLowerCase() + e.slice(1);
			if(rllc.st.callbacks[e]) {
				rllc.st.callbacks[e].apply(rllc, jQuery.isArray(data) ? data : [data]);
			}
		}
	},
	_getCloseBtn = function(type) {
		if(type !== _currPopupType || !rllc.currTemplate.closeBtn) {
			rllc.currTemplate.closeBtn = jQuery( rllc.st.closeMarkup.replace('%title%', rllc.st.tClose ) );
			_currPopupType = type;
		}
		return rllc.currTemplate.closeBtn;
	},
	_checkInstance = function() {
		if(!jQuery.customPopup.instance) {
			rllc = new CustomJs();
			rllc.init();
			jQuery.customPopup.instance = rllc;
		}
	},
	
	supportsTransitions = function() {
		var s = document.createElement('p').style, 
			v = ['ms','O','Moz','Webkit']; 
		if( s['transition'] !== undefined ) {
			return true; 
		}
		while( v.length ) {
			if( v.pop() + 'Transition' in s ) {
				return true;
			}
		}		
		return false;
	};

CustomJs.prototype = {
	constructor: CustomJs,
	init: function() {
		var appVersion = navigator.appVersion;
		rllc.isIE7 = appVersion.indexOf("MSIE 7.") !== -1; 
		rllc.isIE8 = appVersion.indexOf("MSIE 8.") !== -1;
		rllc.isLowIE = rllc.isIE7 || rllc.isIE8;
		rllc.isAndroid = (/android/gi).test(appVersion);
		rllc.isIOS = (/iphone|ipad|ipod/gi).test(appVersion);
		rllc.supportsTransition = supportsTransitions();
		rllc.probablyMobile = (rllc.isAndroid || rllc.isIOS || /(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent) );
		_document = jQuery(document);
		rllc.popupsCache = {};
	},

	open: function(data) {
		if(!_body) {
			_body = jQuery(document.body);
		}
		var i;
		if(data.isObj === false) { 
			rllc.items = data.items.toArray();
			rllc.index = 0;
			var items = data.items,
				item;
			for(i = 0; i < items.length; i++) {
				item = items[i];
				if(item.parsed) {
					item = item.el[0];
				}
				if(item === data.el[0]) {
					rllc.index = i;
					break;
				}
			}
		} else {
			rllc.items = jQuery.isArray(data.items) ? data.items : [data.items];
			rllc.index = data.index || 0;
		}
		if(rllc.isOpen) {
			rllc.updateItemHTML();
			return;
		}
		
		rllc.types = []; 
		_wrapClasses = '';
		if(data.mainEl && data.mainEl.length) {
			rllc.ev = data.mainEl.eq(0);
		} else {
			rllc.ev = _document;
		}

		if(data.key) {
			if(!rllc.popupsCache[data.key]) {
				rllc.popupsCache[data.key] = {};
			}
			rllc.currTemplate = rllc.popupsCache[data.key];
		} else {
			rllc.currTemplate = {};
		}

		rllc.st = jQuery.extend(true, {}, jQuery.customPopup.defaults, data ); 
		rllc.fixedContentPos = rllc.st.fixedContentPos === 'auto' ? !rllc.probablyMobile : rllc.st.fixedContentPos;
		if(rllc.st.modal) {
			rllc.st.closeOnContentClick = false;
			rllc.st.closeOnBgClick = false;
			rllc.st.showCloseBtn = false;
			rllc.st.enableEscapeKey = false;
		}
		
		if(!rllc.bgOverlay) {
			rllc.bgOverlay = _getEl('bg').on('click'+EVENT_NS, function() {
				rllc.close();
			});

			rllc.wrap = _getEl('wrap').attr('tabindex', -1).on('click'+EVENT_NS, function(e) {
				if(rllc._checkIfClose(e.target)) {
					rllc.close();
				}
			});
			rllc.container = _getEl('container', rllc.wrap);
		}

		rllc.contentContainer = _getEl('content');
		if(rllc.st.preloader) {
			rllc.preloader = _getEl('preloader', rllc.container, rllc.st.tLoading);
		}

		var modules = jQuery.customPopup.modules;
		for(i = 0; i < modules.length; i++) {
			var n = modules[i];
			n = n.charAt(0).toUpperCase() + n.slice(1);
			rllc['init'+n].call(rllc);
		}
		_rllcTrigger('BeforeOpen');


		if(rllc.st.showCloseBtn) {
			if(!rllc.st.closeBtnInside) {
				rllc.wrap.append( _getCloseBtn() );
			} else {
				_rllcOn(MARKUP_PARSE_EVENT, function(e, template, values, item) {
					values.close_replaceWith = _getCloseBtn(item.type);
				});
				_wrapClasses += ' rllc-close-btn-in';
			}
		}

		if(rllc.st.alignTop) {
			_wrapClasses += ' rllc-align-top';
		}

		if(rllc.fixedContentPos) {
			rllc.wrap.css({
				overflow: rllc.st.overflowY,
				overflowX: 'hidden',
				overflowY: rllc.st.overflowY
			});
		} else {
			rllc.wrap.css({ 
				top: _window.scrollTop(),
				position: 'absolute'
			});
		}
		if( rllc.st.fixedBgPos === false || (rllc.st.fixedBgPos === 'auto' && !rllc.fixedContentPos) ) {
			rllc.bgOverlay.css({
				height: _document.height(),
				position: 'absolute'
			});
		}

		if(rllc.st.enableEscapeKey) {
			_document.on('keyup' + EVENT_NS, function(e) {
				if(e.keyCode === 27) {
					rllc.close();
				}
			});
		}

		_window.on('resize' + EVENT_NS, function() {
			rllc.updateSize();
		});


		if(!rllc.st.closeOnContentClick) {
			_wrapClasses += ' rllc-auto-cursor';
		}
		
		if(_wrapClasses)
			rllc.wrap.addClass(_wrapClasses);
			
		var windowHeight = rllc.wH = _window.height();
		var windowStyles = {};
		if( rllc.fixedContentPos ) {
            if(rllc._hasScrollBar(windowHeight)){
                var s = rllc._getScrollbarSize();
                if(s) {
                    windowStyles.marginRight = s;
                }
            }
        }

		if(rllc.fixedContentPos) {
			if(!rllc.isIE7) {
				windowStyles.overflow = 'hidden';
			} else {
				jQuery('body, html').css('overflow', 'hidden');
			}
		}

		
		var classesToadd = rllc.st.mainClass;
		if(rllc.isIE7) {
			classesToadd += ' rllc-ie7';
		}
		if(classesToadd) {
			rllc._addClassToMFP( classesToadd );
		}

		rllc.updateItemHTML();
		_rllcTrigger('BuildControls');
		jQuery('html').css(windowStyles);
		
		rllc.bgOverlay.add(rllc.wrap).prependTo( rllc.st.prependTo || _body );
		rllc._lastFocusedEl = document.activeElement;
		
		setTimeout(function() {
			
			if(rllc.content) {
				rllc._addClassToMFP(READY_CLASS);
				rllc._setFocus();
			} else {
				rllc.bgOverlay.addClass(READY_CLASS);
			}
			
			_document.on('focusin' + EVENT_NS, rllc._onFocusIn);

		}, 16);

		rllc.isOpen = true;
		rllc.updateSize(windowHeight);
		_rllcTrigger(OPEN_EVENT);

		return data;
	},

	close: function() {
		if(!rllc.isOpen) return;
		_rllcTrigger(BEFORE_CLOSE_EVENT);

		rllc.isOpen = false;
		if(rllc.st.removalDelay && !rllc.isLowIE && rllc.supportsTransition )  {
			rllc._addClassToMFP(REMOVING_CLASS);
			setTimeout(function() {
				rllc._close();
			}, rllc.st.removalDelay);
		} else {
			rllc._close();
		}
	},

	_close: function() {
		_rllcTrigger(CLOSE_EVENT);
		var classesToRemove = REMOVING_CLASS + ' ' + READY_CLASS + ' ';
		rllc.bgOverlay.detach();
		rllc.wrap.detach();
		rllc.container.empty();
		if(rllc.st.mainClass) {
			classesToRemove += rllc.st.mainClass + ' ';
		}

		rllc._removeClassFromMFP(classesToRemove);
		if(rllc.fixedContentPos) {
			var windowStyles = {marginRight: ''};
			if(rllc.isIE7) {
				jQuery('body, html').css('overflow', '');
			} else {
				windowStyles.overflow = '';
			}
			jQuery('html').css(windowStyles);
		}
		
		_document.off('keyup' + EVENT_NS + ' focusin' + EVENT_NS);
		rllc.ev.off(EVENT_NS);
		rllc.wrap.attr('class', 'rllc-wrap').removeAttr('style');
		rllc.bgOverlay.attr('class', 'rllc-bg');
		rllc.container.attr('class', 'rllc-container');
		if(rllc.st.showCloseBtn &&
		(!rllc.st.closeBtnInside || rllc.currTemplate[rllc.currItem.type] === true)) {
			if(rllc.currTemplate.closeBtn)
				rllc.currTemplate.closeBtn.detach();
		}

		if(rllc._lastFocusedEl) {
			jQuery(rllc._lastFocusedEl).focus(); 
		}
		rllc.currItem = null;	
		rllc.content = null;
		rllc.currTemplate = null;
		rllc.prevHeight = 0;
		_rllcTrigger(AFTER_CLOSE_EVENT);
	},
	
	updateSize: function(winHeight) {
		if(rllc.isIOS) {
			var zoomLevel = document.documentElement.clientWidth / window.innerWidth;
			var height = window.innerHeight * zoomLevel;
			rllc.wrap.css('height', height);
			rllc.wH = height;
		} else {
			rllc.wH = winHeight || _window.height();
		}

		if(!rllc.fixedContentPos) {
			rllc.wrap.css('height', rllc.wH);
		}
		_rllcTrigger('Resize');
	},

	updateItemHTML: function() {
		var item = rllc.items[rllc.index];
		rllc.contentContainer.detach();
		if(rllc.content)
			rllc.content.detach();

		if(!item.parsed) {
			item = rllc.parseEl( rllc.index );
		}

		var type = item.type;	
		_rllcTrigger('BeforeChange', [rllc.currItem ? rllc.currItem.type : '', type]);
		rllc.currItem = item;

		if(!rllc.currTemplate[type]) {
			var markup = rllc.st[type] ? rllc.st[type].markup : false;
			_rllcTrigger('FirstMarkupParse', markup);
			if(markup) {
				rllc.currTemplate[type] = jQuery(markup);
			} else {
				rllc.currTemplate[type] = true;
			}
		}

		if(_prevContentType && _prevContentType !== item.type) {
			rllc.container.removeClass('rllc-'+_prevContentType+'-holder');
		}
		
		var newContent = rllc['get' + type.charAt(0).toUpperCase() + type.slice(1)](item, rllc.currTemplate[type]);
		rllc.appendContent(newContent, type);

		item.preloaded = true;

		_rllcTrigger(CHANGE_EVENT, item);
		_prevContentType = item.type;
		
		rllc.container.prepend(rllc.contentContainer);

		_rllcTrigger('AfterChange');
	},

	appendContent: function(newContent, type) {
		rllc.content = newContent;
		if(newContent) {
			if(rllc.st.showCloseBtn && rllc.st.closeBtnInside &&
				rllc.currTemplate[type] === true) {
				if(!rllc.content.find('.rllc-close').length) {
					rllc.content.append(_getCloseBtn());
				}
			} else {
				rllc.content = newContent;
			}
		} else {
			rllc.content = '';
		}

		_rllcTrigger(BEFORE_APPEND_EVENT);
		rllc.container.addClass('rllc-'+type+'-holder');
		rllc.contentContainer.append(rllc.content);
	},

	parseEl: function(index) {
		var item = rllc.items[index],
			type;

		if(item.tagName) {
			item = { el: jQuery(item) };
		} else {
			type = item.type;
			item = { data: item, src: item.src };
		}

		if(item.el) {
			var types = rllc.types;
			for(var i = 0; i < types.length; i++) {
				if( item.el.hasClass('rllc-'+types[i]) ) {
					type = types[i];
					break;
				}
			}

			item.src = item.el.attr('data-rllc-src');
			if(!item.src) {
				item.src = item.el.attr('href');
			}
		}

		item.type = type || rllc.st.type || 'inline';
		item.index = index;
		item.parsed = true;
		rllc.items[index] = item;
		_rllcTrigger('ElementParse', item);
		return rllc.items[index];
	},

	addGroup: function(el, options) {
		var eHandler = function(e) {
			e.rllcEl = this;
			rllc._openClick(e, el, options);
		};

		if(!options) {
			options = {};
		} 

		var eName = 'click.customPopup';
		options.mainEl = el;
		
		if(options.items) {
			options.isObj = true;
			el.off(eName).on(eName, eHandler);
		} else {
			options.isObj = false;
			if(options.delegate) {
				el.off(eName).on(eName, options.delegate , eHandler);
			} else {
				options.items = el;
				el.off(eName).on(eName, eHandler);
			}
		}
	},
	_openClick: function(e, el, options) {
		var midClick = options.midClick !== undefined ? options.midClick : jQuery.customPopup.defaults.midClick;

		if(!midClick && ( e.which === 2 || e.ctrlKey || e.metaKey ) ) {
			return;
		}

		var disableOn = options.disableOn !== undefined ? options.disableOn : jQuery.customPopup.defaults.disableOn;
		if(disableOn) {
			if(jQuery.isFunction(disableOn)) {
				if( !disableOn.call(rllc) ) {
					return true;
				}
			} else { 
				if( _window.width() < disableOn ) {
					return true;
				}
			}
		}
		
		if(e.type) {
			e.preventDefault();
			if(rllc.isOpen) {
				e.stopPropagation();
			}
		}
			
		options.el = jQuery(e.rllcEl);
		if(options.delegate) {
			options.items = el.find(options.delegate);
		}
		rllc.open(options);
	},

	updateStatus: function(status, text) {
		if(rllc.preloader) {
			if(_prevStatus !== status) {
				rllc.container.removeClass('rllc-s-'+_prevStatus);
			}
			if(!text && status === 'loading') {
				text = rllc.st.tLoading;
			}
			var data = {
				status: status,
				text: text
			};
			_rllcTrigger('UpdateStatus', data);

			status = data.status;
			text = data.text;
			rllc.preloader.html(text);
			rllc.preloader.find('a').on('click', function(e) {
				e.stopImmediatePropagation();
			});
			rllc.container.addClass('rllc-s-'+status);
			_prevStatus = status;
		}
	},

	_checkIfClose: function(target) {
		if(jQuery(target).hasClass(PREVENT_CLOSE_CLASS)) {
			return;
		}
		var closeOnContent = rllc.st.closeOnContentClick;
		var closeOnBg = rllc.st.closeOnBgClick;

		if(closeOnContent && closeOnBg) {
			return true;
		} else {
			if(!rllc.content || jQuery(target).hasClass('rllc-close') || (rllc.preloader && target === rllc.preloader[0]) ) {
				return true;
			}

			if(  (target !== rllc.content[0] && !jQuery.contains(rllc.content[0], target))  ) {
				if(closeOnBg) {
					if( jQuery.contains(document, target) ) {
						return true;
					}
				}
			} else if(closeOnContent) {
				return true;
			}

		}
		return false;
	},
	_addClassToMFP: function(cName) {
		rllc.bgOverlay.addClass(cName);
		rllc.wrap.addClass(cName);
	},
	_removeClassFromMFP: function(cName) {
		this.bgOverlay.removeClass(cName);
		rllc.wrap.removeClass(cName);
	},
	_hasScrollBar: function(winHeight) {
		return (  (rllc.isIE7 ? _document.height() : document.body.scrollHeight) > (winHeight || _window.height()) );
	},
	_setFocus: function() {
		(rllc.st.focus ? rllc.content.find(rllc.st.focus).eq(0) : rllc.wrap).focus();
	},
	_onFocusIn: function(e) {
		if( e.target !== rllc.wrap[0] && !jQuery.contains(rllc.wrap[0], e.target) ) {
			rllc._setFocus();
			return false;
		}
	},
	_parseMarkup: function(template, values, item) {
		var arr;
		if(item.data) {
			values = jQuery.extend(item.data, values);
		}
		_rllcTrigger(MARKUP_PARSE_EVENT, [template, values, item] );

		jQuery.each(values, function(key, value) {
			if(value === undefined || value === false) {
				return true;
			}
			arr = key.split('_');
			if(arr.length > 1) {
				var el = template.find(EVENT_NS + '-'+arr[0]);

				if(el.length > 0) {
					var attr = arr[1];
					if(attr === 'replaceWith') {
						if(el[0] !== value[0]) {
							el.replaceWith(value);
						}
					} else if(attr === 'img') {
						if(el.is('img')) {
							el.attr('src', value);
						} else {
							el.replaceWith( '<img src="'+value+'" class="' + el.attr('class') + '" />' );
						}
					} else {
						el.attr(arr[1], value);
					}
				}

			} else {
				template.find(EVENT_NS + '-'+key).html(value);
			}
		});
	},

	_getScrollbarSize: function() {
		if(rllc.scrollbarSize === undefined) {
			var scrollDiv = document.createElement("div");
			scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
			document.body.appendChild(scrollDiv);
			rllc.scrollbarSize = scrollDiv.offsetWidth - scrollDiv.clientWidth;
			document.body.removeChild(scrollDiv);
		}
		return rllc.scrollbarSize;
	}
}; 


jQuery.customPopup = {
	instance: null,
	proto: CustomJs.prototype,
	modules: [],

	open: function(options, index) {
		_checkInstance();	

		if(!options) {
			options = {};
		} else {
			options = jQuery.extend(true, {}, options);
		}
			

		options.isObj = true;
		options.index = index || 0;
		return this.instance.open(options);
	},

	close: function() {
		return jQuery.customPopup.instance && jQuery.customPopup.instance.close();
	},

	registerModule: function(name, module) {
		if(module.options) {
			jQuery.customPopup.defaults[name] = module.options;
		}
		jQuery.extend(this.proto, module.proto);			
		this.modules.push(name);
	},

	defaults: {   
		
		disableOn: 0,	
		key: null,
		midClick: false,
		mainClass: '',
		preloader: true,
		focus: '', 
		closeOnContentClick: false,
		closeOnBgClick: true,
		closeBtnInside: true, 
		showCloseBtn: true,
		enableEscapeKey: true,
		modal: false,
		alignTop: false,
		removalDelay: 0,
		prependTo: null,
		fixedContentPos: 'auto', 
		fixedBgPos: 'auto',
		overflowY: 'auto',
		closeMarkup: '<button title="%title%" type="button" class="rllc-close">&times;</button>',
		tClose: 'Close (Esc)',
		tLoading: 'Loading...'
	}
};



jQuery.fn.customPopup = function(options) {
	_checkInstance();

	var jqEl = jQuery(this);
	if (typeof options === "string" ) {
		if(options === 'open') {
			var items,
				itemOpts = _isJQ ? jqEl.data('customPopup') : jqEl[0].customPopup,
				index = parseInt(arguments[1], 10) || 0;
			if(itemOpts.items) {
				items = itemOpts.items[index];
			} else {
				items = jqEl;
				if(itemOpts.delegate) {
					items = items.find(itemOpts.delegate);
				}
				items = items.eq( index );
			}
			rllc._openClick({rllcEl:items}, jqEl, itemOpts);
		} else {
			if(rllc.isOpen)
				rllc[options].apply(rllc, Array.prototype.slice.call(arguments, 1));
		}

	} else {
		options = jQuery.extend(true, {}, options);
		if(_isJQ) {
			jqEl.data('customPopup', options);
		} else {
			jqEl[0].customPopup = options;
		}
		rllc.addGroup(jqEl, options);
	}
	return jqEl;
};


 _checkInstance(); })(window.jQuery || window.Zepto);