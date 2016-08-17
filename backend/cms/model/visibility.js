/**
 * Created by csibi on 2015.04.22..
 */
visibility = function(caller) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.model = caller;

    this.panel = false;

    this.changes = this.instance();
    this.states = {
        "revert": false,
        "undo": [],
        "current": {}
    };
    this.errors = {};
    this.helper = helper.model;
    this.dontlook = false;

    /* object specific props */
    this.visibility_section = false;

    this.init = function () {
    };

    this.changer = function(field, value) {
        if (!this.skip_set_change) {
            var undo = this.states.current[field];
            //this.states.current = this.get();
            this.create_change({ "section": this, "field": field, "value": value, "undo": undo });
        }
    };

    this.force_blur = function() {
        // blur something?
        routerunner.page.unqueue(this.model.id + ".visibility", "apply_ready");
    };

    /* panel input functions */
    this.set = function(field, value) {
        this.states.current[field] = value;

        if (field == "section") {
            this.panel.panel.find("[data-section='" + value + "']").trigger("click");
        } else {
            var input = this.panel.panel.find("[name='" + field + "']");
            if (input.is(":disabled")) {
                var section = input.closest(".form-section").attr("id");
                this.panel.panel.find("[data-section='" + section + "']").trigger("click");
            }
            if (input.length && input.is(":checkbox")) {
                this.panel.panel.find("[name='" + field + "'].make-switch").bootstrapSwitch("state",
                    (value ? true : false));
            } else if (input.length) {
                this.panel.panel.find("[id='" + field + "'].make-switch").bootstrapSwitch("state",
                    (value ? true : false));
                this.panel.panel.find("[name='" + field + "']").val(value);
            }
        }
        //this.panel_set();
    };
    this.get = function() {
        var ret = {};

        if (this.panel) {
            ret["section"] = this.panel.selected_section;
            var inputs = this.panel.sections[ret["section"]];

            var form = this.panel.panel.find("#" + ret["section"]);
            $.each(inputs, function (index, attr_name) {
                var input = form.find("[name='" + attr_name + "']");
                if (input.is(":checkbox")) {
                    ret[attr_name] = input.is(":checked");
                } else {
                    ret[attr_name] = input.val();
                }
            });
        }
        return ret;
    };
    this.load = function() {
        if (this.model.panel.visibility) {
            this.panel = this.model.panel.visibility;
        }
        if (this.panel) {
            this.states.current = this.get();
        }
        this.event();
        this.update();
    };
    this.panel_set = function() {
        var value_object = this.states.current;
        if (value_object["section"]
            && $("[id='routerunner-visibility-" + this.model.reference + "']").length) {

            this.panel.panel.find("[data-section='" + value_object["section"] + "']").trigger("click");

            var form = this.panel.panel.find("#" + value_object["section"]);

            switch (value_object["section"]) {
                case "visibility-simple":
                    /*
                    form.find("[name='active']").attr("checked",
                        (value_object["active"] ? value_object["active"] : false));*/
                    form.find("[name='active']").bootstrapSwitch("state",
                        (value_object["active"] ? value_object["active"] : false));
                    break;
                case "visibility-date":
                    if (value_object["begin"]) {
                        form.find("[id='begin']").bootstrapSwitch("state", true);
                        form.find("[name='begin']").val(value_object["begin"]);
                    }
                    if (value_object["end"]) {
                        form.find("[id='end']").bootstrapSwitch("state", true);
                        form.find("[name='end']").val(value_object["end"]);
                    }
                    break;
            }
        }
    };
    this.event = function() {
        var self = this;

        if (this.panel && this.panel.panel) {
            this.panel.panel.find("[data-section]").on("click", function () {
                if (self.states.current.section != $(this).data("section")) {
                    self.changer("section", $(this).data("section"));
                }
            });
            this.panel.panel.find(":input[name]").on("change", function () {
                if ($(this).is(":checkbox")) {
                    self.changer($(this).attr("name"), $(this).is(":checked"));
                } else {
                    self.changer($(this).attr("name"), $(this).val());
                }
            });
            this.panel.panel.find("[name].make-switch").on("switchChange.bootstrapSwitch", function () {
                self.changer($(this).attr("name"), $(this).is(":checked"));
            });

            if (self.state() == "disabled") {
                this.panel.panel.find("[data-section]").attr("disabled", true);
                this.panel.panel.find(":input[name]").attr("disabled", true);
                this.panel.panel.find("[name].make-switch").each(function() {
                    $(this).bootstrapSwitch("readonly", true);
                });
            }
        }
    };

    this.update = function() {
        var self = this;
        var changes = this.instance();
        $.each(changes, function() {
            $.each(this.changes, function(field, value) {
                self.set(field, value);
            });
        });
    };

    this.init();
};