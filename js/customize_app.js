/*-----------------------------------------------------------------------------------

 Theme Name: The GoodWin eCommerce Template
 Author: BigSteps
 Author URI: http://themeforest.net/user/bigsteps
 Version: 1.2

 -----------------------------------------------------------------------------------*/

"use strict";

(function ($) {

	function calcScrollWidth() {
		var _ = $('<div style="width:100px;height:100px;overflow:scroll;visibility: hidden;"><div style="height:200px;"></div>');
		$('body').append(_);
		var w = (_[0].offsetWidth - _[0].clientWidth);
		$(_).remove();
		return (w);
	}

	function debouncer(func, wait, immediate) {
		var timeout;
		return function () {
			var context = this,
				args = arguments;
			var later = function () {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};

	function extendDefaults(source, properties) {
		var property;
		for (property in properties) {
			if (properties.hasOwnProperty(property)) {
				source[property] = properties[property];
			}
		}
		return source;
	}

	var GOODWIN = GOODWIN || {};

	GOODWIN.initialization = {
		init: function () {
			this.removePreloader(1000);
			this.checkDevice();
			this.hideBeforeLoad('.mobilemenu-content', 1000);
			this.productTab('.product-tab');
			this.accordionSetActive();
			this.sliderTextTopShift();
			this.imageLoadedProductPage('.prd-block .prd-has-loader');
			this.imageLoaded('.prd.prd-has-loader, .prd-hor.prd-has-loader, .has-loader');
          
		},
		removePreloader: function (delay) {
			setTimeout(function () {
				$body.addClass('no-loader').removeClass('document-ready');
			}, delay)
			setTimeout(function () {
				$('.body-loader').remove()
			}, (delay + 1000))
		},
		checkDevice: function () {
			var isTouchDevice = 'ontouchstart' in window || navigator.msMaxTouchPoints;
			if (navigator.userAgent.indexOf('Windows') > 0) {
				$body.addClass('win');
				isTouchDevice = false;
			}
			if (isTouchDevice) {
				$('body').addClass('touch');
				swipemode = true;
			}
			if (navigator.userAgent.indexOf('Mac') > 0) {
				$('body').addClass('mac');
			}
			if (navigator.userAgent.match(/Android/)) {
				$('body').addClass('android');
			}
		},
		
		scrollOnLoad: function () {
			var $elem = $($(location).attr('href').split('#')[1]);
			if ($elem.length) {
				setTimeout(function () {
					var speed = $('body').height() / 3 > 500 ? $('body').height() / 3 : 500;
					var wHeight = $(window).height() < $elem.height() * 2 ? 0 : $(window).height() - $elem.height() * 2,
						offsetTop = $elem.offset().top - wHeight;
					$('html,body').animate({
						scrollTop: offsetTop
					}, speed);
				}, 500)
			}
		},
		
		imageLoadedProductPage: function (image) {
			$(image).each(function () {
				var $this = $(this);
				if ($this.closest('.prd')) $this.find('img').css({
					opacity: 0
				});
				$this.imagesLoaded(function () {
					$this.addClass('loaded');
					$this.find('img').animate({
						opacity: 1
					}, 200);
				});
			})
		},
		imageLoaded: function (image, carousel) {
			var $imageL = $(image);
			if (carousel) {
				$imageL = image;
			}
			$imageL.each(function () {
				var $this = $(this);
				if ($('.prd-img-area', $this).length) {
					$('.prd-img-area', $this).imagesLoaded(function () {
						$this.addClass('loaded');
					});
				} else {
					$this.imagesLoaded(function () {
						$this.addClass('loaded');
					});
				}
			})
		},
		productWidth: function (product) {
			$(product).each(function () {
				var $this = $(this);
				$this.removeClass('prd-w-md prd-w-sm prd-w-xs');
				var w = $this.find('.prd-img-area').width(),
					wClass = '';
				if (w >= 220 && w < 250) {
					wClass = 'prd-w-md';
				} else if (w >= 190 && w < 220) {
					wClass = 'prd-w-sm';
				} else if (w <= 190) {
					wClass = 'prd-w-xs';
				}
				$this.addClass(wClass);
			})
		},
		
		removeEmptyLinked: function (absent, linked) {
			if (!$(absent).length) $(linked).remove;
		},
	
		backToTop: function (button) {
			var $button = $(button),
				windowH = $(window).height();
			if ($(window).scrollTop() > windowH / 2) {
				$button.addClass('is-visible');
			}
			$(window).scroll(function () {
				if ($(this).scrollTop() > windowH / 2) {
					$button.addClass('is-visible');
				} else {
					$button.removeClass('is-visible');
				}
			});

			function scrollToTop() {
				$body.addClass('blockSticky');
				var speed = $(window).scrollTop() / 4 > 500 ? $(window).scrollTop() / 4 : 500;
				if (isMobile) {
					speed = speed * 2;
				}
				$("html, body").animate({
					scrollTop: 0
				}, speed, function () {
					$body.removeClass('blockSticky');
				});
				GOODWIN.stickyheader.destroySticky();
			}

			$button.on('click', function (e) {
				scrollToTop();
				e.preventDefault();
			});
			$('.logo-holder-s').on('click', function (e) {
				if (isMobile) {
					scrollToTop();
					e.preventDefault();
				}
			});
		},

		sliderTextTopShift: function () {
			var SliderTextTopShift = {
				default: {
					header: '.hdr',
					text: '.bnslider-text-content-flex'
				},
				init: function (options) {
					$.extend(this.default, options);
					if (!isMobile && !$('.aside').length) {
						if ($(this.default.header).hasClass('hdr--transparent')) {
							$(this.default.header).addClass('visible');
							$(this.default.text).css({
								'padding-top': $(this.default.header).outerHeight() * .85
							})
						}
					} else {
						$(this.default.text).css({
							'padding-top': ''
						})
					}
					return this;
				},
				reinit: function () {
					this.init();
					return this;
				}
			}
			GOODWIN.slidertexttopshift = Object.create(SliderTextTopShift).init({
				header: '.hdr',
				text: '.bnslider-text-content-flex'
			});
		},
		sideFixed: function () {
			var SideFixed = {
				default: {
					el: '.side-fixed'
				},
				init: function (options) {
					$.extend(this.default, options);
					this.reinit();
				},
				reinit: function () {
					var $this = $(this.default.el);
					$this.css({
						'height': '100vh'
					});
					setTimeout(function () {
						$this.css({
							'height': $('body').height()
						});
					}, 500);
				}
			}
			GOODWIN.sidefixed = Object.create(SideFixed);
			GOODWIN.sidefixed.init();
		},
		
		hideBeforeLoad: function (el, timeOut) {
			$(el).css('visibility', 'hidden');
			setTimeout(function () {
				$(el).css('visibility', '').addClass('loaded');
			}, timeOut);
		},
		
		productTab: function (tab) {
			var $tabs = $(tab),
				setCurrent = false;
			$tabs.tabCollapse({
				accordion: false,
				tabsClass: 'd-none d-lg-flex',
				accordionClass: 'd-lg-none'
			});
			$('a', $tabs).each(function () {
				var $this = $(this);
				if ($this.parent('li').is('.active')) {
					var curTab = $this.attr("href");
					$(curTab).addClass('active');
					setCurrent = true;
				}
			});
			if (!setCurrent) {
				$('li:first-child a', $tabs).tab('show');
			}
			$document.on('click', '.js-tabcollapse-panel-heading', function () {
				var $panel = $(this).closest('.panel');
				if (!$panel.find('.panel-heading.active').length) return false;
				$('html,body').animate({
					scrollTop: $panel.offset().top - 60
				}, 0);
			})
		},
		accordionSetActive: function () {
			$('body').on('show.bs.collapse', '.panel-collapse', function (e) {
				$(e.currentTarget).siblings('.panel-heading').addClass('active');
			}).on('hide.bs.collapse', '.panel-collapse', function (e) {
				$(e.currentTarget).siblings('.panel-heading').removeClass('active');
			});
		},
		
		flowtype: function () {
			var FlowType = {
				default: {
					maximum: 9999,
					minimum: 1,
					maxFont: 9999,
					minFont: 1
				},
				init: function (bnr) {
					var that = this;
					$(bnr).each(function () {
						var $this = $(this);
						$this.imagesLoaded(function () {
							var fontratio = Math.round($this.attr("data-fontratio") * 100) / 100;
							if (fontratio > 0) {
								that._changes($this, fontratio)
							}
						});
					});
				},
				hide: function (bnr) {
					var that = this;
					$(bnr).each(function () {
						$(this).removeClass('fontratio-calc');
					});
				},
				reinit: function (bnr) {
					var that = this;
					$(bnr).each(function () {
						var $this = $(this),
							fontratio = Math.round($this.attr("data-fontratio") * 100) / 100;
						$this.removeClass('fontratio-calc');
						if (fontratio > 0) {
							that._changes($this, fontratio)
						}
					});
				},
				_changes: function (el, fontRatio) {
					var $el = $(el),
						elw = $el.width(),
						width = elw > this.default.maximum ? this.default.maximum : elw < this.default.minimum ? this.default.minimum : elw,
						fontBase = width / fontRatio,
						fontSize = fontBase > this.default.maxFont ? this.default.maxFont : fontBase < this.default.minFont ? this.default.minFont : fontBase;
					$el.css('font-size', fontSize + 'px').addClass('fontratio-calc');
				}
			}
			GOODWIN.flowtype = Object.create(FlowType);
			GOODWIN.flowtype.init('.bnr[data-fontratio]');
		},
		
		dependHeight: function (standart, depend, styled) {
			if ($(depend).length && $(standart).length) {
				var css = $(depend).attr("style"),
					style = styled + ':' + $(standart).outerHeight(true) + 'px';
				if (css === undefined) css = style;
				else css += style;
				$(depend).attr('style', css);
			}
		}
	
		
	};
        
        
	GOODWIN.header = {
		init: function () {
			//this.mobileMenu('.mobilemenu');
			this.headerDrop();
			this.scrollMenuInit({
				menu: '.hdr-onerow-menu .mmenu-js',
				arrowPrev: '.prev-menu-js',
				arrowNext: '.next-menu-js',
				bodyFlagClass: 'has-scrollmenu',
				scrollStep: 10, // scroll menu step in px
				scrollSpeed: 4 // scroll menu speed in msec
			});
			this.miniCartInit({
				headerCart: '.minicart-js',
				toggleBtn: '.minicart-link',
				closeBtn: '.minicart-drop-close',
				dropdn: '.minicart-drop',
				header: '.hdr',
				sticky: '.sticky-holder',
				stickyFlag: 'has-sticky'
			});
			this.megaMenu();
			this.mmobilePush();
			this.searchAutoFill('.js-search-autofill', 'a', '.search-input');
			//this.collapseCategory('.hdr-category', '.hdr .mmenu--vertical.mmenu-js');
		},
		promoTopline: function (topline, close) {
			var $topline = $(topline),
				$close = $(close),
				speed = 300,
				timeout = 1000;
			if ($('.hdr-mobile-style2').length && isMobile) {
				speed = 100;
				timeout = 0;
			}

//			function checkCookie() {
//				if ($.cookie('goodwinPromoTopLine') != 'yes') {
//					setTimeout(function () {
//						$topline.slideDown(speed, function () {
//							promoToplineHeight = $topline.outerHeight();
//							if (GOODWIN.stickyheader) GOODWIN.stickyheader.setHeaderHeight();
//						});
//					}, timeout);
//				} else {
//					$topline.slideUp(0);
//					promoToplineHeight = 0;
//					if (GOODWIN.stickyheader) GOODWIN.stickyheader.setHeaderHeight();
//				}
//			}

			$close.on('click', function () {
				if ($('body').hasClass('demo')) {
					$topline.slideUp(speed);
					promoToplineHeight = 0;
					if (GOODWIN.stickyheader) GOODWIN.stickyheader.setHeaderHeight();
				} else {
					$.cookie('goodwinPromoTopLine', 'yes', {
						expires: parseInt($topline.attr('data-expires'), 10)
					});
					checkCookie();
				}
			});
//			checkCookie();
		},
		collapseCategory: function (btn, menu) {
			var $menu = $(menu),
				$button = $(btn);
			$button.on('mouseenter', function (e) {
				$button.addClass('opened');
				$menu.stop(true, false).slideDown();
				GOODWIN.flowtype.reinit(menu + '.bnr[data-fontratio]');
				e.preventDefault();
			}).on('mouseleave', function (e) {
				$button.removeClass('opened');
				$menu.stop(true, false).slideUp();
				e.preventDefault();
			})
		},
		searchAutoFill: function (parent, link, target) {
			$(parent).find(link).on('click', function (e) {
				if ($(target).val() == $(this).html()) {
					return false;
				}
				$(target).val($(this).html()).focus().trigger('keyup');
				e.preventDefault();
			})
		},
		mobileMenu: function () {
			var MobileMenu = {
				MobileMenuData: {
					mobilemenu: '.mobilemenu',
					toggleMenu: '.mobilemenu-toggle',
					mobileCaret: '.js-accordion-mbmenu ul.nav li .arrow',
					mobileLink: '.js-accordion-mbmenu ul.nav li > a',
					search: '.dropdn_search .dropdn-content',
					currency: '.dropdn_currency',
					lang: '.dropdn_language',
					settings: '.dropdn_settings_',
					searchMobile: '.mobilemenu-search',
					currencyMobile: '.mobilemenu-currency',
					langMobile: '.mobilemenu-language',
					settingsMobile: '.mobilemenu-settings_',
					headerM: '.hdr-mobile',
					headerD: '.hdr-desktop',
					logo: '.hdr-desktop .logo-holder',
					cart: '.hdr-desktop .minicart-holder',
					links: '.hdr-desktop .links-holder',
					logoMobile: '.hdr-mobile .logo-holder',
					cartMobile: '.hdr-mobile .minicart-holder',
					linksMobile: '.hdr-mobile .links-holder'
				},
				init: function (options) {
					$.extend(this.MobileMenuData, options);
					var obj = {
						$mobilemenu: $(this.MobileMenuData.mobilemenu),
						$toggleMenu: $(this.MobileMenuData.toggleMenu),
						$mobileCaret: $(this.MobileMenuData.mobileCaret),
						$mobileLink: $(this.MobileMenuData.mobileLink),
						$search: $(this.MobileMenuData.search),
						$lang: $(this.MobileMenuData.lang),
						$currency: $(this.MobileMenuData.currency),
						$settings: $(this.MobileMenuData.settings),
						$searchMobile: $(this.MobileMenuData.searchMobile),
						$langMobile: $(this.MobileMenuData.langMobile),
						$currencyMobile: $(this.MobileMenuData.currencyMobile),
						$settingsMobile: $(this.MobileMenuData.settingsMobile),
						$mobileCaret: $(this.MobileMenuData.mobileCaret),
						$mobileLink: $(this.MobileMenuData.mobileLink),
						$headerM: $(this.MobileMenuData.headerM),
						$headerD: $(this.MobileMenuData.headerD),
						$logo: $(this.MobileMenuData.logo),
						$cart: $(this.MobileMenuData.cart),
						$links: $(this.MobileMenuData.links),
						$logoMobile: $(this.MobileMenuData.logoMobile),
						$cartMobile: $(this.MobileMenuData.cartMobile),
						$linksMobile: $(this.MobileMenuData.linksMobile)
					}
					$.extend(this.MobileMenuData, obj);
					if ($(this.MobileMenuData.menu).length) {
						this._handlers(this);
					}
					if (isMobile) {
						this._mobileEvent();
						this._attachMenu();
					} else if ($('.hdr').hasClass('slide-menu')) {
						this._mobileEvent();
						this._attachMenuD();
					}
				},
				reinit: function () {
					this.MobileMenuData.$mobileLink.off('click.mobileMenu');
					this.MobileMenuData.$mobileCaret.off('click.mobileMenu');
					if (!isMobile) {
						if (!$('.hdr').hasClass('slide-menu')) {
							$('body').removeClass('is-fixed');
							this.MobileMenuData.$mobilemenu.removeClass('active');
							this.MobileMenuData.$toggleMenu.removeClass('active');
							this._detachMenu();
						} else {
							this._mobileEvent();
							this._detachMenu();
							this._attachMenuD();
						}
					} else if ($('.hdr').hasClass('slide-menu')) {
						this._mobileEvent();
						this._detachMenuD();
						this._attachMenu();
					} else {
						this._mobileEvent();
						this._attachMenu();
					}
				},
				_handlers: function () {
					var _ = this.MobileMenuData;
					_.$toggleMenu.on('click.mobileMenu', function () {
						_.$mobilemenu.toggleClass('active');
						_.$toggleMenu.toggleClass('active');
						$('body').toggleClass('slidemenu-open');
						if (isMobile) {
							if ($('body').hasClass('is-fixed')) {
								setTimeout(function () {
									$('body').removeClass('is-fixed');
									$('.mobilemenu-scroll').scrollLock('disable');
								}, 500);
							} else {
								$('body').addClass('is-fixed');
								$('.mobilemenu-scroll').scrollLock('enable');
							}
						}
						return false;
					});
					_.$mobilemenu.on('click.mobileMenu', function (e) {
						if ($(e.target).is(_.$mobilemenu)) {
							_.$mobilemenu.toggleClass('active');
							_.$toggleMenu.toggleClass('active');
							$('body').toggleClass('slidemenu-open');
							if (isMobile) {
								if ($('body').hasClass('is-fixed')) {
									setTimeout(function () {
										$('body').removeClass('is-fixed');
										$('.mobilemenu-scroll').scrollLock('disable');
									}, 500);
								} else {
									$('body').addClass('is-fixed');
									$('.mobilemenu-scroll').scrollLock('enable');
								}
							}
							e.preventDefault();
						}
					});
				},
				_attachMenuD: function () {
					var _ = this.MobileMenuData;
					if (_.$search.length) {
						_.$search.find('.container').detach().appendTo(_.$searchMobile);
					}
					if (_.$lang.length) {
						_.$lang.find('.dropdn').detach().appendTo(_.$langMobile);
					}
					if (_.$currency.length) {
						_.$currency.find('.dropdn').detach().appendTo(_.$currencyMobile);
					}
					if (_.$settings.length) {
						_.$settings.find('.dropdn').detach().appendTo(_.$settingsMobile);
					}
				},
				_attachMenu: function () {
					var _ = this.MobileMenuData;
					if (_.$search.length) {
						_.$search.find('.container').detach().appendTo(_.$searchMobile);
					}
					if (_.$currency.length) {
						_.$currency.find('.dropdn').detach().appendTo(_.$currencyMobile);
					}
					if (_.$lang.length) {
						_.$lang.find('.dropdn').detach().appendTo(_.$langMobile);
					}
					if (_.$settings.length) {
						_.$settings.find('.dropdn').detach().appendTo(_.$settingsMobile);
					}
					if (_.$cart.length) {
						_.$cart.children().detach().appendTo(_.$cartMobile);
					}
					if (_.$links.length) {
						if (!$.trim(_.$linksMobile.html())) {
							if (_.$links.length > 1) {
								_.$linksMobile.html('');
								_.$links.each(function (i) {
									_.$linksMobile.append('<div class="links-mobile-holder-' + i + '"></div>');
									$(this).addClass('links-holder-' + i);
									$(this).children().detach().appendTo(".links-mobile-holder-" + i, _.$linksMobile);
								})
							} else _.$links.children().detach().appendTo(_.$linksMobile);
						}
					}
				},
				_detachMenuD: function () {
					var _ = this.MobileMenuData;
					if (_.$searchMobile.length) {
						_.$searchMobile.find('.container').detach().appendTo(_.$search);
					}
					if (_.$currencyMobile.length) {
						_.$currencyMobile.find('.dropdn').detach().appendTo(_.$currency);
					}
					if (_.$langMobile.length) {
						_.$langMobile.find('.dropdn').detach().appendTo(_.$lang);
					}
					if (_.$settingsMobile.length) {
						_.$settingsMobile.find('.dropdn').detach().appendTo(_.$settings);
					}
				},
				_detachMenu: function () {
					var _ = this.MobileMenuData;
					if (_.$searchMobile.length) {
						_.$searchMobile.find('.container').detach().appendTo(_.$search);
					}
					if (_.$currencyMobile.length) {
						_.$currencyMobile.find('.dropdn').detach().appendTo(_.$currency);
					}
					if (_.$langMobile.length) {
						_.$langMobile.find('.dropdn').detach().appendTo(_.$lang);
					}
					if (_.$settingsMobile.length) {
						_.$settingsMobile.find('.dropdn').detach().appendTo(_.$settings);
					}
					if (_.$cartMobile.length) {
						_.$cartMobile.children().detach().appendTo(_.$cart);
					}
					if (_.$linksMobile.length) {
						if (_.$links.length > 1) {
							_.$links.each(function (i) {
								$(".links-mobile-holder-" + i, _.$linksMobile).children().detach().appendTo(".links-holder-" + i);
							})
							_.$linksMobile.html('');
						} else _.$linksMobile.children().detach().appendTo(_.$links);
					}
				},
				_mobileEvent: function () {
					var _ = this.MobileMenuData;
					_.$mobileCaret.on('click.mobileMenu', function (e) {
						e.preventDefault();
						var $parent = $(this).parent();
						if ($parent.hasClass('mmenu-submenu-open')) {
							$('li.mmenu-submenu-open ul', $parent).slideUp(200);
							$('li', $parent).removeClass('mmenu-submenu-open');
							$parent.removeClass('mmenu-submenu-open');
							$('> ul', $parent).slideUp(200);
							$parent.removeData('firstclick');
						} else {
							$parent.addClass('mmenu-submenu-open');
							$(' > ul', $parent).slideDown(200);
							$parent.data('firstclick', true);
						}
					});
					if (_.$mobilemenu.hasClass('dblclick')) {
						_.$mobileLink.on('click.mobileMenu', function (e) {
							e.preventDefault();
							var $parent = $(this).parent();
							if (!$parent.data('firstclick') && $parent.find('ul').length) {
								$parent.addClass('mmenu-submenu-open');
								$(' > ul', $parent).slideDown(200);
								$parent.data('firstclick', true);
							} else {
								var href = $(this).attr("href"),
									target = $(this).attr("target") ? $(this).attr("target") : '_self';
								window.open(href, target);
								$parent.removeData('firstclick');
							}
						});
					}
				}
			}
			GOODWIN.mobilemenu = Object.create(MobileMenu);
			GOODWIN.mobilemenu.init({
				menu: '.mobilemenu'
			});
		},
		megaMenu: function () {
			var MegaMenu = {
				MegaMenuData: {
					header: '.hdr',
					menu: '.mmenu-js',
					submenu: '.mmenu-submenu',
					toggleMenu: '.toggleMenu',
					simpleDropdn: '.mmenu-item--simple',
					megaDropdn: '.mmenu-item--mega',
					headerCart: '.minicart-js',
					headerCartToggleBtn: '.minicart-link',
					headerCartDropdn: '.minicart-drop',
					dropdn: '.dropdn',
					vertical: false,
					titleHeight: 50
				},
				init: function (options) {
					$.extend(this.MegaMenuData, options);
					if ($(this.MegaMenuData.menu).length) {
						MegaMenu._handlers(this);
					}
				},
				_handlers: function (menu) {
					function setMaxHeight(wHeight, submenu) {
						if ($menu.hasClass('mmenu--vertical')) return false;
						if (submenu.length) {
							var maxH = $('body').hasClass('has-sticky') ? (wHeight - $header.find('.sticky-holder').outerHeight()) : (wHeight - submenu.prev().offset().top - submenu.prev().outerHeight());
							submenu.children(':first').css({
								'max-height': maxH + 'px'
							})
						}
					}

					function clearMaxHeight() {
						$submenu.each(function () {
							var $this = $(this);
							$this.css({
								'max-height': ''
							});
						})
					}

					var $menu = $(menu.MegaMenuData.menu),
						submenu = menu.MegaMenuData.submenu,
						$submenu = $(menu.MegaMenuData.submenu, $menu),
						$header = $(menu.MegaMenuData.header),
						$toggleMenu = $(menu.MegaMenuData.toggleMenu),
						megaDropdnClass = menu.MegaMenuData.megaDropdn,
						simpleDropdnClass = menu.MegaMenuData.simpleDropdn,
						vertical = menu.MegaMenuData.vertical,
						$headerCart = $(menu.MegaMenuData.headerCart),
						$headerCartToggleBtn = $headerCart.find(menu.MegaMenuData.headerCartToggleBtn),
						$headerCartDropdn = $headerCart.find(menu.MegaMenuData.headerCartDropdn),
						$dropdn = $(menu.MegaMenuData.dropdn, $header);
					if (vertical && (window.innerWidth || $window.width()) < 1024) {
						$menu.on("click.mmenu", ".submenu a", function (e) {
							var $this = $(this);
							if (!$this.data('firstclick')) {
								$this.data('firstclick', true);
								e.preventDefault();
							}
						});
						$menu.on("click.mmenu", megaDropdnClass + '> a,' + simpleDropdnClass + '> a', function (e) {
							if (!$(this).parent('li').hasClass('hovered')) {
								setMaxHeight($window.height(), $(this).next());
								$submenu.scrollTop(0);
								$('li', $menu).removeClass('hovered');
								$(this).parent('li').addClass('hovered');
								e.preventDefault();
							} else {
								clearMaxHeight();
								$(this).parent('li').removeClass('hovered');
								$(submenu + 'a').removeData('firstclick');
							}
						});
						$menu.on("click.mmenu", function (e) {
							e.stopPropagation();
						})
					} else if ($('body').hasClass('touch') && $(window).width() < 1024) {
						$menu.on("click.mmenu", ".submenu a", function (e) {
							var $this = $(this);
							if (!$this.data('firstclick')) {
								$this.data('firstclick', true);
								e.preventDefault();
							}
						});
						$menu.on("click.mmenu", megaDropdnClass + '> a,' + simpleDropdnClass + '> a', function (e) {
							if (!$(this).parent('li').hasClass('hovered')) {
								setMaxHeight($window.height(), $(this).next());
								$submenu.scrollTop(0);
								$('li', $menu).removeClass('hovered');
								$(this).parent('li').addClass('hovered');
								e.preventDefault();
							} else {
								clearMaxHeight();
								$(this).parent('li').removeClass('hovered');
								$(submenu + 'a', $menu).removeData('firstclick');
							}
						});
						$menu.on("click.mmenu", function (e) {
							e.stopPropagation();
						})
					} else {
						$menu.on("mouseenter", megaDropdnClass + '> a,' + simpleDropdnClass + '> a', function () {
							var $this = $(this),
								$submenu = $this.next(submenu);
							setMaxHeight($(window).height(), $submenu);
							$submenu.scrollTop(0);
							$this.parent('li').addClass('hovered');
							if ($headerCartDropdn.hasClass('opened')) {
								$headerCartToggleBtn.trigger('click')
							}
							$dropdn.each(function () {
								var $this = $(this);
								if ($this.hasClass('is-hovered')) {
									$('>a', $this).trigger('click')
								}
							})
							if ($('body').hasClass('has-scrollmenu') && $this.closest(simpleDropdnClass).length) {
								$this.next().css({
									'margin-left': -$menu.parent().scrollLeft()
								})
							}
						}).on("mouseleave", megaDropdnClass + ',' + simpleDropdnClass, function () {
							clearMaxHeight();
							var $this = $(this);
							$this.removeClass('hovered');
						});
					}
					$toggleMenu.on('click', function (e) {
						var $this = this;
						$header.toggleClass('open');
						$this.toggleClass('open');
						$menu.addClass('disable').delay(1000).queue(function () {
							$this.removeClass('disable').dequeue();
						});
						e.preventDefault();
					});
					if (vertical) {
						$('li.mmenu-item--simple', $menu).on('mouseenter', function () {
							var $this = $(this),
								$elm = $('.mmenu-submenu', this).length ? $('.mmenu-submenu', this) : $('ul:first', this),
								windowH = $window.height(),
								isYvisible = (windowH + $window.scrollTop()) - ($elm.offset().top + $elm.outerHeight());
							if (isYvisible < 0 && !$this.hasClass('mmenu-item--mega')) {
								$elm.css({
									'margin-top': isYvisible + 'px'
								});
							}
						})
					}
					$('li', $submenu).on('mouseenter', function () {
						var $this = $(this).addClass('active');
						if ($('> a .mmenu-preview', $this).length) {
							var $ul = $this.closest('ul'),
								$img = $('.mmenu-preview', $this);
							$ul.css({
								'min-width': '',
								'overflow': ''
							});
							$ul.css({
								'min-width': 454,
								'overflow': 'hidden'
							});
							$ul.append($img.clone());
						}
						if ($('ul', $this).length) {
							var $elm = $('.mmenu-submenu', this).length ? $('.mmenu-submenu', this) : $('ul:first', this),
								windowW = window.innerWidth || $window.width(),
								windowH = $window.height(),
								isXvisible,
								isYvisible,
								menuItemPos = $this.position();
							if ($this.closest('.mmenu-item--mega').length) {
								if (!$('body').hasClass('rtl')) {
									$elm.css({
										top: menuItemPos.top,
										left: menuItemPos.left + Math.round($this.outerWidth())
									});
								} else {
									$elm.css({
										top: menuItemPos.top,
										left: menuItemPos.left - $elm.outerWidth()
									});
								}
							}
							if ($elm.hasClass('sub-level')) {
								$elm.closest('.mmenu-submenu').addClass('mmenu--not-hide')
								//.css({'padding-right': scrollWidth + 'px'});
							}
							isXvisible = $('body').hasClass('rtl') ? $elm.offset().left >= 0 : ($elm.offset().left + $elm.width()) <= windowW,
								isYvisible = (windowH + $window.scrollTop()) - ($elm.offset().top + $elm.outerHeight());
							if (!isXvisible) {
								$this.addClass('to-right');
							} else {
								$this.removeClass('to-right');
							}
							if (isYvisible < 0) {
								$elm.css({
									'margin-top': isYvisible + 'px'
								});
							}
						}
					}).on('mouseleave', function () {
						var $elm = $('.mmenu-submenu', this).length ? $('.mmenu-submenu', this) : $('ul:first', this);
						var $this = $(this).removeClass('to-right').removeClass('active');
						if ($('> a .mmenu-preview', $this).length) {
							var $ul = $this.closest('ul');
							$ul.css({
								'min-width': '',
								'overflow': ''
							});
							$ul.find('>.mmenu-preview').remove();
						}
						$elm.css({
							'margin-top': ''
						});
						if (!$this.closest('.sub-level').length) {
							$elm.closest('.mmenu-submenu').removeClass('mmenu--not-hide').css({
								'padding-right': ''
							});
						}
					})
				}
			};
			GOODWIN.megamenu = Object.create(MegaMenu);
			GOODWIN.megamenu.init({
				menu: '.mmenu-js'
			});
			GOODWIN.vmegamenu = Object.create(MegaMenu);
			GOODWIN.vmegamenu.init({
				menu: '.vmmenu-js',
				vertical: true
			});
		},
		mmobilePush: function () {
			var mMenuPush = function () {
				this.curItem,
					this.curLevel = 0;
				var defaults = {
					initElem: ".mobilemenu",
					menuTitle: "Menu"
				}
				if (arguments[0] && typeof arguments[0] === "object") {
					this.options = extendDefaults(defaults, arguments[0]);
				}

				function extendDefaults(source, extender) {
					for (var option in extender) {
						if (source.hasOwnProperty(option)) {
							source[option] = extender[option];
						}
					}
				}

				mMenuPush.prototype.setHeigth = function () {
					$('.nav-wrapper').css({
						"height": $('mmenu-submenu-active .nav-level-' + (this.curLevel + 1)).outerHeight()
					});
				};
				(function (mMenuPush) {
					var initElem = ($(defaults.initElem).length) ? $(defaults.initElem) : false;
					if (initElem) {
						defaults.initElem = initElem;
						_clickHandlers(mMenuPush);
						_updateMenuTitle(mMenuPush);
						$('.nav-wrapper').css({
							"height": $('.nav-wrapper ul.nav').outerHeight()
						})
					}
				}(this));

				function _clickHandlers(menu) {
					defaults.initElem.on('click', 'a', function (e) {
						if ($(e.target).parent('li').find('ul').length) {
							e.preventDefault();
							menu.curItem = $(this).parent();
							_updateActiveMenu(menu);
						}
					});
					defaults.initElem.on('click', '.nav-toggle', function () {
						_updateActiveMenu(menu, 'back');
					});
				};

				function _updateActiveMenu(menu, direction) {
					_slideMenu(menu, direction);
					if (direction === "back") {
						var curItem = menu.curItem;
						setTimeout(function () {
							curItem.removeClass('mmenu-submenu-open mmenu-submenu-active');
						}, 300);
						menu.curItem = menu.curItem.parent().closest('li');
						menu.curItem.addClass('mmenu-submenu-open mmenu-submenu-active');
						_updateMenuTitle(menu);
					} else {
						menu.curItem.addClass('mmenu-submenu-open mmenu-submenu-active');
						_updateMenuTitle(menu);
					}
				};

				function _updateMenuTitle(menu) {
					var title = defaults.menuTitle;
					if (menu.curLevel > 0) {
						title = menu.curItem.children('a').html();
						defaults.initElem.find('.nav-toggle').addClass('back-visible');
					} else {
						defaults.initElem.find('.nav-toggle').removeClass('back-visible');
					}
					$('.nav-title').html(title);
				};

				function _updateHeight(menu) {
					if (menu.curLevel > 0) {
						menu.curItem.children('ul').css({
							"padding-top": defaults.initElem.find('.nav-toggle').outerHeight()
						});
						$('.nav-wrapper').css({
							"height": menu.curItem.children('ul').outerHeight()
						});
					} else {
						$('.nav-wrapper').css({
							"height": $('.nav-wrapper .nav-level-1').outerHeight()
						});
					}
				}

				function _slideMenu(menu, direction) {
					if (direction === "back") {
						menu.curLevel = (menu.curLevel > 0) ? menu.curLevel - 1 : 0;
						setTimeout(function () {
							_updateHeight(menu);
						}, 300);
					} else {
						menu.curLevel += 1;
						setTimeout(function () {
							_updateHeight(menu);
						}, 100);
					}
					defaults.initElem.children('ul').css({
						"transform": "translateX(-" + (menu.curLevel * 100) + "%)"
					});
				};
			}
			GOODWIN.mobilemenupush = new mMenuPush({
				initElem: ".js-push-mbmenu .nav-wrapper"
			});
		},
		headerDrop: function () {
			var HeaderDrop = (function (options) {
				var data = {
					dropLink: '.dropdn-link',
					dropLinkParent: '.dropdn',
					dropClose: '.dropdn-close'
				};

				function HeaderDrop(options) {
					$.extend(data, options);
					this.init()
				}

				HeaderDrop.prototype = $.extend({}, HeaderDrop.prototype, {
					init: function (options) {
						this._handlers();
						return this;
					},
					reinit: function (windowW) {
						if (!isMobile) {
							this._hideDrop();
						}
						this._handlers();
						return this;
					},
					_handlers: function () {
						var that = this,
							$dropLink = $(data.dropLink),
							$dropLinkParent = $dropLink.closest(data.dropLinkParent),
							$dropClose = $(data.dropClose, $dropLinkParent);
						if (isMobile) {
							if (!$dropLink.data('mclick')) {
								$dropClose.off('.dropdn');
								$dropLink.off('.dropdn');
								$dropLinkParent.off('.dropdn');
								$document.off('.dropdn');
								$dropLink.on('click.dropdn', function (e) {
									var $this = $(this);
									if ($this.closest('.mobilemenu').length) {
										$this.parent().toggleClass('is-hovered');
									} else if ($this.next().length) {
										if ($this.parent().hasClass('is-hovered')) {
											$this.parent().removeClass('is-hovered');
											setTimeout(function () {
												$('body').removeClass('is-fixed');
											}, 500);
											$this.next().find('ul').scrollLock('disable');
										} else {
											$dropLink.parent().removeClass('is-hovered');
											$this.parent().addClass('is-hovered');
											$this.next().find('ul').scrollLock('enable');
										}
									}
									e.preventDefault();
								});
								$dropLinkParent.on('click.dropdn', function (e) {
									if ($(e.target).is($('.dropdn-content')) && !$(e.target).closest('.mobilemenu').length) {
										$dropLinkParent.removeClass('is-hovered');
										setTimeout(function () {
											$('body').removeClass('is-fixed');
										}, 500);
										$dropLinkParent.find('ul').scrollLock('disable');
										e.preventDefault();
									}
								});
								$dropClose.on('click.dropdn', function (e) {
									if (!$(this).closest('.mobilemenu').length) {
										$dropLink.parent().removeClass('is-hovered');
										setTimeout(function () {
											$('body').removeClass('is-fixed');
										}, 500);
										$dropLink.parent().find('ul').scrollLock('disable');
									}
									e.preventDefault();
								});
								$dropLink.data('mclick', true);
								$dropLink.removeData('hover');
								$dropLink.removeData('click');
							}
						} else if ($('body').hasClass('is-dropdn-click')) {
							if (!$dropLink.data('click')) {
								$dropClose.off('.dropdn');
								$dropLink.off('.dropdn');
								$dropLinkParent.on('.dropdn');
								$dropLinkParent.off('.dropdn');
								$dropLink.on('click.dropdn', function (e) {
									var $this = $(this);
									if ($this.next().length) {
										if ($this.parent().hasClass('is-hovered')) {
											$this.parent().removeClass('is-hovered');
											setTimeout(function () {
												$this.next().find('.search-input').val('');
											}, 500);
										} else {
											$dropLink.parent().removeClass('is-hovered');
											$this.parent().addClass('is-hovered');
											$this.next().css({
												'min-height': that._getDropHeight($this) + 'px',
												'top': that._getDropPos($this) + 'px'
											});
											if ($this.parent().hasClass('dropdn_search')) {
												setTimeout(function () {
													$this.next().find('.search-input').focus()
												}, 100);
											}
										}
										e.preventDefault();
									}
								});
								$document.on('click.dropdn', function (e) {
									var $this = $(e.target);
									if (!$this.closest('.dropdn').length) {
										$dropLinkParent.removeClass('is-hovered');
										setTimeout(function () {
											if ($this.next().find('.search-input').length) {
												$this.next().find('.search-input').val('');
											}
										}, 500);
									}
								});
								$dropClose.on('click.dropdn', function (e) {
									var $this = $(e.target);
									$dropLink.parent().removeClass('is-hovered');
									setTimeout(function () {
										$this.next().find('.search-input').val('');
									}, 500);
									e.preventDefault();
								});
								$dropLink.data('click', true);
								$dropLink.removeData('mclick');
								$dropLink.removeData('hover');
							}
						} else {
							if (!$dropLink.data('hover')) {
								$dropLink.off('.dropdn');
								$document.off('.dropdn');
								$dropLinkParent.off('.dropdn');
								$dropLink.on('mouseenter.dropdn', function () {
									var $this = $(this);
									if ($this.next().length) {
										$dropLink.parent().removeClass('is-hovered');
										$this.parent().addClass('is-hovered');
										if (!$this.closest('.mobilemenu').length) $this.next().css({
											'min-height': getDropHeight($this) + 'px',
											'top': getDropPos($this) + 'px'
										});
									}
								});
								$dropLinkParent.on('mouseleave.dropdn', function () {
									var $this = $(this);
									$this.removeClass('is-hovered');
								});
								$dropLink.data('hover', true);
								$dropLink.removeData('click');
							}
						}
					},
					_getDropHeight: function (dropdn) {
						var h;
						if (dropdn.closest('.container').parent().next().length) {
							h = dropdn.closest('.container').parent().next().outerHeight();
						} else if (dropdn.closest('.container').parent().prev().length) {
							h = dropdn.closest('.container').parent().prev().outerHeight();
						}
						return h + 1;
					},
					_getDropPos: function (dropdn) {
						var $parent = dropdn.closest('.container').parent();
						if ($parent.length) {
							if ($('.hdr').hasClass('hdr-style-4') && dropdn.parent().hasClass('dropdn_search')) {
								return $parent.outerHeight();
							} else if ($('.hdr').hasClass('hdr-style-5') && !dropdn.closest('.hdr-topline').length) {
								return $parent.outerHeight();
							} else if ($('.hdr').hasClass('hdr-style-11') && dropdn.closest('.hdr-topline').length) {
								return $('.hdr-desktop .hdr-content').offset().top;
							} else if ($('.hdr').hasClass('hdr-style-2') || $('.hdr').hasClass('hdr-style-7') || $('.hdr').hasClass('hdr-style-8') || $('.hdr').hasClass('hdr-style-11') || $('.hdr').hasClass('hdr-style-12')) {
								return $parent.outerHeight();
							} else return $parent.outerHeight() + $parent.offset().top;
						}
					},
					_hideDrop: function () {
						$('body').removeClass('is-fixed');
						$(data.dropLink).parent().removeClass('is-hovered');
						$(data.dropLink).next().css({
							'min-height': '',
							'top': ''
						});
					}
				})
				return HeaderDrop;
			})();
			GOODWIN.headerdrop = new HeaderDrop();
		},
		scrollMenuInit: function (data) {
			var ScrollMenu = (function (options) {
				var initialized = false;
				var data = {
					headerNone: '.hdr.slide-menu',
					headerOneRowMenu: '.hdr-onerow-menu',
					menu: '.mmenu-js',
					arrowPrev: '.prev-menu-js',
					arrowNext: '.next-menu-js',
					bodyFlagClass: 'has-scrollmenu',
					scrollStep: 10,
					scrollSpeed: 4
				};

				function ScrollMenu(options) {
					$.extend(data, options);
					this.init()
				}

				ScrollMenu.prototype = $.extend({}, ScrollMenu.prototype, {
					init: function () {
						if ($(data.headerNone).length || !$(data.headerOneRowMenu).length) return false;
						initialized = true;
						this._handlers();
						this._isScroll($(data.menu), isMobile);
						return this;
					},
					reinit: function () {
						if ($(data.headerNone).length || !$(data.headerOneRowMenu).length) return false;
						var $this = $(data.menu);
						if (initialized) {
							this._isScroll($(data.menu), isMobile);
							return $this;
						} else return false;
					},
					// destroy
					destroy: function () {
						var $this = $(data.menu),
							$menuWrap = $this.parent(),
							options = $this.data('options');
						if ($this.data('initialized')) {
							$this.removeData('initialized');
							$menuWrap.animate({
								scrollLeft: 0
							}, 0);
							$(data.arrowNext + ',' + data.arrowPrev).off('.scrollmenu');
							$('body').removeClass(data.bodyFlagClass);
						} else return false;
					},
					// handlers
					_handlers: function () {
						var $this = $(data.menu),
							$menuWrap = $this.parent(),
							step;

						function scroll(menu) {
							var $menu = menu;
							$menu.animate({
								scrollLeft: step
							}, data.scrollSpeed, 'linear', function () {
								(step !== 0) ? scroll($menu) : false
							});
							$menu.scrollLeft() + $menu.innerWidth() >= $menu[0].scrollWidth ? $(data.arrowNext).addClass('disable') : $(data.arrowNext).removeClass('disable');
							$menu.scrollLeft() > 0 ? $(data.arrowPrev).removeClass('disable') : $(data.arrowPrev).addClass('disable');
						}

						$(data.arrowNext).on('mouseenter.scrollmenu', function () {
							step = '+=' + data.scrollStep;
							scroll($menuWrap);
						}).on('mouseleave.scrollmenu', function () {
							step = 0;
						});
						$(data.arrowPrev).on('mouseenter.scrollmenu', function () {
							step = '-=' + data.scrollStep;
							scroll($menuWrap);
						}).on('mouseleave.scrollmenu', function () {
							step = 0;
						});
						return $this;
					},
					scrollToStart: function () {
						var $this = this;
						if ($this.data('initialized')) {
							this._isScroll($(data.menu), isMobile);
							return $this;
						} else return false;
					},
					_isScroll: function (menu, isMobile) {
						var $this = menu,
							$menuWrap = $this.parent();
						$('body').removeClass(data.bodyFlagClass);
						$menuWrap.animate({
							scrollLeft: 0
						}, 0);
						$(data.arrowPrev).addClass('disable');
						$(data.arrowNext).removeClass('disable');
						if (!isMobile && ($this.width() >= $menuWrap.width())) $('body').addClass(data.bodyFlagClass);
						return $this;
					}
				});
				return ScrollMenu;
			})();
			GOODWIN.scrollmenu = new ScrollMenu(data);
		},
		miniCartInit: function (data) {
			var MiniCart = (function (options) {
				var data = {
					headerCart: '.minicart-js',
					toggleBtn: '.minicart-link',
					closeBtn: '.minicart-drop-close',
					dropdn: '.minicart-drop',
					header: '.hdr',
					sticky: '.sticky-holder',
					stickyFlag: 'has-sticky'
				};

				function MiniCart(options) {
					$.extend(data, options);
					this.init()
				}

				MiniCart.prototype = $.extend({}, MiniCart.prototype, {
					init: function (options) {
						this._handlers($(data.headerCart), isMobile);
						return this;
					},
					reinit: function (windowW) {
						this._handlers($(data.headerCart), isMobile);
						return this;
					},
					_handlers: function (cart, isMobile) {
						var $this = cart,
							self = this;
//						$('.minicart-drop').scrollLock('disable');
						if (isMobile) {
							if (!$this.data('mobile')) {
								$(data.dropdn).removeClass('opened');
								$(data.headerCart).removeClass('is-hovered');
								$(data.dropdn).css({
									'top': '',
									'height': '',
									'max-height': ''
								});
								$(data.toggleBtn).on('click.miniCart', function (e) {
									self.open($this);
									return false;
									e.preventDefault();
								});
								$(data.closeBtn).on('click.miniCart', function (e) {
									self.close($this);
									return false;
								});
								$this.off('.miniCart').removeData('desktop').data('mobile', true).on('click.miniCart', function (e) {
									if ($(e.target).is($(data.dropdn))) {
										self.close($this);
										e.preventDefault();
									}
								});
							}
						} else {
							if (!$this.data('desktop')) {
								$(data.toggleBtn + ',' + data.closeBtn).off('.miniCart');
								$(data.dropdn + ',' + data.dropdn + '> .container').css({
									'height': ''
								});
								$(data.toggleBtn).on('click.miniCart', function (e) {
									$(data.dropdn).toggleClass('opened');
									$(data.headerCart).toggleClass('is-hovered');
									$('.minicart-drop').scrollLock('enable');
//									self._topCalc($this);
									e.preventDefault();
								});
								$document.on('click.miniCart', function (e) {
									var $this = $(e.target);
									if (!$this.closest(data.dropdn).length && !$this.closest(data.headerCart).length) {
										$(data.dropdn).removeClass('opened');
										$(data.headerCart).removeClass('is-hovered');
									}
								});
								self._bodyFixed($this, false);
								$this.off('.miniCart').removeClass('active, mobile').removeData('mobile').data('desktop', true)
							}
						}
						return $this;
					},
					// open minicart
					open: function (cart) {
						var $this = cart ? cart : this;
						$this.toggleClass('active');
						if ($('body').hasClass('is-fixed')) {
							this._bodyFixed($this, false);
						} else {
							this._bodyFixed($this, true);
							this._heightCalc($this);
						}
						return $this;
					},
					// close minicart
					close: function (cart) {
						var $this = cart ? cart : this;
						$this.removeClass('active');
						this._bodyFixed($this, false);
						$(data.headerCart).removeClass('is-hovered');
						$(data.dropdn + ',' + data.dropdn + '> .container').css({
							'height': ''
						});
						return $this;
					},
					_heightCalc: function () {
						var height = isMobile ? window.innerHeight : $(window).height();
						$(data.dropdn + ',' + data.dropdn + '> .container').css({
							'height': height + 'px'
						});
					},
//					_topCalc: function () {
//						if ($(data.dropdn).length) {
//							var $dropdn = $(data.dropdn),
//								$parent = $dropdn.closest('.container').parent(),
//								hTop = $parent.outerHeight(),
//								maxH = $(window).height() - $parent.outerHeight() - $parent.offset().top;
//							if ($('body').hasClass(data.stickyFlag)) {
//								hTop = $(data.sticky).outerHeight();
//								maxH = $(window).height() - $parent.outerHeight();
//							}
//							if (!isMobile) {
//								$dropdn.css({
//									'top': hTop + 'px',
//									'max-height': maxH + 'px'
//								})
//							}
//						}
//					},
                                        
					_bodyFixed: function (cart, state) {
						if (state) {
							$('body,' + data.sticky).addClass('is-fixed').css({
								'padding-right': scrollWidth + 'px'
							});
							$('.minicart-drop-content').scrollLock('enable');
						} else {
//							$('.minicart-drop-content').scrollLock('disable');
							$('body,' + data.sticky).removeClass('is-fixed').css({
								'padding-right': ''
							});
						}
					}
				});
				return MiniCart;
			})();
			GOODWIN.minicart = new MiniCart(data);
		},
		stickyHeaderInit: function () {
			var StickyHeader = (function () {
				var data = {
					header: '.hdr_sticky',
					headerM: '.hdr-mobile',
					headerD: '.hdr-desktop',
					hdrLogo: '.logo-holder',
					hdrNav: '.nav-holder',
					hdrCart: '.minicart-holder',
					sticky: '.sticky-holder',
					stickyLogo: '.logo-holder-s',
					stickyNav: '.nav-holder-s',
					stickyCart: '.minicart-holder-s',
					mobileMenu: '.mmenu',
					promoTopline: '.promo-topline',
					offset: 500
				};

				function StickyHeader(options) {
					$.extend(data, options);
					this.init()
				}

				StickyHeader.prototype = $.extend({}, StickyHeader.prototype, {
					init: function () {
						if (!$(data.header).length) return false;
						if (!isMobile && !$('body').hasClass('has-sticky')) {
							this._setHeigth();
						} else if ($(data.header).hasClass('hdr-mobile-style2')) {
							this._setScrollSimple();
							return false;
						}
						this._setScroll(isMobile);
						this._multirow();
						this._multirowS();
						return this;
					},
					reinit: function () {
						if (!$(data.header).length) return false;
						$window.off('scroll.stickyHeader');
						if (!isMobile) {
							this._setHeigth();
						} else if ($(data.header).hasClass('hdr-mobile-style2')) {
							if ($('body').hasClass('has-sticky')) {
								this.destroySticky();
								this.setHeaderHeight();
							}
							this._setScrollSimple();
							return false;
						}
						this._multirow();
						this._multirowS();
						this._setScroll(isMobile);
						return this;
					},
					_multirow: function () {
						if (isMobile) return false;
						if ($(data.hdrNav).outerHeight() > 60) {
							$(data.header).addClass('mmenu-multirow');
						} else $(data.header).removeClass('mmenu-multirow');
					},
					_multirowS: function () {
						if (isMobile) return false;
						if ($('body').hasClass('has-sticky')) {
							if ($(data.stickyNav).outerHeight() > 60) {
								$(data.header).addClass('mmenu-multirow-s');
							} else $(data.header).removeClass('mmenu-multirow-s');
						}
					},
					destroySticky: function () {
						var $sticky = $(data.sticky),
							$stickyLogo = $(data.stickyLogo),
							$stickyNav = $(data.stickyNav),
							$stickyCart = $(data.stickyCart);
						if (isMobile) {
							var $hdrLogo = $(data.hdrLogo, $(data.headerM)),
								$hdrNav = $(data.hdrNav),
								$hdrCart = $(data.hdrCart, $(data.headerM));
						} else {
							var $hdrLogo = $(data.hdrLogo, $(data.headerD)),
								$hdrNav = $(data.hdrNav, $(data.headerD)),
								$hdrCart = $(data.hdrCart, $(data.headerD));
						}
						this._removeSticky($stickyNav, $hdrNav, $stickyCart, $hdrCart, $sticky);
					},
					setHeaderHeight: function () {
						if ($(data.header).hasClass('hdr-mobile-style2') && isMobile) {
							if (promoToplineHeight > 0) {
								promoToplineHeight = $(data.promoTopline).outerHeight();
							}
							$(data.header).css({
								height: $(data.headerM).height() + promoToplineHeight
							});
						}
					},
					_setScrollSimple: function () {
						this.setHeaderHeight();
						$window.on('scroll.stickyHeader', function () {
							if ($body.hasClass('blockSticky')) return false;
							if ($window.scrollTop() > promoToplineHeight) {
								if ($(data.headerM).hasClass('is-sticky')) return false;
								$(data.headerM).addClass('is-sticky');
							} else {
								$(data.headerM).removeClass('is-sticky');
							}
						});
					},
					_setScroll: function (isMobile) {
						var that = this;
						var $header = $(data.header),
							$sticky = $(data.sticky),
							$stickyLogo = $(data.stickyLogo),
							$stickyNav = $(data.stickyNav),
							$stickyCart = $(data.stickyCart),
							stickyH = $header.height(),
							offset = data.offset;
						if (isMobile) {
							var $hdrNav = $(data.hdrNav, $(data.headerM)),
								$hdrCart = $(data.hdrCart, $(data.headerM));
						} else {
							var $hdrNav = $(data.hdrNav, $(data.headerD)),
								$hdrCart = $(data.hdrCart, $(data.headerD));
						}

						$window.on('scroll.stickyHeader', function () {
							if ($body.hasClass('blockSticky')) return false;
							var st = $window.scrollTop();
							if (st > (stickyH + offset)) {
								if (!$('body').hasClass('has-sticky')) {
									that._setSticky($hdrNav, $stickyNav, $hdrCart, $stickyCart, $sticky);
								}
							} else {
								if ($('body').hasClass('has-sticky')) {
									that._removeSticky($stickyNav, $hdrNav, $stickyCart, $hdrCart, $sticky);
								}
							}
						});
						return this;
					},
					_setSticky: function (hdrNav, stickyNav, hdrCart, stickyCart, sticky) {
						hdrNav.children().detach().appendTo(stickyNav);
						hdrCart.children().detach().appendTo(stickyCart);
						sticky.addClass('animated fadeIn');
						$body.addClass('has-sticky');
//						GOODWIN.minicart._topCalc();
						this._multirowS();
						this._clearActive($(data.header));
					},
					_removeSticky: function (stickyNav, hdrNav, stickyCart, hdrCart, sticky) {
						stickyNav.children().detach().appendTo(hdrNav);
						stickyCart.children().detach().appendTo(hdrCart);
						sticky.removeClass('animated fadeIn');
						$body.removeClass('has-sticky');
						this._clearActive($(data.header));
//						GOODWIN.minicart._topCalc();
					},
					_setHeigth: function () {
						var $header = $(data.header),
							$hdrNav = $(data.hdrNav);
						$hdrNav.css({
							'height': ''
						});
						$header.removeClass('animated fadeIn').css({
							'height': ''
						});
						if (!$('body').hasClass('has-sticky')) {
							$hdrNav.css({
								'height': $hdrNav.height()
							})
						} else {
							$('body').removeClass('has-sticky');
						}

						return this;
					},
					_clearActive: function (parent) {
						parent.find('.hovered, .is-hovered, .opened').removeClass('hovered is-hovered opened');
					}
				});
				return StickyHeader;
			})();
			GOODWIN.stickyheader = new StickyHeader();
		}
	}
	GOODWIN.product = {
		init: function () {
			this.productGalleryBuild('.js-prd-gallery');
			this.productSlideMain('.js-main-image--slide');
			this.productHoverHeight('.prd');
			this.simpleFancyGallery('.prd-block [data-fancybox="galleryQW"], .prd-block [data-fancybox="gallery"]');
		},
		
		simpleFancyGallery: function (link) {
//			$(link).fancybox({
//				loop: false,
//				animationEffect: "zoom",
//				buttons: ["close"],
//				thumbs: {
//					autoStart: true
//				},
//				arrows: false,
//				touch: false,
//				beforeShow: function (instance, slide) {
//					$(".fancybox-container").last().addClass("fancybox--light");
//				}
//			});
		},
		productGalleryBuild: function (gallery) {
			productGallery = function () {
				var defaults = {
					$prdBlock: $('#prdGallery'),
					zoomLink: '.prd-block_zoom-link',
					prdPreviews: '.product-previews-carousel',
					imageHolder: '.prd-block_main-image-holder',
					zoomImg: '.zoom',
					videoHolder: '.js-main-image-video',
					verticalSelector: '.prd-block--prv-left, .prd-block--prv-right'
				};
				this.data = extendDefaults({}, defaults);
				if (arguments[0] && typeof(arguments[0]) === "object") {
					this.options = extendDefaults(this.data, arguments[0]);
				}
				(function (productGallery) {
					var _ = productGallery;
					var addData = {
						$imageHolder: $(_.data.imageHolder, _.data.$prdBlock),
						$prdPreviews: $(_.data.prdPreviews, _.data.$prdBlock),
						$zoomLink: $(_.data.zoomLink, _.data.$prdBlock),
						$zoomImg: $(_.data.zoomImg, _.data.$prdBlock),
						$video: $(_.data.videoHolder, _.data.$prdBlock)
					};
					$.extend(_.data, addData);
					_galleryBuild(_);
					_zoomLinkEvent(_);
					if (isMobile) {
						_productZoom(_);
					} else if (!_.data.$imageHolder.closest('.prd-block_gallery-mobile').length) {
						_productZoom(_);
					}
					_videoEvent(_);
				})(this);
				productGallery.prototype.previewsReInit = function () {
					var _ = this;
					if (isMobile && _.data.$imageHolder.closest('.prd-block_gallery-mobile').length && !_.data.$prdPreviews.hasClass('slick-initialized')) {
						_productZoom(_);
						_videoEvent(_);
					}
					;
				};
				productGallery.prototype.elevateZoomReInit = function () {
					var _ = this,
						$this = _.data.$zoomImg;
					if ($(_.data.$video).hasClass('showed-video')) {
						return false;
					}
					if ($('.zoomContainer').length) {
						$this.data('ezPlus').destroy();
						$this.removeData('ezPlus');
					}
					$('.zoomContainer, .ezp-spinner').remove();
					if ($this.closest('.zoomWrapper').length) {
						$this.removeAttr('style').unwrap();
					}
					_.data.$imageHolder.removeAttr('style');
					setTimeout(function () {
						_elevateZoom(_);
					}, 100);
				};

				function _elevateZoom(el) {
					var _ = el,
						$prdPreviews = _.data.$prdPreviews,
						$imageHolder = _.data.$imageHolder,
						$zoomImg = _.data.$zoomImg,
						zoompos = $('body').is('.rtl') ? 11 : 1,
						galleryID = $prdPreviews.attr('id'),
						append,
						zoomtype;
					if (!$('body').hasClass('touch')) {
						$imageHolder.removeClass('hideZoom');
						//if (!$imageHolder.hasClass('zoomInit')){
						append = '#' + _.data.$prdBlock.attr('id') + " " + _.data.imageHolder;
						zoomtype = $zoomImg.closest('[data-zoomtype]').data('zoomtype') ? $zoomImg.closest('[data-zoomtype]').data('zoomtype') : 'window';
						$zoomImg.ezPlus({
							zoomType: zoomtype,
							zIndex: 149,
							zoomWindowPosition: zoompos,
							zoomContainerAppendTo: append,
							gallery: galleryID,
							galleryActiveClass: 'active',
							zoomWindowFadeIn: 500,
							zoomWindowFadeOut: 500,
							lensFadeIn: 500,
							lensFadeOut: 500,
							imageCrossfade: true,
							responsive: true,
							loadingIcon: 'images/ajax_loader.gif',
							cursor: 'crosshair'
						});
						$imageHolder.addClass('zoomInit');
						//}
					} else {
						$imageHolder.addClass('hideZoom');
					}
				}

				function _galleryBuild(el) {
					var _ = el,
						$prdPreviews = _.data.$prdPreviews,
						_galleryObj = [];
					$prdPreviews.find('[data-zoom-image]').each(function () {
						var $this = $(this),
							src = $this.attr('data-zoom-image'),
							image = {};
						image["src"] = src;
						image["opts"] = {
							thumb: src,
							caption: $this.find('img').attr('alt')
						};
						_galleryObj.push(image);
					});
					_.galleryObj = _galleryObj;
				}

				function _getActiveIndex(carousel) {
					var current = 0,
						videoPrev = 0;
					if ($(carousel).find('a.active').length) {
						current = $('a.active', $(carousel)).index();
						videoPrev = $('a.active', $(carousel)).prevAll('[data-video], .prd-video').length;
					}
					return current - videoPrev;
				}

				function _zoomLinkEvent(el) {
					var _ = el,
						$zoomLink = _.data.$zoomLink,
						$prdPreviews = _.data.$prdPreviews;
					if ($zoomLink.closest('.prd-block, .prd-single').find('.product-previews-carousel').length) {
						$zoomLink.on('click', function (e) {
							e.preventDefault();
							var carouselID = $prdPreviews.attr('id'),
								activeIndex = _getActiveIndex('#' + carouselID),
								items = _.galleryObj;
							$.fancybox.open(items, {
								loop: false,
								animationEffect: "zoom",
								touch: false,
								buttons: ["close"],
								thumbs: {
									autoStart: true
								},
								arrows: false,
								beforeShow: function (instance, slide) {
									$(".fancybox-container").last().addClass("fancybox--light");
								}
							});
							$.fancybox.getInstance('jumpTo', activeIndex);
						});
					} else {
						$zoomLink.on('click', function (e) {
							var $this = $(this);
							$.fancybox.open(
								[{
									src: $this.attr('href'),
									opts: {
										caption: $this.data('caption')
									}
								}], {
									beforeShow: function (instance, slide) {
										$(".fancybox-container").last().addClass("fancybox--light");
									}
								});
							e.preventDefault();
						});
					}
				}

				function _productZoom(el) {
					var _ = el,
						$imageHolder = _.data.$imageHolder,
						$prdPreviews = _.data.$prdPreviews;
					if ($imageHolder.hasClass('js-main-image-zoom')) {
						$imageHolder.imagesLoaded(function () {
							_elevateZoom(_);
						});
						if (!$prdPreviews.hasClass('slick-initialized')) {
							_previewsCarousel(_, $prdPreviews, 'zoom');
						}
					} else {
						if (!$prdPreviews.hasClass('slick-initialized')) {
							_previewsCarousel(_, $prdPreviews, 'nozoom');
						}
					}
				}

				function _videoSetStartPoster(el) {
					var _ = el,
						$prdPreviews = _.data.$prdPreviews;
					if ($prdPreviews.find('[data-video]:first-child').length) {
						var $this = $prdPreviews.find('[data-video]:first-child');
						data.$video.addClass('showed-video');
						data.$video.children('video').attr('poster', $this.find('img').attr('src'));
					}
				}

				function _videoEvent(el) {
					var _ = el,
						$mainImg = $('img', _.data.$imageHolder),
						$parent = _.data.$video;
					_videoSetStartPoster(_);
					$('[data-video]', _.data.$prdBlock).on('click', function (e) {
						e.stopPropagation();
						e.preventDefault();
						var $this = $(this),
							$slider = $this.closest('.prd-block, .prd-single').find('.prd-block_main-image-holder'),
							$next = $('.js-main-image-next', $slider),
							$prev = $('.js-main-image-prev', $slider);
						$next.removeClass('slick-disabled');
						$prev.removeClass('slick-disabled');
						$this.siblings().removeClass('active');
						$this.addClass('active');
						if ($this.is(':first-child')) {
							$prev.addClass('slick-disabled');
						}
						if ($this.is(':last-child')) {
							$next.addClass('slick-disabled');
						}
						var $this = $(this),
							$video = _.data.$video,
							video = _.data.$video.children('video').get(0),
							sources = video.getElementsByTagName('source');
						if (video != null) {
							if ($this.data('video') == sources[0].src) {
								if (!video.paused) {
									video.pause();
								} else {
									$video.children('video').attr('poster', $this.find('img').attr('src'));
									video.play();
									$parent.css({
										'max-height': $mainImg.height(),
										'height': $mainImg.height()
									});
									$mainImg.addClass('unvisible');
									$mainImg.closest('.zoomWrapper').addClass('unvisible');
									$video.addClass('showed-video');
								}
							} else {
								video.pause();
								$video.children('video').attr('poster', '');
								sources[0].src = $this.data('video');
								video.load();
								setTimeout(function () {
									var videoH = video.videoHeight * $video.width() / video.videoWidth;
									$parent.css({
										'max-height': $mainImg.height(),
										'height': $mainImg.height()
									});
									$video.addClass('showed-video').css({
										'height': videoH + 'px'
									});
									$mainImg.addClass('unvisible');
									$mainImg.closest('.zoomWrapper').addClass('unvisible');
									$video.children('video').attr('poster', $this.find('img').attr('src'));
									video.play();
								}, 100);
							}
						}
					});
				}

				function _previewsCarousel(el, carousel, zoom) {
					var _ = el,
						$this = $(carousel),
						zoom = zoom,
						$mainImg = $('img', _.data.$imageHolder),
						$parent = _.data.$imageHolder;
					//$this.imagesLoaded(function() {
					// set modal height on callback
					$this.on('init', function () {
						$this.find('.slick-slide').first().addClass('active');
						if ($this.closest('#quickView').length) {
							var $modal = $this.closest('#quickView').find('.modal-content');
							$modal.css({
								'height': $modal.find('.prd-block').outerHeight() + 'px'
							});
							setTimeout(function () {
								$modal.addClass('loaded');
							}, 500);
						}
						$('.slick-slide', $this).on('click', function () {
							var $slider = $this.closest('.prd-block, .prd-single').find('.prd-block_main-image-holder'),
								$next = $('.js-main-image-next', $slider),
								$prev = $('.js-main-image-prev', $slider);
							setTimeout(function () {
								$next.removeClass('slick-disabled');
								$prev.removeClass('slick-disabled');
								if ($('.slick-slide.active', $this).is(':first-child')) {
									$prev.addClass('slick-disabled');
								}
								if ($('.slick-slide.active', $this).is(':last-child')) {
									$next.addClass('slick-disabled');
								}
							}, 100);
						});
					});
					$this.slick({
						slidesToShow: 4,
						slidesToScroll: 1,
						dots: false,
						infinite: false, //don't change
						vertical: $this.closest(_.data.verticalSelector).length ? true : false,
						swipe: swipemode,
						responsive: [{
							breakpoint: maxMD,
							settings: {
								vertical: false
							}
						}, {
							breakpoint: maxXS,
							settings: {
								slidesToShow: 3
							}
						}]
					});
					$this.on('click', '.slick-slide', function (e) {
						if (!$(e.target).is($('[data-video]')) && !$(e.target).closest('[data-video]').length) {
							$parent.css({
								'max-height': '',
								'height': ''
							});
							$mainImg.removeClass('unvisible');
							$mainImg.closest('.zoomWrapper').removeClass('unvisible');
							;
							var video = _.data.$video.children("video").get(0);
							video.pause();
							_.data.$video.removeClass('showed-video');
							_.elevateZoomReInit();
						}
						if (zoom === 'nozoom' || $('body').hasClass('touch')) {
							e.preventDefault();
							e.stopPropagation();
							var newImg = $(this).attr('data-zoom-image');
							$this.find('.slick-slide').removeClass('active');
							$(this).addClass('active');
							$mainImg.attr('src', newImg);
						}
					});
					//});
				}
			};
			$(gallery).each(function (i) {
				productGalleryArray[i] = new productGallery({
					$prdBlock: $('#' + $(this).attr('id'))
				});
			});
		},
		productSlideMain: function (carousel) {
			$(carousel).each(function () {
				var $carousel = $(this),
					$next = $('.js-main-image-next', $carousel),
					$prev = $('.js-main-image-prev', $carousel),
					$productblock = $carousel.closest('.prd-block, .prd-single'),
					$previewsCarousel = $productblock.find('.product-previews-carousel');
				$prev.addClass('slick-disabled');

				function prevSlide() {
					if ($('.slick-active.active', $previewsCarousel).length) {
						if ($('.slick-active.active', $previewsCarousel).prev().hasClass('prd-video')) {
							if (!$('.slick-active.slick-active', $previewsCarousel).prev().prev().length) {
								$prev.addClass('slick-disabled');
							}
							$('.slick-active.active', $previewsCarousel).prev().prev().trigger('click');
							return false;
						} else $('.slick-active.active', $previewsCarousel).prev().trigger('click');
					} else {
						if ($('.slick-active.slick-active', $previewsCarousel).prev().hasClass('prd-video')) {
							if (!$('.slick-active.slick-active', $previewsCarousel).prev().prev().length) {
								$prev.addClass('slick-disabled');
							}
							$('.slick-current.slick-active', $previewsCarousel).prev().prev().trigger('click');
							return false;
						} else $('.slick-current.slick-active', $previewsCarousel).prev().trigger('click');
					}
					$previewsCarousel.slick('slickPrev');
				}

				function nextSlide() {
					if ($('.slick-active.active', $previewsCarousel).length) {
						if ($('.slick-active.active', $previewsCarousel).next().hasClass('prd-video')) {
							if (!$('.slick-active.slick-active', $previewsCarousel).next().next().length) {
								$next.addClass('slick-disabled');
							}
							$('.slick-active.active', $previewsCarousel).next().next().trigger('click');
							;
							return false;
						} else $('.slick-active.active', $previewsCarousel).next().trigger('click');
					} else {
						if ($('.slick-active.slick-active', $previewsCarousel).next().hasClass('prd-video')) {
							if (!$('.slick-active.slick-active', $previewsCarousel).next().next().length) {
								$next.addClass('slick-disabled');
							}
							$('.slick-current.slick-active', $previewsCarousel).next().next().trigger('click');
							return false;
						} else $('.slick-current.slick-active', $previewsCarousel).next().trigger('click');
					}
					$previewsCarousel.slick('slickNext');
				}

				$prev.on('click', function (e) {
					prevSlide();
					e.preventDefault();
				});
				$next.on('click', function (e) {
					nextSlide();
					e.preventDefault();
				});
				$carousel.on('swipeleft', function (e) {
					nextSlide();
				}).on('swiperight', function (e) {
					prevSlide();
				});
			});
		},
		productHeightResize: function (product) {
			$(product).each(function () {
				var $this = $(this);
				$this.removeData('bottom1', 'bottom2');
				$('.color-swatch', $this).css({'bottom': ''});
				$('.countdown-box', $this).css({'bottom': ''});
			})
		},
		productHoverHeight: function (product) {
			var product = product,
				speed = 180;
			$document.on('mouseenter', product, function (e) {
				if (w < maxMD) return false;
				var $this = $(this);
				var $slick = $this.closest('.slick-list');
				if ($this.closest('.single-prd-carousel').length) return false;
				$this.css({
					'height': $(this)[0].getBoundingClientRect().height + 'px'
				});
				if (!$this.hasClass('hovered')) {
					$this.addClass('hovered');
					if ($this.hasClass('prd-style2') && !$this.hasClass('prd-outstock')) {
						var shiftY = $('.prd-hover', $this).height();
						if ($this.data('bottom1') === undefined) {
							$this.data('bottom1', parseInt($('.color-swatch', $this).css('bottom'), 10));
						}
						if ($this.data('bottom2') === undefined) {
							$this.data('bottom2', parseInt($('.countdown-box', $this).css('bottom'), 10));
						}
						$('.prd-info', $this).stop().animate({'top': -shiftY + 'px'}, speed);
						$('.color-swatch', $this).stop().animate({'bottom': $this.data('bottom1') + shiftY}, speed);
						$('.countdown-box', $this).stop().animate({'bottom': $this.data('bottom2') + shiftY}, speed);
					}
				}
				$slick.addClass('slick-list--offset');
				$slick.parent().addClass('prd-hovered');
			}).on('mouseleave', product, function (e) {
				if (w < maxMD) return false;
				var $this = $(this);
				var $slick = $this.closest('.slick-list');
				if ($this.closest('.single-prd-carousel').length) return false;
				$this.removeClass('hovered');
				$slick.removeClass('slick-list--offset');
				$slick.parent().removeClass('prd-hovered');
				if ($('.bootstrap-select', $this).hasClass('open')) $('select', $this).selectpicker('toggle');
				$this.css({
					'height': ''
				});
				if ($this.hasClass('prd-style2') && !$this.hasClass('prd-outstock')) {
					$('.prd-info', $this).stop().animate({'top': 0}, speed);
					$('.color-swatch', $this).stop().animate({'bottom': $this.data('bottom1')}, speed);
					$('.countdown-box', $this).stop().animate({'bottom': $this.data('bottom2')}, speed);
				}
			});
			$(document).on('mouseenter', '.dropdown-toggle', function (e) {
				$(this).attr("title", "");
			})
//			$(".prd-carousel [data-fancybox]").fancybox({
//				backFocus : false
//			})
		}
		
	};
	GOODWIN.catalog = {
		init: function () {

		},
		
		postAjaxCatalog: function () {
			//GOODWIN.emptycategorydisable.reinit();
			//GOODWIN.catalog.blockCollapse('.sidebar-block');
			//GOODWIN.catalog.markSelectedFilter();
			//GOODWIN.catalog.emptyBlockDisable('.prd-rating');
			GOODWIN.colortoggle.reinit();
			GOODWIN.initialization.productWidth('.prd, .prd-hor');
			GOODWIN.product.selectPicker();
			GOODWIN.product.quickView();
			GOODWIN.initialization.imageLoaded('.prd.prd-has-loader, .prd-hor.prd-has-loader, .has-loader');
			GOODWIN.initialization.countdown('.prd .js-countdown');
		},
		
		emptyBlockDisable: function (obj) {
			$(obj).each(function () {
				$(this).remove();
			})
		}
		
		
	}
	GOODWIN.forms = {
		init: function () {

		},
		
	};
	GOODWIN.sections = {
		init: function () {

		},

	}
	GOODWIN.beforeReady = {
		init: function () {
			GOODWIN.header.mobileMenu('.mobilemenu');
		}
	};
	GOODWIN.documentReady = {
		init: function () {
			GOODWIN.initialization.init();
			GOODWIN.header.init();
			GOODWIN.sections.init();
			GOODWIN.forms.init();
			GOODWIN.product.init();
			GOODWIN.catalog.init();
			// special function on ready init
			GOODWIN.initialization.flowtype();
			GOODWIN.header.promoTopline('.promo-topline', '.js-promo-topline-close');
			GOODWIN.initialization.sliderTextTopShift();
			GOODWIN.initialization.backToTop('.js-back-to-top');
			GOODWIN.initialization.productWidth('.prd, .prd-hor');
			GOODWIN.initialization.sideFixed();
		}
	};
	GOODWIN.documentLoad = {
		init: function () {
			w = window.innerWidth || $window.width();
			GOODWIN.header.stickyHeaderInit();
			if (GOODWIN.sidefixed) GOODWIN.sidefixed.reinit();
			GOODWIN.initialization.scrollOnLoad();
			GOODWIN.initialization.productWidth('.prd, .prd-hor');
			$('.slick-initialized').slick('setPosition');
			$('body').removeClass('hide-until-loaded');
		}
	};
	GOODWIN.documentResize = {
		init: function () {
			clearTimeout(resizeTimer);
			if ((window.innerWidth || $window.width()) == w) {
				if (!$body.hasClass('touch')) {
					resizeTimer = setTimeout(function () {
						scrollWidth = calcScrollWidth();
						w = window.innerWidth || $window.width();
						isMobile = w < mobileMenuBreikpoint;
//						GOODWIN.initialization.compensateScrollBar();
						GOODWIN.mobilemenupush.setHeigth();
						GOODWIN.slidertexttopshift.reinit();
						GOODWIN.setfullheight.reinit();
						GOODWIN.setfullheightslider.reinit();
						GOODWIN.sidefixed.reinit();
						GOODWIN.minicart.reinit(w);
					}, 500)
				}
			} else {
//				GOODWIN.carouseltab.hide();
				GOODWIN.flowtype.hide('.bnr[data-fontratio]');
				GOODWIN.product.productHeightResize('.prd');
				resizeTimer = setTimeout(function () {
					scrollWidth = calcScrollWidth();
					w = window.innerWidth || $window.width();
					isMobile = w < mobileMenuBreikpoint;
					GOODWIN.mobilemenu.reinit();
//					GOODWIN.prdrepos1.reinit(w);
//					GOODWIN.prdrepos.reinit(w);
//					GOODWIN.initialization.compensateScrollBar();
					GOODWIN.stickyheader.reinit(w);
//					GOODWIN.prdcarousel.reinit();
					GOODWIN.fixedsidebar ? GOODWIN.fixedsidebar.reinit(w) : false;
					GOODWIN.slidertexttopshift.reinit();
//					GOODWIN.setfullheight.reinit();
//					GOODWIN.setfullheightslider.reinit();
//					GOODWIN.colortoggle.reinit();
//					GOODWIN.productisotopeSM.reinit();
//					GOODWIN.carouseltab.reinit();
					GOODWIN.initialization.productWidth('.prd, .prd-hor');
					GOODWIN.scrollmenu.reinit(w);
					GOODWIN.minicart.reinit(w);
					GOODWIN.headerdrop.reinit();
					GOODWIN.mobilemenupush.setHeigth();
//					GOODWIN.timeline.reinit(w);
					GOODWIN.sidefixed.reinit();
					GOODWIN.flowtype.reinit('.bnr[data-fontratio]');
					$('.slick-initialized').slick('setPosition');
					$.each(productGalleryArray, function (i) {
						productGalleryArray[i].elevateZoomReInit();
						productGalleryArray[i].previewsReInit();
					});
				}, 500);
			}
		}
	};
	var $body = $('body'),
		$window = $(window),
		$document = $(document),
		w = window.innerWidth || $window.width(),
		resizeTimer,
		scrollWidth = calcScrollWidth(),
		promoToplineHeight = 0,
		productGalleryArray = [],
		swipemode = false,
		maxXS = 480,
		maxSM = 768,
		maxMD = 992,
		mobileMenuBreikpoint = 991,
		isMobile = w < mobileMenuBreikpoint,
		productGallery;
	GOODWIN.beforeReady.init();
	$document.on('ready', GOODWIN.documentReady.init);
	$window.on('load', GOODWIN.documentLoad.init);
	$window.on('resize', GOODWIN.documentResize.init);
})(jQuery)