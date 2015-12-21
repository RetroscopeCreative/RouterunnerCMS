/**
 * Created by csibi on 2014.08.29..
 */

hidden = function(_model, _input, _field) {
    var model = _model;
    this.input = _input;
    var field = _field;

    var _revert = $(this.input).html();

    this.revert = function() {
        $(this.input).html(_revert);
        return this;
    };
    this.return = function() {
        return $(this.input).html();
    };

    return this;
};
