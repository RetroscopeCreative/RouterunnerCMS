/**
 * Created by csibi on 2014.10.17..
 */

visibility_panel = function(caller, id) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.id = id;
    this.class = "routerunner-visibility";
    this.selector = "#" + id;
    this.menu_selector = "#routerunner-model-navbar ul.nav > #model-panel-visibility-menu";
    this.panel = false;
    this.helper = helper.modelpanel;

    this.caller = caller;
    this.model = caller.model;

    this.selected_section = "visibility-simple";
    this.sections = {
        "visibility-simple": ["active"],
        "visibility-date": ["begin", "end"],
        "visibility-user": ["params"]
    };


    this.change_id = false;
    this.changes = {};

    this.content = {
        url: "RouterunnerCMS/backend/ajax/modelpanel/visibility.php",
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

            self.panel.find("[data-enable]").unbind("click").bind("click", function() {
                $(this).parent().children(".active").removeClass("active");
                $(this).addClass("active");
                var target = $($(this).data("enable"));
                target.parent().children().each(function() {
                    if (this == target.get(0)) {
                        $(this).removeClass("disabled");
                        $(this).find(".need-to-disable").each(function() {
                            if ($(this).hasClass("make-switch")) {
                                $(this).bootstrapSwitch("readonly", false);
                            } else {
                                $(this).attr("disabled", $(this).data("disabled"));
                            }
                        });
                    } else {
                        $(this).addClass("disabled");
                        $(this).find(".need-to-disable").each(function() {
                            if ($(this).hasClass("make-switch")) {
                                $(this).bootstrapSwitch("readonly", true);
                            } else {
                                $(this).data("disabled", $(this).is(":disabled")).attr("disabled", true);
                            }
                        });
                    }
                });
                //target.parent().children().addClass("disabled");
                //target.removeClass("disabled");
                self.selected_section = $(this).data("section");
            });

            self.panel.find(".bs-md-datetimepicker").bootstrapMaterialDatePicker({
                format: 'YYYY-MM-DD HH:mm',
                lang: 'hu',
                weekStart: 1,
                okButton: "OK",
                cancelButton: "Cancel"
            });
            self.panel.find(".bs-md-datetimepicker").on("change", function(event) {
                var val = $(this).val();
                if (val) {
                    $("#" + $(this).attr("id")).bootstrapMaterialDatePicker("setDate", val);
                }
            });
            self.panel.find(".bs-md-datetimepicker").each(function() {
                $(this).trigger("change");
            }).attr("disabled", (self.state() == "disabled"));

            self.panel.find(".make-switch").bootstrapSwitch();
            self.panel.find(".make-switch[data-enable]").on("switchChange.bootstrapSwitch", function(event, state) {
                $($(this).data("enable")).attr("disabled", !$(this).is(":checked"));
            });
            self.panel.find(".make-switch").each(function() {
                $(this).trigger("switchChange.bootstrapSwitch");
            });


            var found = false;
            $(self.panel.find(".form-section").get().reverse()).each(function() {
                $(this).find("input[type='text']").each(function() {
                    if ($(this).val()) {
                        found = this;
                    }
                });
                if (!found) {
                    $(this).find("input[type='checkbox']").each(function() {
                        if ($(this).is(":checked")) {
                            found = this;
                        }
                    });
                }
            });
            if (found) {
                self.panel.find(".btn[data-section='" + $(found).closest(".form-section").attr("id") + "']").trigger("click");
            }

            self.resize();

            self.caller.unqueue("visibility_panel");
        });
    };

    this.destroy = function() {
        //this.save_changes();

        this.panel.remove();
    };

    this.resize = function() {
        var self = this;
        setTimeout(function() {
            if (self.panel.width() < 780) {
                self.panel.find(".visibility-cols").css("width", "100%");
            } else {
                self.panel.find(".visibility-cols").removeAttr("style");
            }
        }, 500);
    };

    this.init();
};