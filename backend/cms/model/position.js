/**
 * Created by csibi on 2015.04.22..
 */
position = function(caller) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.model = caller;

    this.panel = false;

    this.changes = this.instance();
    this.states = {
        "revert": false,
        "undo": [],
        "current": { "from": false, "to": false }
    };
    this.errors = {};
    this.helper = helper.model;

    /* object specific props */
    this.pointer = false;
    this.reference = false;
    this.parent = false;
    this.prev = false;

    this.init = function() {
        if (this.model.inline_elem && this.model.class_id > 0) {
            this.states.revert = {
                "parent": $(this.model.inline_elem).data("parent"),
                "prev": $(this.model.inline_elem).data("prev")
            };
            this.parent = this.states.revert.parent;
            this.prev = this.states.revert.prev;
        }
        this.reference = this.model.reference;

        this.pointer = new pointer(this.model.reference, this.parent, this.prev);
        //this.store_position();
    };

    this.change = function(move) {
        var pointer_changes = {};

        var from_parent = false;
        var from_children = false;

        var from = false;
        if (this.reference && this.pointer.pointer_parent()) {
            from = {
                "parent": parseInt(this.pointer.pointer_parent()),
                "prev": parseInt(this.pointer.pointer_prev())
            };
        }

        var to = false;
        move.parent = parseInt(move.parent);
        move.prev = parseInt(move.prev);


        if (move && move.parent != undefined && move.prev != undefined) {
            to = {
                "parent": move.parent,
                "prev": move.prev
            };
        }

        var field = "move";
        if (!from && to) {
            field = "insert";
        } else if (from && !to) {
            field = "remove";
        }

        var change = {
            "from": from,
            "to": to
        };

        this.states.current = change;

        if (!this.skip_set_change) {
            var change_obj = this.create_change({"section": this, "field": field, "value": change});

            if (field == "move") {
                this.pointer.move(change_obj.label, to.parent, to.prev);
            } else if (field == "insert") {
                this.pointer.insert(change_obj.label, to.parent, to.prev);
            } else if (field == "remove") {
                this.pointer.remove(change_obj.label);
            }

            //routerunner.move(this.model.id, from, false);
        }
    };

    this.store_position = function() {
        routerunner.set("position/" + this.reference, this);
        var parent = (this.parent ? this.parent : 0);
        var children = routerunner.get("parent/" + parent);
        if (!children) {
            children = [];
        }
        children.push(this.reference);
        routerunner.set("parent/" + parent, children);
    };

    /* panel input functions */
    this.set = function(value, panel_set, container) {
        this.change(value);
        this.inline_set(container);
        if (!panel_set) {
            this.panel_set();
        }
    };

    this.force_blur = function() {
        // blur something?
        routerunner.page.unqueue(this.model.id + ".position", "apply_ready");
    };

    this.inline_set = function(container) {
        var value_object = $.extend({}, this.states.current);

        if (!value_object.from && value_object.to) {
            // insert model into inline
            if (this.model.inline_elem && this.model.outer_html) {
                var parent_to_move_to, i = 0, len = (container && container.length ? container.length : 1);
                while (i < len) {
                    if (container && container.length) {
                        parent_to_move_to = ($.isArray(container) ? container.shift() : container);
                    } else {
                        parent_to_move_to = $(routerunner.content_document).find(".routerunner-container[data-parent='" +
                        value_object.to.parent + "']");
                    }
                    var inline_elem = (this.model.inline_elem instanceof jQuery
                        ? this.model.inline_elem : $(this.model.inline_elem));
                    var child_selector = "> .routerunner-model";
                    if (inline_elem.data("child_selector")) {
                        child_selector = _.unescape(inline_elem.data("child_selector"));
                    } else if (parent_to_move_to.data("child_selector")) {
                        child_selector = _.unescape(parent_to_move_to.data("child_selector"));
                    }
                    if (value_object.to.prev == 0 && parent_to_move_to.find(child_selector).parent().length) {
                        $(parent_to_move_to.find(child_selector).parent().get(0)).prepend(this.model.outer_html);
                    } else if (parent_to_move_to.find(child_selector + "[data-reference='" + value_object.to.prev + "']").length) {
                        $(parent_to_move_to.find(child_selector + "[data-reference='" + value_object.to.prev + "']").get(0)).after(this.model.outer_html);
                    } else if (parent_to_move_to.find(child_selector).parent().length) {
                        $(parent_to_move_to.find(child_selector).parent().get(0)).append(this.model.outer_html);
                    } else if (value_object.to.prev == 0) {
                        $(parent_to_move_to.get(0)).prepend(this.model.outer_html);
                    } else {
                        $(parent_to_move_to.get(0)).append(this.model.outer_html);
                    }
                    $.each(this.model.outer_html, function() {
                        if (this.nodeType === 1 && this.nodeName !== "SCRIPT") {
                            if ($(this).is(":hidden")) {
                                var parent_object = $(this);
                                var hidden_parent = false;
                                while (!hidden_parent && parent_object.length) {
                                    if (parent_object.parent().is(":visible")) {
                                        hidden_parent = parent_object;
                                    } else {
                                        parent_object = parent_object.parent();
                                    }
                                }
                                if (hidden_parent) {
                                    hidden_parent.removeAttr("style");
                                    if (hidden_parent.is(":hidden")) {
                                        hidden_parent.show();
                                    }
                                    if (hidden_parent.is(":hidden")) {
                                        hidden_parent.css("display", "block");
                                    }
                                }
                            }

                            var scroll_to;
                            if ($(this).offset().top > ($(window).scrollTop() + $(window).height())
                                || $(this).offset().top < $(window).scrollTop()) {
                                scroll_to = $(this).offset().top;
                                $(routerunner.content_document).find("body").stop(true, true).animate({
                                    "scrollTop": scroll_to
                                }, 200);
                            }
                        }
                    });
                    i++;
                }
                this.model.bind_event();
                this.model.state("browse");
                this.model.state("disable");
            }
        } else if (value_object.from && !value_object.to) {
            // remove model from inline
            this.remove_inline(this.model.inline_elem);
        } else if (value_object.from && value_object.to) {
            // move model in inline environment
            var parent_to_move_to, i = 0, len = (container && container.length ? container.length : 1);
            while (i < len) {
                if (container && container.length) {
                    parent_to_move_to = ($.isArray(container) ? container.shift() : container);
                } else {
                    parent_to_move_to = $(routerunner.content_document).find(".routerunner-container[data-parent='" +
                    value_object.to.parent + "']");
                }
                var child_selector = (parent_to_move_to.data("child_selector")
                    ? _.unescape(parent_to_move_to.data("child_selector")) : "> .routerunner-model");
                if (value_object.to.prev == 0 && parent_to_move_to) {
                    parent_to_move_to.find(child_selector).parent().prepend(this.model.inline_elem);
                } else if (parent_to_move_to.find(child_selector + "[data-reference='" + value_object.to.prev + "']").length) {
                    parent_to_move_to.find(child_selector + "[data-reference='" + value_object.to.prev + "']").after(this.model.inline_elem);
                } else if (parent_to_move_to.find(child_selector + ":eq(0)").length) {
                    parent_to_move_to.find(child_selector + ":eq(0)").before(this.model.inline_elem);
                } else {
                    parent_to_move_to.find(child_selector).parent().append(this.model.inline_elem);
                }
                i++;
            }
        }
    };
    this.panel_set = function(value_object) {
        if (!value_object) {
            value_object = this.states.current;
        }
        var tree = $.jstree.reference("routerunner-tree");
        var node = false;
        var parent = false;
        var prev = false;

        if (tree && !value_object.from && value_object.to) {
            // insert model into tree
            var inline = $(this.model.inline_elem).data();

            parent = "#";
            if (value_object.to.parent > 0 && tree.get_node("jstreenode_" + value_object.to.parent)) {
                parent = tree.get_node("jstreenode_" + value_object.to.parent);
            }
            if (!parent) {
                parent = "#";
            }
            var icon = "fa fa-plus icon-state-info";
            if (inline["override"] && inline["override"]["icon"]) {
                icon = inline["override"]["icon"];
            }
            var li_attr = {
                "data-reference": this.model.reference,
                "data-table_id": this.model.class_id,
                "data-model_class": this.model.class_name,
                "data-jstree": {
                    "type": this.model.class_name,
                    "icon": icon
                },
                "id": "jstreenode_" + this.model.reference
            };
            node = {
                "type": this.model.class_name,
                "text": "<span class='tree-label label label-info'>" + this.model.get_label(20) + "</span> <span class='label label-danger'>new " + this.model.class_name + "</span>",
                "icon": icon,
                "state": {
                    "opened": false,
                    "disabled": false,
                    "selected": ((routerunner.page.current_model && routerunner.page.current_model.reference == this.model.reference) ? true : false)
                },
                "children": [],
                "li_attr": li_attr,
                "a_attr": {}
            };
            prev = 0;
            if (value_object.to.prev > 0) {
                var prev_dom = tree.get_node("jstreenode_" + value_object.to.prev, true);
                if (prev_dom) {
                    prev = $(prev_dom).index() + 1;
                }
            }
            tree.create_node(parent, node, prev);
            if (parent != "#") {
                tree.open_node(parent);
            }

            // insert model into model_selector?

        } else if (tree && value_object.from && !value_object.to) {
            // remove model from tree
            this.remove_panel(this.model.inline_elem);

            // remove model from model_selector?

        } else if (tree && value_object.from && value_object.to) {
            // move model in tree
            node = tree.get_node("jstreenode_" + this.model.reference);
            parent = "#";
            if (value_object.to.parent > 0) {
                parent = tree.get_node("jstreenode_" + value_object.to.parent);
            }
            if (!parent) {
                parent = "#";
            }
            prev = 0;
            if (value_object.to.prev > 0) {
                var prev_dom = tree.get_node("jstreenode_" + value_object.to.prev, true);
                if (prev_dom) {
                    prev = $(prev_dom).index() + 1;
                }
            }
            $("#routerunner-tree").data("skip-move", true);
            tree.move_node(node, parent, prev);
            $("#routerunner-tree").data("skip-move", false);
        }
    };

    this.get = function() {
        return $.extend({}, this.states.current);
    };
    this.load = function() {
        if (this.model.panel.movement) {
            this.panel = this.model.panel.movement;
        }
        this.event();
    };

    this.remove = function(elem) {
        this.remove_inline(elem);
        this.remove_panel(elem);
        this.remove_children(elem);
    };
    this.remove_inline = function(elem) {
        $(elem).remove();
        /*
        if (this.undo === true) {
            $(elem).removeClass("routerunner-to-remove");
        } else {
            $(elem).addClass("routerunner-to-remove");
        }
        */
    };
    this.remove_panel = function(elem) {
        var tree = $.jstree.reference("routerunner-tree");
        var node = tree.get_node("jstreenode_" + $(elem).data("reference"));
        if (this.model.class_id < 0) {
            if (this.undo === true) {
                alert("undo new");
            } else {
                tree.delete_node(node);
            }
        } else {
            if (this.undo === true) {
                tree.enable_node(node);
            } else {
                tree.disable_node(node);
            }
        }
    };
    this.remove_children = function(elem) {
        var self = this;
        var reference = $(elem).data("reference");
        if (reference) {
            if ($(routerunner.content_document).find(".routerunner-model[data-parent=" + reference + "]").length) {
                $(routerunner.content_document).find(".routerunner-model[data-parent=" +
                    reference + "]").each(function () {
                    self.remove(this);
                });
            }
        }
    };

    this.update = function(label) {
        var change = this.instance(label);
        this.panel_set(change.changes);
    };

    this.event = function() {
        var self = this;

        // jstree drag&drop

        // insert elem

        // remove elem?

    };

    this.init();
};