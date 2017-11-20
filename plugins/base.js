/**
 * Created by csibi on 2015.05.08..
 */
routerunner_base = function(caller) {
    var _parent = (caller ? caller : window);
    var _instances = {}; // container to hold default instances
    var _ns_instances = {}; // container to hold namespaced instances
    var _instance_nss = []; // array to hold namespace names
    var _state = false;
    var _laststate = false;
    var _actions = [];
    var _queue = [];

    this.container = {};

    this.states = {
        home: 0,
        browse: 1,
        edit: 2,
        newsletter: 3,
        panel_destroy: 7,
        enabled: 8,
        disabled: 9,

        loaded: 100,
        refresh: 101,
        resize: 102,
        destroy: 199,

        modelselect: 200,
        updatechanges: 201,

        revert: 800,

        force_blur: 899,
        apply: 900
    };
    this.statenames = {};

    this.init = function() {
        var self = this;
        $.each(this.states, function(statename, state_no) {
            self.statenames[state_no] = statename;
        });
    };

    this.parent = function() {
        return _parent;
    };
    this.rootrunner = function() {
        if (typeof routerunner != "undefined") {
            return routerunner;
        } else if (typeof routerunner_form != "undefined" && this instanceof routerunner_form) {
            return this;
        } else if (typeof this.form != "undefined" && this instanceof routerunner_input) {
            return this;
        } else {
            return this;
        }
    };
    this.instance = function(index, setter, namespace) {
        if (index == undefined) {
            index = false;
        }

        if (setter || setter === null) {
            if (namespace) {
                if (index === false && _ns_instances[namespace]) {
                    index = this.object_len(_ns_instances[namespace]);
                } else if (index === false) {
                    index = 0;
                }
                if (!_ns_instances[namespace]) {
                    _ns_instances[namespace] = {};
                }
                if ($.inArray(namespace, _instance_nss) === -1) {
                    _instance_nss.push(namespace);
                }
                if (setter === null) {
                    delete _ns_instances[namespace][index];
                } else {
                    _ns_instances[namespace][index] = setter;
                    if (typeof _ns_instances[namespace][index]["state"] == "function") {
                        _ns_instances[namespace][index].state(this.rootrunner().state());
                    }
                }
            } else {
                if (index === false) {
                    index = this.object_len(_instances);
                }
                if (setter === null) {
                    delete _instances[index];
                } else {
                    _instances[index] = setter;
                    if (typeof _instances[index]["state"] == "function") {
                        _instances[index].state(this.rootrunner().state());
                    }
                }
            }
        }
        if (index !== false) {
            if (namespace) {
                return ((_ns_instances[namespace] && _ns_instances[namespace][index])
                    ? _ns_instances[namespace][index] : false);
            } else {
                return (_instances[index] ? _instances[index] : false);
            }
        } else {
            if (namespace) {
                return (_ns_instances[namespace] ? _ns_instances[namespace] : false);
            } else {
                return _instances;
            }
        }
    };
    this.last_instance = function(offset) {
        var last_instance = false;
        var keys = Object.keys(this.instance());
        if (offset && $.inArray(offset, keys) > -1) {
            keys = keys.slice(0, $.inArray(offset, keys));
        }
        if (keys.length && (last_instance_key = keys.pop())) {
            last_instance = this.instance(last_instance_key);
        }
        return last_instance;
    };
    this.state = function(setter, silent, context, namespace) {
        var self = this;
        if (setter) {
            var setter_no = false;
            var setter_str = false;
            if (!isNaN(parseInt(setter))) {
                setter_no = setter;
                setter_str = this.rootrunner().statenames[setter_no];
            } else {
                setter_str = setter;
                setter_no = this.rootrunner().states[setter];
            }
            if (_state) {
                _laststate = this.rootrunner().statenames[_state];
            }
            _state = setter_no;
            var instances = {};
            if (!silent && typeof this[setter_str] == "function") {
                if (context) {
                    this[setter_str](context);
                } else {
                    this[setter_str]();
                }
            }
            if (!namespace && (instances = this.instance())) {
                $.each(instances, function(index, instance) {
                    if (typeof instance["state"] == "function") {
                        instance.state(setter_no, silent);
                    }
                });
            }
            if (_instance_nss.length) {
                $.each(_instance_nss, function(index, ns) {
                    var ns_instances = {};
                    if ((!namespace || namespace === ns) && (ns_instances = self.instance(false, false, ns))) {
                        $.each(ns_instances, function(ns_index, ns_instance) {
                            if (typeof ns_instance["state"] == "function") {
                                ns_instance.state(setter_no, silent);
                            }
                        });
                    }
                });
            }
        }
        return this.rootrunner().statenames[_state];
    };
    this.laststate = function() {
        return _laststate;
    };
    this.action = function(setter, context, namespace) {
        if (setter) {
            var current_state = this.state(false, false, false, namespace);
            this.state(setter, false, context, namespace);
            this.state(current_state, true, false, namespace);
            _actions.push(setter);
        }
        return _actions;
    };
    this.queue = function(setter, namespace) {
        if (setter) {
            if (namespace && !this.get(namespace)) {
                this.set(namespace, []);
            }
            (namespace ? this.get(namespace) : _queue).push(setter);
        }
        return (namespace ? this.get(namespace) : _queue);
    };
    this.unqueue = function(setter, namespace) {
        var index = $.inArray(setter, (namespace ? this.get(namespace) : _queue));
        (namespace ? this.get(namespace) : _queue).splice(index, 1);
        return (namespace ? this.get(namespace) : _queue);
    };
    this.ready = function(namespace) {
        return (!(namespace ? this.get(namespace) : _queue).length);
    };
    this.construct = function() {
        return this;
    };
    this.self = function() {
        return this;
    };
    this.wait_for_ready = function(fn, namespace, timeout) {
        var self = this;
        timeout = ((!timeout) ? 0 : timeout+1);
        clearTimeout(this.rootrunner().get("delay/timers/" + namespace));
        if (this.ready(namespace)) {
            fn();
        } else if (timeout <= this.rootrunner().get("delay/timeout")) {
            this.rootrunner().set("delay/timers/" + namespace, setTimeout(function() {
                self.wait_for_ready(fn, namespace, timeout);
            }, this.rootrunner().get("delay/interval")));
        } else {
            throw Error("Timeout!");
            console.log("Timeout!", fn, namespace, this.rootrunner().get("delay"));
        }
    };

    this._get = function(path, strict, namespace) {
        var self = this;
        var _container = (namespace == undefined || self[namespace] == undefined)
            ? this.container : this.container[namespace];
        if (typeof path == "string") {
            path = path.split("/");
        }
        if (strict == undefined) {
            strict = true;
        }
        $.each(path, function(index, value) {
            if (_container[value] != undefined) {
                _container = _container[value];
            } else if (strict) {
                _container = false;
                return _container;
            }
        });
        return _container;
    };
    this._set = function(path, setter, namespace) {
        var _container = this.container;
        if (namespace != undefined && _container[namespace] != undefined) {
            _container = _container[namespace];
        }
        path = path.split("/");
        var route = false;
        while (route = path.shift()) {
            if (typeof _container[route] != "object") {
                _container[route] = (path.length ? {} : setter);
            }
            _container = _container[route];

        }
        return setter;
    };
    this.get = function(path) {
        return this._get(path);
    };
    this.set = function(path, setter) {
        return this._set(path, setter);
    };
    this.config = function(path, setter) {
        if (setter != undefined) {
            var ret = this._set(path, setter, 'config');
            this[path] = ret;
            return ret;
        } else {
            return this._get(path, false, 'config');
        }
    };
    this.remove_change = function(label) {
        this.instance(label, null);

        var changes = this.rootrunner().get("changes");
        if (!changes) {
            changes = [];
        }
        var index = false;
        if ((index = $.inArray(label, changes)) > -1) {
            changes.splice(index, 1);
        }
        this.rootrunner().set("changes", changes);

        var changes_by_model = this.rootrunner().get("changes_by_model");
        if (!changes_by_model) {
            changes_by_model = {};
        }
        if (changes_by_model[label]) {
            delete changes_by_model[label];
        }
        this.rootrunner().set("changes_by_model", changes_by_model);

        if (this.states && this.states.undo && (index = $.inArray(label, this.states.undo)) > -1) {
            this.states.undo.splice(index, 1);
        }
    };

    this.load_script = function(script, ready, debug) {
        var self = this;
        if (debug || (script.substr(-4) == ".css" && script.substr(-4) != ".php" &&
            !(script.substr(0, 1) == "/" || script.substr(0, 4) == "http"))) {
            if (script.substr(-3) == ".js") {
                var script_elem = document.createElement("script");
                script_elem.setAttribute("type", "text/javascript");
                script_elem.setAttribute("src", script);
                document.getElementsByTagName("head")[0].appendChild(script_elem);
            } else if (script.substr(-4) == '.css') {
                var script_elem = document.createElement("link");
                script_elem.setAttribute("rel", "stylesheet");
                script_elem.setAttribute("type", "text/css");
                script_elem.setAttribute("href", script);
                document.getElementsByTagName("head")[0].appendChild(script_elem);
            } else {
                var script_elem = document.createElement("link");
                script_elem.setAttribute("data-type", "unknown");
                script_elem.setAttribute("href", script);
                document.getElementsByTagName("head")[0].appendChild(script_elem);
            }
            $(script_elem).on('load', function(){
                if (typeof ready == "function") {
                    ready();
                }
            });
        } else {
            var params = {};
            $.extend(params, {
                url: script,
                dataType: false,
                async: false
            });

            if (!params.dataType && (script.substr(-4) == ".php" || script.substr(-4) == ".htm" || script.substr(-5) == ".html")) {
                params.dataType = "html";
            } else if (!params.dataType && script.substr(-5) == ".json") {
                params.dataType = "json";
            } else if (!params.dataType && script.substr(-4) == ".xml") {
                params.dataType = "xml";
            } else if (!params.dataType) {
                params.dataType = "script";
            }
            $.ajax(params).done(function (data) {
                if (typeof ready == "function") {
                    ready(data);
                }
            }).fail(function (jqxhr, settings, exception) {
                console.log(jqxhr, settings, exception);
                throw Error("Script loader error!");
            });
        }
    };

    this.parseFn = function(str) {
        var arr = str.split(".");
        var obj = window;
        $.each(arr, function(index, obj_name) {
            if (obj && typeof obj[obj_name] != "undefined") {
                obj = obj[obj_name];
            } else {
                obj = false;
            }
        });
        return obj;
    };

    this.init();

    return this;
};
