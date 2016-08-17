/**
 * Created by csibi on 2015.02.11..
 */
image_iviewer = function (_model, _input, _field) {
    this.model = _model;
    this.input = _input;
    var field = _field;
    var mediasize = (_field.mediasize ? _field.mediasize : "m");
    this.return_data = {
        src: '',
        x: false,
        y: false,
        width: (_field.width ? parseInt(_field.width) : false),
        height: (_field.height ? parseInt(_field.height) : false),
        zoom: false,
        angle: 0
    };
    this.hidden_elem = false;
    this.img_left = (_field.img_left ? _field.img_left : 0);
    this.img_top = (_field.img_top ? _field.img_top : 0);
    this.is_panel = false;

    this.states = {
        "revert": {},
        "undo": [],
        "current": {}
    };

    var iviewer = false;
    this.img_object = false;
    var img_is_input = false;

    this.zoom_min = 100;

    this.cropper = {};

    this.skip_set_change = false;

    var _revert = $(this.input).attr("src");

    this.panel_init = function() {
    };
    this.inline_init = function() {
        var self = this;
        this.hidden_elem = $("<div></div>").css({
            position: "fixed",
            top: "-1000000px",
            left: "-1000000px"
        }).attr("id", "hidden-" + this.model.id + "-" + mediasize);
        $("body").append(this.hidden_elem);

        var img;
        if ($(this.inline_input).is("img")) {
            img = $(this.inline_input);
            img_is_input = true;
        } else {
            img = $(this.inline_input).find("img:eq(0)");
        }
        if (!img.attr("src")) {
            img.width(this.return_data.width).height(this.return_data.height);
        }
    };

    this.panel_edit = function() {
    };

    this.inline_edit = function() {
        var self = this;
        var img;
        if ($(this.inline_input).is("img")) {
            img = $(this.inline_input);
            img_is_input = true;
        } else {
            img = $(this.inline_input).find("img:eq(0)");
        }
        if (!img.attr("src")) {
            img.width(this.return_data.width).height(this.return_data.height);
        }

        if (!iviewer) {
            this.img_object = img.clone();

            var data, id, holder;
            if (img.attr("id")) {
                id = "iviewer-" + img.attr("id");
            }
            if (img.is(".routerunner-model")) {
                holder = img;
            } else if (img.closest(".routerunner-model").length) {
                holder = img.closest(".routerunner-model");
            }
            if (!id) {
                id = "iviewer-" + holder.data("routerunner-id") + "-" + mediasize;
            }
            data = holder.data();
            var src = img.attr("src");
            this.return_data.src = src;

            var img_dim = {
                width: img.width(),
                height: img.height(),
                naturalWidth: img[0].naturalWidth,
                naturalHeight: img[0].naturalHeight
            };
            if (!this.return_data.width && !this.return_data.height) {
                this.return_data.width = img_dim.width;
                this.return_data.height = img_dim.height;
            }

            this.zoom_min = 100;
            if (img_dim.height / img_dim.naturalHeight > img_dim.width / img_dim.naturalWidth) {
                this.zoom_min = img_dim.height / img_dim.naturalHeight * 100;
            } else {
                this.zoom_min = img_dim.width / img_dim.naturalWidth * 100;
            }

            if (this.is_panel || (typeof self.state == "function" && self.state() == "edit")) {
                if (!data["data_" + mediasize]) {
                    data["data_" + mediasize] = {
                        src: src,
                        x: false,
                        y: false,
                        width: this.return_data.width,
                        height: this.return_data.height,
                        zoom: false,
                        angle: 0
                    };
                }

                var size_data, size_json;
                size_json = false;
                if (typeof data["data_" + mediasize] == "object") {
                    size_json = data["data_" + mediasize];
                } else {
                    size_data = _.unescape(data["data_" + mediasize]).replace(/\\\"/g, '"');
                    size_json = $.parseJSON(size_data);
                }

                if (size_json && typeof size_json == "object") {
                    iviewer = $("<div></div>").attr("id", id);

                    if (size_json.src) {
                        this.return_data.src = size_json.src;
                        if (field.width && isNaN(parseInt(field.width))) {
                            var loaded_img = $("<img src='" + this.return_data.src + "'/>").css("height", field.height);
                            this.hidden_elem.append(loaded_img);
                            loaded_img.on("load", function() {
                                self.change_width($(this).height());
                            });
                        } else if (field.height && isNaN(parseInt(field.height))) {
                            var loaded_img = $("<img src='" + this.return_data.src + "'/>").css("width", field.width);
                            this.hidden_elem.append(loaded_img);
                            loaded_img.on("load", function() {
                                self.change_height($(this).height());
                            });
                        }
                    }
                    this.return_data.x = (!isNaN(parseInt(size_json.x)) ? parseInt(size_json.x) : false);
                    this.return_data.y = (!isNaN(parseInt(size_json.y)) ? parseInt(size_json.y) : false);
                    this.return_data.zoom = (!isNaN(parseFloat(size_json.zoom)) ? parseFloat(size_json.zoom) : 100);
                    this.return_data.angle = (!isNaN(parseInt(size_json.angle)) ? parseInt(size_json.angle) : 0);

                    _revert = $.extend({}, this.return_data);
                    this.states.revert["data_" + mediasize] = _revert;

                    iviewer.css("position", "relative");
                    iviewer.css("left", this.img_left);
                    iviewer.css("top", this.img_top);
                    iviewer.width($(img).width()).height($(img).height());
                    $(img).replaceWith(iviewer);

                    var iviewerOptions = {
                        src: this.return_data.src,
                        zoom: this.return_data.zoom,
                        zoom_min: this.zoom_min,
                        zoom_max: 150,
                        zoom_animation: false,
                        zoom_delta: 1.1,
                        mousewheel: false
                    };
                    iviewer.iviewer(iviewerOptions);

                    var iviewer_elem = $("#" + id);

                    iviewer_elem.bind('ivieweronfinishload', function () {
                        self.iviewer_init(iviewer_elem);

                        self.bind_imagecrop_events();
                    }).one('ivieweronfinishload', function (ev, src) {
                        if (self.model.class_id > 0) {
                            self.skip_set_change = true;
                        }

                        self.inline_imagecrop(self.return_data);

                        setTimeout(function() {
                            self.skip_set_change = false;
                        }, 500);
                    });

                    if (!img.attr("src")) {
                        self.iviewer_init(iviewer_elem);
                        iviewer_elem.find(".iviewer_upload").trigger("click");
                    }
                }
            }
        }
    };

    this.upload_init = function(btn, iviewer_elem) {
        Dropzone.autoDiscover = false;

        var self = this, frm;
        if (iviewer_elem.next(".dropzone").length) {
            frm = iviewer_elem.next(".dropzone");
        } else {
            var holder = btn.closest(".routerunner-model");
            var id = "upload-" + holder.data("routerunner-id");
            frm = $('<div class="dropzone" id="' + id + '"></div>');
            frm.attr("style", iviewer_elem.attr("style")).css("min-height", "inherit");
            frm.hide();
            iviewer_elem.after(frm);
            var upload_path = routerunner.settings.MEDIA_ROOT + routerunner.settings.UPLOAD_ROOT;
            var dropzone = new Dropzone(frm[0], {url: upload_path + "index.php"});
            dropzone.on("success", function (file, filename) {
                if (self.model && self.model.property &&
                    (self.model.property.label.get() == undefined
                    || self.model.property.label.get() == "")) {
                    var file_label = file.name.substr(0, file.name.lastIndexOf("."));
                    self.model.property.label.set(file_label);
                    self.model.property.label.change("label", file_label);
                }
                var iviewer_data = {
                    src: upload_path + filename,
                    x: false,
                    y: false,
                    zoom: false,
                    angle: 0,
                    width: self.return_data.width,
                    height: self.return_data.height
                };

                if (field.width && isNaN(parseInt(field.width))) {
                    var loaded_img = $("<img src='" + upload_path + filename + "'/>").css("height", field.height);
                    self.hidden_elem.append(loaded_img);
                    loaded_img.on("load", function() {
                        iviewer_data.width = $(this).width();
                        self.imagecrop(iviewer_data, true);
                    });
                } else if (field.height && isNaN(parseInt(field.height))) {
                    var loaded_img = $("<img src='" + upload_path + filename + "'/>").css("width", field.width);
                    self.hidden_elem.append(loaded_img);
                    loaded_img.on("load", function() {
                        iviewer_data.height = $(this).height();
                        self.imagecrop(iviewer_data, true);
                    });
                }

                self.imagecrop(iviewer_data, true);
                frm.hide();
                iviewer_elem.show();
            });
        }
        return frm;
    };

    this.change_width = function(size) {
        iviewer.width(size);
        if (parseInt(this.return_data.width) != parseInt(size)) {
            this.change_coord({"width": parseInt(size)});
        }
    };
    this.change_height = function(size) {
        iviewer.height(size);
        if (parseInt(this.return_data.height) != parseInt(size)) {
            this.change_coord({"height": parseInt(size)});
        }
    };

    this.iviewer_init = function(iviewer_elem) {
        var self = this;

        if (self.return_data.width) {
            iviewer_elem.css("width", self.return_data.width + "px");
        }
        if (self.return_data.height) {
            iviewer_elem.css("height", self.return_data.height + "px");
        }

        var btn;
        if (!iviewer_elem.find(".iviewer_common.iviewer_button.btn").length) {
            btn = $('<button class="iviewer_upload iviewer_common iviewer_button btn btn-icon-only btn-circle default"><span class="fa fa-upload"></span></button>').css({
                "bottom": "inherit",
                "top": "10px",
                "left": "20px"
            }).attr("title", "upload media");
            iviewer_elem.append(btn);
            var frm = self.upload_init(btn, iviewer_elem);
            btn.bind("click", function() {
                iviewer_elem.hide();
                frm.show();
            });
            /*
             btn = $('<button class="iviewer_delete iviewer_common iviewer_button btn btn-icon-only btn-circle default"><span class="fa fa-trash-o"></span></button>').css({
             "bottom": "inherit",
             "top": "10px",
             "left": "55px"
             }).attr("title", "delete media");
             iviewer_elem.append(btn);
             */
            iviewer_elem.find(".iviewer_zoom_in").addClass("btn btn-icon-only btn-circle default").append('<span class="fa fa-expand"></span>').css("left", "20px").attr("title", "zoom +");
            iviewer_elem.find(".iviewer_zoom_out").addClass("btn btn-icon-only btn-circle default").append('<span class="fa fa-compress"></span>').css("left", "55px").attr("title", "zoom -");
            iviewer_elem.find(".iviewer_zoom_zero").addClass("btn btn-icon-only btn-circle default").append('<span class="fa fa-caret-square-o-up"></span>').css("left", "95px").attr("title", "zoom 0");
            iviewer_elem.find(".iviewer_zoom_fit").addClass("btn btn-icon-only btn-circle default").append('<span class="fa fa-arrows"></span>').css("left", "130px").attr("title", "zoom fit");
            iviewer_elem.find(".iviewer_rotate_left").addClass("btn btn-icon-only btn-circle default").append('<span class="fa fa-mail-reply"></span>').css("left", "240px").attr("title", "rotate <-");
            iviewer_elem.find(".iviewer_rotate_right").addClass("btn btn-icon-only btn-circle default").append('<span class="fa fa-mail-forward"></span>').css("left", "275px").attr("title", "rotate ->");
            iviewer_elem.find(".iviewer_zoom_status").addClass("btn btn-circle default").css("left", "165px").attr("title", "zoom status");
        }
    };

    this.revert = function () {
        this.imagecrop(_revert);
        return this;
    };

    this.edit = function () {
        this.inline_edit();
        //this.panel_edit();
    };

    this.iviewer_destroy = function() {
        iviewer.iviewer("destroy");
        iviewer.replaceWith(this.img_object);
        if (img_is_input) {
            this.input = this.img_object[0];
        }
        this.img_object = false;
        iviewer = false;
    };

    this.browse = function () {
        if (routerunner.state() == "browse" && iviewer && $(iviewer).length) {
            this.iviewer_destroy();
        }
    };

    this.change_coord = function(coord) {
        var self = this;
        var changed = false;
        var src_last = self.return_data.src;
        $.each(coord, function(key, value) {
            if (self.return_data[key] != value) {
                self.return_data[key] = value;
                changed = true;
            }
        });
        if (changed && !this.skip_set_change) {
            var changed_data = $.extend({}, self.return_data);
            self.change("data_" + mediasize, changed_data);
        }
        return (src_last != self.return_data.src);
    };

    this.imagecrop = function(value) {
        if (value !== undefined) {
            var src_changed = this.change_coord(value);
            this.inline_imagecrop(this.return_data, src_changed);
        } else {
            return this.return_data;
        }
    };

    this.inline_imagecrop = function(value, src_changed) {
        var self = this, tmp_value = value;
        if (iviewer) {
            var iviewer_elem = $("#" + iviewer.attr("id"));

            if (!isNaN(parseInt(this.return_data.width))) {
                iviewer_elem.width(this.return_data.width);
            }
            if (!isNaN(parseInt(this.return_data.height))) {
                iviewer_elem.height(this.return_data.height);
            }

            var new_value = $.extend({}, value);

            src_changed = (src_changed == undefined ? true : src_changed);
            if (src_changed) {
                iviewer_elem.iviewer("loadImage", new_value.src.replace(/\\/g, ""));

                iviewer_elem.one('ivieweronfinishload', function (ev, src) {
                    var img = iviewer_elem.find("img");
                    //var actual_zoom = (iviewer_elem.iviewer("info", "zoom") / 100);
                    var img_dim = {
                        width: new_value.width,
                        height: new_value.height,
                        naturalWidth: img[0].naturalWidth,
                        naturalHeight: img[0].naturalHeight
                    };
                    self.zoom_min = 100;
                    if (img_dim.height / img_dim.naturalHeight > img_dim.width / img_dim.naturalWidth) {
                        self.zoom_min = img_dim.height / img_dim.naturalHeight * 100;
                    } else {
                        self.zoom_min = img_dim.width / img_dim.naturalWidth * 100;
                    }
                    iviewer_elem.iviewer("updateOptions", { "zoom_min": self.zoom_min });

                    var angle = iviewer_elem.iviewer("angle");
                    if (angle != 0) {
                        iviewer_elem.iviewer("angle", 360 - angle);
                    }
                    iviewer_elem.iviewer("angle", new_value.angle);

                    if (new_value.x === false || new_value.y === false || new_value.zoom === false) {
                        iviewer_elem.iviewer("fit");
                        iviewer_elem.iviewer("center");
                        var coords = iviewer_elem.iviewer("info", "coords");
                        if (new_value.x === false && !isNaN(coords.x)) {
                            new_value.x = coords.x;
                        }
                        if (new_value.y === false && !isNaN(coords.y)) {
                            new_value.y = coords.y;
                        }
                    }
                    if (new_value.zoom === false) {
                        new_value.zoom = iviewer_elem.iviewer("info", "zoom");
                        iviewer_elem.trigger("ivieweronzoom");
                    } else {
                        iviewer_elem.iviewer("set_zoom", (new_value.zoom < self.zoom_min ? self.zoom_min : new_value.zoom));
                    }
                    if (new_value.x !== false && new_value.y !== false) {
                        iviewer_elem.iviewer("setCoords", new_value.x, new_value.y);
                        iviewer_elem.trigger("ivieweronstopdrag");
                    }
                });
            } else if (iviewer_elem.find("img").attr("src")) {
                var angle = iviewer_elem.iviewer("angle");
                if (angle != 0) {
                    iviewer_elem.iviewer("angle", 360 - angle);
                }
                iviewer_elem.iviewer("angle", new_value.angle);

                if (new_value.x === false || new_value.y === false || new_value.zoom === false) {
                    iviewer_elem.iviewer("fit");
                    iviewer_elem.iviewer("center");
                    var coords = iviewer_elem.iviewer("info", "coords");
                    if (new_value.x === false && !isNaN(coords.x)) {
                        new_value.x = coords.x;
                    }
                    if (new_value.y === false && !isNaN(coords.y)) {
                        new_value.y = coords.y;
                    }
                }
                if (new_value.zoom === false) {
                    new_value.zoom = iviewer_elem.iviewer("info", "zoom");
                    iviewer_elem.trigger("ivieweronzoom");
                } else {
                    iviewer_elem.iviewer("set_zoom", (new_value.zoom < self.zoom_min ? self.zoom_min : new_value.zoom));
                }
                if (new_value.x !== false && new_value.y !== false) {
                    iviewer_elem.iviewer("setCoords", new_value.x, new_value.y);
                    iviewer_elem.trigger("ivieweronstopdrag");
                }
            }
        }
    };

    this.bind_imagecrop_events = function() {
        var self = this;
        var iviewer_elem = $(iviewer);
        var id = iviewer_elem.attr("id");
        iviewer_elem.bind('ivieweronstopdrag', function (ev, point) {
            if (!self.is_panel && (self.state() != "edit")) {
                return false;
            }
            var current_coord = iviewer_elem.iviewer("info", "coords");
            var change = {
                x: current_coord.x,
                y: current_coord.y
            };
            self.change_coord(change);
        }).bind('ivieweronzoom', function (ev, new_zoom) {
            if (!self.is_panel && (self.state() != "edit")) {
                return false;
            }
            if (new_zoom === undefined) {
                new_zoom = iviewer_elem.iviewer("info", "zoom");
            }
            if (new_zoom && !isNaN(parseInt(new_zoom)) && new_zoom >= self.zoom_min) {
                var change = {
                    zoom: new_zoom
                };
                self.change_coord(change);
            }
        }).bind('iviewerangle', function (ev, angle) {
            if (!self.is_panel && (self.state() != "edit")) {
                return false;
            }
            if (angle && angle.angle && !isNaN(parseInt(angle.angle))) {
                var change = {
                    angle: angle.angle
                };
                self.change_coord(change);
            }
        });
    };
    /*
     this.panel_imagecrop = function(input) {
     console.log("panel_imagecrop", input);
     };
     */

    this.applycrop = function(args) {
        var change;
        var ret = {};
        if (iviewer) {
            var iviewer_elem = $("#" + iviewer.attr("id"));
            if (!args.value["data_" + mediasize].x && !args.value["data_" + mediasize].y) {
                var current_coord = iviewer_elem.iviewer("info", "coords");
                change = {
                    x: current_coord.x,
                    y: current_coord.y
                };
                args.value["data_" + mediasize] = $.extend(args.value["data_" + mediasize], change);
                this.change_coord(change);
            }
            if (!args.value["data_" + mediasize].zoom) {
                change = {zoom: iviewer_elem.iviewer("info", "zoom")};
                args.value["data_" + mediasize] = $.extend(args.value["data_" + mediasize], change);
                this.change_coord(change);
            }

            var success = false;
            var self = this;
            var url = routerunner.settings["BACKEND_DIR"] + '/backend/ajax/action/crop_iviewer.php';
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
                this.img_object.attr("src", success);
                $.each(args.value, function (data_name, data_value) {
                    self.inline_input.data(data_name, data_value);
                });
                $(this.input).find("img:eq(0)").attr("src", success);
                this.iviewer_destroy();
            } else {
                alert("error in image cropping");
            }
        }
        return ret;
    };

    return this;
};