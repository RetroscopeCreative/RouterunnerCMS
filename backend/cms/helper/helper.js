/**
 * Created by csibi on 2014.08.28..
 */
var helper = {
    script_loaded: [],
    scripts_queued: [],
    timers_queued: [],
    construct: function() {
        return this;
    },
    json2array: function(json) {
        return $.map(json, function(el) { return el; });
    },
    ajax: function(url, data, params, done, fail, always, xhr_holder){
        if (params.async !== undefined && params.async === false) {
            Metronic.blockUI({
                target: '.backend-wrapper',
                animate: true
            });
        }
        if (xhr_holder && typeof xhr_holder == "object") {
            if (xhr_holder[url] && xhr_holder[url].abort && typeof xhr_holder[url].abort == "function") {
                xhr_holder[url].abort();
            }
        } else {
            xhr_holder = {};
        }
        if (params == undefined) {
            params = {};
        }
        params.url = url;
        params.data = data;
        if (params.type == undefined){
            params.type = 'get';
        }
        if (params.dataType == undefined){
            params.dataType = 'html';
        }


        xhr_holder[url] = $.ajax(params).done(function(data, textStatus, jqXHR) {
            if (done) {
                done(data, textStatus, jqXHR);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (fail) {
                fail(jqXHR, textStatus, errorThrown);
            }
        }).always(function(jqXHR, textStatus, errorThrown) {
            if (!params.async) {
                Metronic.unblockUI('.backend-wrapper');
            }
            xhr_holder[url] = false;
            if (always) {
                always(jqXHR, textStatus, errorThrown);
            }
        });
    },
    load_script: function(script, success_fn, wait_for_all){
        var self = this;
        wait_for_all = (wait_for_all == undefined) ? false : wait_for_all;
        if (routerunner.get("debug")) {
            var script_elem = document.createElement("script");
            script_elem.setAttribute("type", "text/javascript");
            script_elem.setAttribute("src", script);
            document.getElementsByTagName("head")[0].appendChild(script_elem);
            if (wait_for_all) {
                self.scripts_queued.push(script);
                self.timers_queued.push(success_fn);
            }
            $(script_elem).load(function(){
                self.script_loaded.push(script);
                if (wait_for_all) {
                    var index = $.inArray(script, self.scripts_queued);
                    self.scripts_queued.splice(index, 1);
                    if (!self.scripts_queued.length) {
                        $.each(self.timers_queued, function(fn_index, fn) {
                            setTimeout(fn, 1);
                        });
                    }
                } else {
                    setTimeout(success_fn, 1);
                }
            });
        } else {
            if (wait_for_all) {
                self.scripts_queued.push(script);
                self.timers_queued.push(success_fn);
            }
            $.ajax({
                url: script,
                dataType: "script",
                async: false
            }).done(function() {
                self.script_loaded.push(script);
                if (wait_for_all) {
                    var index = $.inArray(script, self.scripts_queued);
                    self.scripts_queued.splice(index, 1);
                    if (!self.scripts_queued.length) {
                        $.each(self.timers_queued, function(fn_index, fn) {
                            setTimeout(fn, 1);
                        });
                    }
                } else {
                    setTimeout(success_fn, 1);
                }
            }).fail(function(jqxhr, settings, exception) {
                console.log(jqxhr, settings, exception);
                throw Error('Script loader error!');
            });
        }
    },
    url_exists: function(url)
    {
        try {
            var http = new XMLHttpRequest();
            http.open('HEAD', url, false);
            http.send();
            return http.status != 404;
        } catch (err) {
            return false;
        }
    },
    condition_return: function(condition){
        return 'return (' + condition + ');';
    },
    delayed_call: function(fn, condition, uid, delayed_started, exception_fn){
        var self = this;
        if (uid == undefined || !uid) {
            uid = this.guid();
        }
        if (delayed_started == undefined || !delayed_started) {
            delayed_started = new Date().getTime();
        }
        if (routerunner.get("delay/timers")[uid]) {
            clearTimeout(routerunner.get("delay/timers")[uid]);
        }
        if ((typeof condition == 'string') && (eval(condition) === true)) {
            fn();
        } else if (typeof condition == "function" && condition() === true) {
            fn();
        } else {
            if ((new Date().getTime() - delayed_started) > routerunner.get("delay/timeout") * 1000) {
                if (exception_fn && typeof exception_fn == "function") {
                    exception_fn();
                }
                console.log('delayed timeout', fn, condition);
                throw Error('Error occurred! Delayed call timeout!');
            } else {
                routerunner.get("delay/timers")[uid] = setTimeout(function() {
                    self.delayed_call(fn, condition, uid, delayed_started, exception_fn)
                }, routerunner.get("delay/interval"));
            }
        }
   },

    select_element: function(elem) {
        var wndw = ((routerunner && routerunner.iframe && routerunner.iframe.contentWindow)
            ? routerunner.iframe.contentWindow : window);
        var doc = wndw.document
            , text = elem
            , range, selection;
        if (doc.body.createTextRange) {
            range = doc.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (wndw.getSelection) {
            selection = wndw.getSelection();
            range = doc.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    },

    first: function(elem) {
        if (typeof elem == "object") {
            var _first = false;
            $.each(elem, function() {
                _first = this;
                return false;
            });
            return _first;
        } else if ($.isArray(elem)) {
            return elem[0];
        } else {
            return false;
        }
    },

    guid: function() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    },

    object_class: function(input) {
        var ret = false;
        if (typeof input == "object") {
            if (input instanceof property) {
                ret = "property";
            } else if (input instanceof visibility) {
                ret = "visibility";
            } else if (input instanceof position) {
                ret = "position";
            } else if (input instanceof remove) {
                ret = "remove";
            } else if (input instanceof pageproperties) {
                ret = "pageproperties";
            } else if (input instanceof routerunner_form) {
                ret = input.id;
            }
        } else if (typeof input == "string") {
            ret = input;
        }
        return ret;
    },

    i18n: function(input) {
        if (routerunner.settings.localization && routerunner.settings.localization[input]) {
            return routerunner.settings.localization[input];
        }
        return input;
    }
};