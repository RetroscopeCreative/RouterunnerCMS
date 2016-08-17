/**
 * Created by csibi on 2015.02.11..
 */
image = function (_model, _input, _field) {
    this.model = _model;
    this.input = _input;
    var field = _field;

    var _revert = $(this.input).html();

    this.revert = function () {
        $(this.input).attr("src", _revert);
        return this;
    };
    this.return = function () {
        return $(this.input).attr("src");
    };

    this.edit = function () {

    };

    this.browse = function () {

    };

    return this;
};