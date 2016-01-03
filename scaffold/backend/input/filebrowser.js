/**
 * Created by csibi on 2015.02.11..
 */
filebrowser = function (_model, _input, _field) {
    this.model = _model;
    this.input = (this.input ? this.input : _input);
    var field = _field;
    this.field_data = {};
    var mediasize = (_field.mediasize ? _field.mediasize : "m");

    this.states = {
        "revert": {},
        "undo": [],
        "current": {}
    };

    this.img_object = {inline: false, panel: false};
    this.filebrowser = {inline: false, panel: false};
    this.file_form = {inline: false, panel: false};
    this.property_holder = {inline: false, panel: false};
    this.holder = {inline: false, panel: false};
    this.modal = false;
    this.modal_body = false;

    var _revert = $(this.input).attr("src");

    this.is_panel = function(target) {
        return (this.panel_input && $(target).attr("id") == this.panel_input.attr("id"));
    };

    this.panel_init = function() {
        var self = this;

        var img;
        if ($(this.panel_input).is("img")) {
            this.img_object["panel"] = $(this.panel_input);
        } else {
            this.img_object["panel"] = $(this.panel_input).find("img:eq(0)");
        }
        if (!this.return_data) {
            this.return_data = this.img_object["panel"].attr("src");
        }

        this.holder["panel"] = this.img_object["panel"].closest(".panel-property");

        if (!$("body").find(".browse-modal").length) {
            this.modal = $('<div class="browse-modal modal fade" style="width: 80%; height: 80%; padding: 0; padding-right: 0 !important; margin: auto auto;" role="dialog" aria-hidden="true"> \
                                <div class="modal-dialog"> \
                                    <div class="modal-content"> \
                                        <div class="modal-header"> \
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button> \
                                            <h4 class="modal-title">File browser</h4> \
                                        </div> \
                                        <div class="modal-body"> \
                                        </div> \
                                    </div> \
                                </div> \
                            </div>');
            $("body").append(this.modal);
        } else {
            this.modal = $("body").find(".browse-modal");
        }
        this.modal_body = this.modal.find(".modal-body");
        this.panel_edit();
    };
    this.inline_init = function() {
        var self = this;

        var img;
        if ($(this.inline_input).is("img")) {
            this.img_object["inline"] = $(this.inline_input);
        } else {
            this.img_object["inline"] = $(this.inline_input).find("img:eq(0)");
        }
        if (!this.return_data) {
            this.return_data = this.img_object["inline"].attr("src");
        }

        if (this.img_object["inline"].is(".routerunner-model")) {
            this.holder["inline"] = this.img_object["inline"];
        } else if (this.img_object["inline"].closest(".routerunner-model").length) {
            this.holder["inline"] = this.img_object["inline"].closest(".routerunner-model");
        }

        if (!$("body").find(".browse-modal").length) {
            this.modal = $('<div class="browse-modal modal fade" role="dialog" aria-hidden="true"> \
                                <div class="modal-dialog"> \
                                    <div class="modal-content"> \
                                        <div class="modal-header"> \
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button> \
                                            <h4 class="modal-title">File browser</h4> \
                                        </div> \
                                        <div class="modal-body"> \
                                        </div> \
                                    </div> \
                                </div> \
                            </div>');
            $("body").append(this.modal);
        } else {
            this.modal = $("body").find(".browse-modal");
        }
        this.modal_body = this.modal.find(".modal-body");
    };

    this.panel_edit = function() {
        this.inline_edit("panel");
    };

    this.panel_destroy = function() {
        var type = "panel";
        if (this.filebrowser["panel"]) {
            this.img_object["panel"] = false;
            this.file_form["panel"] = false;
            this.property_holder["panel"] = false;
            this.holder["panel"] = false;
            this.filebrowser["panel"] = false;
        }
    };

    this.inline_edit = function(type) {
        if (!type) { type = "inline"; }
        var self = this;

        if (!this.filebrowser[type] && this.holder[type].length) {
            // construct browser
            if (type == "panel") {
                this.property_holder[type] = this.holder[type].find(".property-data_" + mediasize);
            }

            var data = this.holder[type].data();
            if ($.isEmptyObject(this.field_data) && data.fields && data.fields["" + mediasize]
                && typeof data.fields["" + mediasize] == "object") {
                this.field_data = data.fields["" + mediasize];
            } else if ($.isEmptyObject(this.field_data) && data.fieldData) {
                this.field_data = data.fieldData;
            }

            var filename = (self.return_data ? self.return_data.substr(self.return_data.lastIndexOf("/")+1) : "");
            if (filename.indexOf("?") > -1) {
                filename = filename.substr(0, filename.indexOf("?"));
            }
            this.file_form[type] = $("<div class='frm row' style='padding: 0 15px;'><input type='text' class='col-md-9' style='padding: 1px 5px;' readonly value='" + filename + "' /><button class='browse col-md-3'>Browse</button></div>");
            this.file_form[type].find(".browse").on("click", function () {
                var dir = routerunner.settings["MEDIA_ROOT"].substring(0, routerunner.settings["MEDIA_ROOT"].length - 1);
                var url = routerunner.settings["BACKEND_DIR"] + '/backend/thirdparty/kcfinder/browse.php?type=' + dir + '&dir=' + dir + '/public';

                self.modal_body.kcfinder({
                    url: url,
                    theme: "default",
                    lang: "hu",
                    callback: function (file) {
                        file = file.replace(routerunner.settings["BASE"], "");

                        self.change_value(file, self.img_object[type]);
                        self.force_change = true;
                        if (self.img_object["inline"]) {
                            self.img_object["inline"].attr("src", file);
                        }
                        if (self.img_object["panel"]) {
                            self.img_object["panel"].attr("src", file);
                        }
                        self.force_change = false;
                        self.file_form[type].find("input").val(file.substr(file.lastIndexOf("/") + 1));
                        self.modal.modal("hide");
                    },
                    callbackMultiple: function (files) {
                        //alert('Selected files:\n  "' + files.join('",\n  "') + '"');
                    }
                });

                self.modal.on('shown.bs.modal', function() {
                    self.modal_body.height(self.modal.height() - self.modal.find(".modal-header").outerHeight() - 4);
                });
                self.modal.modal("show");
                return false;
            });
            this.img_object[type].before(this.file_form[type]);

            this.filebrowser[type] = true;
        } else if (this.filebrowser[type]) {
            this.file_form[type].find(".browse").attr("disabled", false).css("opacity", 1);
        }
    };

    this.inline_browsed = function(value) {
        return this.browsed(value, "inline");
    };
    this.panel_browsed = function(value) {
        return this.browsed(value, "panel");
    };

    this.browsed = function(value, type) {
        var self = this;
        if (type && !$.isArray(type)) {
            types = [type];
        } else {
            types = ["panel", "inline"];
        }
        $.each(types, function(index, current_type) {
            if (self.img_object[current_type]) {
                self.img_object[current_type].attr("src", value);
            }
            if (self.file_form[type]) {
                self.file_form[type].find("input").val(value);
            }
        });
        return this.return_data;
    };

    this.change_value = function(value, target) {
        var self = this;

        var changed_by_other = false;
        var last_data = this.return_data;
        self.return_data = value;

        if (!self.skip_set_property) {
            if (self.field_data && self.field_data.connected) {
                var property_name = self.field_data.connected.property || false;
                var connected_property = self.model.property[property_name] || false;
                if (connected_property) {
                    var connected_filter = self.field_data.connected.filter || false;
                    if (connected_filter && typeof connected_filter == "object" && connected_filter[key]) {
                        var connected_value = connected_property.get();
                        if (typeof connected_value == "object") {
                            $.each(connected_filter, function (attr_name, attr_pattern_str) {
                                if (attr_name == key) {
                                    var attr_pattern = new RegExp(attr_pattern_str);
                                    if (connected_value[attr_name]
                                        && attr_pattern.test(connected_value[attr_name])) {
                                        connected_value[attr_name] = value;
                                    }
                                }
                            });
                            connected_property.skip_set_property = true;
                            connected_property.set(connected_value);
                            if (typeof connected_property.change_coord == "function") {
                                connected_property.change_coord(connected_value);
                            }
                            if (typeof connected_property.change_value == "function") {
                                connected_property.change_value(connected_value);
                            }
                            connected_property.skip_set_property = false;
                        }
                    } else if (connected_filter) {
                        var connected_value = connected_property.get();
                        var attr_pattern = new RegExp(connected_filter);
                        if (connected_value && attr_pattern.test(connected_value)) {
                            connected_property.skip_set_property = true;
                            connected_property.set(value);
                            if (typeof connected_property.change_coord == "function") {
                                connected_property.change_coord(value);
                            }
                            if (typeof connected_property.change_value == "function") {
                                connected_property.change_value(value);
                            }
                            connected_property.skip_set_property = false;
                        }
                    } else {
                        connected_property.set(value);
                    }
                }
            }
        }
        if (this.skip_set_property || this.force_change) {
            changed_by_other = true;
        }
        if ((changed_by_other || last_data != self.return_data) && !this.skip_set_change) {
            var changed_data = self.return_data;
            self.change("" + mediasize, changed_data);
        }
        if (!this.skip_set_property) {
            if (this.is_panel(target)) {
                this.inline_set(this.return_data);
            } else if (this.panel_input) {
                this.panel_set(this.return_data);
            }
        }

        return true;
    };

    this.revert = function () {
        //this.imagecrop(_revert);
        return this;
    };

    this.edit = function () {
        this.inline_edit();
        //this.panel_edit();
    };

    this.browse = function () {
        if (routerunner.state() == "browse") {
            if (this.file_form["panel"] && this.file_form["panel"].find(".browse").length) {
                this.file_form["panel"].find(".browse").attr("disabled", true).css("opacity", 0.5);
            }
            if (this.file_form["inline"] && this.file_form["inline"].find(".browse").length) {
                this.file_form["inline"].find(".browse").attr("disabled", true).css("opacity", 0.5);
            }
        }
    };

    return this;
};