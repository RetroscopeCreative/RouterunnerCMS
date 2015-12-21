/**
 * Created by csibi on 2014.10.15..
 */
helper.modelpanel = $.extend({}, routerunner.components.helper, {
    loader: '<div class="fa-item model-loading"><span class="fa fa-cog fa-spin"></span> loading...</div>',
    model_panel: "#routerunner-model",
    model_sidebar: "#routerunner-model-navbar",

    panel_content: function(panel, fn) {
        var self = this;

        var div = false;
        if ($(panel.selector).length) {
            div = $(panel.selector);
            div.html("").append($(self.loader));
        } else {
            div = $("<div></div>");
            div.attr("id", panel.id).addClass(panel.class);
            div.append($(self.loader));
        }

        $(self.model_panel).append(div);

        var ajax_data = (panel.content.data ? panel.content.data : {});
        var ajax_params = $.extend((panel.content.params ? panel.content.params : {}), {async: true, type: "post"});
        this.ajax(panel.content.url, ajax_data, ajax_params, function(returned_html) {
            if (returned_html) {
                div.html(returned_html);
                if (!$(panel.menu_selector).closest("#routerunner-model-navbar").is(":visible")) {
                    $(panel.menu_selector).closest("#routerunner-model-navbar").slideDown(200);
                }
                $(panel.menu_selector).children("a").attr("href", "#" + panel.id);
                $(panel.menu_selector).show();
            }
            if (typeof fn == "function") {
                fn(div);
            }
        }, function() {
            // error in panel content
        }, function() {
            // error in panel content
        }, routerunner.xhr);

        return div;
    },

    scrollspied: function(elem) {
        var spied = elem.find(".scrollspied:eq(0)");
        var nav = $(spied.data("target"));
        var offset = (spied.data("offset") ? spied.data("offset") : 0);
        nav.find("ul.nav.navbar-nav li a").bind("click", function(e) {
            e.preventDefault();

            var id = $(this).attr("href");
            var target = spied.find(id);
            spied.animate({ scrollTop: target.position().top + spied.scrollTop() }, 200);
            $(this).closest("ul").find(".active").removeClass("active");
            $(this).closest("li").addClass("active");
            spied.trigger("scroll");
        });
        spied.bind("scroll", function(e) {
            var index = 0;
            var max = nav.find("ul.nav.navbar-nav li").length;
            var naved = nav.find("ul.nav.navbar-nav li:eq(" + index + ") a");
            var section = spied.find(naved.attr("href"));
            var toselect = false;
            while (naved.length) {
                if (section.length && section.position().top > -offset && section.position().top + section.height() < spied.height() + offset) {
                    toselect = naved;
                    index = max;
                }
                index++;
                naved = nav.find("ul.nav.navbar-nav li:eq(" + index + ") a");
                section = spied.find(naved.attr("href"));
            }
            if (!toselect) {
                index = 0;
                while (naved.length && section.length && section.position().top + section.height() < offset) {
                    index++;
                    if (index < max) {
                        naved = nav.find("ul.nav.navbar-nav li:eq(" + index + ") a");
                        section = spied.find(naved.attr("href"));
                    }
                }
                toselect = naved;
            }
            toselect.closest("ul").find(".active").removeClass("active");
            toselect.closest("li").addClass("active");
        });
    }
});