/**
 * Created by csibi on 2015.03.03..
 */
fault = function(caller, error) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.caller = caller; // change object
    this.property = caller.caller; // property, pageprops, position or visibility
    this.model = (this.property instanceof routerunner_input ? this.property.form : caller.caller.model);

    this.id = false;
    this.reference = false;
    this.label = "error";
    this.msg = "Invalid value!";

    this.init = function() {
        if (error.label) {
            this.label = error.label;
        }
        if (error.msg) {
            this.msg = error.msg;
        }
        if (!this.id) {
            this.id = this.label + "-" + new Date().getTime();
        }
        if (!this.reference) {
            if (this.property instanceof routerunner_input) {
                this.reference = this.property.form.id + "-" + this.caller.params.field + "-" + this.label;
            } else {
                this.reference = this.model.id + "-" + this.property.property + "-" + this.label;
            }
        }
        this.model.errors[this.id] = this;

        this.panel_set();
    };

    this.destroy = function() {
        delete this.model.errors[this.id];
        if (this.property instanceof property) {
            this.property.error_unset(this);
        }
    };
/*
    this.modelselect = function() {
        this.panel_set();
    };
*/
    this.panel_set = function() {
        if (this.property instanceof property) {
            this.property.error_set(this);
        }
    };

    this.changelist = function() {
        var self = this;
        var label = $('<span class="label label-sm error-label label-danger">' + this.label +
            '<span class="fa fa-exclamation-circle"></span></span>');
        label.bind("click", function(e) {
            e.stopPropagation();

            if (self.model instanceof model) {
                self.model.select(self.property);
                routerunner.panel.changes.off();
            } else if (self.model instanceof pageproperties) {
                var timer = 200;
                var field = self.caller.params.field;
                if (!self.model.panel.find("[name='" + field + "']:visible").length) {
                    routerunner.page.pageproperties.toggle($("#page-properties").get(0), true);
                };
                setTimeout(function() {
                    if (self.model.panel.find("[name='" + field + "']:visible").length) {
                        self.model.panel.find("[name='" + field + "']:visible").get(0).focus();
                    }
                }, timer);
            }
            return false;
        });
        return label;
    };

    this.init();
};