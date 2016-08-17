/**
 * Created by csibi on 2014.10.17..
 */

movement_panel = function(caller, id) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.id = id;
    this.class = "routerunner-movement";
    this.selector = "#" + id;
    this.menu_selector = "#routerunner-model-navbar ul.nav > #model-panel-movement-menu";
    this.panel = false;
    this.helper = helper.modelpanel;

    this.caller = caller;
    this.model = caller.model;

    this.tree = false;

    this.change_id = false;
    this.changes = {};

    this.content = {
        url: routerunner.settings["BACKEND_DIR"] + "/backend/ajax/modelpanel/movement.php",
        data: {
            reference: this.model.reference,
            model_class: this.model.model_class,
            route: this.model.route
        },
        params: {}
    };

    this.init = function() {
        var self = this;

        this.panel = this.helper.panel_content(this, function(panel) {
            var types = $("#routerunner-movement").data("jstreetypes");
            var jstree = $("#routerunner-tree").jstree({
                "core" : {
                    "check_callback" : true,
                    "themes" : {
                        "responsive": false
                    },
                    "data": {
                        "url": routerunner.settings["BACKEND_DIR"] + "/backend/ajax/common/node.php",
                        "method": "get",
                        "data": function (node) {
                            var container = ((self.model && self.model.inline_elem) ? self.model.inline_elem : false);
                            var _data =  {
                                "reference": (node.data ? node.data.reference :
                                    ((container && $(container).data("treeroot"))
                                        ? $(container).data("treeroot") : null)),
                                "model_class": (node.data ? node.data.model_class : null),
                                "table_id": (node.data ? node.data.table_id : null),
                                "route": (node.data ? node.data.route :
                                    ((container && $(container).data("traverse"))
                                        ? $(container).data("traverse") : "")),
                                "current": ((self.model && self.model.reference && self.model.class_id
                                    && self.model.class_id > 0) ? self.model.reference :
                                        ((self.model.position && self.model.position.states &&
                                        self.model.position.states.current && self.model.position.states.current.to &&
                                        self.model.position.states.current.to.parent)
                                            ? self.model.position.states.current.to.parent : false))
                            };
                            return _data;
                        }
                    },
                    "check_callback": true
                },
                "types" : types,

                "dnd" : {
                    "is_draggable" : function(node) {
                        if (node[0].state.disabled || self.state() == "disabled") {
                            return false;
                        }
                        return true;
                    }
                },

                "plugins": [ "types", "dnd" ]
            });

            this.tree = jstree;

            jstree.on("move_node.jstree", function (e, data) {
                if (!$("#routerunner-tree").data("skip-move")) {
                    var parent = false;
                    if (data.parent.substr(0, 11) == 'jstreenode_') {
                        parent = data.parent.substr(11);
                    } else if (data.parent == '#') {
                        parent = 0;
                    }
                    var children = $.jstree.reference(this.id).get_node(data.parent).children;

                    var ref = false;
                    if (data.node.id.substr(0, 11) == 'jstreenode_') {
                        ref = data.node.id.substr(11);
                    } else if (data.node.id == '#') {
                        ref = 0;
                    }

                    if (ref) {
                        var model = routerunner.container.models_by_ref[ref];
                    }

                    var position = (data.position >= 0 ? data.position - 1 : -1);

                    var createds = $("#routerunner-tree").data("created");

                    var prev = false;
                    if (children[position] && children[position].substr(0, 11) == 'jstreenode_') {
                        prev = children[position].substr(11);
                    } else if (children[position] && createds && createds[children[position]]) {
                        prev = createds[children[position]]["reference"];
                    } else if (position == -1) {
                        prev = 0;
                    } else if (children[position] == '#') {
                        prev = 0;
                    }

                    var container = new Array();
                    if ($.isArray(model)) {
                        $.each(model, function() {
                            if ($(this.inline_elem)
                                    .closest(".routerunner-container[data-reference='" + parent + "']").length) {
                                container.push($(this.inline_elem)
                                    .closest(".routerunner-container[data-reference='" + parent + "']"));
                            }
                        });
                        model = model.shift();
                    } else if (model && model.inline_elem) {
                        if ($(model.inline_elem)
                                .closest(".routerunner-container[data-reference='" + parent + "']").length) {
                            container.push($(model.inline_elem)
                                .closest(".routerunner-container[data-reference='" + parent + "']"));
                        }
                    }
                    if (!container.length && model && model.inline_elem
                        && $(model.inline_elem).closest(".routerunner-container").length) {
                        container.push($(model.inline_elem).closest(".routerunner-container"));
                    }
                    if (!container.length
                        && $(routerunner.content_document).find(".routerunner-container[data-reference='" +
                            parent + "']").length) {
                        container.push($(routerunner.content_document).find(".routerunner-container[data-reference='" +
                            parent + "']"));
                    }
                    if (parent === 0 && container.length && container[0].data("parent")) {
                        parent = container[0].data("parent");
                    }

                    var move = {
                        "parent": parent,
                        "prev": prev
                    };

                    if (model) {
                        model.position.set(move, true, container);
                    }
                }
            });

            jstree.on("ready.jstree", function (e) {
                self.update_tree();
            });


            self.caller.unqueue("movement_panel");
        });
    };

    this.update_tree = function() {
        var self = this;
        var changes = routerunner.get("changes");
        if (!changes) {
            changes = [];
        }

        // first set position changes on tree (each position update refreshes tree)
        $.each(changes, function(index, change_label) {
            var change = routerunner.common.change_by_label(change_label);
            if (change) {
                var object = change.caller;
                if (object instanceof position && typeof object.update == "function") {
                    object.update(change_label);
                }
            }
        });

        // then set label updates on tree
        $.each(changes, function(index, change_label) {
            var change = routerunner.common.change_by_label(change_label);
            if (change) {
                var object = change.caller;
                if (object instanceof property && typeof object.update == "function") {
                    object.update();
                }
            }
        });
    };

    this.destroy = function() {
        this.panel.remove();
    };

    this.init();
};