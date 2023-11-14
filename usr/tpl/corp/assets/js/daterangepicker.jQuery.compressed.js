jQuery.fn.daterangepicker = function(s) {
    var c = jQuery(this);
    var d = jQuery.extend({
        presetRanges: [{
            text: "Today",
            dateStart: "today",
            dateEnd: "today"
        }, {
            text: "Last 7 days",
            dateStart: "today-7days",
            dateEnd: "today"
        }, {
            text: "Month to date",
            dateStart: function() {
                return Date.parse("today").moveToFirstDayOfMonth()
            },
            dateEnd: "today"
        }, {
            text: "Year to date",
            dateStart: function() {
                var w = Date.parse("today");
                w.setMonth(0);
                w.setDate(1);
                return w
            },
            dateEnd: "today"
        }, {
            text: "The previous Month",
            dateStart: function() {
                return Date.parse("1 month ago").moveToFirstDayOfMonth()
            },
            dateEnd: function() {
                return Date.parse("1 month ago").moveToLastDayOfMonth()
            }
        }],
        presets: {
            specificDate: "Конкретная дата",
            allDatesBefore: "До определенной даты",
            allDatesAfter: "После определенной даты",
            dateRange: "В диапазоне"
        },
        rangeStartTitle: "Начало",
        rangeEndTitle: "Окончание",
        nextLinkText: "Next",
        prevLinkText: "Prev",
        doneButtonText: "Done",
        earliestDate: Date.parse("-15years"),
        latestDate: Date.parse("+15years"),
        constrainDates: false,
        rangeSplitter: "-",
        dateFormat: "m/d/yy",
        closeOnSelect: true,
        arrows: false,
        appendTo: "body",
        onClose: function() {},
        onOpen: function() {},
        onChange: function() {},
        datepickerOptions: null
    }, s);
    var f = {
        onSelect: function(z, y) {
            if (i.find(".ui-daterangepicker-specificDate").is(".ui-state-active")) {
                i.find(".range-end").datepicker("setDate", i.find(".range-start").datepicker("getDate"))
            }
            $(this).trigger("constrainOtherPicker");
            var x = a(i.find(".range-start").datepicker("getDate"));
            var w = a(i.find(".range-end").datepicker("getDate"));
            if (c.length == 2) {
                c.eq(0).val(x);
                c.eq(1).val(w)
            } else {
                c.val((x != w) ? x + " " + d.rangeSplitter + " " + w : x)
            }
            if (d.closeOnSelect) {
                if (!i.find("li.ui-state-active").is(".ui-daterangepicker-dateRange") && !i.is(":animated")) {
                    j()
                }
            }
            d.onChange()
        },
        defaultDate: +0
    };
    c.bind("change", d.onChange);
    d.datepickerOptions = (s) ? jQuery.extend(f, s.datepickerOptions) : f;
    var l, k = Date.parse("today");
    var n, h;
    if (c.size() == 2) {
        n = Date.parse(c.eq(0).val());
        h = Date.parse(c.eq(1).val());
        if (n == null) {
            n = h
        }
        if (h == null) {
            h = n
        }
    } else {
        n = Date.parse(c.val().split(d.rangeSplitter)[0]);
        h = Date.parse(c.val().split(d.rangeSplitter)[1]);
        if (h == null) {
            h = n
        }
    }
    if (n != null) {
        l = n
    }
    if (h != null) {
        k = h
    }
    var i = jQuery('<div class="ui-daterangepicker ui-widget ui-helper-clearfix ui-widget-content ui-corner-all"></div>');
    var u = (function() {
        var y = jQuery('<ul class="ui-widget-content"></ul>').appendTo(i);
        jQuery.each(d.presetRanges, function() {
            jQuery('<li class="ui-daterangepicker-' + this.text.replace(/ /g, "") + ' ui-corner-all"><a href="#">' + this.text + "</a></li>").data("dateStart", this.dateStart).data("dateEnd", this.dateEnd).appendTo(y)
        });
        var w = 0;
        jQuery.each(d.presets, function(x, z) {
            jQuery('<li class="ui-daterangepicker-' + x + " preset_" + w + ' ui-helper-clearfix ui-corner-all"><span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">' + z + "</a></li>").appendTo(y);
            w++
        });
        y.find("li").hover(function() {
            jQuery(this).addClass("ui-state-hover")
        }, function() {
            jQuery(this).removeClass("ui-state-hover")
        }).click(function() {
            i.find(".ui-state-active").removeClass("ui-state-active");
            jQuery(this).addClass("ui-state-active");
            p(jQuery(this), i, m, e);
            return false
        });
        return y
    })();

    function a(y) {
        if (!y.getDate()) {
            return ""
        }
        var x = y.getDate();
        var A = y.getMonth();
        var z = y.getFullYear();
        A++;
        var w = d.dateFormat;
        return jQuery.datepicker.formatDate(w, y)
    }
    jQuery.fn.restoreDateFromData = function() {
        if (jQuery(this).data("saveDate")) {
            jQuery(this).datepicker("setDate", jQuery(this).data("saveDate")).removeData("saveDate")
        }
        return this
    };
    jQuery.fn.saveDateToData = function() {
        if (!jQuery(this).data("saveDate")) {
            jQuery(this).data("saveDate", jQuery(this).datepicker("getDate"))
        }
        return this
    };

    function t() {
        if (i.data("state") == "closed") {
            v();
            i.fadeIn(300).data("state", "open");
            d.onOpen()
        }
    }

    function j() {
        if (i.data("state") == "open") {
            i.fadeOut(300).data("state", "closed");
            d.onClose()
        }
    }

    function b() {
        if (i.data("state") == "open") {
            j()
        } else {
            t()
        }
    }

    function v() {
        var w = o || c;
        var A = w.offset(),
            y = "left",
            z = A.left,
            x = jQuery(window).width() - z - w.outerWidth();
        if (z > x) {
            y = "right", z = x
        }
        i.parent().css(y, z).css("top", A.top + w.outerHeight())
    }

    function p(z, y, A, w) {
        if (z.is(".ui-daterangepicker-specificDate")) {
            w.hide();
            A.show();
            y.find(".title-start").text(d.presets.specificDate);
            y.find(".range-start").restoreDateFromData().css("opacity", 1).show(400);
            y.find(".range-end").restoreDateFromData().css("opacity", 0).hide(400);
            setTimeout(function() {
                w.fadeIn()
            }, 400)
        } else {
            if (z.is(".ui-daterangepicker-allDatesBefore")) {
                w.hide();
                A.show();
                y.find(".title-end").text(d.presets.allDatesBefore);
                y.find(".range-start").saveDateToData().datepicker("setDate", d.earliestDate).css("opacity", 0).hide(400);
                y.find(".range-end").restoreDateFromData().css("opacity", 1).show(400);
                setTimeout(function() {
                    w.fadeIn()
                }, 400)
            } else {
                if (z.is(".ui-daterangepicker-allDatesAfter")) {
                    w.hide();
                    A.show();
                    y.find(".title-start").text(d.presets.allDatesAfter);
                    y.find(".range-start").restoreDateFromData().css("opacity", 1).show(400);
                    y.find(".range-end").saveDateToData().datepicker("setDate", d.latestDate).css("opacity", 0).hide(400);
                    setTimeout(function() {
                        w.fadeIn()
                    }, 400)
                } else {
                    if (z.is(".ui-daterangepicker-dateRange")) {
                        w.hide();
                        A.show();
                        y.find(".title-start").text(d.rangeStartTitle);
                        y.find(".title-end").text(d.rangeEndTitle);
                        y.find(".range-start").restoreDateFromData().css("opacity", 1).show(400);
                        y.find(".range-end").restoreDateFromData().css("opacity", 1).show(400);
                        setTimeout(function() {
                            w.fadeIn()
                        }, 400)
                    } else {
                        w.hide();
                        y.find(".range-start, .range-end").css("opacity", 0).hide(400, function() {
                            A.hide()
                        });
                        var B = (typeof z.data("dateStart") == "string") ? Date.parse(z.data("dateStart")) : z.data("dateStart")();
                        var x = (typeof z.data("dateEnd") == "string") ? Date.parse(z.data("dateEnd")) : z.data("dateEnd")();
                        y.find(".range-start").datepicker("setDate", B).find(".ui-datepicker-current-day").trigger("click");
                        y.find(".range-end").datepicker("setDate", x).find(".ui-datepicker-current-day").trigger("click")
                    }
                }
            }
        }
        return false
    }
    var m = jQuery('<div class="ranges ui-widget-header ui-corner-all ui-helper-clearfix"><div class="range-start"><span class="title-start">Start Date</span></div><div class="range-end"><span class="title-end">End Date</span></div></div>').appendTo(i);
    m.find(".range-start, .range-end").datepicker(d.datepickerOptions);
    m.find(".range-start").datepicker("setDate", l);
    m.find(".range-end").datepicker("setDate", k);
    m.find(".range-start, .range-end").bind("constrainOtherPicker", function() {
        if (d.constrainDates) {
            if ($(this).is(".range-start")) {
                i.find(".range-end").datepicker("option", "minDate", $(this).datepicker("getDate"))
            } else {
                i.find(".range-start").datepicker("option", "maxDate", $(this).datepicker("getDate"))
            }
        }
    }).trigger("constrainOtherPicker");
    var e = jQuery('<button class="btnDone ui-state-default ui-corner-all">' + d.doneButtonText + "</button>").click(function() {
        i.find(".ui-datepicker-current-day").trigger("click");
        j()
    }).hover(function() {
        jQuery(this).addClass("ui-state-hover")
    }, function() {
        jQuery(this).removeClass("ui-state-hover")
    }).appendTo(m);
    jQuery(this).click(function() {
        b();
        return false
    });
    m.hide().find(".range-start, .range-end, .btnDone").hide();
    i.data("state", "closed");
    m.find(".ui-datepicker").css("display", "block");
    jQuery(d.appendTo).append(i);
    i.wrap('<div class="ui-daterangepickercontain"></div>');
    if (d.arrows && c.size() == 1) {
        var g = jQuery('<a href="#" class="ui-daterangepicker-prev ui-corner-all" title="' + d.prevLinkText + '"><span class="ui-icon ui-icon-circle-triangle-w">' + d.prevLinkText + "</span></a>");
        var q = jQuery('<a href="#" class="ui-daterangepicker-next ui-corner-all" title="' + d.nextLinkText + '"><span class="ui-icon ui-icon-circle-triangle-e">' + d.nextLinkText + "</span></a>");
        jQuery(this).addClass("ui-rangepicker-input ui-widget-content").wrap('<div class="ui-daterangepicker-arrows ui-widget ui-widget-header ui-helper-clearfix ui-corner-all"></div>').before(g).before(q).parent().find("a").click(function() {
            var x = m.find(".range-start").datepicker("getDate");
            var w = m.find(".range-end").datepicker("getDate");
            var y = Math.abs(new TimeSpan(x - w).getTotalMilliseconds()) + 86400000;
            if (jQuery(this).is(".ui-daterangepicker-prev")) {
                y = -y
            }
            m.find(".range-start, .range-end ").each(function() {
                var z = jQuery(this).datepicker("getDate");
                if (z == null) {
                    return false
                }
                jQuery(this).datepicker("setDate", z.add({
                    milliseconds: y
                })).find(".ui-datepicker-current-day").trigger("click")
            });
            return false
        }).hover(function() {
            jQuery(this).addClass("ui-state-hover")
        }, function() {
            jQuery(this).removeClass("ui-state-hover")
        });
        var o = c.parent()
    }
    jQuery(document).click(function() {
        if (i.is(":visible")) {
            j()
        }
    });
    i.click(function() {
        return false
    }).hide();
    return this
};
var TimePeriod = function(n, f, e, q, o, k, g) {
    var m = "years months days hours minutes seconds milliseconds".split(/\s+/);
    var j = function(i) {
        return function() {
            return this[i]
        }
    };
    var b = function(i) {
        return function(v) {
            this[i] = v;
            return this
        }
    };
    for (var p = 0; p < m.length; p++) {
        var d = m[p],
            c = d.slice(0, 1).toUpperCase() + d.slice(1);
        TimePeriod.prototype[d] = 0;
        TimePeriod.prototype["get" + c] = j(d);
        TimePeriod.prototype["set" + c] = b(d)
    }
    if (arguments.length == 7) {
        this.years = n;
        this.months = f;
        this.setDays(e);
        this.setHours(q);
        this.setMinutes(o);
        this.setSeconds(k);
        this.setMilliseconds(g)
    } else {
        if (arguments.length == 2 && arguments[0] instanceof Date && arguments[1] instanceof Date) {
            var u = n.clone();
            var t = f.clone();
            var s = u.clone();
            var h = (u > t) ? -1 : +1;
            this.years = t.getFullYear() - u.getFullYear();
            s.addYears(this.years);
            if (h == +1) {
                if (s > t) {
                    if (this.years !== 0) {
                        this.years--
                    }
                }
            } else {
                if (s < t) {
                    if (this.years !== 0) {
                        this.years++
                    }
                }
            }
            u.addYears(this.years);
            if (h == +1) {
                while (u < t && u.clone().addDays(Date.getDaysInMonth(u.getYear(), u.getMonth())) < t) {
                    u.addMonths(1);
                    this.months++
                }
            } else {
                while (u > t && u.clone().addDays(-u.getDaysInMonth()) > t) {
                    u.addMonths(-1);
                    this.months--
                }
            }
            var l = t - u;
            if (l !== 0) {
                var a = new TimeSpan(l);
                this.setDays(a.getDays());
                this.setHours(a.getHours());
                this.setMinutes(a.getMinutes());
                this.setSeconds(a.getSeconds());
                this.setMilliseconds(a.getMilliseconds())
            }
        }
    }
    return this
};