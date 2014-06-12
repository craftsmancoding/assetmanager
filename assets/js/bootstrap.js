/*****Bootstrap plugins*****/
!function(a){a(function(){"use strict";a.support.transition=function(){var a=function(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd",msTransition:"MSTransitionEnd",transition:"transitionend"},c;for(c in b){if(a.style[c]!==undefined){return b[c]}}}();return a&&{end:a}}()})}(window.jQuery);!function(a){"use strict";var b='[data-dismiss="alert"]',c=function(c){a(c).on("click",b,this.close)};c.prototype.close=function(b){function f(){e.trigger("closed").remove()}var c=a(this),d=c.attr("data-target"),e;if(!d){d=c.attr("href");d=d&&d.replace(/.*(?=#[^\s]*$)/,"")}e=a(d);b&&b.preventDefault();e.length||(e=c.hasClass("alert")?c:c.parent());e.trigger(b=a.Event("close"));if(b.isDefaultPrevented())return;e.removeClass("in");a.support.transition&&e.hasClass("fade")?e.on(a.support.transition.end,f):f()};a.fn.alert=function(b){return this.each(function(){var d=a(this),e=d.data("alert");if(!e)d.data("alert",e=new c(this));if(typeof b=="string")e[b].call(d)})};a.fn.alert.Constructor=c;a(function(){a("body").on("click.alert.data-api",b,c.prototype.close)})}(window.jQuery);!function(a){"use strict";var b=function(b,c){this.$element=a(b);this.options=a.extend({},a.fn.button.defaults,c)};b.prototype.setState=function(a){var b="disabled",c=this.$element,d=c.data(),e=c.is("input")?"val":"html";a=a+"Text";d.resetText||c.data("resetText",c[e]());c[e](d[a]||this.options[a]);setTimeout(function(){a=="loadingText"?c.addClass(b).attr(b,b):c.removeClass(b).removeAttr(b)},0)};b.prototype.toggle=function(){var a=this.$element.parent('[data-toggle="buttons-radio"]');a&&a.find(".active").removeClass("active");this.$element.toggleClass("active")};a.fn.button=function(c){return this.each(function(){var d=a(this),e=d.data("button"),f=typeof c=="object"&&c;if(!e)d.data("button",e=new b(this,f));if(c=="toggle")e.toggle();else if(c)e.setState(c)})};a.fn.button.defaults={loadingText:"loading..."};a.fn.button.Constructor=b;a(function(){a("body").on("click.button.data-api","[data-toggle^=button]",function(b){var c=a(b.target);if(!c.hasClass("btn"))c=c.closest(".btn");c.button("toggle")})})}(window.jQuery);!function(a){"use strict";var b=function(b,c){this.$element=a(b);this.options=c;this.options.slide&&this.slide(this.options.slide);this.options.pause=="hover"&&this.$element.on("mouseenter",a.proxy(this.pause,this)).on("mouseleave",a.proxy(this.cycle,this))};b.prototype={cycle:function(b){if(!b)this.paused=false;this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval));return this},to:function(b){var c=this.$element.find(".active"),d=c.parent().children(),e=d.index(c),f=this;if(b>d.length-1||b<0)return;if(this.sliding){return this.$element.one("slid",function(){f.to(b)})}if(e==b){return this.pause().cycle()}return this.slide(b>e?"next":"prev",a(d[b]))},pause:function(a){if(!a)this.paused=true;clearInterval(this.interval);this.interval=null;return this},next:function(){if(this.sliding)return;return this.slide("next")},prev:function(){if(this.sliding)return;return this.slide("prev")},slide:function(b,c){var d=this.$element.find(".active"),e=c||d[b](),f=this.interval,g=b=="next"?"left":"right",h=b=="next"?"first":"last",i=this,j=a.Event("slide");this.sliding=true;f&&this.pause();e=e.length?e:this.$element.find(".item")[h]();if(e.hasClass("active"))return;if(a.support.transition&&this.$element.hasClass("slide")){this.$element.trigger(j);if(j.isDefaultPrevented())return;e.addClass(b);e[0].offsetWidth;d.addClass(g);e.addClass(g);this.$element.one(a.support.transition.end,function(){e.removeClass([b,g].join(" ")).addClass("active");d.removeClass(["active",g].join(" "));i.sliding=false;setTimeout(function(){i.$element.trigger("slid")},0)})}else{this.$element.trigger(j);if(j.isDefaultPrevented())return;d.removeClass("active");e.addClass("active");this.sliding=false;this.$element.trigger("slid")}f&&this.cycle();return this}};a.fn.carousel=function(c){return this.each(function(){var d=a(this),e=d.data("carousel"),f=a.extend({},a.fn.carousel.defaults,typeof c=="object"&&c);if(!e)d.data("carousel",e=new b(this,f));if(typeof c=="number")e.to(c);else if(typeof c=="string"||(c=f.slide))e[c]();else if(f.interval)e.cycle()})};a.fn.carousel.defaults={interval:5e3,pause:"hover"};a.fn.carousel.Constructor=b;a(function(){a("body").on("click.carousel.data-api","[data-slide]",function(b){var c=a(this),d,e=a(c.attr("data-target")||(d=c.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,"")),f=!e.data("modal")&&a.extend({},e.data(),c.data());e.carousel(f);b.preventDefault()})})}(window.jQuery);!function(a){"use strict";var b=function(b,c){this.$element=a(b);this.options=a.extend({},a.fn.collapse.defaults,c);if(this.options.parent){this.$parent=a(this.options.parent)}this.options.toggle&&this.toggle()};b.prototype={constructor:b,dimension:function(){var a=this.$element.hasClass("width");return a?"width":"height"},show:function(){var b,c,d,e;if(this.transitioning)return;b=this.dimension();c=a.camelCase(["scroll",b].join("-"));d=this.$parent&&this.$parent.find("> .accordion-group > .in");if(d&&d.length){e=d.data("collapse");if(e&&e.transitioning)return;d.collapse("hide");e||d.data("collapse",null)}this.$element[b](0);this.transition("addClass",a.Event("show"),"shown");this.$element[b](this.$element[0][c])},hide:function(){var b;if(this.transitioning)return;b=this.dimension();this.reset(this.$element[b]());this.transition("removeClass",a.Event("hide"),"hidden");this.$element[b](0)},reset:function(a){var b=this.dimension();this.$element.removeClass("collapse")[b](a||"auto")[0].offsetWidth;this.$element[a!==null?"addClass":"removeClass"]("collapse");return this},transition:function(b,c,d){var e=this,f=function(){if(c.type=="show")e.reset();e.transitioning=0;e.$element.trigger(d)};this.$element.trigger(c);if(c.isDefaultPrevented())return;this.transitioning=1;this.$element[b]("in");a.support.transition&&this.$element.hasClass("collapse")?this.$element.one(a.support.transition.end,f):f()},toggle:function(){this[this.$element.hasClass("in")?"hide":"show"]()}};a.fn.collapse=function(c){return this.each(function(){var d=a(this),e=d.data("collapse"),f=typeof c=="object"&&c;if(!e)d.data("collapse",e=new b(this,f));if(typeof c=="string")e[c]()})};a.fn.collapse.defaults={toggle:true};a.fn.collapse.Constructor=b;a(function(){a("body").on("click.collapse.data-api","[data-toggle=collapse]",function(b){var c=a(this),d,e=c.attr("data-target")||b.preventDefault()||(d=c.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""),f=a(e).data("collapse")?"toggle":c.data();a(e).collapse(f)})})}(window.jQuery);!function(a){function d(){a(b).parent().removeClass("open")}"use strict";var b='[data-toggle="dropdown"]',c=function(b){var c=a(b).on("click.dropdown.data-api",this.toggle);a("html").on("click.dropdown.data-api",function(){c.parent().removeClass("open")})};c.prototype={constructor:c,toggle:function(b){var c=a(this),e,f,g;if(c.is(".disabled, :disabled"))return;f=c.attr("data-target");if(!f){f=c.attr("href");f=f&&f.replace(/.*(?=#[^\s]*$)/,"")}e=a(f);e.length||(e=c.parent());g=e.hasClass("open");d();if(!g)e.toggleClass("open");return false}};a.fn.dropdown=function(b){return this.each(function(){var d=a(this),e=d.data("dropdown");if(!e)d.data("dropdown",e=new c(this));if(typeof b=="string")e[b].call(d)})};a.fn.dropdown.Constructor=c;a(function(){a("html").on("click.dropdown.data-api",d);a("body").on("click.dropdown",".dropdown form",function(a){a.stopPropagation()}).on("click.dropdown.data-api",b,c.prototype.toggle)})}(window.jQuery);!function(a){function c(){var b=this,c=setTimeout(function(){b.$element.off(a.support.transition.end);d.call(b)},500);this.$element.one(a.support.transition.end,function(){clearTimeout(c);d.call(b)})}function d(a){this.$element.hide().trigger("hidden");e.call(this)}function e(b){var c=this,d=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var e=a.support.transition&&d;this.$backdrop=a('<div class="modal-backdrop '+d+'" />').appendTo(document.body);if(this.options.backdrop!="static"){this.$backdrop.click(a.proxy(this.hide,this))}if(e)this.$backdrop[0].offsetWidth;this.$backdrop.addClass("in");e?this.$backdrop.one(a.support.transition.end,b):b()}else if(!this.isShown&&this.$backdrop){this.$backdrop.removeClass("in");a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one(a.support.transition.end,a.proxy(f,this)):f.call(this)}else if(b){b()}}function f(){this.$backdrop.remove();this.$backdrop=null}function g(){var b=this;if(this.isShown&&this.options.keyboard){a(document).on("keyup.dismiss.modal",function(a){a.which==27&&b.hide()})}else if(!this.isShown){a(document).off("keyup.dismiss.modal")}}"use strict";var b=function(b,c){this.options=c;this.$element=a(b).delegate('[data-dismiss="modal"]',"click.dismiss.modal",a.proxy(this.hide,this))};b.prototype={constructor:b,toggle:function(){return this[!this.isShown?"show":"hide"]()},show:function(){var b=this,c=a.Event("show");this.$element.trigger(c);if(this.isShown||c.isDefaultPrevented())return;a("body").addClass("modal-open");this.isShown=true;g.call(this);e.call(this,function(){var c=a.support.transition&&b.$element.hasClass("fade");if(!b.$element.parent().length){b.$element.appendTo(document.body)}b.$element.show();if(c){b.$element[0].offsetWidth}b.$element.addClass("in");c?b.$element.one(a.support.transition.end,function(){b.$element.trigger("shown")}):b.$element.trigger("shown")})},hide:function(b){b&&b.preventDefault();var e=this;b=a.Event("hide");this.$element.trigger(b);if(!this.isShown||b.isDefaultPrevented())return;this.isShown=false;a("body").removeClass("modal-open");g.call(this);this.$element.removeClass("in");a.support.transition&&this.$element.hasClass("fade")?c.call(this):d.call(this)}};a.fn.modal=function(c){return this.each(function(){var d=a(this),e=d.data("modal"),f=a.extend({},a.fn.modal.defaults,d.data(),typeof c=="object"&&c);if(!e)d.data("modal",e=new b(this,f));if(typeof c=="string")e[c]();else if(f.show)e.show()})};a.fn.modal.defaults={backdrop:true,keyboard:true,show:true};a.fn.modal.Constructor=b;a(function(){a("body").on("click.modal.data-api",'[data-toggle="modal"]',function(b){var c=a(this),d,e=a(c.attr("data-target")||(d=c.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,"")),f=e.data("modal")?"toggle":a.extend({},e.data(),c.data());b.preventDefault();e.modal(f)})})}(window.jQuery);!function(a){"use strict";var b=function(a,b){this.init("tooltip",a,b)};b.prototype={constructor:b,init:function(b,c,d){var e,f;this.type=b;this.$element=a(c);this.options=this.getOptions(d);this.enabled=true;if(this.options.trigger!="manual"){e=this.options.trigger=="hover"?"mouseenter":"focus";f=this.options.trigger=="hover"?"mouseleave":"blur";this.$element.on(e,this.options.selector,a.proxy(this.enter,this));this.$element.on(f,this.options.selector,a.proxy(this.leave,this))}this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},getOptions:function(b){b=a.extend({},a.fn[this.type].defaults,b,this.$element.data());if(b.delay&&typeof b.delay=="number"){b.delay={show:b.delay,hide:b.delay}}return b},enter:function(b){var c=a(b.currentTarget)[this.type](this._options).data(this.type);if(!c.options.delay||!c.options.delay.show)return c.show();clearTimeout(this.timeout);c.hoverState="in";this.timeout=setTimeout(function(){if(c.hoverState=="in")c.show()},c.options.delay.show)},leave:function(b){var c=a(b.currentTarget)[this.type](this._options).data(this.type);if(this.timeout)clearTimeout(this.timeout);if(!c.options.delay||!c.options.delay.hide)return c.hide();c.hoverState="out";this.timeout=setTimeout(function(){if(c.hoverState=="out")c.hide()},c.options.delay.hide)},show:function(){var a,b,c,d,e,f,g;if(this.hasContent()&&this.enabled){a=this.tip();this.setContent();if(this.options.animation){a.addClass("fade")}f=typeof this.options.placement=="function"?this.options.placement.call(this,a[0],this.$element[0]):this.options.placement;b=/in/.test(f);a.remove().css({top:0,left:0,display:"block"}).appendTo(b?this.$element:document.body);c=this.getPosition(b);d=a[0].offsetWidth;e=a[0].offsetHeight;switch(b?f.split(" ")[1]:f){case"bottom":g={top:c.top+c.height,left:c.left+c.width/2-d/2};break;case"top":g={top:c.top-e,left:c.left+c.width/2-d/2};break;case"left":g={top:c.top+c.height/2-e/2,left:c.left-d};break;case"right":g={top:c.top+c.height/2-e/2,left:c.left+c.width};break}a.css(g).addClass(f).addClass("in")}},isHTML:function(a){return typeof a!="string"||a.charAt(0)==="<"&&a.charAt(a.length-1)===">"&&a.length>=3||/^(?:[^<]*<[\w\W]+>[^>]*$)/.exec(a)},setContent:function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.isHTML(b)?"html":"text"](b);a.removeClass("fade in top bottom left right")},hide:function(){function d(){var b=setTimeout(function(){c.off(a.support.transition.end).remove()},500);c.one(a.support.transition.end,function(){clearTimeout(b);c.remove()})}var b=this,c=this.tip();c.removeClass("in");a.support.transition&&this.$tip.hasClass("fade")?d():c.remove()},fixTitle:function(){var a=this.$element;if(a.attr("title")||typeof a.attr("data-original-title")!="string"){a.attr("data-original-title",a.attr("title")||"").removeAttr("title")}},hasContent:function(){return this.getTitle()},getPosition:function(b){return a.extend({},b?{top:0,left:0}:this.$element.offset(),{width:this.$element[0].offsetWidth,height:this.$element[0].offsetHeight})},getTitle:function(){var a,b=this.$element,c=this.options;a=b.attr("data-original-title")||(typeof c.title=="function"?c.title.call(b[0]):c.title);return a},tip:function(){return this.$tip=this.$tip||a(this.options.template)},validate:function(){if(!this.$element[0].parentNode){this.hide();this.$element=null;this.options=null}},enable:function(){this.enabled=true},disable:function(){this.enabled=false},toggleEnabled:function(){this.enabled=!this.enabled},toggle:function(){this[this.tip().hasClass("in")?"hide":"show"]()}};a.fn.tooltip=function(c){return this.each(function(){var d=a(this),e=d.data("tooltip"),f=typeof c=="object"&&c;if(!e)d.data("tooltip",e=new b(this,f));if(typeof c=="string")e[c]()})};a.fn.tooltip.Constructor=b;a.fn.tooltip.defaults={animation:true,placement:"top",selector:false,template:'<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover",title:"",delay:0}}(window.jQuery);!function(a){"use strict";var b=function(a,b){this.init("popover",a,b)};b.prototype=a.extend({},a.fn.tooltip.Constructor.prototype,{constructor:b,setContent:function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.isHTML(b)?"html":"text"](b);a.find(".popover-content > *")[this.isHTML(c)?"html":"text"](c);a.removeClass("fade top bottom left right in")},hasContent:function(){return this.getTitle()||this.getContent()},getContent:function(){var a,b=this.$element,c=this.options;a=b.attr("data-content")||(typeof c.content=="function"?c.content.call(b[0]):c.content);return a},tip:function(){if(!this.$tip){this.$tip=a(this.options.template)}return this.$tip}});a.fn.popover=function(c){return this.each(function(){var d=a(this),e=d.data("popover"),f=typeof c=="object"&&c;if(!e)d.data("popover",e=new b(this,f));if(typeof c=="string")e[c]()})};a.fn.popover.Constructor=b;a.fn.popover.defaults=a.extend({},a.fn.tooltip.defaults,{placement:"right",content:"",template:'<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'})}(window.jQuery);!function(a){function b(b,c){var d=a.proxy(this.process,this),e=a(b).is("body")?a(window):a(b),f;this.options=a.extend({},a.fn.scrollspy.defaults,c);this.$scrollElement=e.on("scroll.scroll.data-api",d);this.selector=(this.options.target||(f=a(b).attr("href"))&&f.replace(/.*(?=#[^\s]+$)/,"")||"")+" .nav li > a";this.$body=a("body");this.refresh();this.process()}"use strict";b.prototype={constructor:b,refresh:function(){var b=this,c;this.offsets=a([]);this.targets=a([]);c=this.$body.find(this.selector).map(function(){var b=a(this),c=b.data("target")||b.attr("href"),d=/^#\w/.test(c)&&a(c);return d&&c.length&&[[d.position().top,c]]||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){b.offsets.push(this[0]);b.targets.push(this[1])})},process:function(){var a=this.$scrollElement.scrollTop()+this.options.offset,b=this.$scrollElement[0].scrollHeight||this.$body[0].scrollHeight,c=b-this.$scrollElement.height(),d=this.offsets,e=this.targets,f=this.activeTarget,g;if(a>=c){return f!=(g=e.last()[0])&&this.activate(g)}for(g=d.length;g--;){f!=e[g]&&a>=d[g]&&(!d[g+1]||a<=d[g+1])&&this.activate(e[g])}},activate:function(b){var c,d;this.activeTarget=b;a(this.selector).parent(".active").removeClass("active");d=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]';c=a(d).parent("li").addClass("active");if(c.parent(".dropdown-menu")){c=c.closest("li.dropdown").addClass("active")}c.trigger("activate")}};a.fn.scrollspy=function(c){return this.each(function(){var d=a(this),e=d.data("scrollspy"),f=typeof c=="object"&&c;if(!e)d.data("scrollspy",e=new b(this,f));if(typeof c=="string")e[c]()})};a.fn.scrollspy.Constructor=b;a.fn.scrollspy.defaults={offset:10};a(function(){a('[data-spy="scroll"]').each(function(){var b=a(this);b.scrollspy(b.data())})})}(window.jQuery);!function(a){"use strict";var b=function(b){this.element=a(b)};b.prototype={constructor:b,show:function(){var b=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.attr("data-target"),e,f,g;if(!d){d=b.attr("href");d=d&&d.replace(/.*(?=#[^\s]*$)/,"")}if(b.parent("li").hasClass("active"))return;e=c.find(".active a").last()[0];g=a.Event("show",{relatedTarget:e});b.trigger(g);if(g.isDefaultPrevented())return;f=a(d);this.activate(b.parent("li"),c);this.activate(f,f.parent(),function(){b.trigger({type:"shown",relatedTarget:e})})},activate:function(b,c,d){function g(){e.removeClass("active").find("> .dropdown-menu > .active").removeClass("active");b.addClass("active");if(f){b[0].offsetWidth;b.addClass("in")}else{b.removeClass("fade")}if(b.parent(".dropdown-menu")){b.closest("li.dropdown").addClass("active")}d&&d()}var e=c.find("> .active"),f=d&&a.support.transition&&e.hasClass("fade");f?e.one(a.support.transition.end,g):g();e.removeClass("in")}};a.fn.tab=function(c){return this.each(function(){var d=a(this),e=d.data("tab");if(!e)d.data("tab",e=new b(this));if(typeof c=="string")e[c]()})};a.fn.tab.Constructor=b;a(function(){a("body").on("click.tab.data-api",'[data-toggle="tab"], [data-toggle="pill"]',function(b){b.preventDefault();a(this).tab("show")})})}(window.jQuery);!function(a){"use strict";var b=function(b,c){this.$element=a(b);this.options=a.extend({},a.fn.typeahead.defaults,c);this.matcher=this.options.matcher||this.matcher;this.sorter=this.options.sorter||this.sorter;this.highlighter=this.options.highlighter||this.highlighter;this.updater=this.options.updater||this.updater;this.$menu=a(this.options.menu).appendTo("body");this.source=this.options.source;this.shown=false;this.listen()};b.prototype={constructor:b,select:function(){var a=this.$menu.find(".active").attr("data-value");this.$element.val(this.updater(a)).change();return this.hide()},updater:function(a){return a},show:function(){var b=a.extend({},this.$element.offset(),{height:this.$element[0].offsetHeight});this.$menu.css({top:b.top+b.height,left:b.left});this.$menu.show();this.shown=true;return this},hide:function(){this.$menu.hide();this.shown=false;return this},lookup:function(b){var c=this,d,e;this.query=this.$element.val();if(!this.query){return this.shown?this.hide():this}d=a.grep(this.source,function(a){return c.matcher(a)});d=this.sorter(d);if(!d.length){return this.shown?this.hide():this}return this.render(d.slice(0,this.options.items)).show()},matcher:function(a){return~a.toLowerCase().indexOf(this.query.toLowerCase())},sorter:function(a){var b=[],c=[],d=[],e;while(e=a.shift()){if(!e.toLowerCase().indexOf(this.query.toLowerCase()))b.push(e);else if(~e.indexOf(this.query))c.push(e);else d.push(e)}return b.concat(c,d)},highlighter:function(a){var b=this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&");return a.replace(new RegExp("("+b+")","ig"),function(a,b){return"<strong>"+b+"</strong>"})},render:function(b){var c=this;b=a(b).map(function(b,d){b=a(c.options.item).attr("data-value",d);b.find("a").html(c.highlighter(d));return b[0]});b.first().addClass("active");this.$menu.html(b);return this},next:function(b){var c=this.$menu.find(".active").removeClass("active"),d=c.next();if(!d.length){d=a(this.$menu.find("li")[0])}d.addClass("active")},prev:function(a){var b=this.$menu.find(".active").removeClass("active"),c=b.prev();if(!c.length){c=this.$menu.find("li").last()}c.addClass("active")},listen:function(){this.$element.on("blur",a.proxy(this.blur,this)).on("keypress",a.proxy(this.keypress,this)).on("keyup",a.proxy(this.keyup,this));if(a.browser.webkit||a.browser.msie){this.$element.on("keydown",a.proxy(this.keypress,this))}this.$menu.on("click",a.proxy(this.click,this)).on("mouseenter","li",a.proxy(this.mouseenter,this))},keyup:function(a){switch(a.keyCode){case 40:case 38:break;case 9:case 13:if(!this.shown)return;this.select();break;case 27:if(!this.shown)return;this.hide();break;default:this.lookup()}a.stopPropagation();a.preventDefault()},keypress:function(a){if(!this.shown)return;switch(a.keyCode){case 9:case 13:case 27:a.preventDefault();break;case 38:if(a.type!="keydown")break;a.preventDefault();this.prev();break;case 40:if(a.type!="keydown")break;a.preventDefault();this.next();break}a.stopPropagation()},blur:function(a){var b=this;setTimeout(function(){b.hide()},150)},click:function(a){a.stopPropagation();a.preventDefault();this.select()},mouseenter:function(b){this.$menu.find(".active").removeClass("active");a(b.currentTarget).addClass("active")}};a.fn.typeahead=function(c){return this.each(function(){var d=a(this),e=d.data("typeahead"),f=typeof c=="object"&&c;if(!e)d.data("typeahead",e=new b(this,f));if(typeof c=="string")e[c]()})};a.fn.typeahead.defaults={source:[],items:8,menu:'<ul class="typeahead dropdown-menu"></ul>',item:'<li><a href="#"></a></li>'};a.fn.typeahead.Constructor=b;a(function(){a("body").on("focus.typeahead.data-api",'[data-provide="typeahead"]',function(b){var c=a(this);if(c.data("typeahead"))return;b.preventDefault();c.typeahead(c.data())})})}(window.jQuery)


/* =========================================================
 * bootstrap-datepicker.js 
 * http://www.eyecon.ro/bootstrap-datepicker
 * =========================================================
 * Copyright 2012 Stefan Petre
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */
 
!function( $ ) {
	
	// Picker object
	
	var Datepicker = function(element, options){
		this.element = $(element);
		this.format = DPGlobal.parseFormat(options.format||this.element.data('date-format')||'mm/dd/yyyy');
		this.picker = $(DPGlobal.template)
							.appendTo('body')
							.on({
								click: $.proxy(this.click, this)//,
								//mousedown: $.proxy(this.mousedown, this)
							});
		this.isInput = this.element.is('input');
		this.component = this.element.is('.date') ? this.element.find('.add-on') : false;
		
		if (this.isInput) {
			this.element.on({
				focus: $.proxy(this.show, this),
				//blur: $.proxy(this.hide, this),
				keyup: $.proxy(this.update, this)
			});
		} else {
			if (this.component){
				this.component.on('click', $.proxy(this.show, this));
			} else {
				this.element.on('click', $.proxy(this.show, this));
			}
		}
	
		this.minViewMode = options.minViewMode||this.element.data('date-minviewmode')||0;
		if (typeof this.minViewMode === 'string') {
			switch (this.minViewMode) {
				case 'months':
					this.minViewMode = 1;
					break;
				case 'years':
					this.minViewMode = 2;
					break;
				default:
					this.minViewMode = 0;
					break;
			}
		}
		this.viewMode = options.viewMode||this.element.data('date-viewmode')||0;
		if (typeof this.viewMode === 'string') {
			switch (this.viewMode) {
				case 'months':
					this.viewMode = 1;
					break;
				case 'years':
					this.viewMode = 2;
					break;
				default:
					this.viewMode = 0;
					break;
			}
		}
		this.startViewMode = this.viewMode;
		this.weekStart = options.weekStart||this.element.data('date-weekstart')||0;
		this.weekEnd = this.weekStart === 0 ? 6 : this.weekStart - 1;
		this.onRender = options.onRender;
		this.fillDow();
		this.fillMonths();
		this.update();
		this.showMode();
	};
	
	Datepicker.prototype = {
		constructor: Datepicker,
		
		show: function(e) {
			this.picker.show();
			this.height = this.component ? this.component.outerHeight() : this.element.outerHeight();
			this.place();
			$(window).on('resize', $.proxy(this.place, this));
			if (e ) {
				e.stopPropagation();
				e.preventDefault();
			}
			if (!this.isInput) {
			}
			var that = this;
			$(document).on('mousedown', function(ev){
				if ($(ev.target).closest('.datepicker').length == 0) {
					that.hide();
				}
			});
			this.element.trigger({
				type: 'show',
				date: this.date
			});
		},
		
		hide: function(){
			this.picker.hide();
			$(window).off('resize', this.place);
			this.viewMode = this.startViewMode;
			this.showMode();
			if (!this.isInput) {
				$(document).off('mousedown', this.hide);
			}
			//this.set();
			this.element.trigger({
				type: 'hide',
				date: this.date
			});
		},
		
		set: function() {
			var formated = DPGlobal.formatDate(this.date, this.format);
			if (!this.isInput) {
				if (this.component){
					this.element.find('input').prop('value', formated);
				}
				this.element.data('date', formated);
			} else {
				this.element.prop('value', formated);
			}
		},
		
		setValue: function(newDate) {
			if (typeof newDate === 'string') {
				this.date = DPGlobal.parseDate(newDate, this.format);
			} else {
				this.date = new Date(newDate);
			}
			this.set();
			this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
			this.fill();
		},
		
		place: function(){
			var offset = this.component ? this.component.offset() : this.element.offset();
			this.picker.css({
				top: offset.top + this.height,
				left: offset.left
			});
		},
		
		update: function(newDate){
			this.date = DPGlobal.parseDate(
				typeof newDate === 'string' ? newDate : (this.isInput ? this.element.prop('value') : this.element.data('date')),
				this.format
			);
			this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
			this.fill();
		},
		
		fillDow: function(){
			var dowCnt = this.weekStart;
			var html = '<tr>';
			while (dowCnt < this.weekStart + 7) {
				html += '<th class="dow">'+DPGlobal.dates.daysMin[(dowCnt++)%7]+'</th>';
			}
			html += '</tr>';
			this.picker.find('.datepicker-days thead').append(html);
		},
		
		fillMonths: function(){
			var html = '';
			var i = 0
			while (i < 12) {
				html += '<span class="month">'+DPGlobal.dates.monthsShort[i++]+'</span>';
			}
			this.picker.find('.datepicker-months td').append(html);
		},
		
		fill: function() {
			var d = new Date(this.viewDate),
				year = d.getFullYear(),
				month = d.getMonth(),
				currentDate = this.date.valueOf();
			this.picker.find('.datepicker-days th:eq(1)')
						.text(DPGlobal.dates.months[month]+' '+year);
			var prevMonth = new Date(year, month-1, 28,0,0,0,0),
				day = DPGlobal.getDaysInMonth(prevMonth.getFullYear(), prevMonth.getMonth());
			prevMonth.setDate(day);
			prevMonth.setDate(day - (prevMonth.getDay() - this.weekStart + 7)%7);
			var nextMonth = new Date(prevMonth);
			nextMonth.setDate(nextMonth.getDate() + 42);
			nextMonth = nextMonth.valueOf();
			var html = [];
			var clsName,
				prevY,
				prevM;
			while(prevMonth.valueOf() < nextMonth) {
				if (prevMonth.getDay() === this.weekStart) {
					html.push('<tr>');
				}
				clsName = this.onRender(prevMonth);
				prevY = prevMonth.getFullYear();
				prevM = prevMonth.getMonth();
				if ((prevM < month &&  prevY === year) ||  prevY < year) {
					clsName += ' old';
				} else if ((prevM > month && prevY === year) || prevY > year) {
					clsName += ' new';
				}
				if (prevMonth.valueOf() === currentDate) {
					clsName += ' active';
				}
				html.push('<td class="day '+clsName+'">'+prevMonth.getDate() + '</td>');
				if (prevMonth.getDay() === this.weekEnd) {
					html.push('</tr>');
				}
				prevMonth.setDate(prevMonth.getDate()+1);
			}
			this.picker.find('.datepicker-days tbody').empty().append(html.join(''));
			var currentYear = this.date.getFullYear();
			
			var months = this.picker.find('.datepicker-months')
						.find('th:eq(1)')
							.text(year)
							.end()
						.find('span').removeClass('active');
			if (currentYear === year) {
				months.eq(this.date.getMonth()).addClass('active');
			}
			
			html = '';
			year = parseInt(year/10, 10) * 10;
			var yearCont = this.picker.find('.datepicker-years')
								.find('th:eq(1)')
									.text(year + '-' + (year + 9))
									.end()
								.find('td');
			year -= 1;
			for (var i = -1; i < 11; i++) {
				html += '<span class="year'+(i === -1 || i === 10 ? ' old' : '')+(currentYear === year ? ' active' : '')+'">'+year+'</span>';
				year += 1;
			}
			yearCont.html(html);
		},
		
		click: function(e) {
			e.stopPropagation();
			e.preventDefault();
			var target = $(e.target).closest('span, td, th');
			if (target.length === 1) {
				switch(target[0].nodeName.toLowerCase()) {
					case 'th':
						switch(target[0].className) {
							case 'switch':
								this.showMode(1);
								break;
							case 'prev':
							case 'next':
								this.viewDate['set'+DPGlobal.modes[this.viewMode].navFnc].call(
									this.viewDate,
									this.viewDate['get'+DPGlobal.modes[this.viewMode].navFnc].call(this.viewDate) + 
									DPGlobal.modes[this.viewMode].navStep * (target[0].className === 'prev' ? -1 : 1)
								);
								this.fill();
								this.set();
								break;
						}
						break;
					case 'span':
						if (target.is('.month')) {
							var month = target.parent().find('span').index(target);
							this.viewDate.setMonth(month);
						} else {
							var year = parseInt(target.text(), 10)||0;
							this.viewDate.setFullYear(year);
						}
						if (this.viewMode !== 0) {
							this.date = new Date(this.viewDate);
							this.element.trigger({
								type: 'changeDate',
								date: this.date,
								viewMode: DPGlobal.modes[this.viewMode].clsName
							});
						}
						this.showMode(-1);
						this.fill();
						this.set();
						break;
					case 'td':
						if (target.is('.day') && !target.is('.disabled')){
							var day = parseInt(target.text(), 10)||1;
							var month = this.viewDate.getMonth();
							if (target.is('.old')) {
								month -= 1;
							} else if (target.is('.new')) {
								month += 1;
							}
							var year = this.viewDate.getFullYear();
							this.date = new Date(year, month, day,0,0,0,0);
							this.viewDate = new Date(year, month, Math.min(28, day),0,0,0,0);
							this.fill();
							this.set();
							this.element.trigger({
								type: 'changeDate',
								date: this.date,
								viewMode: DPGlobal.modes[this.viewMode].clsName
							});
						}
						break;
				}
			}
		},
		
		mousedown: function(e){
			e.stopPropagation();
			e.preventDefault();
		},
		
		showMode: function(dir) {
			if (dir) {
				this.viewMode = Math.max(this.minViewMode, Math.min(2, this.viewMode + dir));
			}
			this.picker.find('>div').hide().filter('.datepicker-'+DPGlobal.modes[this.viewMode].clsName).show();
		}
	};
	
	$.fn.datepicker = function ( option, val ) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data('datepicker'),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data('datepicker', (data = new Datepicker(this, $.extend({}, $.fn.datepicker.defaults,options))));
			}
			if (typeof option === 'string') data[option](val);
		});
	};

	$.fn.datepicker.defaults = {
		onRender: function(date) {
			return '';
		}
	};
	$.fn.datepicker.Constructor = Datepicker;
	
	var DPGlobal = {
		modes: [
			{
				clsName: 'days',
				navFnc: 'Month',
				navStep: 1
			},
			{
				clsName: 'months',
				navFnc: 'FullYear',
				navStep: 1
			},
			{
				clsName: 'years',
				navFnc: 'FullYear',
				navStep: 10
		}],
		dates:{
			days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
			daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
			daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
			months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
			monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
		},
		isLeapYear: function (year) {
			return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0))
		},
		getDaysInMonth: function (year, month) {
			return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month]
		},
		parseFormat: function(format){
			var separator = format.match(/[.\/\-\s].*?/),
				parts = format.split(/\W+/);
			if (!separator || !parts || parts.length === 0){
				throw new Error("Invalid date format.");
			}
			return {separator: separator, parts: parts};
		},
		parseDate: function(date, format) {
			var parts = date.split(format.separator),
				date = new Date(),
				val;
			date.setHours(0);
			date.setMinutes(0);
			date.setSeconds(0);
			date.setMilliseconds(0);
			if (parts.length === format.parts.length) {
				var year = date.getFullYear(), day = date.getDate(), month = date.getMonth();
				for (var i=0, cnt = format.parts.length; i < cnt; i++) {
					val = parseInt(parts[i], 10)||1;
					switch(format.parts[i]) {
						case 'dd':
						case 'd':
							day = val;
							date.setDate(val);
							break;
						case 'mm':
						case 'm':
							month = val - 1;
							date.setMonth(val - 1);
							break;
						case 'yy':
							year = 2000 + val;
							date.setFullYear(2000 + val);
							break;
						case 'yyyy':
							year = val;
							date.setFullYear(val);
							break;
					}
				}
				date = new Date(year, month, day, 0 ,0 ,0);
			}
			return date;
		},
		formatDate: function(date, format){
			var val = {
				d: date.getDate(),
				m: date.getMonth() + 1,
				yy: date.getFullYear().toString().substring(2),
				yyyy: date.getFullYear()
			};
			val.dd = (val.d < 10 ? '0' : '') + val.d;
			val.mm = (val.m < 10 ? '0' : '') + val.m;
			var date = [];
			for (var i=0, cnt = format.parts.length; i < cnt; i++) {
				date.push(val[format.parts[i]]);
			}
			return date.join(format.separator);
		},
		headTemplate: '<thead>'+
							'<tr>'+
								'<th class="prev">&lsaquo;</th>'+
								'<th colspan="5" class="switch"></th>'+
								'<th class="next">&rsaquo;</th>'+
							'</tr>'+
						'</thead>',
		contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>'
	};
	DPGlobal.template = '<div class="datepicker dropdown-menu">'+
							'<div class="datepicker-days">'+
								'<table class=" table-condensed">'+
									DPGlobal.headTemplate+
									'<tbody></tbody>'+
								'</table>'+
							'</div>'+
							'<div class="datepicker-months">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
								'</table>'+
							'</div>'+
							'<div class="datepicker-years">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
								'</table>'+
							'</div>'+
						'</div>';

}( window.jQuery );