/**
 * Created by csibi on 2014.10.17..
 */

changes_panel = function(caller, selector) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.selector = selector;
    this.panel = false;
    this.helper = helper.panel;
    this.looked = false;
    this.current_model = false;
    this.icon = {
        on: "fa-hand-o-down",
        off: "fa-history",
        error: "fa-exclamation-circle"
    };
    this.state = "off";

    this.init = function () {
        var self = this;
        this.panel = $(this.selector);

        $("#action-mode-changes").bind("click", function(){
            self.toggle(this);
            return false;
        });

        $("#routerunner-changes-close").bind("click", function() {
            self.off();
        });
        $("#routerunner-changes-refresh").bind("click", function() {
            self.refresh();
        });
    };

    this.icon_set = function(state) {
        var self = this;
        if (!state) {
            state = ($("#action-mode-changes .badge").length ? "error" :
                (self.panel.is(":visible") ? "on" : "off"));
        }
        self.state = state;
        var states = Object.keys(this.icon);
        $.each(states, function() {
            if (this != self.state) {
                $("#action-mode-changes .icon").removeClass(self.icon[this]);
            } else {
                $("#action-mode-changes .icon").addClass(self.icon[this]);
            }
        });
    };

    this.on = function() {
        var self = this;
        this.content();
        if (self.panel) {
            this.icon_set("on");
            $("#routerunner-modelselector-panel, #routerunner-model-panel").slideUp(200, function() {
                self.panel.slideDown(200);
            });
        }
    };
    this.off = function() {
        var self = this;
        this.panel.slideUp(200, function() {
            $("#routerunner-modelselector-panel, #routerunner-model-panel").slideDown(200);
            self.icon_set();
        });
    };

    this.toggle = function() {
        if (this.panel.is(":visible")) {
            this.off();
        } else {
            this.on();
        }
    };

    this.content = function() {
        var self = this;
        $("#routerunner-changes").children().remove();

        var changes = routerunner.get("changes");
        if (!changes) {
            changes = [];
        }

        var li = $("<li></li>").attr("id", "session-opened");

        var content = this.helper.get_changes_content_row("fa fa-undo", "Session", "opened",
            routerunner.get("session_open_date"));

        li.append(content);

        /*
        // temporary removed
        li.bind("click", function() {
            self.undo_preview(this, -1);
        });
        */

        $("#routerunner-changes").append(li);

        $.each(changes, function(index, change_label) {
            var change = routerunner.common.change_by_label(change_label);

            if (change) {
                self.changerow(change, index);
            }
        });

        var undo_state = routerunner.get("undo_state");
        if (undo_state !== false) {
            routerunner.set("undo_state", false);
            $("#routerunner-changes li:eq(" + undo_state + ")").trigger("click");
        }
    };

    this.changerow = function(change, index) {
        var self = this;

        var change_caller = change.caller;
        var model = change.model;

        var icon = "fa fa-question-circle";
        if (change_caller instanceof pageproperties) {
            icon = "fa fa-code";
        } else if (change_caller instanceof visibility) {
            icon = "fa fa-eye-slash";
        } else if (change_caller instanceof property) {
            icon = "fa fa-pencil-square";
        } else if (change.caller && change.caller.property) {
            switch (change.caller.property) {
                case "insert":
                    icon = "fa fa-plus-square";
                    break;
                case "move":
                    icon = "fa fa-external-link-square";
                    break;
                case "remove":
                    icon = "fa fa-minus-square";
                    break;
            }
        }

        var li = $("<li></li>").attr("id", change.label);

        if (change_caller instanceof pageproperties) {
            model_label = "Meta data";
        } else if (change_caller instanceof routerunner_input) {
            model_label = change_caller.form.id;
        } else {
            var model_label = "#" + model.reference;
            if (tmp_label = model.get_label()) {
                model_label = tmp_label;
            }
        }

        var property_label = "";
        if (change_caller.property) {
            property_label = change_caller.property;
        } else if ((changes_keys = Object.keys(change.changes)).length > 0) {
            property_label = changes_keys.shift();
        }

        var errors = change.errorlist();

        var content = self.helper.get_changes_content_row(icon, model_label, property_label,
            change.data.date, errors);

        li.append(content);

        /*
        // temporary removed
        li.bind("click", function() {
            self.undo_preview(this, index);
        });
        */

        if ($("#routerunner-changes").children("[id='" + change.label + "']").length) {
            $("#routerunner-changes").children("[id='" + change.label + "']").replaceWith(li);
        } else {
            $("#routerunner-changes").append(li);
        }
    };

    this.undo_preview = function(elem, index) {
        var self = this;

        var changes = routerunner.get("changes");
        var undo_index = index+1;

        var undo_state = routerunner.get("undo_state");
        if (undo_state === false) {
            undo_state = changes.length;
        }
        var change = false;
        if (undo_index > undo_state) {
            // redo from undo_state to undo_index
            for (var i = undo_state + 1; i <= undo_index; i++) {
                if (change = routerunner.common.change_by_label(changes[i-1])) {
                    change.redo();
                    $("#routerunner-changes li:eq(" + i + ")").removeClass("undid");
                }
            }
        } else if (undo_index < undo_state) {
            // undo from undo_state to undo_index
            for (var i = undo_state; i > undo_index; i--) {
                if (change = routerunner.common.change_by_label(changes[i-1])) {
                    change.undo();
                    $("#routerunner-changes li:eq(" + i + ")").addClass("undid");
                }
            }
        }
        if (undo_index == changes.length) {
            undo_index = false;
        }
        routerunner.set("undo_state", undo_index);

        $("#routerunner-changes li .task-config").remove();

        /*
        if (routerunner.page.current_model) {
            this.current_model = routerunner.page.current_model;
        }
        */
        if (undo_index !== false) {
            //routerunner.page.current_model = false;
            if (routerunner.state() != "disabled") {
                routerunner.state("disabled");
            }

            var btn = $('<div class="task-config"><a class="btn btn-xs btn-warning btn-undo" href="javascript:;">' +
            '<i class="fa fa-undo"></i> Apply</a></div>');
            btn.bind("click", function() {
                self.undo_apply(this, index);
            });
            $(elem).append(btn);

            routerunner.panel.action.changes_waiting(true);
        } else {
            //routerunner.page.current_model = false;
            routerunner.action("enabled");
            routerunner.state("edit");
            routerunner.panel.action.changes_waiting(false);
        }
        /*
        if (this.current_model) {
            this.current_model.select();
        }
        */
    };

    this.undo_apply = function(elem, index) {
        var self = this;

        var changes = routerunner.get("changes");
        var undo_index = index+1;
        var change;

        for (var i = changes.length-1; i >= undo_index; i--) {
            if (change = routerunner.common.change_by_label(changes[i])) {
                change.destroy();
                $("#routerunner-changes li:eq(" + (i + 1) + ")").remove();
            }
        }
        $(elem).find(".task-config").remove();
        routerunner.page.current_model = false;
        routerunner.state("edit");
        routerunner.panel.action.changes_waiting(false);
    };

    this.revert = function() {
        var self = this;
        this.helper.delayed_call(function() {
            self.refresh();
        }, function() {
            return (!routerunner.get("changes").length);
        });
        routerunner.page.current_model = false;
        routerunner.state("edit");
        routerunner.panel.action.changes_waiting(false);
    };
    this.refresh = function(change, force) {
        if (this.panel.is(":visible") || force) {
            if (change && change instanceof changed) {
                var index = $.inArray(change.label, routerunner.get("changes"));
                this.changerow(change, index);
            } else {
                this.content();
            }
        }
    };
    this.save = function(change_id) {
        if (change_id == undefined) {
            change_id = false;
        }

        var url = routerunner.settings["BACKEND_DIR"] + '/backend/ajax/model/save_changes.php';
        var data = {};
        if (change_id) {
            data = {
                change_id: change_id
            };
        }
        var params = {
            dataType: 'json'
        };
        this.helper.ajax(url, data, params, function(data){
            if (data.success && data.change_id) {
                self.change_id = data.change_id;
            }
        }, function(){
            alert("fail happened in save changes");
        });
    };

    this.init();
};