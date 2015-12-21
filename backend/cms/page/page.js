/**
 * Created by csibi on 2014.10.15..
 */
page = function(caller) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.models = this.instance();
    this.pageproperties = {};
    this.panels = {};
    this.current_model = false;
    this.helper = helper.page;

    this.panel = $("#routerunner-panel");
    this.modelpanel = $("#routerunner-model-panel");
    //this.content = $(".routerunner-content");
    this.content = $(".content-iframe");

    this.panel_position = {
        "panel_left": 0,
        "panel_width": 400,
        "panel_visible": 50,
        "page_width": 960,
        "view_width": $(window).width(),
        "frame_height": $(".page-header").height() + $(".page-footer").height()
    };
    this.min_panel_width = 510;
    this.panel_header = 50;
    this.delay_close = 500;
    this.resize_timer = false;
    this.resize_delay = 0;
    this.is_resized = false;

    this.init = function () {
        var self = this;

        this.bind_events();

        this.page_init(true);

        self.pageproperties = self.instance("pageproperties",
            new pageproperties(self, "#routerunner-pageproperties-panel"), "page");

        routerunner.component("cms", "model", false, "model");
        routerunner.component("cms", "modelpanel", false, "model");
        routerunner.component("cms", "input", false, "model");
        routerunner.component("cms", "changed", false, "model");
        routerunner.component("cms", "fault", false, "model");

        self.content.height($(window).height() - self.panel_position.frame_height);
    };
    this.update_params = function() {
        if (routerunner.settings.container_width != undefined && !isNaN(parseInt(routerunner.settings.container_width))) {
            this.panel_position.page_width = routerunner.settings.container_width;
        } else if (this.content.find(".container").length) {
            this.panel_position.page_width = this.content.find(".container").outerWidth(true);
        }
        if (routerunner.settings.panel_width != undefined && !isNaN(parseInt(routerunner.settings.panel_width))) {
            this.min_panel_width = routerunner.settings.panel_width;
        }
    };

    this.page_init = function(first) {
        var self = this;

        this.update_params();

        // load components & attach models
        var attached = {};
        var models_to_attach = routerunner.get("models_to_attach");
        var fn = function(model) {
            routerunner.components.panel.instance("modelselector").add(model, false);

            this.state(routerunner.state());
        };
        if (first || typeof model == "undefined") {
            routerunner.queue(function () {
                self.model_attacher(models_to_attach, fn);
            }, "model.loaded");
        } else {
            self.model_attacher(models_to_attach, fn);
        }
    };

    this.browse = function() {
        this.content.removeClass("panel-open");
        $.each(CKEDITOR.instances, function() {
            if ($(this.element.$).closest("body").length) {
                try {
                    this.setReadOnly(true);
                } catch (err) {
                    console.log("ckeditor setReadOnly=true halted", err);
                }
            }
        });
        $("body > [id^='cke_toolbar']").hide();
        $("body > .modal").hide();
        $("body > .select2-container").hide();

        this.resize();
    };

    this.edit = function() {
        this.content.addClass("panel-open");
        $.each(CKEDITOR.instances, function() {
            if ($(this.element.$).closest("body").length && this.editable()) {
                try {
                    this.setReadOnly(false);
                } catch (err) {
                    console.log("ckeditor setReadOnly=false halted", err);
                }
            }
        });

        this.resize();
    };

    this.bind_events = function() {
        var self = this;

        $(window).scroll(function() {
            if ($("body").data("last-class") != $("body").attr("class") || !$("body").hasClass("top-fixed")) {
                $("body").data("last-class", $("body").attr("class"));
                var header_visible_height = ($(".page-header-to-fixed").hasClass("fixed")
                    ? $(".page-header-to-fixed").outerHeight()
                    : $(".page-header").outerHeight() - $(window).scrollTop());

                var panel_height = $(window).height() - header_visible_height;
                self.panel.height(panel_height).css("top", header_visible_height + "px");
                var model_panel_height = panel_height - $("#routerunner-modelselector-panel").outerHeight(true);
                $("#routerunner-model-panel").height(model_panel_height);
                $("#routerunner-pageproperties-panel .extended").css("max-height", model_panel_height + "px");
            }
        });

        $(document).on("mouseenter", "#routerunner-panel.open-on-hover", function() {
            $(this).stop(true, true);
            $(this).data("hover", true);
            var min_panel_width = (self.min_panel_width < self.panel_position.view_width)
                ? self.min_panel_width : self.panel_position.view_width;
            var anim = {
                "left": (self.panel_position.view_width - min_panel_width) + "px"
            };
            if ($(this).hasClass("left-side")) {
                anim["left"] = "0px";
            }
            $(this).animate(anim);
        }).on("mouseleave", "#routerunner-panel.open-on-hover", function() {
            var that = this;
            $(this).stop(true, true);
            $(this).data("hover", false);
            setTimeout(function() {
                if (!$(that).data("hover") && !self.panel.hasClass("locked")) {
                    var anim = {
                        "left": (self.panel_position.view_width - self.panel_position.panel_visible) + "px"
                    };
                    if ($(that).hasClass("left-side")) {
                        anim["left"] = (self.panel_position.panel_visible - self.panel_position.panel_width) + "px";
                    }
                    $(that).animate(anim);
                }
            }, self.delay_close);
        }).on("click", "#routerunner-expand-panel-btn", function() {
            if (self.panel.hasClass("expanded")) {
                self.panel.removeClass("expanded");
                $(this).children("span.fa").removeClass("fa-compress").addClass("fa-expand");
                $(this).addClass("default").removeClass("btn-info");
            } else {
                self.panel.addClass("expanded");
                $(this).children("span.fa").removeClass("fa-expand").addClass("fa-compress");
                $(this).addClass("btn-info").removeClass("default");
            }
            routerunner.components.common.cookie_settings("expanded", self.panel.hasClass("expanded"));
        }).on("click", "#routerunner-lock-panel-btn", function() {
            if (self.panel.hasClass("locked")) {
                self.panel.removeClass("locked");
                $(this).children("span.fa").removeClass("fa-unlock").addClass("fa-lock");
                $(this).addClass("default").removeClass("btn-info");
            } else {
                self.panel.addClass("locked");
                self.content.addClass("panel-open");
                $(this).children("span.fa").removeClass("fa-lock").addClass("fa-unlock");
                $(this).addClass("btn-info").removeClass("default");
            }
            routerunner.components.common.cookie_settings("locked", self.panel.hasClass("locked"));
        }).on("click", "#routerunner-place-panel-btn", function() {
            if (self.panel.hasClass("left-side")) {
                self.panel.removeClass("left-side");
                $(this).children("span.fa").removeClass("fa-arrow-right").addClass("fa-arrow-left");
                $(this).addClass("default").removeClass("btn-info");
                $("body").removeClass("left-sided-panel");
            } else {
                self.panel.addClass("left-side");
                $(this).children("span.fa").removeClass("fa-arrow-left").addClass("fa-arrow-right");
                $(this).addClass("btn-info").removeClass("default");
                $("body").addClass("left-sided-panel");
            }
            routerunner.components.common.cookie_settings("left-side", self.panel.hasClass("left-side"));
            if (self.panel.is(":visible")) {
                self.resize();
            }
        });

        if (routerunner.components.common.cookie_settings("expanded")) {
            $("#routerunner-expand-panel-btn").trigger("click");
        }
        if (routerunner.components.common.cookie_settings("locked")) {
            $("#routerunner-lock-panel-btn").trigger("click");
        }
        if (routerunner.components.common.cookie_settings("left-side")) {
            $("#routerunner-place-panel-btn").trigger("click");
        }
    };

    this.resize = function() {
        var self = this;
        if (this.resize_timer) {
            clearTimeout(this.resize_timer);
        }

        this.resize_timer = setTimeout(function() {
            if (self.content.hasClass("panel-open")) {
                // open editor panel
                self.panel_position.view_width = $(window).width();
                self.panel_position.panel_width = self.panel_position.view_width - self.panel_position.page_width;
                self.panel_position.panel_visible = self.panel_position.panel_width;
                if (self.panel_position.panel_width > self.panel_position.view_width
                    || self.panel_position.panel_width < 0) {
                    self.panel_position.panel_width = self.panel_position.view_width;
                    self.panel_position.panel_visible = 50;
                }
                var min_panel_width = (self.min_panel_width < self.panel_position.view_width)
                    ? self.min_panel_width : self.panel_position.view_width;

                self.panel_header = self.panel_position.panel_width;
                if (self.panel_position.view_width - self.panel_position.page_width < min_panel_width) {
                    self.panel_position.panel_width = min_panel_width;
                    //self.panel_position.panel_visible = self.panel_header;
                    self.panel.addClass("open-on-hover");
                } else {
                    self.panel.removeClass("open-on-hover");
                }
                //this.panel.width(self.panel_position.panel_width);
                var content_w = self.panel_position.page_width;
                var panel_w = self.panel_position.panel_width;
                if (self.panel_position.page_width > self.panel_position.view_width) {
                    content_w = self.panel_position.view_width - 50;
                }
                self.content.width(content_w);
                self.content.height($(window).height() - self.panel_position.frame_height);
                $(window).trigger("scroll");

                var is_locked = function() {
                    if (self.panel.hasClass("locked")) {
                        $("#routerunner-panel").trigger("mouseenter");
                    }
                    $.each(routerunner.page.models, function() {
                        this.action("resize");
                    });
                };

                if (self.panel.hasClass("left-side")) {
                    self.content.css("left", (self.panel_position.view_width - panel_w) + "px");
                    self.panel.removeClass("hidden").animate({
                        "left": "0px"
                    }, 200, is_locked);
                } else {
                    self.content.css("left", "");
                    self.panel.removeClass("hidden").animate({
                        "left": (self.panel_position.view_width - self.panel_position.panel_visible) + "px"
                    }, 200, is_locked);
                }

            } else {
                // close editor panel
                var anim = {"left": "100%"};
                if (self.panel.hasClass("left-side")) {
                    anim = {"left": "-100%"};
                }
                self.panel.animate(anim, 200, function () {
                    self.panel.addClass("hidden");
                    self.content.removeAttr("style");
                    self.content.height($(window).height() - self.panel_position.frame_height);
                });
            }
            self.panel.width(panel_w);
            if ($("#routerunner-panel").length) {
                $("#routerunner-model").height($(window).height() - parseInt($("#routerunner-panel").offset().top)
                    - $("#routerunner-modelselector-panel").outerHeight()
                    - $("#routerunner-model-navbar").outerHeight() - 52);
            }
        }, this.resize_delay);
    };

    this.create_model = function(container, create_params) {
        var self = this;
        create_params.parent = (container.data("parent") ? container.data("parent") :
            (container.data("reference") ? container.data("reference") : 0));

        if (created_elem = self.helper.create_model(create_params)) {
            created_reference = created_elem.reference;

            var to = {
                parent: (container.data("parent") ? container.data("parent") :
                    (container.data("reference") ? container.data("reference") : 0)),
                prev: ((container.data("prev") !== undefined) ? container.data("prev") : 0)
            };
            created_elem.target = {
                elem: container,
                method: (container.data("method") ? container.data("method") : "prepend")
            };


            var child_selector = false;
            if (created_elem.backend_context && created_elem.backend_context.child_selector
                && (child_selector = created_elem.backend_context.child_selector)
                && container.find(child_selector).length) {
                created_elem.target.elem = container.find(child_selector).parent();
            } else if (container.data("child_selector")
                && (child_selector = _.unescape(container.data("child_selector")))
                && container.find(child_selector).length) {
                created_elem.target.elem = container.find(child_selector).parent();
            } else if (container.data("placeto") && container.find(container.data("placeto")).length) {
                created_elem.target.elem = container.find(container.data("placeto"));
            } else {
                created_elem.target.elem = container;
            }

            if (to.prev === null || created_elem.target.method == "append") {
                if (!child_selector) {
                    child_selector = "> *";
                }
                var selected_children = created_elem.target.elem.find(child_selector);
                if (selected_children.length
                    && ((prev_child = selected_children.get().pop()) && $(prev_child).data("reference"))) {
                    to.prev = $(prev_child).data("reference");
                }
            }

            var created_elem_html = created_elem.html;
            if ((created_elem.html_before || created_elem.html_after) &&
                ((child_selector && !created_elem.target.elem.find(child_selector).length) || !child_selector)) {
                    created_elem_html = created_elem.html_before + created_elem_html + created_elem.html_after;
            }
            created_elem_html = $(created_elem_html);

            var createds = routerunner.get("createds");
            createds[created_elem.model.class + "/" + created_elem.model.table_id] = {
                model_class: created_elem.model.class,
                route: created_elem.model.route,
                reference: created_reference,
                table_id: created_elem.model.table_id
            };
            routerunner.set("createds", createds);

            var fn = function(elem){
                elem.position.set(to, true, container);

                elem.just_created = true;
                if (routerunner.state() != "edit") {
                    routerunner.state("edit");
                    elem.state("edit");
                } else {
                    elem.state("edit");
                }
                elem.select();

                routerunner.components.panel.instance("modelselector").add(elem, true);
            };

            var models_to_attach = routerunner.get("models_to_attach");
            attached = self.instance(created_elem.model.backend_ref, new model(self, created_elem_html, fn));
            routerunner.container.models_by_ref[created_reference] = attached;
            delete models_to_attach[created_elem.model.backend_ref];
            routerunner.set("models_to_attach", models_to_attach);
            self.models = self.instance();
        }
    };

    this.model_attacher = function(models_to_attach, fn) {
        var self = this;
        $.each(models_to_attach, function (model_id, to_attach) {
            if (to_attach.length) {
                attached = self.instance(model_id, new model(self, to_attach, fn));
                var reference = $(attached.inline_elem).data("reference");
                if (routerunner.container.models_by_routeref[attached.route + "/" + reference]) {
                    attached = $.extend(routerunner.container.models_by_routeref[attached.route + "/" + reference], attached);
                } else {
                    routerunner.container.models_by_ref[reference] = attached;
                    routerunner.container.models_by_routeref[attached.route + "/" + reference] = attached;
                }
            }

            delete models_to_attach[model_id];
        });
        routerunner.set("models_to_attach", models_to_attach);
        self.models = self.instance();

        routerunner.framework_ready = true;
    };

    this.clear_page = function() {
        var self = this;
        $.each(this.instance(), function(model_id, model) {
            self.instance(model_id, null);
            routerunner.components.panel.instance("modelselector").remove(model_id);
        });
        routerunner.container.models_by_ref = {};
        routerunner.container.models_by_routeref = {};
        if (routerunner.current_modelpanel) {
            routerunner.current_modelpanel.clear();
        }
        $("body > [id^='cke_toolbar']").remove();
        $("body > .modal").remove();
        $("body > .select2-container").remove();

        this.browse();
    };

    this.apply = function () {
        var self = this;

        routerunner.set("apply_finished", false);

        this.pageproperties.label_apply();

        var ck_blurdelay = CKEDITOR.focusManager._.blurDelay;
        CKEDITOR.focusManager._.blurDelay = 0;
        var affected_models_length = 0;

        $.each(routerunner.page.pageproperties.instance(), function(instance_id, pageprop_obj) {
            var label = "pageproperties." + ((pageprop_obj instanceof property) ? "property." : "") + instance_id;
            self.queue(label, "apply_ready");
        });
        routerunner.page.pageproperties.force_blur();

        $.each(routerunner.affected_models, function(_index, model_id) {
            var obj = routerunner.page.models[model_id];
            if (obj && obj.instance() && typeof obj.instance() == "object" && Object.keys(obj.instance()).length) {
                $.each(obj.instance(), function (instance_id, instance_obj) {
                    var label = (obj instanceof pageproperties ? "pageproperties" : obj.id) + "." + ((instance_obj instanceof property) ? "property." : "") + instance_id;
                    self.queue(label, "apply_ready");
                });
                routerunner.set("synchronized", true);
                obj.action("force_blur");
                routerunner.set("synchronized", false);
                affected_models_length++;
            }
        });

        self.helper.delayed_call(function(){
            setTimeout(function() {
                if (routerunner.panel.action.has_error()) {
                    alert("Error in changes!");
                    routerunner.set("apply_finished", "halt");
                } else {
                    var changes = routerunner.get("changes");
                    if (changes) {
                        while (change_label = changes.shift()) {
                            var change = routerunner.common.change_by_label(change_label);
                            if (change) {
                                change.apply();
                            }
                        }
                    }
                    routerunner.set("apply_finished", true);
                }
                CKEDITOR.focusManager._.blurDelay = ck_blurdelay;
            }, 200);
        }, function(){
            return (self.ready("apply_ready") || !affected_models_length);
        }, undefined, undefined, function() {
            console.log("apply unready", self, self.queue());
        });
    };

    this.init();
};