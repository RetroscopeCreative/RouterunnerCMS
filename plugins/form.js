/**
 * Created by csibi on 2015.05.08..
 */

routerunner_input = function(input, params, form, input_name) {
    var _base = new routerunner_base();
    $.extend(this, _base);

    this.form = form;
    this.input = input;
    this.input_name = input_name;
    this.input_wrapper = false;
    this.error_wrapper = false;
    this.errors = false;

    this.params = $.extend({
        "type": "input",
        "field": false,
        "mandatory": {
            "value": false,
            "msg": ""
        },
        "regexp": {
            "value": false,
            "options": "im",
            "msg": ""
        },
        "error": {
            "selector": ":input",
            "template": "<i class='fa fa-warning tooltips' data-container='body' data-placement='bottom' data-html='true' data-original-title='{text}'></i>",
            "method": "before"
        },
        "help": "",
        "change": {
            "event": "change",
            "value": "val",
            "call": false
        },
        "default": null
    }, params);

    this.init = function() {
        var self = this;

        this.input_wrapper = this.input;
        if (!$(this.input).data("routerunner-input")
            && (input_wrapper = $(this.input).closest("[data-routerunner-input]").get(0))) {
            this.input_wrapper = input_wrapper;
        }
        if (this.params.error.selector
            && (error_wrapper = $(this.input_wrapper).find(this.params.error.selector).get(0))) {
            this.error_wrapper = error_wrapper;
        }
        if (!this.error_wrapper && this.params.error.selector) {
            this.error_wrapper = $(this.input).closest("form").find(this.params.error.selector).get(0);
        }
        if (!this.error_wrapper) {
            this.error_wrapper = this.input_wrapper;
        }

        this.bind_event();
    };

    this.check = function() {
        var change_event = (this.params.change.event ? this.params.change.event : "change");
        $(this.input).trigger(change_event);
        return this.errors;
    };

    this.bind_event = function() {
        var self = this;
        var change_event = (this.params.change.event ? this.params.change.event : "change");

        $(this.input).bind(change_event, function(evt) {
            var method = self.params.error.method;
            self.errors = self.has_error();

            var _continue = true;

            if (self.params.change.call) {
                var called_object = self.parseFn(self.params.change.call.object);
                var called_function = self.params.change.call.function;
                if (typeof called_object == "object" && typeof called_object[called_function] == "function") {
                    _continue = (called_object[called_function](self) === false ? false : true);
                }
            }

            if (_continue) {
                var clear_error = false;
                if (self.errors) {
                    var error_template = '';
                    $.each(self.errors, function (key, text) {
                        var template = self.params.error.template;

                        if (text && typeof text == "string") {
                            template = template.replace("{text}", text);
                        }
                        template = template.replace("{key}", key);

                        error_template += template;
                    });
                    var error_object = $(error_template);

                    if (method == "replaceWith") {
                        $(self.input_wrapper).data("error-wrapper-original", self.error_wrapper);
                    }

                    if ($(self.error_wrapper).data("error-object")
                        && $(self.error_wrapper).data("error-object")[self.input_name]) {
                        $(self.error_wrapper).data("error-object")[self.input_name].remove();
                    }
                    $(self.error_wrapper)[method](error_object);
                    if (method == "replaceWith") {
                        self.error_wrapper = error_object.get(0);
                    }

                    if (self.params.error.addClass) {
                        $.each(self.params.error.addClass, function(selector, classname) {
                            var obj = $(self.input_wrapper);
                            if (selector) {
                                obj = obj.find(selector);
                            }
                            obj.addClass(classname);
                        });
                    }

                    if (self.error_wrapper == self.input && method == "before") {
                        $(self.error_wrapper).closest("div").addClass("input-icon").addClass("right");
                        $('.tooltips').tooltip();
                    }

                    var wrapper_error_object = ($(self.error_wrapper).data("error-object")
                        ? $(self.error_wrapper).data("error-object") : {});
                    wrapper_error_object[self.input_name] = error_object;
                    $(self.error_wrapper).data("error-object", wrapper_error_object);
                } else if ($(self.input_wrapper).data("error-wrapper-original")) {
                    $(self.error_wrapper).replaceWith($(self.input_wrapper).data("error-wrapper-original"));
                    self.error_wrapper = $(self.input_wrapper).data("error-wrapper-original");
                    clear_error = true;
                } else if (self.error_wrapper == self.input && method == "before") {
                    $(self.error_wrapper).closest("div").removeClass("input-icon").removeClass("right");
                    if ($(self.error_wrapper).data("error-object")
                        && $(self.error_wrapper).data("error-object")[self.input_name]) {
                        $(self.error_wrapper).data("error-object")[self.input_name].remove();
                    }
                    clear_error = true;
                } else if (self.error_wrapper == self.input && method == "after") {
                    $(self.error_wrapper).closest("div").removeClass("input-icon").removeClass("right");
                    if ($(self.error_wrapper).data("error-object")
                        && $(self.error_wrapper).data("error-object")[self.input_name]) {
                        $(self.error_wrapper).data("error-object")[self.input_name].remove();
                    }
                    clear_error = true;
                } else if (self.error_wrapper && method == "append") {
                    //$(self.error_wrapper).children().remove();
                    if ($(self.error_wrapper).data("error-object")
                        && $(self.error_wrapper).data("error-object")[self.input_name]) {
                        $(self.error_wrapper).data("error-object")[self.input_name].remove();
                    }
                    clear_error = true;
                }
                if (clear_error) {
                    if (self.params.error.addClass) {
                        $.each(self.params.error.addClass, function(selector, classname) {
                            var obj = $(self.input_wrapper);
                            if (selector) {
                                obj = obj.find(selector);
                            }
                            obj.removeClass(classname);
                        });
                    }
                }
            }
        });
    };

    this.getter = function() {
        var ret = false;
        var get_value = (this.params.change.value ? this.params.change.value : "val");
        if (typeof this.input[get_value] == "function") {
            ret = this.input[get_value]();
        } else if (typeof $(this.input)[get_value] == "function") {
            ret = $(this.input)[get_value]();
        }
        return ret;
    };

    this.set = function(value) {
        var ret = false;
        var get_value = (this.params.change.value ? this.params.change.value : "val");
        if (typeof this.input[get_value] == "function") {
            this.input[get_value](value);
        } else if (typeof $(this.input)[get_value] == "function") {
            $(this.input)[get_value](value);
        }
    };

    this.has_error = function() {
        var errors = {};

        var value = this.getter();

        if (this.params.mandatory && this.params.mandatory.value && this.params.mandatory.value === true
            && (!value || value == "0")) {
            errors["mandatory"] = (this.params.mandatory.msg ? this.params.mandatory.msg : true);
        }
        if (this.params.regexp && this.params.regexp.value) {
            var pattern = new RegExp(this.params.regexp.value, this.params.regexp.options);
            if (!pattern.test(value)) {
                errors["regexp"] = (this.params.regexp.msg ? this.params.regexp.msg : true);
            }
        }
        if (this.params.regexp && $.isArray(this.params.regexp)) {
            $.each(this.params.regexp, function(key, regexp_obj) {
                var pattern, isOk = false;
                if ($.isArray(regexp_obj.value)) {
                    $.each(regexp_obj.value, function(obj_key, obj_pattern) {
                        var obj_option = ($.isArray(regexp_obj.options)
                        && typeof regexp_obj.options[obj_key] != "undefined")
                            ? regexp_obj.options[obj_key] : regexp_obj.options;
                        pattern = new RegExp(obj_pattern, obj_option);
                        if (pattern.test(value)) {
                            isOk = true;
                        }
                    });
                } else {
                    pattern = new RegExp(regexp_obj.value, regexp_obj.options);
                    isOk = pattern.test(value);
                }
                if (!isOk) {
                    if (errors["regexp"]) {
                        if (!$.isArray(errors["regexp"])) {
                            errors["regexp"] = new Array(errors["regexp"]);
                        }
                        errors["regexp"].push((regexp_obj.msg ? regexp_obj.msg : true));
                    } else {
                        errors["regexp"] = (regexp_obj.msg ? regexp_obj.msg : true);
                    }
                }
            });
            if ($.isArray(errors["regexp"])) {
                errors["regexp"] = errors["regexp"].join("\n");
            }
        }

        if (!Object.keys(errors).length) {
            errors = false;
        }
        return errors;
    };

    this.init();
};

