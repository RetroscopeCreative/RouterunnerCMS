/**
 * Created by csibi on 2015.04.22..
 */
property  = function(caller, property, property_data) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.model = caller;

    this.changes = this.instance();
    this.states = {
        "revert": {},
        "undo": [],
        "current": {}
    };
    this.errors = {};
    this.helper = helper.model;

    /* object specific props */
    this.default_value = null;
    this.property = property;
    this.property_data = property_data;

    this.inline_input = false;
    this.panel_input = false;
    this.inline_event = false;
    this.panel_event = false;
    this.inline_event_bind = false;
    this.panel_event_bind = false;

    this.is_label = false;
    this.is_hidden = false;

    this.is_loaded = false;

    this.popover_timer = false;
    this.popover_delay = 2000;

    this.rules = {};

    this.property_init = function () {
        if (!this.property_data) {
            var properties = $(this.model.inline_elem).data("fields");
            if (properties[this.property]) {
                this.property_data = properties[this.property];
            }
        }
        if (this.property_data && this.property_data["is_label"] && this.property_data["is_label"] === true) {
            this.is_label = true;
        }

        if ((this.inline_input = this.helper.get_input_elem(this.model.inline_elem, this.property_data))
            && !$(this.input_inline).data("property")) {
            this.load_inline();
            this.is_hidden = true;
        } else {
            this.model.unqueue(this.property, this.model.id + "_ready");
        }
    };

    this.load_inline = function() {
        var self = this;
        if (this.model && this.inline_input) {
            if (!$(this.inline_input).attr("id")) {
                $(this.inline_input).attr("id", "rr-" + new Date().getTime());
            }
            if (this.script =
                    this.helper.get_script_object(this.inline_input, this.model, this.property, this.property_data)) {
                if (this.script.script) {
                    this.load(self.script.script, function () {
                        self.is_loaded = true;

                        var input_instance = self.helper.get_input_controller(self.script);
                        $.extend(self, input_instance);

                        var init_fn = self.control_get("init", "inline", "init");
                        if (typeof self[init_fn] == "function") {
                            self[init_fn](self);
                        }

                        if (self.property_data) {
                            // set id if not set yet
                            if (!$(self.inline_input).data("routerunner-id")) {
                                $(self.inline_input).data("routerunner-id", "id_" + new Date().getTime());
                            }

                            // some data & class set
                            $(self.inline_input).addClass("property-" + self.property);
                            $(self.inline_input).data("property",
                                "property-" + self.property).data("field_data", self.property_data);
                            if (self.property_data["output"]
                                && typeof window[self.property_data["output"]] == "function") {
                                window[self.property_data["output"]](self.inline_input)
                            }

                            // set help popover if data exists
                            if (self.property_data.help && self.property_data.help.inline) {
                                var help_data = {
                                    "trigger": "manual",
                                    "container": "body",
                                    "placement": "bottom",
                                    "content": self.property_data.help.inline,
                                    "original-title": (self.property_data.help.title
                                        ? self.property_data.help.title : "")
                                };
                                if (self.property_data.help.inline_data) {
                                    help_data = $.extend(help_data, self.property_data.help.inline_data);
                                }
                                $.each(help_data, function (data_name, data_value) {
                                    $(self.inline_input).data(data_name, data_value);
                                });
                                $(self.inline_input).addClass("popovers").popover({
                                    content: function () {
                                        return $(this).data("content");
                                    }
                                }).on("mouseenter", function () {
                                    var popover = this;
                                    if (routerunner.state() == "edit") {
                                        self.popover_timer = setTimeout(function () {
                                            $(popover).popover("show");
                                        }, self.popover_delay);
                                    }
                                }).on("mouseleave", function () {
                                    clearTimeout(self.popover_timer);
                                    $(this).popover("hide");
                                });
                            }

                            $(self.inline_input).on("click", function (evt) {
                                self.model.select();
                            });

                            self.inline_event();
                            var starter_value = self.inline_get();
                            self.states.current[self.property] = starter_value;
                            self.states.revert[self.property] = starter_value;

                            if (self.model && self.model.class_id && self.model.class_id < 0) {
                                self.default_value = starter_value;
                            } else if (!starter_value && self.property_data && self.property_data.default) {
                                if ($.isArray(self.property_data.default) && self.property_data.default.value) {
                                    self.default_value = self.property_data.default.value;
                                } else {
                                    self.default_value = self.property_data.default;
                                }
                            }

                            self.model.unqueue(self.property, self.model.id + "_ready");
                        }
                    });
                } else {
                    this.model.unqueue(this.property, this.model.id + "_ready");
                }
            }
        }
        $(this.input_inline).data("property", this);
    };
    this.load_panel = function(panel_elem) {
        var self = this;
        this.panel_input = $(panel_elem).find(".input");
        if (!this.panel_input.length) {
            this.panel_input = $(panel_elem).find(":input");
        }
        if (!this.panel_input.length) {
            this.panel_input = panel_elem;
        }
        if (this.model && this.panel_input.length) {
            if (this.script =
                    this.helper.get_script_object(this.panel_input, this.model, this.property, this.property_data)) {

                if (this.is_loaded) {
                    var input_instance = self.helper.get_input_controller(self.script);
                    delete input_instance["input"];
                    $.each(input_instance, function(name, value) {
                        if (typeof value == "object") {
                            self[name] = $.extend(value, self[name]);
                        } else if (!self[name]) {
                            self[name] = value;
                        }
                    });

                    var init_fn = self.control_get("init", "panel", "init");
                    if (typeof self[init_fn] == "function") {
                        self[init_fn](self);
                    }

                    self.panel_event();
                } else if (self.script.script) {
                    this.load(self.script.script, function () {
                        self.is_loaded = true;

                        var input_instance = self.helper.get_input_controller(self.script);
                        $.extend(self, input_instance);

                        var init_fn = self.control_get("init", "panel", "init");
                        if (typeof self[init_fn] == "function") {
                            self[init_fn](self);
                        }

                        self.panel_event();
                    });
                } else {
                    self.is_loaded = true;

                    var input_instance = self.helper.get_input_controller(self.script);
                    $.extend(self, input_instance);

                    var init_fn = self.control_get("init", "panel", "init");
                    if (typeof self[init_fn] == "function") {
                        self[init_fn](self);
                    }

                    self.panel_event();
                }
            }
        }

        if (this.panel_input && this.state() == "disabled") {
            $(this.panel_input).attr("disabled", true);
        }

        //var field_name = $(property).data("field-name");
        if (this.is_hidden) {
            //$(panel_elem).addClass("visible-on-page").hide();
            $(panel_elem).addClass("visible-on-page");
        } else {
            $(panel_elem).addClass("hidden-on-page");
        }
    };

    this.change = function(field_name, field_value) {
        var change_object = false;
        if (!this.skip_set_change) {
            var undo = this.states.revert[this.property];
            var global_last_change;
            if (routerunner.get("changes").length) {
                global_last_change = routerunner.get("changes")[routerunner.get("changes").length - 1];
            }
            if (last_change = this.last_instance()) {
                if (global_last_change == last_change.label
                    && (last_last_change = this.last_instance(last_change.label))) {
                    undo = last_last_change.changes[this.property];
                } else if (global_last_change != last_change.label
                    && last_change.changes && last_change.changes[this.property]) {
                    undo = last_change.changes[this.property];
                }
            }
            if (typeof undo == "object") {
                undo = $.extend({}, undo);
            }

            if (this.is_label && this.model
                && routerunner.page.pageproperties.resource.reference == this.model.reference) {
                routerunner.page.pageproperties.label_change(field_value);
            }

            this.states.current[field_name] = field_value;
            change_object = this.create_change({
                    "section": this,
                    "field": field_name,
                    "value": field_value,
                    "undo": undo
                });
            this.check(change_object);
        }
        return change_object;
    };

    /* inline input functions */
    this.inline_set = function(value) {
        var return_value = this.control_get("value", "inline", "html");
        var selector = this.control_get("selector", "inline", "");
        var input = ((!selector || $(this.inline_input).is(selector))
            ? $(this.inline_input) : $(this.inline_input).find(selector));
        if (typeof this[return_value] == "function") {
            return this[return_value](value);
        } else if (input.length) {
            if (typeof input[return_value] == "function") {
                input[return_value](value);
            } else if (input.attr(return_value)) {
                input.attr(return_value, value);
            } else {
                input.data(return_value, value);
            }
        }
    };
    this.inline_get = function() {
        var return_value = this.control_get("value", "inline", "html");
        var selector = this.control_get("selector", "inline", "");
        var input = ((!selector || $(this.inline_input).is(selector))
            ? $(this.inline_input) : $(this.inline_input).find(selector));
        if (typeof this[return_value] == "function") {
            return this[return_value]();
        } else if (input.length) {
            if (typeof input[return_value] == "function") {
                return input[return_value]();
            } else if (input.attr(return_value)) {
                return input.attr(return_value);
            } else {
                return input.data(return_value);
            }
        }
    };
    this.inline_event = function(custom_inline_change_event) {
        var self = this;
        if (!this.inline_event_bind) {
            var change_event = this.control_get("event", "inline", "change");
            var selector = this.control_get("selector", "inline", "");
            var input = ((!selector || $(this.inline_input).is(selector))
                ? $(this.inline_input) : $(this.inline_input).find(selector));
            if (change_event != "change" && typeof this[change_event] == "function") {
                this.inline_event_bind = this[change_event];
                this[change_event](input);
            } else if (custom_inline_change_event && typeof custom_inline_change_event == "function") {
                this.inline_event_bind = custom_inline_change_event;
                custom_inline_change_event(input);
            } else if (input.length) {
                this.inline_event_bind = function () {
                    var changed_value = self.inline_get();
                    self.change(self.property, changed_value);
                    self.panel_set(changed_value);
                    self.label_set(20, true);
                };
                input.bind(change_event, this.inline_event_bind);
            }
        }
    };

    this.inline_focus = function() {
        // not yet
    };

    /* panel input functions */
    this.panel_set = function(value) {
        var return_value = this.control_get("value", "panel", "val");
        var selector = this.control_get("selector", "panel", "");
        var input = ((!selector || $(this.panel_input).is(selector))
            ? $(this.panel_input) : $(this.panel_input).find(selector));
        if (typeof this[return_value] == "function") {
            return this[return_value](value);
        } else if (input.length) {
            if (typeof input[return_value] == "function") {
                input[return_value](value);
            } else if (input.attr(return_value)) {
                input.attr(return_value, value);
            } else {
                input.data(return_value, value);
            }
        }
    };
    this.panel_get = function() {
        var return_value = this.control_get("value", "panel", "val");
        var selector = this.control_get("selector", "panel", "");
        var input = ((!selector || $(this.panel_input).is(selector))
            ? $(this.panel_input) : $(this.panel_input).find(selector));
        if (typeof this[return_value] == "function") {
            return this[return_value]();
        } else if (input.length) {
            if (input.is('input[type=checkbox]') && return_value == 'val') {
                return input.is(':checked');
            } else if (typeof input[return_value] == "function") {
                return input[return_value]();
            } else if (input.attr(return_value)) {
                return input.attr(return_value);
            } else {
                return input.data(return_value);
            }
        }
    };
    this.panel_event = function(custom_panel_change_event) {
        var self = this;
        var change_event = this.control_get("event", "panel", "change");
        var selector = this.control_get("selector", "panel", "");
        var input = ((!selector || $(this.panel_input).is(selector))
            ? $(this.panel_input) : $(this.panel_input).find(selector));
        if (change_event != "change" && typeof this[change_event] == "function") {
            this.panel_event_bind = this[change_event];
            this[change_event](input);
        } else if (custom_panel_change_event && typeof custom_panel_change_event == "function") {
            this.panel_event_bind = custom_panel_change_event;
            custom_panel_change_event(input);
        } else {
            input.on(change_event, function () {
                var changed_value = self.panel_get();
                self.change(self.property, changed_value);
                self.inline_set(changed_value);
                self.label_set(20, true);
            });
        }
    };

    this.panel_focus = function() {
        $(this.panel_input).get(0).focus();
    };

    this.get = function(maxchar, resource_uri, label) {
        var value = label;
        if (!value) {
            value = ((this.states.current && this.states.current[this.property])
                ? this.states.current[this.property] : '');
        }
        if (!value) {
            value = this.panel_get();
        }
        if (!value) {
            value = this.inline_get();
        }
        if (!value) {
            value = "";
        }
        if (maxchar && !isNaN(parseInt(maxchar)) && value && value.length > maxchar) {
            value = value.substr(0, maxchar) + "...";
        }
        if (resource_uri) {
            value += " (";
            if (this.model && this.model.class_name && this.model.class_id) {
                value += this.model.class_name + "/" + this.model.class_id;
            } else if (this.model && this.model.reference) {
                value += "ref:" + this.model.reference;
            }
            value += ")";
        }
        return value;
    };
    this.set = function(value) {
        this.inline_set(value);
        this.panel_set(value);
        this.label_set(20, true);
    };

    this.force_blur = function() {
        if (this.model && this.model.class_id && this.model.class_id < 0) {
            var change_event = this.control_get("event", "panel", "change");
            $(this.panel_input).trigger(change_event);
        }
        if ($(this.inline_input).hasClass("cke_editable")
            && CKEDITOR && CKEDITOR.instances[$(this.inline_input).attr("id")]) {
            CKEDITOR.instances[$(this.inline_input).attr("id")].fire("blur");
        } else {
            $(this.inline_input).trigger("blur").trigger("focusout");
        }
        if ($(this.panel_input).next().hasClass("cke")
            && CKEDITOR && CKEDITOR.instances[$(this.panel_input).attr("id")]) {
            CKEDITOR.instances[$(this.panel_input).attr("id")].fire("blur");
        } else {
            $(this.panel_input).trigger("blur").trigger("focusout");
        }
        routerunner.page.unqueue(this.model.id + ".property." + this.property, "apply_ready");
    };

    this.control_get = function(data, mode, default_value) {
        var ret = default_value;
        if (this.property_data) {
            if (!this.property_data.control && this.property_data.change) {
                this.property_data.control = this.property_data.change; // deprecated parameter
            }
            if (this.property_data.control && this.property_data.control[data]) {
                ret = ((this.property_data.control[data][mode])
                    ? this.property_data.control[data][mode] : this.property_data.control[data]);
            }
        }
        return ret;
    };

    this.label_set = function(maxchar, resource_uri, value) {
        if (this.is_label) {
            var label = this.get(maxchar, false, value);
            if (label.indexOf("<br") > -1) {
                label = label.substr(0, label.indexOf("<br")) + "...";
            }
            if ($.jstree.reference("routerunner-tree") && $.jstree.reference("routerunner-tree").get_node) {
                var node = $.jstree.reference("routerunner-tree").get_node("jstreenode_" + this.model.reference);
                if (node) {
                    label = "<span class='tree-label label label-info'>" + label + "</span>";
                    if (node.text) {
                        var node_text = $("<div></div>").html(node.text);
                        if (node_text.find(".tree-label").length) {
                            $(node_text).find(".tree-label").replaceWith($(label));
                            label = $(node_text).html();
                        }
                    } else if (this.model && this.model.class_id && this.model.class_id < 0 && this.model.class_name) {
                        label += " <span class='label label-danger'>new " + this.model.class_name + "</span>";
                    }
                    $.jstree.reference("routerunner-tree").set_text(node, label);
                }
            }
            label = this.get(maxchar, true, value);
            if (label.indexOf("<br") > -1) {
                label = label.substr(0, label.indexOf("<br")) + "...";
            }
            $("#routerunner-model-selector").children("#modelselector_" + this.model.id).text(label);
            return this;
        }
        return false;
    };

    this.has_error = function() {
        var ret = false;
        if (last_change = this.last_instance()) {
            ret = this.check(last_change, true);
        }
        return ret;
    };

    this.modelselect = function() {
        this.panel_set(this.get());
        if (this.has_error()) {
            var last_change = this.last_instance();
            var last_error = last_change.last_instance();
            if (last_error instanceof fault) {
                last_error.panel_set();
            }
        }
        var laststate = this.model.laststate();
        if (laststate != "modelselect" && typeof this[laststate] == "function") {
            this[laststate]();
        }
    };

    this.check = function(change, skip_instance) {
        if (!this.rules["mandatory"]) {
            var mandatory = (this.property_data.mandatory ? this.property_data.mandatory : false);
            this.rules["mandatory"] = {
                "value": (mandatory && mandatory.value) ? mandatory.value : false,
                "label": (mandatory && mandatory.label) ? mandatory.label : "mandatory",
                "msg": (mandatory && mandatory.msg) ? mandatory.msg : "Mandatory field! Please fill it!"
            };
        }
        if (!this.rules["regexp"] && this.property_data.regexp) {
            var regexp = this.property_data.regexp;
            this.rules["regexp"] = {
                "pattern": new RegExp(regexp.value, (regexp.options ? regexp.options : "im")),
                "label": (regexp && regexp.label) ? regexp.label : "incorrect",
                "msg": (regexp && regexp.msg) ? regexp.msg : "Value is in an invalid format!"
            };
        }
        var value = change.changes[this.property];
        var is_wrong = false;
        var fail = false;

        if (this.rules["mandatory"] && this.rules["mandatory"].value == true) {
            is_wrong = (!value);
            if (!fail && is_wrong) {
                fail = true;
            }

            if (!skip_instance) {
                this.fault_instance(change, this.rules["mandatory"], is_wrong);
            }
        }

        if (this.rules["regexp"]) {
            is_wrong = (!this.rules["regexp"].pattern.test(value));
            if (!fail && is_wrong) {
                fail = true;
            }

            if (!skip_instance) {
                this.fault_instance(change, this.rules["regexp"], is_wrong);
            }
        }
        return fail;
    };

    this.fault_instance = function(change, error, is_wrong, skip_instance) {
        var fault_exist = change.instance(error.label);
        if (is_wrong && !fault_exist) {
            var faulted = change.instance(error.label, new fault(change, error));
            routerunner.panel.action.error_add(faulted);
        } else if (!is_wrong && fault_exist) {
            routerunner.panel.action.error_substract(fault_exist);
            fault_exist.destroy();
            change.instance(error.label, null);
            this.error_unset(error.id);
        } else if (!is_wrong) {
            if ((last_instance = change.caller.last_instance(change.label))
                && (last_faulted = last_instance.instance(error.label))) {
                routerunner.panel.action.error_substract(last_faulted);
            }
            this.error_unset();
        }
    };

    this.error_set = function(error) {
        this.errors[error.id] = error;
        $(this.panel_input).closest(".panel-property").addClass("routerunner-error");
        $(this.panel_input).closest(".form-group").addClass("has-error");

        if ($(this.panel_input).closest(".form-group").find(".help-block").length && error.msg) {
            $(this.panel_input).data("original-help",
                $(this.panel_input).closest(".form-group").find(".help-block").html());
            $(this.panel_input).closest(".form-group").find(".help-block").html(error.msg);
        }

        $(this.inline_input).addClass("routerunner-error");
        if ($(this.inline_input).hasClass("popovers") && $(this.inline_input).data("content") && error.msg) {
            $(this.inline_input).data("original-popover-content", $(this.inline_input).data("content"));
            $(this.inline_input).data("content", error.msg);
        }
    };
    this.error_unset = function(error, force) {
        if (error) {
            delete this.errors[error.id];
        }

        if (force !== undefined || !this.has_error()) {
            $(this.panel_input).closest(".panel-property").removeClass("routerunner-error");
            $(this.panel_input).closest(".form-group").removeClass("has-error");
            $(this.inline_input).removeClass("routerunner-error");

            if ($(this.panel_input).data("original-help")
                && $(this.panel_input).closest(".form-group").find(".help-block").length) {
                $(this.panel_input).closest(".form-group").find(".help-block")
                    .html($(this.panel_input).data("original-help"));
                $(this.panel_input).removeData("original-help");
            }

            if ($(this.inline_input).hasClass("popovers") && $(this.inline_input).data("original-popover-content")) {
                $(this.inline_input).data("content", $(this.inline_input).data("original-popover-content"));
                $(this.inline_input).removeData("original-popover-content");
            }
        }
    };

    this.update = function() {
        this.label_set(20, true);
    };

    this.disabled = function() {
        if (this.panel_input) {
            //$(this.panel_input).attr("disabled", true);
            $(this.panel_input).hide();
        }
        this.state("browse");
    };

    this.property_init();
};