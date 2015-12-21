common = function(caller) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.helper = false;

    this.init = function () {
        this.helper = helper.common;
    };

    this.loaded = function() {
        var self = this;
        $(document).ready(function(){
            $(".dropdown-toggle").dropdown();

            $(".custom-dropdown-toggle").each(function() {
                var toggle_selector = $(this).data("toggle");
                var dropdown = $(this).parent().find(toggle_selector);
                var btngroup = $(this).parent(".btn-group");
                dropdown.css({
                    position: "fixed",
                    left: btngroup.offset().left + "px",
                    top: (btngroup.offset().top + btngroup.height()) + "px",
                    "z-index": 99999
                });
                dropdown.hide();
                $(this).click(function() {
                    if (dropdown.is(":visible")) {
                        dropdown.slideUp(200);
                    } else {
                        dropdown.slideDown(200);
                    }
                });
            });

            self.init();
        });
    };

    this.mode_selection = function() {
        if (typeof this[routerunner.mode() + "_mode"] == 'function') {
            this[routerunner.mode() + "_mode"]();
        }
    };

    this.browse_mode = function() {
        routerunner.model.browse_mode();
    };

    this.edit_mode = function() {
        routerunner.model.edit_mode();
    };

    this.cookie_settings = function(setting, value) {
        $.cookie.json = true;
        var settings = $.cookie("routerunner-settings");
        if (typeof settings != "object") {
            settings = {};
        }
        if (value != undefined) {
            settings[setting] = value;
            $.cookie("routerunner-settings", settings, { expires: 30, path: '/' });
        }
        if (setting != undefined) {
            var ret = false;
            if (settings[setting] != undefined) {
                ret = settings[setting];
            }
            return ret;
        } else {
            return settings;
        }
    };

    this.change_by_label = function(label) {
        var ret = false;

        var changes_by_model = routerunner.get("changes_by_model");

        var change_reference = false;
        if (change_reference = changes_by_model[label]) {
            var model = false;
            if (change_reference.model && change_reference.model == "pageproperties") {
                model = routerunner.page.pageproperties;
            } else if (change_reference.model && routerunner_forms
                && routerunner_forms.forms[change_reference.model]) {
                model = routerunner_forms.forms[change_reference.model];
            } else if (change_reference.model) {
                model = routerunner.page.models[change_reference.model];
            }
        }

        var property = false;
        if (model instanceof routerunner_form) {
            property = model.inputs[change_reference.property];
            if ($.isArray(property)) {
                var visible = false;;
                $.each(property, function() {
                    if (!visible && $(this.input).is(":visible")) {
                        visible = this;
                    }
                });
                property = visible;
            }
        } else if (model instanceof pageproperties) {
            property = model;
        } else if (model && change_reference.property && model.property[change_reference.property]) {
            property = model.property[change_reference.property];
        } else if (model && change_reference.property
                && (change_reference.property == "move" || change_reference.property == "insert"
                    || change_reference.property == "remove")) {
            property = model.position;
            property.property = change_reference.property;
        } else if (model && change_reference.property && change_reference.property.substr(0, 11) == "visibility/") {
            property = model.visibility;
            property.property = change_reference.property.substr(11);
        }

        if (property) {
            ret = property.instance(label);
        }

        return ret;
    };

};

CKEDITOR.disableAutoInline = true;
