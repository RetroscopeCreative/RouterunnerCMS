/**
 * Created by csibi on 2014.10.17..
 */

properties_panel = function(caller, id, ready_fn) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.ready_fn = (ready_fn != undefined ? ready_fn : false);
    this.id = id;
    this.class = "routerunner-properties";
    this.menu_selector = "#routerunner-model-navbar ul.nav > #model-panel-properties-menu";
    this.selector = "#" + id;
    this.panel = false;
    this.helper = helper.modelpanel;

    this.caller = caller;
    this.model = caller.model;

    this.change_id = false;
    this.changes = {};

    this.content = {
        url: routerunner.settings["BACKEND_DIR"] + "/backend/ajax/modelpanel/properties.php",
        data: {
            reference: this.model.reference,
            model_class: this.model.class_name,
            route: this.model.route
        },
        params: {}
    };

    this.init = function () {
        var self = this;

        this.panel = this.helper.panel_content(this, function(panel) {
            var panel_w = false;
            self.helper.delayed_call(function() {
                $.each(panel.find(".panel-property"), function() {
                    if (self.model.property && self.model.property[$(this).data("field-name")]) {
                        self.model.property[$(this).data("field-name")].load_panel(this);
                    }
                });

                self.helper.delayed_call(function() {
                    self.caller.unqueue("properties_panel");
                }, function() {
                    var all_loaded = true;
                    $.each(self.model.property, function() {
                        if (!this.is_loaded) {
                            all_loaded = false;
                        }
                    });
                    return all_loaded;
                });
            }, function() {
                var new_panel_w = $("#routerunner-panel").width();
                var ret = (new_panel_w === panel_w);
                panel_w = new_panel_w;
                return ret;
            });
        });
    };

    this.disabled = function() {
        this.init();
    };

    this.destroy = function() {
        this.model.action("panel_destroy");
        this.panel.remove();
    };

    this.property_init = function(field_name, selector, params) {
        this.global_set("property-params-" + field_name, {
            selector: selector,
            params: params
        });
    };

    this.filter_init = function() {
        var self = this;

        $("#property_filter").select2();
        $("#property_filter").on("change", function (e) {
            var filters = $(this).val();
            self.panel.find(".panel-property").each(function () {
                var _hasClass = ((!filters || !filters.length) ? true : false);
                var elem = this;
                if (filters) {
                    $.each(filters, function (index, filter) {
                        if ($(elem).hasClass(filter)) {
                            _hasClass = true;
                        } else {
                            _hasClass = false;
                            return false;
                        }
                    });
                }
                if (_hasClass) {
                    $(elem).show();
                } else {
                    $(elem).hide();
                }
            });
        });
        setTimeout(function() {
            $("#property_filter").next(".select2").removeAttr("style").css("width", "100%");
        }, 200);
    };

    this.init();
};