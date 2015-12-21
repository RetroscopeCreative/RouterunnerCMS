/**
 * Created by csibi on 2015.05.04..
 */
pointers = function() {
    var pointer_root = this;

    this.pointers = $("<div></div>");
    this.pointers.attr("id", "root");
    this.pointers.append("<div id='0'></div>");

    this.changes_on_pointers = {};

    pointer = function(reference, parent, prev) {
        this.object = $("<div></div>");

        this.reference = reference;
        this.object.attr("id", reference);
        this.object.data("pointer", this);

        this.changed_by_label = {};

        this.init = function() {
            var prev_object = false;
            if (prev && pointer_root.pointers.find("#" + prev).length) {
                prev_object = pointer_root.pointers.find("#" + prev);
            }

            if (prev_object) {
                prev_object.after(this.object);
            } else if (parent !== false) {
                var parent_object = pointer_root.pointers;
                if (parent && parent != "0" && !pointer_root.pointers.find("#" + parent).length) {
                    pointer_root.pointers.append("<div id='" + parent + "'></div>");
                }
                if (parent && pointer_root.pointers.find("#" + parent).length) {
                    parent_object = pointer_root.pointers.find("#" + parent);
                }

                parent_object.prepend(this.object);
            }
        };

        this.pointer_prev = function() {
            if (this.object.prev().length) {
                return this.object.prev().attr("id");
            } else {
                return 0;
            }
        };
        this.pointer_next = function() {
            if (this.object.next().length) {
                return this.object.next().attr("id");
            } else {
                return false;
            }
        };
        this.pointer_children = function() {
            var ret = [];
            this.object.children().each(function() {
                ret.push(this.attr("id"));
            });
            return ret;
        };
        this.pointer_parent = function() {
            if (this.object.parent().length) {
                return this.object.parent().attr("id");
            } else {
                return false;
            }
        };

        this.move = function(change_label, parent, prev) {
            var self = this;

            // change part of action
            var change = function() {
                if (prev && (move_after = pointer_root.pointers.find("#" + prev)).length) {
                    move_after.after(self.object);
                } else {
                    if ((move_to_parent = pointer_root.pointers.find("#" + parent)).length) {
                        move_to_parent.prepend(self.object);
                    } else if (pointer_root.pointers.is("#" + parent)) {
                        move_to_parent = pointer_root.pointers;
                        move_to_parent.prepend(self.object);
                    }
                }
            };

            // undo part of action
            var after = false;
            var before = false;
            var prepend = false;
            if (self.object.prev().length) {
                after = self.object.prev();
            } else if (self.object.next().length) {
                var before = self.object.next();
            } else if (self.object.parent().length) {
                var prepend = self.object.parent();
            }
            var undo = function() {
                if (after) {
                    after.after(self.object);
                } else if (before) {
                    before.before(self.object);
                } else if (prepend) {
                    prepend.prepend(self.object);
                }
            };

            change();

            this.store_change(change_label, undo, change);
        };

        this.insert = function(change_label, parent, prev) {
            var self = this;

            // change part of action
            var change = function() {
                if (prev && (move_after = pointer_root.pointers.find("#" + prev)).length) {
                    move_after.after(self.object);
                } else {
                    if ((move_to_parent = pointer_root.pointers.find("#" + parent)).length) {
                        move_to_parent.prepend(self.object);
                    } else if (pointer_root.pointers.is("#" + parent)) {
                        move_to_parent = pointer_root.pointers;
                        move_to_parent.prepend(self.object);
                    }
                }
            };

            // undo part of action
            var undo = function() {
                self.object.remove();
            };

            change();

            this.store_change(change_label, undo, change);
        };

        this.remove = function(change_label) {
            var self = this;

            // change part of action
            var change = function() {
                self.object.remove();
            };

            // undo part of action
            var after = false;
            var before = false;
            var prepend = false;
            if (self.object.prev().length) {
                after = self.object.prev();
            } else if (self.object.next().length) {
                var before = self.object.next();
            } else if (self.object.parent().length) {
                var prepend = self.object.parent();
            }
            var undo = function() {
                if (after) {
                    after.after(self.object);
                } else if (before) {
                    before.before(self.object);
                } else if (prepend) {
                    prepend.prepend(self.object);
                }
            };

            change();

            this.store_change(change_label, undo, change);
        };

        this.store_change = function(change_label, undo, redo) {
            this.changed_by_label[change_label] = {
                "undo": undo,
                "redo": redo
            };
            pointer_root.changes_on_pointers[change_label] = this;
        };

        this.undo = function(change_label) {
            if (this.changed_by_label[change_label]) {
                this.changed_by_label[change_label].undo();
            }
        };
        this.redo = function(change_label) {
            if (this.changed_by_label[change_label]) {
                this.changed_by_label[change_label].redo();
            }
        };

        this.get_pointers = function(parent_ref, prev_ref) {
            var ret = {
                "parent": 0,
                "prev": 0
            };
            ret.parent = parent_ref.parent().attr("id");
            if (prev_ref.prev().length) {
                ret.prev = prev_ref.prev().attr("id");
            }
            return ret;
        };

        this.init();
    };
};