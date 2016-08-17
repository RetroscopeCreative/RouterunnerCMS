/**
 * Created by csibi on 2014.10.17..
 */

user_panel = function(caller, selector) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.selector = selector;
    this.panel = false;

    this.init = function () {
        this.panel = $(this.selector);
    };

    this.on = function() {

    };
    this.off = function() {

    };

    this.browse = function() {
        this.off();
    };
    this.edit = function() {
        this.on();
    };

    this.init();
};