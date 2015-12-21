/**
 * Created by csibi on 2015.02.11..
 */
image_cropper = function (_model, _input, _field) {
    this.model = _model;
    this.input = (this.input ? this.input : _input);
    var field = _field;
    this.field_data = {};
    var mediasize = (_field.mediasize ? _field.mediasize : "m");

    if (!$(routerunner.content_document).find("body > #cropper_css").length) {
        $(routerunner.content_document).find("body").append('<link id="cropper_css" rel="stylesheet" type="text/css" href="Routerunner/backend/thirdparty/cropper/dist/cropper.min.css">');
        //$(routerunner.content_document).find("body").append('<script src="Routerunner/backend/thirdparty/cropper/dist/cropper.min.js" type="text/javascript"></script>');
    }

    this.return_data = {
        src: '',
        x: 0,
        y: 0,
        width: (_field.width && !isNaN(parseInt(_field.width)) ? parseInt(_field.width) : false),
        height: (_field.height && !isNaN(parseInt(_field.height)) ? parseInt(_field.height) : false),
        rotate: 0,
        canvasData: {}
    };
    this.aspectRatio = NaN;
    this.img_left = (_field.img_left ? _field.img_left : 0);
    this.img_top = (_field.img_top ? _field.img_top : 0);

    this.states = {
        "revert": {},
        "undo": [],
        "current": {}
    };

    this.cropper_plugin = {inline: false, panel: false};
    this.cropper = {inline: false, panel: false};
    this.cropper_container = {inline: false, panel: false};
    this.cropper_params = {};
    this.img_object = {inline: false, panel: false};
    this.cropper_object = {inline: false, panel: false};
    this.cropper_img = {inline: false, panel: false};
    this.file_form = {inline: false, panel: false};
    this.property_holder = {inline: false, panel: false};
    this.holder = {inline: false, panel: false};
    this.modal = false;
    this.modal_body = false;

    this.skip_set_change = false;
    this.skip_set_property = false;
    this.force_change = false;

    var _revert = $(this.input).attr("src");

    this.is_panel = function(target) {
        return (this.panel_input && $(target).attr("id") == this.panel_input.attr("id"));
    };

    this.panel_init = function() {
        var self = this;

        this.cropper_plugin["panel"] = true;

        var img;
        if ($(this.panel_input).is("img")) {
            this.img_object["panel"] = $(this.panel_input);
        } else {
            this.img_object["panel"] = $(this.panel_input).find("img:eq(0)");
        }
        if (!this.img_object["panel"].attr("src")) {
            this.img_object["panel"].width(this.return_data.width).height(this.return_data.height);
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

        this.cropper_plugin["inline"] = true;

        var img;
        if ($(this.inline_input).is("img")) {
            this.img_object["inline"] = $(this.inline_input);
        } else {
            this.img_object["inline"] = $(this.inline_input).find("img:eq(0)");
        }
        if (!this.img_object["inline"].attr("src")) {
            this.img_object["inline"].width(this.return_data.width).height(this.return_data.height);
        }

        if (this.img_object["inline"].is(".routerunner-model")) {
            this.holder["inline"] = this.img_object["inline"];
        } else if (this.img_object["inline"].closest(".routerunner-model").length) {
            this.holder["inline"] = this.img_object["inline"].closest(".routerunner-model");
        }

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
    };

    this.panel_edit = function() {
        this.init_edit("panel");
    };
    this.inline_edit = function() {
        this.init_edit("inline");
    };

    this.panel_destroy = function() {
        var type = "panel";
        if (this.cropper[type]) {
            this.cropper_img["panel"].cropper("destroy");
            this.cropper["panel"] = false;
            this.img_object["panel"] = false;
            this.cropper_object["panel"] = false;
            this.cropper_img["panel"] = false;
            this.file_form["panel"] = false;
            this.property_holder["panel"] = false;
            this.holder["panel"] = false;
        }
    };

    this.init_edit = function(type) {
        if (!type) { type = "inline"; }
        var self = this;

        if (!this.cropper[type] && this.holder[type].length) {
            // construct cropper
            if (type == "panel") {
                this.property_holder[type] = this.holder[type];
            } else {
                this.property_holder[type] = this.holder[type].find(".property-data_" + mediasize);
            }

            var data = this.holder[type].data();
            var size_data, size_json;
            size_json = ((this.cropper["panel"] || this.cropper["inline"]) ? this.return_data : false);
            if (!size_json) {
                if (data["data_" + mediasize]) {
                    if (typeof data["data_" + mediasize] == "object") {
                        size_json = data["data_" + mediasize];
                    } else {
                        size_data = _.unescape(data["data_" + mediasize]).replace(/\\\"/g, '"');
                        if (size_data) {
                            size_json = $.parseJSON(size_data);
                        }
                    }
                } else if (data["fields"] && data["fields"]["data_" + mediasize]
                    && data["fields"]["data_" + mediasize]["default"]) {
                    if (typeof data["fields"]["data_" + mediasize]["default"] == "object") {
                        size_json = data["fields"]["data_" + mediasize]["default"];
                    } else {
                        size_data = _.unescape(data["fields"]["data_" + mediasize]["default"]).replace(/\\\"/g, '"');
                        if (size_data) {
                            size_json = $.parseJSON(size_data);
                        }
                    }
                }
                if (!size_json) {
                    size_json = this.return_data;
                }
            }
            if (!size_json.src && this.img_object[type] && this.img_object[type].attr("src")) {
                size_json.src = this.img_object[type].attr("src");
            }
            if ($.isEmptyObject(this.field_data) && data.fields && data.fields["data_" + mediasize]
                && typeof data.fields["data_" + mediasize] == "object") {
                this.field_data = data.fields["data_" + mediasize];
            } else if ($.isEmptyObject(this.field_data) && data.fieldData) {
                this.field_data = data.fieldData;
            }

            if (size_json && typeof size_json == "object") {
                var field_w = this.return_data.width, field_h = this.return_data.height;
                this.return_data = $.extend(this.return_data, size_json);
                this.return_data.width = (isNaN(parseInt(field_w)) ? false : field_w);
                this.return_data.height = (isNaN(parseInt(field_h)) ? false : field_h);

                this.cropper_object[type] = $("<div></div>").attr("id", "cropper-" + this.model.id + "-" + mediasize);
                var filename = (size_json.src ? size_json.src.substr(size_json.src.lastIndexOf("/")+1) : "");
                this.file_form[type] = $("<div class='frm row' style='padding: 0 15px;'><input type='text' class='col-md-9' style='padding: 1px 5px;' readonly value='" + filename + "' /><button class='browse col-md-3'>Browse</button></div>");
                this.file_form[type].find(".browse").on("click", function () {
                    var dir = routerunner.settings["MEDIA_ROOT"].substring(0, routerunner.settings["MEDIA_ROOT"].length - 1);
                    var url = 'Routerunner/backend/thirdparty/kcfinder/browse.php?type=' + dir + '&dir=' + dir + '/public';

                    self.modal_body.kcfinder({
                        url: url,
                        theme: "default",
                        lang: "hu",
                        callback: function (file) {
                            file = file.replace(routerunner.settings["BASE"], "");
                            self.change_coord({"src": file}, self.cropper_img[type]);
                            self.force_change = true;
                            if (self.cropper_img["inline"]) {
                                self.cropper_img["inline"].cropper("replace", file);
                                self.cropper_img["inline"].cropper("reset");
                                self.bind_cropper("inline");
                                self.cropper_img["inline"].trigger("dragend.cropper");

                            }
                            if (self.cropper_img["panel"]) {
                                self.cropper_img["panel"].cropper("replace", file);
                                self.cropper_img["panel"].cropper("reset");
                                self.bind_cropper("panel");
                                self.cropper_img["panel"].trigger("dragend.cropper");
                            }
                            self.force_change = false;
                            self.file_form[type].find("input").val(file.substr(file.lastIndexOf("/")+1));
                            self.modal.modal("hide");
                        },
                        callbackMultiple: function(files) {
                            //alert('Selected files:\n  "' + files.join('",\n  "') + '"');
                        }
                    });

                    self.modal.on('shown.bs.modal', function() {
                        self.modal_body.height(self.modal.height() - self.modal.find(".modal-header").outerHeight() - 4);
                    });
                    self.modal.modal("show");
                    return false;
                });
                this.cropper_img[type] = this.img_object[type].clone(true, true);
                this.cropper_object[type].append(this.cropper_img[type]);
                this.cropper_img[type].attr("src", this.return_data.src);
                var w = parseInt(this.field_data.width) || parseInt(this.field_data["max-width"])
                    || this.holder[type].width();
                var h = parseInt(this.field_data.height) || parseInt(this.field_data["max-height"])
                    || this.img_object[type].height() || w;
                if (parseInt(w) > this.property_holder[type].width()) {
                    var new_w = this.property_holder[type].width();
                    if (!isNaN(parseInt(h))) {
                        var ratio = w / h;
                        h = new_w / ratio;
                    }
                    w = new_w;
                }
                this.cropper_object[type].width(w).height(h);
                this.cropper_img[type].css({
                    "max-width": w,
                    "max-height": h,
                });
                this.bind_cropper(type);

                this.cropper_img[type].on("built.cropper", function() {
                    self.cropper_container[type] = self.cropper_img[type].cropper('getContainerData');

                    self.skip_set_change = true;
                    self.imagecrop(type);
                    self.cropper_img[type].trigger("dragend.cropper");
                    self.skip_set_change = false;

                    self.cropper_object[type].before(self.file_form[type]);
                    self.property_holder[type].css({
                        position: "static",
                        width: "100%",
                        height: (self.cropper_object[type].find(".cropper-container").height() + self.file_form[type].height() + 27) + "px",
                        padding: "15px"
                    });
                    if (type == "inline" && self.field_data["margin-left"]) {
                        self.cropper_object[type].css("margin-left", self.field_data["margin-left"]);
                    } else if (type == "inline") {
                        var offset = self.property_holder[type].offset().left - self.holder[type].offset().left;
                        self.cropper_object[type].css("margin-left",
                            (self.holder[type].width()
                            - self.cropper_object[type].find(".cropper-container").width()
                            - offset) / 2 + "px");
                    }
                });

                this.img_object[type].replaceWith(this.cropper_object[type]);


                // CROPPER INITED
                this.cropper_params = {
                    autoCrop: true,
                    autoCropArea: 1,
                    strict: false
                };
                if (this.return_data.width && this.return_data.height) {
                    this.aspectRatio = this.return_data.width / this.return_data.height;
                    this.cropper_params["aspectRatio"] = this.aspectRatio;
                }
                this.cropper[type] = this.cropper_img[type].cropper(this.cropper_params);
            }

        } else if (this.cropper[type]) {
            // enable cropper
            this.cropper[type].cropper("enable");
        }
    };

    this.bind_cropper = function(type) {
        var self = this;
        this.cropper_img[type].on("dragend.cropper", function(evt) {
            if (!self.skip_set_property) {
                var cropboxdata = self.cropper_img[type].cropper('getData');
                cropboxdata["canvasData"] = self.cropper_img[type].cropper('getCanvasData');
                cropboxdata["cropBoxData"] = self.cropper_img[type].cropper('getCropBoxData');
                self.cropper_container[type] = self.cropper_img[type].cropper('getContainerData');
                self.change_coord(cropboxdata, evt.target);
            }
        }).on("zoomin.cropper zoomout.cropper", function(evt) {
            //var canvasdata = {canvasData: self.cropper_img[type].cropper('getCanvasData')};
            //self.change_coord(canvasdata, evt.target);
            setTimeout(function() {
                if (!self.skip_set_property) {
                    var cropboxdata = self.cropper_img[type].cropper('getData');
                    cropboxdata["canvasData"] = self.cropper_img[type].cropper('getCanvasData');
                    cropboxdata["cropBoxData"] = self.cropper_img[type].cropper('getCropBoxData');
                    self.cropper_container[type] = self.cropper_img[type].cropper('getContainerData');
                    self.change_coord(cropboxdata, evt.target);
                }
            }, 100);
        });
    };

    this.resize = function (type) {
        var self = this;
        if (type && !$.isArray(type)) {
            types = [type];
        } else {
            types = ["panel", "inline"];
        }
        $.each(types, function(index, current_type) {
            if (self.cropper[current_type]) {
                var w = parseInt(self.field_data.width) || parseInt(self.field_data["max-width"])
                    || self.holder[current_type].width();
                var h = parseInt(self.field_data.height) || parseInt(self.field_data["max-height"])
                    || self.img_object[current_type].height() || w;
                if (parseInt(w) > self.property_holder[current_type].width()) {
                    var new_w = self.property_holder[current_type].width();
                    if (!isNaN(parseInt(h))) {
                        var ratio = w / h;
                        h = new_w / ratio;
                    }
                    w = new_w;
                }
                self.cropper_object[current_type].width(w).height(h);
                self.cropper_img[current_type].css({
                    "max-width": w,
                    "max-height": h,
                });

                self.cropper_img[current_type].cropper("resize");

                self.cropper_container[current_type] = self.cropper_img[current_type].cropper('getContainerData');
            }
        });
    };

    this.inline_imagecrop = function(value) {
        if (value) {
            this.change_coord(value, self.inline_input);
            this.imagecrop("inline")
        }
        return this.return_data;
    };
    this.panel_imagecrop = function(value) {
        if (value) {
            this.change_coord(value, self.panel_input);
            this.imagecrop("panel")
        }
        return this.return_data;
    };

    this.imagecrop = function(type) {
        var self = this;
        var json_value = $.extend(true, {}, self.return_data),
            canvasData = $.extend(true, {}, self.return_data.canvasData),
            cropBoxData = $.extend(true, {}, self.return_data.cropBoxData);
        if (type && !$.isArray(type)) {
            types = [type];
        } else {
            types = ["panel", "inline"];
        }
        $.each(types, function(index, current_type) {
            var other_type = (current_type == "panel" ? "inline" : "panel");
            var ratio = 1;
            if (current_type == "panel" && self.cropper_container["panel"] && self.cropper_container["inline"]) {
                ratio =  self.cropper_container["panel"]["width"] / self.cropper_container["inline"]["width"];
            }

            if (typeof json_value == "object" && json_value.src && json_value.src != self.return_data.src) {
                if (self.cropper_img[current_type]) {
                    self.cropper_img[current_type].cropper("replace", json_value.src);
                }
                if (self.file_form[type]) {
                    self.file_form[type].find("input").val(json_value.src.substr(json_value.src.lastIndexOf("/")+1));
                }
            }
            if (typeof json_value == "object" && canvasData) {
                $.each(canvasData, function(attr, value) {
                    canvasData[attr] = parseFloat(value) * ratio;
                });
                if (self.cropper_img[current_type]) {
                    self.cropper_img[current_type].cropper('setAspectRatio', self.aspectRatio);
                    self.cropper_img[current_type].cropper('setCanvasData', canvasData);
                }
            }
            if (typeof json_value == "object" && cropBoxData) {
                $.each(cropBoxData, function(attr, value) {
                    cropBoxData[attr] = parseFloat(value) * ratio;
                });
                if (self.cropper_img[current_type]) {
                    self.cropper_img[current_type].cropper('setCropBoxData', cropBoxData);
                }
            }
            if (typeof json_value == "object") {
                $.each(json_value, function(attr, value) {
                    if (typeof value != "object" && !isNaN(parseInt(value))) {
                        json_value[attr] = parseFloat(value);// * ratio;
                    }
                });
                //self.cropper_img[current_type].cropper('setData', size_json);
            }
        });
        return this.return_data;
    };

    this.change_coord = function(coord, target) {
        var self = this;
        var changed_by_other = false;
        var last_data = $.extend(true, {}, this.return_data);
        var src_last = self.return_data.src;

        var ratio = 1;
        if (target && this.is_panel(target)) {
            if (self.cropper_container["panel"] && self.cropper_container["inline"]) {
                ratio = self.cropper_container["panel"]["width"] / self.cropper_container["inline"]["width"];
            } else if (self.inline_input.length) {
                return false;
            }
        }

        $.each(coord, function (key, value) {
            if ((typeof self.return_data[key] == "object" && !Object.equals(self.return_data[key], value))
                || (typeof self.return_data[key] != "object" && self.return_data[key] != value)) {
                //console.log("before", ratio, key, value);
                if (typeof value == "object") {
                    $.each(value, function(val_key, val) {
                        value[val_key] = val / ratio;
                    });
                    //} else {
                    //value = value / ratio;
                }
                self.return_data[key] = value;
                //console.log("after", ratio, key, value);

                if (!self.skip_set_property) {
                    if (self.field_data && self.field_data.connected) {
                        var property_name = self.field_data.connected.property || false;
                        var connected_property = self.model.property[property_name] || false;
                        if (connected_property) {
                            var connected_filter = self.field_data.connected.filter || false;
                            if (connected_filter && connected_filter[key]) {
                                var connected_value = $.extend({}, connected_property.get());
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
                                    connected_property.skip_set_property = false;
                                }
                            } else {
                                connected_property.set(value);
                            }
                        }
                    }
                }
            }
        });
        if (this.skip_set_property || this.force_change) {
            changed_by_other = true;
        }
        if ((changed_by_other || !Object.equals(last_data, self.return_data)) && !this.skip_set_change) {
            var changed_data = $.extend(true, {}, self.return_data);
            //console.log("change", this.is_panel(target), changed_data);
            self.change("data_" + mediasize, changed_data);
        }
        if (!this.skip_set_property) {
            this.skip_set_property = true;
            if (this.is_panel(target)) {
                //this.inline_set(this.return_data);
                this.imagecrop("inline");
            } else if (this.panel_input) {
                this.imagecrop("panel");
                //this.panel_set(this.return_data);
            }
            this.skip_set_property = false;
        }

        return (src_last != self.return_data.src);
    };

    this.revert = function () {
        //this.imagecrop(_revert);
        return this;
    };

    this.edit = function () {
        this.inline_edit();
        //this.panel_edit();
        if (routerunner.state() == "edit") {
            if (this.file_form["panel"] && this.file_form["panel"].find(".browse").length) {
                this.file_form["panel"].find(".browse").attr("disabled", false).css("opacity", 1);
            }
            if (this.file_form["inline"] && this.file_form["inline"].find(".browse").length) {
                this.file_form["inline"].find(".browse").attr("disabled", false).css("opacity", 1);
            }
        }
    };

    this.browse = function () {
        if (routerunner.state() == "browse") {
            if (this.cropper["panel"] && this.cropper["panel"].length) {
                this.cropper["panel"].cropper("disable");
            }
            if (this.cropper["inline"] && this.cropper["inline"].length) {
                this.cropper["inline"].cropper("disable");
            }
            if (this.file_form["panel"] && this.file_form["panel"].find(".browse").length) {
                this.file_form["panel"].find(".browse").attr("disabled", true).css("opacity", 0.5);
            }
            if (this.file_form["inline"] && this.file_form["inline"].find(".browse").length) {
                this.file_form["inline"].find(".browse").attr("disabled", true).css("opacity", 0.5);
            }
        }
    };

    this.applycrop = function(args) {
        var change;
        var ret = {};
        if (this.cropper) {
            var success = false;
            var self = this;
            var url = 'Routerunner/backend/ajax/action/crop_cropper.php';
            var params = {
                dataType: 'json',
                async: false,
                type: 'post'
            };
            args["model"] = {
                reference: self.model.reference,
                route: self.model.route
            };
            this.helper.ajax(url, args, params, function (data) {
                success = data.src;
            }, function () {
                alert("error in image cropping");
            });
            if (success && success != "false") {
                ret["" + mediasize] = success;
                if (this.img_object["inline"]) {
                    this.img_object["inline"].attr("src", success);
                }
                if (this.img_object["panel"]) {
                    this.img_object["panel"].attr("src", success);
                }
                $.each(args.value, function (data_name, data_value) {
                    if (self.inline_input) {
                        self.inline_input.data(data_name, data_value);
                    }
                    if (self.panel_input) {
                        self.panel_input.data(data_name, data_value);
                    }
                });
                //$(this.input).find("img:eq(0)").attr("src", success);
                //this.iviewer_destroy();
            } else {
                alert("error in image cropping");
            }
        }
        return ret;
    };

    return this;
};