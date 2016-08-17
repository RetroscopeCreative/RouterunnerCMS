/**
 * Created by csibi on 2015.03.03..
 */
changed = function(caller, label, params, previous_change) {
    var _base = new baserunner(caller);
    $.extend(this, _base, {
        id: false,
        data: {
            "reference": false,
            "resource": false,
            "state": false,
            "date": false,
            "approved": false,
            "approved_session": false,
            "session": false
        }
    }, previous_change, {
        label: label,
        caller: caller,
        model: caller.model,
        section: false,

        panel: false,

        changes: {},
        undo_change: {},
        error_objects: [],
        params: params,
        helper: helper.change
    });

    this.init = function() {
        this.data.session = routerunner.session();
        if (this.model instanceof model) {
            this.data.reference = this.model.reference;
            this.data.resource = {
                "route": this.model.route
            };
        } else if (typeof caller == "object" && caller.form && caller.form.id == "routerunner-page-properties") {
            this.data.resource = routerunner.page.pageproperties.resource;
        }

        this.panel = routerunner.panel.changes;

        if (caller instanceof property) {
            this.section = "property";
        } else if (caller instanceof visibility) {
            this.section = "visibility";
        } else if (caller instanceof position) {
            this.section = "position";
        } else if (caller instanceof remove) {
            this.section = "remove";
        } else if (caller instanceof pageproperties) {
            this.section = "pageproperties";
        } else if (caller instanceof routerunner_input) {
            this.section = caller.form.id;
        }
        this.change();
    };

    this.apply = function() {
        if (this.helper.apply_change(this)) {
            this.caller.remove_change(this.label);
        }
    };

    this.destroy = function() {
        if (this.helper.delete_change(this)) {
            this.caller.remove_change(this.label);
        }
    };

    this.change = function() {
        var self = this;
        switch (this.section) {
            case "position":
                var from = this.params.value.from;
                var to = this.params.value.to;
                this.undo_change = {"from": to, "to": from};
                this.changes = this.params.value;
                break;
            default:
                this.undo_change[this.params.field] = this.params.undo;
                this.changes[this.params.field] = this.params.value;
                break;
        }
        this.helper.log_change(this, function() {
            routerunner.panel.changes.refresh(self);
        });
    };

    this.undo = function() {
        var undo_change = false;
        this.caller.skip_set_change = true;
        switch (this.section) {
            case "position":
                if (undo_change = this.caller.instance(this.label)) {
                    value_to_set = undo_change.undo_change;
                    if (value_to_set !== null) {
                        this.caller.set(value_to_set.to);
                    }
                    undo_change.caller.pointer.undo(this.label);
                }
                break;
            case "visibility":
                if (undo_change = this.caller.instance(this.label)) {
                    value_to_set = undo_change.undo_change[this.params.field];
                    if (value_to_set !== null) {
                        this.caller.set(this.params.field, value_to_set);
                    }
                }
                break;
            case "routerunner-page-properties":
                if (undo_change = this.caller.instance(this.label)) {
                    value_to_set = undo_change.undo_change[this.params.field];
                    if (value_to_set !== null) {
                        this.caller.set(value_to_set);
                    }
                }
                break;
            default:
                if (undo_change = this.caller.instance(this.label)) {
                    value_to_set = undo_change.undo_change[this.params.field];
                    if (value_to_set !== null) {
                        this.caller.set(value_to_set);
                        if (this.caller instanceof property) {
                            if (this.caller.is_label === true && typeof this.caller.label_set == "function") {
                                this.caller.label_set(20, true, value_to_set);
                            }
                            if (last_instance = this.caller.last_instance(this.label)) {
                                var errors = last_instance.instance();
                                $.each(errors, function () {
                                    this.panel_set();
                                    routerunner.panel.action.error_add(this);
                                });
                            } else {
                                this.caller.error_unset(false, true);
                                routerunner.panel.action.error_substract();
                            }
                        }
                    }
                }
                break;
        }
        this.caller.skip_set_change = false;
    };

    this.redo = function() {
        var undo_change = false;
        this.caller.skip_set_change = true;
        switch (this.section) {
            case "position":
                if (undo_change = this.caller.instance(this.label)) {
                    value_to_set = undo_change.changes;
                    if (value_to_set !== null) {
                        this.caller.set(value_to_set.to);
                    }
                    undo_change.caller.pointer.redo(this.label);
                }
                break;
            case "visibility":
                if (undo_change = this.caller.instance(this.label)) {
                    value_to_set = undo_change.changes[this.params.field];
                    if (value_to_set !== null) {
                        this.caller.set(this.params.field, value_to_set);
                    }
                }
                break;
            case "routerunner-page-properties":
                if (undo_change = this.caller.instance(this.label)) {
                    value_to_set = undo_change.changes[this.params.field];
                    if (value_to_set !== null) {
                        this.caller.set(value_to_set);
                    }
                }
                break;
            default:
                if (undo_change = this.caller.instance(this.label)) {
                    value_to_set = undo_change.changes[this.params.field];
                    if (value_to_set !== null) {
                        this.caller.set(value_to_set);
                        if (this.caller instanceof property) {
                            if (this.caller.is_label === true && typeof this.caller.label_set == "function") {
                                this.caller.label_set(20, true, value_to_set);
                            }
                            if ((errors = this.instance()) && Object.keys(errors).length) {
                                $.each(errors, function () {
                                    this.panel_set();
                                    routerunner.panel.action.error_add(this);
                                });
                            } else {
                                this.caller.error_unset(false, true);
                                routerunner.panel.action.error_substract();
                            }
                        }
                    }
                }
                break;
        }
        this.caller.skip_set_change = false;
    };

    this.errorlist = function() {
        var self = this;
        this.error_objects = [];
        var errors = this.instance();
        $.each(errors, function() {
            self.error_objects.push(this.changelist());
        });
        return this.error_objects;
    };

    this.return_change = function() {
        return this.changes;
    };

    this.init();
};