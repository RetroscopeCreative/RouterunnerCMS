/**
 * Created by csibi on 2014.10.17..
 */

action_panel = function(caller, selector) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.selector = selector;
    this.panel = false;
    this.helper = helper.panel;

    this.error_no = 0;
    this.faults = [];

    this.loaded = function() {
        var self = this;

        $(".action-panel .dropdown-submenu").hide();
        $("#action-add-model").bind("click", function(e) {
            if ($(this).data("model")) {
                self.add_model($(this).data("model"));
                if ($(this).closest(".btn-group").find(".dropdown-menu").is(":visible")) {
                    $(".action-panel .custom-dropdown-toggle").trigger("click");
                }
                self.append_buttons();
            } else {
                $("#action-model-selector").trigger("click");
            }
            return false;
        });

        this.init_new_button();

        $(".action-panel").on("click", "#action-mode-edit", function() {
            if (!$(this).is(":disabled")) {
                routerunner.state("edit");
            }
            return false;
        }).on("mousedown", "#action-mode-apply", function() {
            if (!$(this).is(":disabled")) {
                if (document.activeElement) {
                    $(document.activeElement).trigger('blur');
                    $(document.activeElement).blur();
                }

                Metronic.blockUI({
                    target: 'body',
                    animate: true
                });
                routerunner.page.apply();
                self.helper.delayed_call(function() {
                    routerunner.panel.changes.refresh(false, true);

                    Metronic.unblockUI('body');
                    //routerunner.state("browse");
                    if (routerunner.get("apply_finished") === true) {
                        routerunner.refresh();
                    }
                }, function(){
                    return (routerunner.get("apply_finished") === true || routerunner.get("apply_finished") === "halt");
                }, undefined, undefined, function() {
                    console.log("apply unfinished", self);
                });
            }
            return false;
        }).on("mousedown", "#action-mode-revert", function() {
            if (!$(this).is(":disabled")) {
                var changes = routerunner.get("changes");
                if (changes) {
                    while (change_label = changes.pop()) {
                        var change = routerunner.common.change_by_label(change_label);

                        if (change) {
                            change.undo();
                            change.destroy();
                        }
                    }
                    routerunner.panel.changes.refresh(false, true);
                }
                if (changes) {
                    routerunner.refresh();
                } else {
                    routerunner.state("browse");
                }
            }
            return false;
        });
    };

    this.containers = function() {
        var buttons = {};
        $(routerunner.content_document).find(".routerunner-container").each(function() {
            var container = this;
            if ($(this).data("blank")) {
                $.each($(this).data("blank"), function(model_type, model_params) {
                    if (!buttons[model_type]) {
                        buttons[model_type] = {
                            class: model_type,
                            label: ($(container).data(model_type) && $(container).data(model_type)["btn-label"]
                                ? $(container).data(model_type)["btn-label"] : model_type),
                            icon: ($(container).data(model_type) && $(container).data(model_type)["btn-icon"]
                                ? $(container).data(model_type)["btn-icon"] : "fa fa-folder"),
                            places: [container]
                        };
                    } else {
                        buttons[model_type].places.push(container);
                    }
                });
            }
        });
        return buttons;
    };

    this.append_buttons = function() {
        var self = this;
        var buttons = this.containers();
        var ul = $(".action-panel ul.dropdown-menu");
        if (buttons) {
            $.each(buttons, function() {
                var current_button = this;
                var li, a, new_li;

                if (ul.find("li[id='add-" + this.class + "']").length) {
                    li = ul.find("li[id='add-" + this.class + "']");
                    a = li.children("a");
                    new_li = false;
                } else {
                    li = $("<li></li>").data("class", this.class).attr("id", "add-" + this.class);
                    a = $("<a></a>").attr("href", "#");
                    a.html(" " + self.helper.i18n(this.label));
                    var icon = $("<span></span>").addClass(this.icon);
                    a.prepend(icon);
                    li.append(a).data("class", this.class);

                    li.bind("mouseenter click", function () {
                        if ($(this).hasClass("dropdown-submenu")) {
                            var submenu = $(this).children("ul.dropdown-menu");

                            if ($(this).offset().left + $(this).width() + submenu.width() < $(window).width()) {
                                submenu.css({
                                    "left": "100%",
                                    "right": "auto"
                                });
                            } else {
                                submenu.removeAttr("style");
                            }
                        }
                    });

                    a.bind("click", function() {
                        $("#action-add-model").data("model", li);
                        var label = $(this).text();
                        if (li.data("at")) {
                            label += " @ " + li.data("at");
                        }
                        $("#action-btn-label").html(label);

                        if ($(this).parent().data("place")) {
                            $("#action-add-model").trigger("click");
                        }
                        $(".action-panel .dropdown-submenu, .action-panel .dropdown-menu").hide();

                        return false;
                    });

                    new_li = true;
                }


                if (this.places.length > 1) {
                    li.addClass("dropdown-submenu");
                    var ul_places, new_ul;

                    if (li.children("ul.dropdown-menu").length) {
                        ul_places = li.children("ul.dropdown-menu");
                        new_ul = false;
                    } else {
                        ul_places = $("<ul></ul>").addClass("dropdown-menu");
                        new_ul = true;
                    }
                    $.each(this.places, function(index, place){
                        if (!ul_places.children("li[id='add-" + current_button.class + "-" + $(place).data("routerunner-id") + "']").length) {
                            var li_place = $("<li></li>").attr("id", "add-" + current_button.class + "-" + $(place).data("routerunner-id"));
                            var a_place = $("<a></a>").attr("href", "#");
                            if ($(place).data("label") && $(place).data("label").substr(0, 3) == "fn:"
                                && typeof window[$(place).data("label").substr(3)] == "function") {
                                a_place.html(window[$(place).data("label").substr(3)](place));
                            } else if ($(place).data("label")) {
                                a_place.html($(place).data("label"));
                            } else {
                                a_place.html("place #" + index);
                            }
                            if ($(place).data("label-addon")) {
                                a_place.html(a_place.html() + " (" + $(place).data("label-addon") + ")");
                            }
                            a_place.click(function() {
                                li.data("place", place).data("at", $(this).text());
                                a.trigger("click");
                                $(".action-panel .dropdown-submenu, .action-panel .dropdown-menu").hide();
                                return false;
                            });
                            li_place.append(a_place);
                            ul_places.append(li_place);
                        }
                    });
                    if (new_ul) {
                        li.append(ul_places);
                    }
                } else if (this.places.length == 1) {
                    li.data("place", this.places[0]).data("at", false);
                }

                if (new_li) {
                    ul.append(li);
                }
            });
        }
    };

    this.init_new_button = function() {
        var self = this;
        var buttons = this.containers();
        if (buttons) {
            var ul = $(".action-panel ul.dropdown-menu");
            ul.children().remove();
            $.each(buttons, function() {
                var current_button = this;

                var li = $("<li></li>");
                li.data("class", this.class).attr("id", "add-" + this.class);
                var a = $("<a href='#'></a>");
                //a.attr("href", "javascript:;");
                a.html(" " + self.helper.i18n(this.label));
                var icon = $("<span></span>");
                icon.addClass(this.icon);
                a.prepend(icon);
                li.append(a).data("class", this.class);

                li.bind("mouseenter click", function () {
                    if ($(this).hasClass("dropdown-submenu")) {
                        var submenu = $(this).children("ul.dropdown-menu");

                        if ($(this).offset().left + $(this).width() + submenu.width() < $(window).width()) {
                            submenu.css({
                                "left": "100%",
                                "right": "auto"
                                });
                        } else {
                            submenu.removeAttr("style");
                        }
                    }
                });

                a.bind("click", function() {
                    $("#action-add-model").data("model", li);
                    var label = $(this).text();
                    if (li.data("at")) {
                        label += " @ " + li.data("at");
                    }
                    $("#action-btn-label").html(label);

                    if ($(this).parent().data("place")) {
                        $("#action-add-model").trigger("click");
                    }
                    $(".action-panel .dropdown-submenu, .action-panel .dropdown-menu").hide();

                    return false;
                });

                if (this.places.length > 1) {
                    li.addClass("dropdown-submenu");
                    var ul_places = $("<ul></ul>").addClass("dropdown-menu");
                    $.each(this.places, function(index, place){
                        var li_place = $("<li></li>").attr("id", "add-" + current_button.class + "-" + $(place).data("routerunner-id"));
                        var a_place = $("<a></a>").attr("href", "#");
                        if ($(place).data("label") && $(place).data("label").substr(0, 3) == "fn:"
                            && typeof window[$(place).data("label").substr(3)] == "function") {
                            a_place.html(window[$(place).data("label").substr(3)](place));
                        } else if ($(place).data("label")) {
                            a_place.html($(place).data("label"));
                        } else {
                            a_place.html("place #" + index);
                        }
                        if ($(place).data("label-addon")) {
                            a_place.html(a_place.html() + " (" + $(place).data("label-addon") + ")");
                        }
                        a_place.click(function() {
                            li.data("place", place).data("at", $(this).text());
                            a.trigger("click");
                            $(".action-panel .dropdown-submenu, .action-panel .dropdown-menu").hide();
                            return false;
                        });
                        li_place.append(a_place);
                        ul_places.append(li_place);
                    });
                    li.append(ul_places);
                } else if (this.places.length == 1) {
                    li.data("place", this.places[0]).data("at", false);
                }

                ul.append(li);
            });
        }

    };

    this.add_model = function(elem) {
        var self = this;
        //console.log("add_model", $(elem).data());

        var session = routerunner.session();
        //console.log(session);

        var container = $($(elem).data("place"));
        var node_type = $(elem).data("class");
        var created_elem = false;
        var created_reference = false;
        var create_params = {};

        if (container.data(node_type)) {
            create_params = container.data(node_type);
            create_params["class"] = node_type;
            if (container.data("route")) {
                create_params["route"] = container.data("route");
            }
        }

        if (container && create_params && routerunner.page && typeof routerunner.page.create_model == "function") {
            routerunner.page.create_model(container, create_params);
        }
    };

    this.init = function () {
        var self = this;
        this.panel = $(this.selector);
    };

    this.on = function() {
        var self = this;
        // if (routerunner.changes.length) { ?????
        this.panel.slideDown(200);

        $(".action-browse").animate({
            "left": "100%"
        }, 200, function() {
            $(this).css("display", "none");
            $(".action-edit").css({
                "left": "100%",
                "display": "block"
            });
            $(".action-edit").animate({
                "left": "0"
            }, 200);
        });
    };
    this.off = function() {
        var self = this;

        $(".action-edit").animate({
            "left": "100%"
        }, 200, function() {
            $(this).css("display", "none");
            $(".action-browse").css({
                "left": "100%",
                "display": "block"
            });
            $(".action-browse").animate({
                "left": "0"
            }, 200);
        });
    };

    this.browse = function() {
        this.off();
    };
    this.edit = function() {
        if (this.laststate() == "disabled") {
            $("#action-add-model, #action-model-selector, " +
                "#action-mode-apply, #action-mode-revert").attr("disabled", false);
        } else {
            this.on();
        }
    };
    this.disabled = function() {
        $("#action-add-model, #action-model-selector, #action-mode-apply, #action-mode-revert").attr("disabled", true);
    };

    this.changes_waiting = function(is_on) {
        if (is_on && $(".action-changes-waiting").is(":hidden")) {
            $(".action-apply").animate({
                "left": "100%"
            }, 200, function() {
                $(this).css("display", "none");
                $(".action-changes-waiting").css({
                    "left": "100%",
                    "display": "block"
                });
                $(".action-changes-waiting").animate({
                    "left": "0"
                }, 200);
            });
        } else if (!is_on && $(".action-changes-waiting").is(":visible")) {
            $(".action-changes-waiting").animate({
                "left": "100%"
            }, 200, function() {
                $(this).css("display", "none");
                $(".action-apply").css({
                    "left": "100%",
                    "display": "block"
                });
                $(".action-apply").animate({
                    "left": "0"
                }, 200);
            });
        }
    };

    this.error_add = function(fault_object) {
        if (fault_object && ($.inArray(fault_object.reference, this.faults) === -1)) {
            this.error_no++;
            this.faults.push(fault_object.reference);
        } else if (!fault_object) {
            this.error_no++;
        }
        this.has_error(this.error_no);
    };
    this.error_substract = function(fault_object) {
        if (fault_object && ((fault_index = $.inArray(fault_object.reference, this.faults)) > -1)) {
            this.error_no--;
            this.faults.splice(fault_index, 1);
        } else if (!fault_object) {
            this.error_no--;
        }
        this.has_error(this.error_no);
    };

    this.has_error = function(force) {
        var btn = $("#action-mode-changes");
        var no = 0;
        var ret = false;
        if (force === undefined) {
            $.each(routerunner.page.models, function () {
                no += this.get_errors();
            });
            no += routerunner.page.pageproperties.get_errors();
        } else if (!isNaN(parseInt(force))) {
            if (force > 0) {
                no = force;
                force = true;
            } else {
                force = false;
            }
        }
        var label = btn.children(".btn-label");
        if ((force != undefined && force === true) || no > 0) {
            $("#action-mode-apply").attr("disabled", true);
            label.html(label.data("error"));
            btn.removeClass("btn-default").addClass("btn-warning");
            if (btn.children(".badge").length) {
                btn.children(".badge").html(no);
            } else {
                btn.append('<span class="badge badge-danger">' + no + '</span>');
            }
            routerunner.panel.changes.icon_set("error");
            ret = true;
        } else if (no <= 0 || (force != undefined && force === false)) {
            $("#action-mode-apply").attr("disabled", false);
            label.html(label.data("changes"));
            btn.addClass("btn-default").removeClass("btn-warning");
            btn.children(".badge").remove();
            routerunner.panel.changes.icon_set();
        }
        return ret;
    };

    this.init();
};