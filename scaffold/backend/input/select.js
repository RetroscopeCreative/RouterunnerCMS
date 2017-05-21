/**
 * Created by csibi on 2014.08.29..
 */
select = function(_model, _input, _field) {
    this.model = _model;
    this.input = _input;
    var field = _field;
    this.skip_change = false;

    var _revert = $(this.input).html();

    this.revert = function () {
        $(this.input).html(_revert);
        return this;
    };
    this.return = function () {
        return $(this.input).html();
    };

    this.inline_init = function() {
        if (!this.inline_input.data("select2_init")) {
            this.inline_input.select2({
                width: "80%",
                tags: true,
                tokenSeparators: [';'],
                dropdownParent: (routerunner.content_document ? routerunner.content_document.body : document.body)
            });
            this.inline_input.data("select2_init", true);
            this.inline_input.next("span.select2").hide();
        }
    };
    this.panel_init = function() {
        if (!this.panel_input.data("select2_init")) {
            this.panel_input.select2({
                width: "100%",
                tags: true,
                tokenSeparators: [';']
            });
            this.panel_input.data("select2_init", true);
        }
    };

    this.edit = function () {
        if (this.inline_input) {
            this.inline_input.parent().find(".a_tags").hide();
            this.inline_input.next("span.select2").show();
            this.inline_input.show();
        }
    };

    this.browse = function () {
        if (this.inline_input) {
            this.inline_input.parent().find(".a_tags").show();
            this.inline_input.next("span.select2").hide();
            this.inline_input.hide();
        }
    };

    this.inline_value = function (value) {
        return this.get_value(this.inline_input, value);
    };
    this.panel_value = function (value) {
        return this.get_value(this.panel_input, value);
    };
    this.get_value = function (input, value) {
        var select = input;
        if (select && value) {
            if (!$.isArray(value)) {
                value = [value];
            }
            $.each(value, function () {
                var selected = this;
                var found = false;
                $.each(select.find("option"), function () {
                    if ($(this).val() == selected) {
                        $(this).attr("selected", true);
                        found = true;
                    }
                });
                if (!found) {
                    select.append('<option value="' + selected + '" selected="selected">' + selected + '</option>');
                }
            });
            $.each(select.find("option"), function () {
                if ($.inArray($(this).val(), value) === -1) {
                    $(this).attr("selected", false);
                }
            });
            if (input.next("span.select2").length) {
                input.trigger("change.select2");
            }
        }
        if (input) {
            return input.val();
        } else {
            return false;
        }
    };

    return this;
};
