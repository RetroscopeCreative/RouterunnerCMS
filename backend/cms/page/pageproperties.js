/**
 * Created by csibi on 2014.10.15..
 */
pageproperties = function(caller, selector) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.selector = selector;
    this.panel = false;

    this.states = {
        "revert": false,
        "undo": [],
        "current": {}
    };

    this.form = false;
    this.changes = {};
    this.values = {};
    this.resource = {};
    this.errors = {};
    this.error_counter = {};
    this.field_changes = {};

    this.title_changed_by_key = false;
    this.url_changed_by_key = false;
    this.title_changed_by_label = false;
    this.url_changed_by_label = false;

    this.helper = helper.page;

    this.init = function () {
        var self = this;
        this.panel = $(this.selector).css("left", "-100%");

        if (typeof routerunner_forms != "undefined" && (routerunner_forms.forms
            && routerunner_forms.forms["routerunner-page-properties"]
            && (this.form = routerunner_forms.forms["routerunner-page-properties"]))) {
            $(this.form.form).children(".resource").children("input[type=hidden]").each(function () {
                self.resource[$(this).attr("name")] = $(this).val();
            });

            var _input_base = new baserunner(this);
            _input_base.helper = this.helper;
            $.each(this.form.inputs, function (input_name, input) {
                var input_instance = $.extend(input, _input_base);
                self.instance(input_name, input_instance);
            });

            $(document).on("click", "#page-properties", function () {
                self.toggle(this);
                return false;
            });

            $(".routerunner-page-properties").on("change", ":input.page-title", function () {
                $(".routerunner-page-properties").find(":hidden.page-title").val($(this).val());
            }).on("change", ":input.page-url", function () {
                $(".routerunner-page-properties").find(":hidden.page-url").val($(this).val());
            });

            $(".routerunner-page-properties").find(".pageprop-input").each(function () {
                self.values[$(this).attr("name")] = $(this).val();
            });

            var default_title = '';
            var title_data = $(".routerunner-page-properties").find("#title").
                closest(".input[data-routerunner-input]").data("routerunner-input");
            if (title_data && title_data["default"]) {
                default_title = title_data["default"];
            }
            var default_url = '';
            var url_data = $(".routerunner-page-properties").find("#url").
                closest(".input[data-routerunner-input]").data("routerunner-input");
            if (url_data && url_data["default"]) {
                default_url = url_data["default"];
            }
            var pageprops_title = $(".routerunner-page-properties").find("#title").val();
            var pageprops_url = $(".routerunner-page-properties").find("#url").val();
            if (pageprops_title && pageprops_title !== default_title) {
                this.title_changed_by_key = true;
            }
            if (pageprops_url && pageprops_url !== default_url) {
                this.url_changed_by_key = true;
            }

            $(".routerunner-page-properties").on("keyup", ".title", function () {
                self.title_changed_by_key = true;
            }).on("keyup", ".url", function () {
                self.url_changed_by_key = true;
            });
        }
    };

    this.update = function(loc) {
        var self = this;

        var _input_base = new baserunner(this);
        _input_base.helper = this.helper;

        var url = "RouterunnerCMS/backend/ajax/page/pageproperties.php";
        var data = {
                url: loc,
            };
        var params = {};
        this.helper.ajax(url, data, params, function(returned_html) {
            if (returned_html) {
                $("#routerunner-pageproperties-panel").html(returned_html);

                $("#routerunner-pageproperties-panel .routerunner-form").each(function() {
                    var params = ($(this).data("routerunner-form") ? $(this).data("routerunner-form") : {});
                    var name = ($(this).attr("name") ? $(this).attr("name") : $(this).attr("id"));
                    self.form = new routerunner_form(this, params);

                    routerunner_forms.forms[name] = self.form;
                });

                $.each(self.form.inputs, function (input_name, input) {
                    var input_instance = $.extend(input, _input_base);
                    self.instance(input_name, input_instance);
                });

                var frm = $("#routerunner-pageproperties-panel").find("[name='routerunner-page-properties']");

                frm.children(".resource").children("input[type=hidden]").each(function() {
                    self.resource[$(this).attr("name")] = $(this).val();
                });
                frm.find(".pageprop-input").each(function() {
                    self.values[$(this).attr("name")] = $(this).val();
                });

                var default_title = '';
                var title_data = $(".routerunner-page-properties").find("#title").
                    closest(".input[data-routerunner-input]").data("routerunner-input");
                if (title_data && title_data["default"]) {
                    default_title = title_data["default"];
                }
                var default_url = '';
                var url_data = $(".routerunner-page-properties").find("#url").
                    closest(".input[data-routerunner-input]").data("routerunner-input");
                if (url_data && url_data["default"]) {
                    default_url = url_data["default"];
                }
                var pageprops_title = frm.find("#title").val();
                var pageprops_url = frm.find("#url").val();
                if (pageprops_title && pageprops_title !== default_title) {
                    self.title_changed_by_key = true;
                }
                if (pageprops_url && pageprops_url !== default_url) {
                    self.url_changed_by_key = true;
                }
            }
        }, function() {
            // error in panel content
        }, function() {
            // error in panel content
        }, routerunner.xhr);

    };

    this.force_blur = function() {
        $.each(this.instance(), function(instance_id, instance) {
            var label = "pageproperties." + instance_id
            if ($.isArray(instance)) {
                $(instance).each(function() {
                    if ($(this.input).is(":visible")) {
                        $(this.input).trigger("blur").trigger("focusout").trigger("change");
                    }
                });
            } else {
                $(instance.input).trigger("blur").trigger("focusout").trigger("change");
            }
            routerunner.page.unqueue(label, "apply_ready");
        });
    };

    this.label_apply = function() {
        if (this.title_changed_by_label) {
            $("#title").trigger("change");
        }
        if (this.url_changed_by_label) {
            $("#url").trigger("change");
        }
    };

    this.label_change = function(value) {
        var self = this;
        if (!this.title_changed_by_key) {
            $("#title").val(value);
            this.title_changed_by_label = true;
        }

        if (!this.url_changed_by_key) {
            var url = 'RouterunnerCMS/backend/ajax/common/label2ascii.php';
            var params = {"method": "post", "data": {
                "str": value,
                "reference": this.resource.reference }, "dataType": "json" };
            this.ajax(url, params, function (data) {
                if (data && data.ascii) {
                    var val = data.ascii;
                    $("#url").val(val);
                    self.url_changed_by_label = true;
                }
            });
        }
    };

    this.change = function(input) {
        var self = this;

        if (input.form) {
            this.form = input.form;
        }

        if (!input["create_change"]) {
            input["create_change"] = self.create_change;
            input["helper"] = self.helper;
        }

        this.set_error(input);
        //routerunner.panel.action.has_error();

        if (!this.skip_set_change) {
            var undo = self.values[input.params.field];
            var last_change = false;
            if (self.field_changes[input.params.field]
                && (keys = Object.keys(self.field_changes[input.params.field])).length) {
                last_change = self.changes[keys.pop()];
            }
            var change = input.create_change({ "section": self.form, "field": input.params.field, "value": input.getter(), "undo": undo });
            if (last_change && change.label != last_change.label) {
                change.undo_change[input.params.field] = last_change.changes[input.params.field];
            } else if (last_change && change.label == last_change.label) {
                change.undo_change[input.params.field] = last_change.undo_change[input.params.field];
            }
            if (input.errors) {
                $.each(input.errors, function(label, text) {
                    var error = input.params[label];
                    error.label = label;
                    self.fault_instance(change, error, true);
                });
            }
            var existing_errors = change.instance();
            if (change.caller.last_instance(change.label)) {
                existing_errors = $.extend(change.caller.last_instance(change.label).instance(), existing_errors);
            }
            $.each(existing_errors, function(existing_label, fault_object) {
                if (!input.errors[existing_label]) {
                    self.fault_instance(change, fault_object, false);
                }
            });

            if (!self.field_changes[input.params.field]) {
                self.field_changes[input.params.field] = {};
            }
            self.field_changes[input.params.field][change.label] = change;

            self.changes[change.label] = change;
        }
    };

    this.set_error = function(input) {
        var field = input.params.field;
        if (!this.error_counter[field] || !input.errors) {
            this.error_counter[field] = {};
        }
        if (input.errors) {
            this.error_counter[field] = input.errors;
        }
    };

    this.get_errors = function() {
        var errors = 0;
        $.each(this.error_counter, function() {
            errors += Object.keys(this).length;
        });
        return errors;
    };

    this.fault_instance = function(change, error, is_wrong) {
        var fault_exist = change.instance(error.label);
        if (is_wrong && !fault_exist) {
            var faulted = change.instance(error.label, new fault(change, error));
            routerunner.panel.action.error_add(faulted);
        } else if (!is_wrong && fault_exist) {
            routerunner.panel.action.error_substract(fault_exist);
            fault_exist.destroy();
            change.instance(error.label, null);
        } else if (!is_wrong) {
            if ((last_instance = change.caller.last_instance(change.label))
                && (last_faulted = last_instance.instance(error.label))) {
                routerunner.panel.action.error_substract(last_faulted);
            }
        }
    };

    this.on = function(fn) {
        var self = this;

        if (self.panel.is(":hidden")) {
            self.panel.css({
                "display": "block",
                "left": "-100%"
            }).animate({
                "left": "0"
            }, 200, function () {
                if ($(window).width() < 992) {
                    routerunner.action("resize");
                }
                if (typeof fn == "function") {
                    fn();
                }
            });
        } else {
            if (typeof fn == "function") {
                fn();
            }
        }
    };
    this.off = function() {
        var self = this;

        if (!self.panel.is(":hidden")) {
            self.panel.css({
                "display": "block"
            }).animate({
                "left": "-100%"
            }, 200, function () {
                self.panel.css("display", "none");
            });
        }
    };

    this.toggle = function(elem, force) {
        if (force && this.panel.hasClass("opened")) {
            return false;
        }
        this.panel.toggleClass("opened");
        this.panel.find(".page-panel").toggleClass("open");
        this.panel.find(".hideable").toggleClass("panel-hider");

        $(elem).toggleClass("selected");
        if ($(elem).children("span.selected").length) {
            $(elem).children("span.selected").remove();
        } else {
            $(elem).append("<span class='selected'></span>");
        }
    };

    this.content = function(fn) {
        var self = this;
        var _fn = fn;
        var params = {};
        this.ajax("RouterunnerCMS/backend/ajax.php", params, function(data) {
            self.panel.html(data);
            _fn();
        });
    };

    this.resize = function() {
        $(".pageprop-sizable").removeClass("pageprop-input");
        if ($(window).width() < 992) {
            $("#page-title-sm, #page-url-sm").addClass("pageprop-input");
        } else if ($(window).width() >= 992 && $(window).width() < 1200) {
            $("#page-title, #page-url-md").addClass("pageprop-input");
        } else {
            $("#page-title, #page-url").addClass("pageprop-input");
        }
    };

    this.browse = function() {
        this.off();
    };
    this.edit = function() {
        this.on();
        $(".routerunner-page-properties").find(".pageprop-input").attr("disabled", false);
    };

    this.disabled = function() {
        $(".routerunner-page-properties").find(".pageprop-input").attr("disabled", true);
    };

    this.set = function(field, value) {
        $(".routerunner-page-properties").find("[name='" + field + "'].pageprop-input").val(value);
    };

    this.init();
};