routerunner_form = function(form, params) {
    var _base = new routerunner_base();
    $.extend(this, _base);

    this.id = ($(form).attr("name") ? $(form).attr("name") : $(form).attr("id"));
    this.form = form;
    this.params = $.extend({
        "method": ($(this.form).attr("method") ? $(this.form).attr("method") : "post"),
        "action": ($(this.form).attr("action") ? $(this.form).attr("action") : window.location.href),
        "target": ($(this.form).attr("target") ? $(this.form).attr("target") : "_self"),
        "selector": ":input[name]"
    }, params);

    this.inputs = this.instance();
    this.errors = {};
    this.submitted = false;

    this.init = function() {
        var self = this;

        if (!$(this.form).data("routerunner-form")) {
            $(this.form).find(this.params["selector"]).each(function() {
                var name = $(this).attr("name");

                var params = {};
                if ($(this).data("routerunner-input")) {
                    params = $(this).data("routerunner-input");
                } else if ((params_holder = $(this).closest("[data-routerunner-input]")).length) {
                    params = params_holder.data("routerunner-input");
                }
                var input = new routerunner_input(this, params, self, name);
                var exists;

                if (exists = self.instance(name)) {
                    if (!$.isArray(exists)) {
                        exists = [exists];
                    }
                    exists.push(input);
                    input = exists;
                }
                self.instance(name, input);
            });

            $(this.form).data("routerunner-form", true);

            $(this.form).on("submit", function() {
                if (self.submitted) {
                    return false;
                } else {
                    self.submitted = true;
                    self.errors = {};
                    $.each(self.inputs, function (input_name, input_obj) {
                        if ($.isArray(input_obj) && input_obj.length > 1) {
                            $.each(input_obj, function (input_obj_index, input_obj_item) {
                                if (input_obj_item.check()) {
                                    self.errors[input_name] = input_obj_item;
                                }
                            })
                        } else if (input_obj && input_obj.check()) {
                            self.errors[input_name] = input_obj;
                        }
                    });
                    if (Object.keys(self.errors).length) {
                        self.submitted = false;
                        return false;
                    }
                    if ($(self.form).find(".error.forced-error").length) {
						self.submitted = false;
                    }
                }
            });
        }
    };

    this.init();
};

routerunner_forms = function() {
    this.forms = {};
};

var routerunner_forms = new routerunner_forms();

$(document).ready(function() {
    $(".routerunner-form").each(function() {
        var params = ($(this).data("routerunner-form") ? $(this).data("routerunner-form") : {});
        var name = ($(this).attr("name") ? $(this).attr("name") : $(this).attr("id"));
        routerunner_forms.forms[name] = new routerunner_form(this, params);
    });
});