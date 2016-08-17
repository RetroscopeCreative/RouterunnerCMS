/**
 * Created by csibi on 2014.08.29..
 */
contenteditable = function(_model, _input, _field) {
    this.model = _model;
    this.input = _input;
    var field = _field;

    this.blur_timeout = false;

    var _revert = $(this.input).html();

    this.revert = function () {
        $(this.input).html(_revert);
        return this;
    };
    this.return = function () {
        return $(this.input).html();
    };

    this.edit = function () {
        var self = this;

        if (!this.input.attr("contenteditable") || this.input.attr("contenteditable") == "false") {
            this.input.attr("contenteditable", "true");
            this.input.on("focus", function () {
                var current_value = $('<div/>').html($.trim(self.inline_get(this))).text();
                var current_default = $('<div/>').html($.trim(self.default_value)).text();
                if (current_default === current_value) {
                    //self.inline_set("");
                    var input_elem = this;
                    setTimeout(function() {
                        self.helper.select_element(input_elem);
                    }, 200);
                }
            });
            $(this.inline_input).on("change keyup", function () {
                var current_input = this;
                if (self.blur_timeout) {
                    clearTimeout(self.blur_timeout);
                }
                self.blur_timeout = setTimeout(function() {
                    var current_value = $('<div/>').html($.trim(self.inline_get(current_input))).text();
                    if (!current_value && self.default_value) {
                        self.inline_set(self.default_value);
                    }
                    $(current_input).html($("<div>" +
                    $.trim($(current_input).html().replace(/<br[\w/]*>/gim, "\n")) +
                    "</div>").text().replace(/\n/gm, "<br>"));
                    var val = $(current_input).html();
                    self.change(self.property, val);
                    self.set(val);
                }, 500);
            });
        }
    };

    this.browse = function () {
        if (this.input.attr("contenteditable") || this.input.attr("contenteditable") == "true") {
            this.input.attr("contenteditable", "false");
            this.input.off("focus");
            this.input.off("blur");
        }
    };

    return this;
};
