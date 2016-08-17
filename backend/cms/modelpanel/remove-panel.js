/**
 * Created by csibi on 2014.10.17..
 */

remove_panel = function(caller, id) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.id = id;
    this.class = "routerunner-remove";
    this.selector = "#" + id;
    this.menu_selector = "#routerunner-model-navbar ul.nav > #model-panel-remove-menu";
    this.panel = false;
    this.helper = helper.modelpanel;

    this.caller = caller;
    this.model = caller.model;

    this.change_id = false;
    this.changes = {};

    this.content = {
        url: routerunner.settings["BACKEND_DIR"] + "/backend/ajax/modelpanel/remove.php",
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
            self.panel.find(".make-switch").bootstrapSwitch();
            self.panel.find("#are_you_sure").on("switchChange.bootstrapSwitch", function(event, state) {
                if ($(this).is(":checked")) {
                    var portlet = $(this).closest(".portlet");
                    self.panel.find(".portlet-body").slideDown(200, function() {
                        $(this).closest(".scrollspied").animate({
                            "scrollTop": $(this).closest(".scrollspied").scrollTop() + portlet.height()
                        }, 200);
                    });
                } else {
                    self.panel.find(".portlet-body").slideUp(200);
                }
            });

            self.caller.unqueue("remove_panel");
        });
    };

    this.enabled = function() {
        this.panel.show();
    };
    this.disabled = function() {
        this.panel.hide();
    };

    this.destroy = function() {
        this.panel.remove();
    };

    this.store_change = function(elem) {
        var self = this;
    };

    this.save_changes = function() {
        var self = this;
        var url = routerunner.settings["BACKEND_DIR"] + '/backend/ajax/model/set_changes.php';
        var data = {
            change_id: self.change_id,
            reference: self.panel.data("reference"),
            changes: self.changes,
            state: "visibility"
        };
        var params = {
            dataType: 'json'
        };
        this.helper.ajax(url, data, params, function(data){
            if (data.success && data.change_id) {
                self.change_id = data.change_id;
            }
        });
    };

    this.init();
};