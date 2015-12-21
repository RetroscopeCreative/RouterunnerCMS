/**
 * Created by csibi on 2015.09.21..
 */

routerunner_refresh = function(script, params, selector, done, fail, autoplay) {
    var _base = new routerunner_base();
    $.extend(this, _base);

    this.script = script;
    this.url = 'scaffold/component/ajax/' + script;
    this.params = $.extend({
        async: true,
        method: 'post'
    }, params);
    this.selector = selector;
    this.done = done;
    this.fail = fail;
    this.autoplay = autoplay;

    this.last_refresh = false;

    this.load = function() {
        var self = this;
        var response = {};
        var _done = function(data) {
            if (self.selector != undefined && $(self.selector).length) {
                $(self.selector).replaceWith(data);
                self.last_refresh = new Date().getTime();
            }
        };
        if (this.done) {
            _done = this.done;
        }
        var _fail = function(jqXHR, textStatus, errorThrown) {
            console.log("ajax failed", jqXHR, textStatus, errorThrown);
            console.log("ajax data", "script:" + self.script, "params", self.params, "selector:" + self.selector,
                "done", self.done, "fail", self.fail)
            alert("Error: " + textStatus);
        };
        if (this.fail) {
            _fail = this.fail;
        }

        $.ajax(this.url, this.params).done(function(data) {
            _done(data);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            _fail(jqXHR, textStatus, errorThrown)
        });
        return response;
    };

    if (this.autoplay) {
        this.load();
    }
};