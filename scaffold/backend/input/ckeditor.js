/**
 * Created by csibi on 2014.08.29..
 */

ckeditor = function(_model, _input, _field) {
    var model = _model;
    this.input = _input;
    var field = _field;

    this.panel_customevent_loaded = false;
    this.inline_customevent_loaded = false;
    this.panel_ck_ready = false;
    this.inline_ck_ready = false;

    var _revert = $(this.input).html();
    var _instance = false;

    this.init = function () {
        var self = this;

        this.inline_input.attr("contenteditable", "true");
        var params = {
            allowedContent: true,
            readOnly: true,
            autoParagraph: false,
            forcePasteAsPlainText: true,
            extraPlugins: 'sourcedialog',
            removePlugins: 'resize',
            toolbar: [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Sourcedialog', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
                { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
                '/',
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
                { name: 'others', items: [ '-' ] },
                { name: 'about', items: [ 'About' ] }
            ],
            toolbarGroups: [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'forms' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                { name: 'links' },
                { name: 'insert' },
                '/',
                { name: 'styles' },
                { name: 'colors' },
                { name: 'tools' },
                { name: 'others' },
                { name: 'about' }
            ],
            floatSpacePinnedOffsetY: $(".page-header.routerunner-framework").height(),
        };

        if ($(window).width() < 800) {
            params["toolbar"] = [
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] }
            ];
            params["toolbarGroups"] = [
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'insert' },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                { name: 'links' },
                { name: 'styles' },
            ];
        }

        var toolbar_id = "cke_toolbar_" + this.model.id + "_" + this.property;
        var win = (routerunner.content_window ? routerunner.content_window : window);
        var doc = (routerunner.content_document ? routerunner.content_document : window.document);
        var scroll_timer = false;

        var ck_height = 100;
        _instance = CKEDITOR.inline(this.inline_input.get(0), params);

        _instance.on("instanceReady", function() {
            var div = $("[id='cke_" + $(self.inline_input).attr("id") + "']");
            div.css("transition", "all ease-in-out 0.2s");

            _instance.on("focus", function() {
                div.css({
                    "left": self.inline_input.offset().left - $(doc).scrollLeft() + "px",
                    "top": (self.inline_input.offset().top - $(doc).scrollTop() - ck_height < 0)
                        ?  $(".page-header.routerunner-framework").height() + "px"
                        : self.inline_input.offset().top - $(doc).scrollTop() + $(".page-header.routerunner-framework").height() - ck_height + "px"
                });
                div.show().addClass("focused");
                ck_height = (div.height() > 0 ? div.height() : 100);
            });
            _instance.on("blur", function() {
                div.hide().removeClass("focused");
            });
            $(win).on("scroll", function() {
                if (scroll_timer) { clearTimeout(scroll_timer); }
                scroll_timer = setTimeout(function() {
                    if (div.hasClass("focused")) {
                        if (self.inline_input.offset().top - $(doc).scrollTop() - ck_height < 0) {
                            div.css("top", $(".page-header.routerunner-framework").height() + "px");
                        } else {
                            div.css("top", self.inline_input.offset().top - $(doc).scrollTop() + $(".page-header.routerunner-framework").height() - ck_height + "px");
                        }
                    }
                }, 50);
            });
        });

    };
    this.panel_init = function () {
        _instance = CKEDITOR.replace(this.panel_input.get(0), {
            allowedContent: true,
            readOnly: false
        });
    };

    this.revert = function () {
        if (_instance) {
            _instance.setData(_revert);
        }
        return this;
    };
    this.return = function () {
        return $(this.input).html();
    };
    /*
     this.edit = function () {
     if (_instance) {
     if (_instance.status != "unloaded") {
     _instance.setReadOnly(false);
     }
     setTimeout(function () {
     if (_instance.status != "unloaded") {
     _instance.setReadOnly(false);
     }
     //$("#" + _instance.id + "_top").css("display", "block");
     setTimeout(function () {
     if (_instance.status != "unloaded") {
     _instance.setReadOnly(false);
     }
     //$("#" + _instance.id + "_top").css("display", "block");
     }, 300);
     }, 300);
     }
     };

     this.browse = function () {
     if (_instance) {
     if (_instance.status != "unloaded") {
     _instance.setReadOnly(true);
     }
     //$("#" + _instance.id + "_top").css("display", "none");
     }
     };
     */
    this.ckinstance = function () {
        return _instance;
    };

    this.panel_destroy = function () {
        if (_instance && this.panel_input == this.input) {
            _instance.destroy();
            _instance = false;
            this.panel_customevent_loaded = false;
        }
    };

    this.inline_customevent = function () {
        var self = this;
        if (!this.inline_customevent_loaded) {
            var ckinstance = self.ckinstance();
            ckinstance.on("instanceReady", function () {
                var _ck = this;
                self.inline_ck_ready = true;
                self._revert = this.getData();
                self.inline_get = function (ck) {
                    if (ck && ck.element && $(ck.element).attr("id")) {
                        return CKEDITOR.instances[$(ck.element).attr("id")].getData();
                    } else {
                        if (_ck.element && _ck.element.$ && $(_ck.element.$).closest("body").length) {
                            return _ck.getData();
                        }
                    }
                };
                self.inline_set = function (html) {
                    if (_ck.element && _ck.element.$ && $(_ck.element.$).closest("body").length) {
                        _ck.setData(html);
                    }
                };
                ckinstance.on("focus", function() {
                    var current_value = $('<div/>').html($.trim(self.inline_get(this))).text();
                    var current_default = $('<div/>').html($.trim(self.default_value)).text();
                    if (current_default === current_value) {
                        self.inline_set("");
                    }
                    if (self.state() == "edit") {
                        ckinstance.setReadOnly(false);
                    }
                });
                ckinstance.on("blur", function () {
                    var current_value = $('<div/>').html($.trim(self.inline_get(this))).text();
                    if (!current_value && self.default_value) {
                        self.inline_set(self.default_value);
                    }
                    if (this.checkDirty() || Object.keys(self.instance()).length) {
                        var changed_value = self.inline_get(this);
                        var current_value = $('<div/>').html($.trim(changed_value)).text();
                        if (current_value == $.trim(self.default_value)) {
                            changed_value = "";
                        }
                        self.change(self.property, changed_value);
                        self.panel_set(changed_value);
                    }
                });
                //$("#" + ckinstance.id + "_top").css("display", "none");
                self.states.revert[self.property] = self.inline_get(this);
            });
        }
        this.inline_customevent_loaded = true;
    };

    this.panel_customevent = function () {
        var self = this;
        if (!this.panel_customevent_loaded) {
            var ckinstance = self.ckinstance();
            ckinstance.on("instanceReady", function () {
                var _ck = this;
                self.panel_ck_ready = true;
                self.panel_get = function (ck) {
                    if (ck && ck.element && $(ck.element).attr("id")) {
                        return CKEDITOR.instances[$(ck.element).attr("id")].getData();
                    } else {
                        if (_ck.element && _ck.element.$ && $(_ck.element.$).closest("body").length) {
                            return _ck.getData();
                        }
                    }
                };
                self.panel_set = function (html) {
                    if (_ck.element && _ck.element.$ && $(_ck.element.$).closest("body").length) {
                        _ck.setData(html);
                    }
                };
                ckinstance.on("blur", function () {
                    if (this.checkDirty() || Object.keys(self.instance()).length) {
                        var changed_value = self.panel_get(this);
                        var current_value = $('<div/>').html($.trim(changed_value)).text();
                        if (current_value == $.trim(self.default_value)) {
                            changed_value = "";
                        }
                        self.change(self.property, changed_value);
                        self.inline_set(changed_value);
                    }
                });
            });
        }
        this.panel_customevent_loaded = true;
    };
    return this;
};
