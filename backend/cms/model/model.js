/**
 * Created by csibi on 2014.10.15..
 */
model = function(caller, elem, ready_fn) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.ready_fn = (ready_fn != undefined ? ready_fn : false);
    this.page = caller;

    this.outer_html = elem;
    this.inline_elem = ($(elem).is(".routerunner-inline") ? elem : $(elem).find(".routerunner-inline").get(0));
    if ($(this.inline_elem).length > 1) {
        var first_inline = false;
        if ($(this.inline_elem).find('.rr-model')) {
            $.each(this.inline_elem, function () {
                if (!first_inline && ($(this).hasClass('rr-model'))) {
                    first_inline = this;
                }
            });
        }
        if (!first_inline) {
            $.each(this.inline_elem, function () {
                if (!first_inline && (this.nodeType === 1
                    && $.inArray(this.nodeName, ["SCRIPT", "LINK", "STYLE"]) === -1)) {
                    first_inline = this;
                }
            });
        }
        if (!first_inline) {
            $.each(this.inline_elem, function() {
                if (!first_inline) {
                    first_inline = this;
                }
            });
        }
        this.inline_elem = first_inline;
    }

    this.reference = $(this.inline_elem).data("reference");
    this.id = ($(this.inline_elem).data("routerunner-id")
        ? $(this.inline_elem).data("routerunner-id") : "id_" + this.reference);
    this.class_name = $(this.inline_elem).data("class");
    this.class_id = $(this.inline_elem).data("table_id");
    this.route = $(this.inline_elem).data("route");

    this.property = this.instance(false, false, "property");
    this.position = this.instance(false, false, "position");
    this.visibility = this.instance(false, false, "visibility");
    this.remove = this.instance(false, false, "remove");

    this.label_property = false;

    //this.inputs = this.instance();
    this.panel = {};
    this.helper = helper.model;

    this.change_id = false;
    this.changes = {};
    this.errors = {};

    this.hidden_inputs = [];

    this.init = function () {
        var self = this;
        this.reference = $(this.inline_elem).data("reference");

        if ($(self.inline_elem).data("fields") != undefined) {
            $.each($(self.inline_elem).data("fields"), function (prop_name, prop_data) {
                self.queue(prop_name, self.id + "_ready");
                self.instance(prop_name, new property(self, prop_name, prop_data), "property");
            });
        }

        self.instance("position", new position(self), "position");
        self.position = self.instance("position", false, "position");

        self.instance("visibility", new visibility(self), "visibility");
        self.visibility = self.instance("visibility", false, "visibility");

        self.instance("remove", new remove(self), "remove");
        self.remove = self.instance("remove", false, "remove");

        if (typeof self.ready_fn == "function" && self.id != undefined && self.id != "undefined") {
            self.helper.delayed_call(function(){
                self.ready_fn(self);
            }, function(){
                return self.ready(self.id + "_ready");
            }, undefined, undefined, function() {
                console.log("model unready", self, self.queue());
            });
        }

        this.bind_event();

        if (this.inline_elem) {
            $(this.inline_elem).data("model", this);
        } else {
            console.log("no inline_elem, model:", this);
        }
        this.property = this.instance(false, false, "property");
        //this.position = this.instance(false, false, "position");
        //this.remove = this.instance(false, false, "remove");
    };

    this.destroy = function() {
        routerunner.instance("panel").instance("modelselector").remove(this.id);
    };

    this.deselect = function() {
        this.panel.clear();
    };

    this.bind_event = function() {
        var self = this;
        $(this.inline_elem).on("click", function (evt) {
            self.select();
            if (self.state() != "browse") {
                evt.stopImmediatePropagation();
                evt.stopPropagation();
                return false;
            }
        });
    };

    this.get_label = function(maxchar, resource_uri) {
        var self = this;
        var first_input = false;
        var label_input = false;
        if (this.label_property) {
            this.label_property.label_set(maxchar, resource_uri);
            return this.label_property.get(maxchar, resource_uri);
        } else {
            $.each(this.property, function () {
                if (!first_input) {
                    first_input = this;
                }
                if (!self.label_property && (label_input = this.label_set(maxchar, resource_uri))) {
                    self.label_property = label_input;
                }
                if (!self.label_property) {
                    self.label_property = first_input;
                }
            });
            if (self.label_property) {
                return self.label_property.get(maxchar, resource_uri);
            } else {
                return '';
            }
        }
    };

    this.select = function(property) {
        var self = this;
        if (self.state() == "edit" && this.page.current_model != this) {
            if (this.page.current_model) {
                this.page.current_model.action("deselect", false, "panel");
            }

            var ready_fn = function() {
                self.position.load();
                self.visibility.load();
                self.remove.load();
                self.action("modelselect");
                if (property) {
                    property.focus_panel();
                }
            };
            var _modelpanel = this.instance("panel",
                new modelpanel(this, "#routerunner-model-panel", ready_fn), "panel");
            this.panel = _modelpanel;
            routerunner.current_modelpanel = _modelpanel;

            this.page.current_model = this;

            if ($.inArray(this.id, routerunner.affected_models) === -1) {
                routerunner.affected_models.push(this.id);
            }

            routerunner.instance("panel").instance("modelselector").select(this.inline_elem);
        }
    };

    this.get_errors = function() {
        var errors = 0;
        $.each(this.property, function() {
            if (this.has_error()) {
                errors++;
            }
        });
        return errors;
    };

    this.set_changes = function(field, value) {
        var self = this;

        this.changes[field] = value;
        var url = routerunner.settings["BACKEND_DIR"] + '/backend/ajax/model/set_changes.php';
        var data = {
            change_id: this.change_id,
            reference: $(this.inline_elem).data("reference"),
            changes: this.changes,
            state: null
        };
        var params = {
            dataType: 'json'
        };
        this.helper.ajax(url, data, params, function(data){
            if (data.success && data.change_id) {
                self.change_id = data.change_id;
            }
        }, function(){
            alert("fail happened in set changes");
        });

        $(".jstree #jstreenode_" + $(this.inline_elem).data("reference") + ".jstree-node > a.jstree-anchor > span.label")
            .text(this.get_label());

        routerunner.queue(this, "refresh");
        routerunner.action("refresh");
    };
    this.revert_changes = function(field) {
        if (!field) {
            this.changes = {};
        } else {
            delete this.changes[field];
        }
    };

    this.init();
};