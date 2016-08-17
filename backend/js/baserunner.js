/**
 * Created by csibi on 2014.10.15..
 */

String.prototype.right = function(len) {
    var from = (this.lastIndexOf("?") == -1) ? this.length : this.lastIndexOf("?");
    return this.substr(from - len, len);
};

Object.equals = function( x, y ) {
    if ( x === y ) return true;
    // if both x and y are null or undefined and exactly the same

    if ( ! ( x instanceof Object ) || ! ( y instanceof Object ) ) return false;
    // if they are not strictly equal, they both need to be Objects

    if ( x.constructor !== y.constructor ) return false;
    // they must have the exact same prototype chain, the closest we can do is
    // test there constructor.

    for ( var p in x ) {
        if ( ! x.hasOwnProperty( p ) ) continue;
        // other properties were tested using x.constructor === y.constructor

        if ( ! y.hasOwnProperty( p ) ) return false;
        // allows to compare x[ p ] and y[ p ] when set to undefined

        if ( x[ p ] === y[ p ] ) continue;
        // if they have the same strict value or identity then they are equal

        if ( typeof( x[ p ] ) !== "object" ) return false;
        // Numbers, Strings, Functions, Booleans must be strictly equal

        if ( ! Object.equals( x[ p ],  y[ p ] ) ) return false;
        // Objects and Arrays must be tested recursively
    }

    for ( p in y ) {
        if ( y.hasOwnProperty( p ) && ! x.hasOwnProperty( p ) ) return false;
        // allows x[ p ] to be set to undefined
    }
    return true;
}

var helper = {};

baserunner = function(caller) {
    var _parent = (caller ? caller : (routerunner ? routerunner : window));
    var _instances = {}; // container to hold default instances
    var _ns_instances = {}; // container to hold namespaced instances
    var _instance_nss = []; // array to hold namespace names
    var _state = false;
    var _laststate = false;
    var _actions = [];
    var _queue = [];

    this.container = {};

    this.parent_runner = function() {
        return _parent;
    };
    this.object_len = function(obj) {
        return $.map(obj, function(n, i) { return i; }).length;
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
                        _ns_instances[namespace][index].state(routerunner.state());
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
                        _instances[index].state(routerunner.state());
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
                if (!Object.keys(_instances).length && Object.keys(_ns_instances).length) {
                    var tmp_instances = {};
                    $.each(_ns_instances, function(_ns_instance_name, _ns_instance) {
                        tmp_instances = $.extend(tmp_instances, _ns_instance);
                    });
                    if (tmp_instances) {
                        _instances = tmp_instances;
                    }
                }
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
                setter_str = routerunner.statenames[setter_no];
            } else {
                setter_str = setter;
                setter_no = routerunner.states[setter];
            }
            if (_state) {
                _laststate = routerunner.statenames[_state];
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
        return routerunner.statenames[_state];
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
            if (namespace && !this._get(namespace)) {
                this._set(namespace, []);
            }
            (namespace ? this._get(namespace) : _queue).push(setter);
        }
        return (namespace ? this._get(namespace) : _queue);
    };
    this.unqueue = function(setter, namespace) {
        var index = $.inArray(setter, (namespace ? this._get(namespace) : _queue));
        (namespace ? this._get(namespace) : _queue).splice(index, 1);
        return (namespace ? this._get(namespace) : _queue);
    };
    this.ready = function(namespace) {
        return (!(namespace ? this._get(namespace) : _queue).length);
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
        clearTimeout(routerunner.get("delay/timers/" + namespace));
        if (this.ready(namespace)) {
            fn();
        } else if (timeout <= routerunner.get("delay/timeout")) {
            routerunner.set("delay/timers/" + namespace, setTimeout(function() {
                self.wait_for_ready(fn, namespace, timeout);
            }, routerunner.get("delay/interval")));
        } else {
            throw Error("Timeout!");
            console.log("Timeout!", fn, namespace, routerunner.get("delay"));
        }
    };
    this._delayed_call = function(fn, condition, uid, delayed_started, exception_fn){
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
                throw Error('Error occurred! Delayed call timeout!');
            } else {
                routerunner.get("delay/timers")[uid] = setTimeout(function() {
                    self._delayed_call(fn, condition, uid, delayed_started, exception_fn)
                }, routerunner.get("delay/interval"));
            }
        }
    };
    this.guid = function() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    };

    this._get = function(path, strict, namespace, is_global) {
        var container_container = (is_global ? window : this);
        var self = container_container;
        var _container = (namespace == undefined || self[namespace] == undefined)
            ? container_container.container : container_container.container[namespace];
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
    this._set = function(path, setter, namespace, is_global) {
        var container_container = (is_global ? window : this);
        var _container = container_container.container;
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
    this.global_get = function(path) {
        return this._get(path, true, undefined, true);
    };
    this.global_set = function(path, setter) {
        return this._set(path, setter, undefined, true);
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
    this.load = function(script, ready, namespace, params, doc) {
        var self = this;
        Metronic.blockUI({
            target: '.backend-wrapper',
            animate: true
        });
        if (script && (routerunner.get("debug") || script.right(4) == ".css") && script.right(4) != ".php" &&
            !(script.substr(0, 1) == "/" || script.substr(0, 4) == "http") || doc) {
            if (!doc) {
                doc = document;
            }
            if (script.right(3) == ".js") {
                var script_elem = doc.createElement("script");
                script_elem.setAttribute("type", "text/javascript");
                script_elem.setAttribute("src", script);
                doc.getElementsByTagName("head")[0].appendChild(script_elem);
            } else if (script.right(4) == '.css') {
                var script_elem = doc.createElement("link");
                script_elem.setAttribute("rel", "stylesheet");
                script_elem.setAttribute("type", "text/css");
                script_elem.setAttribute("href", script);
                doc.getElementsByTagName("head")[0].appendChild(script_elem);
            } else {
                var script_elem = doc.createElement("link");
                script_elem.setAttribute("data-type", "unknown");
                script_elem.setAttribute("href", script);
                doc.getElementsByTagName("head")[0].appendChild(script_elem);
            }
            this.queue(script, namespace);
            $(script_elem).load(function(){
                Metronic.unblockUI('.backend-wrapper');
                self.unqueue(script, namespace);
                if (typeof ready == "function") {
                    ready();
                }
            });
        } else if (script) {
            this.queue(script, namespace);

            if (!params) {
                params = {};
            }
            $.extend(params, {
                url: script,
                dataType: (params.dataType ? params.dataType : false),
                async: false
            });

            if (!params.dataType && (script.right(4) == ".php" || script.right(4) == ".htm" || script.right(5) == ".html")) {
                params.dataType = "html";
            } else if (!params.dataType && script.right(5) == ".json") {
                params.dataType = "json";
            } else if (!params.dataType && script.right(4) == ".xml") {
                params.dataType = "xml";
            } else if (!params.dataType) {
                params.dataType = "script";
            }
            $.ajax(params).done(function (data) {
                Metronic.unblockUI();
                self.unqueue(script, namespace);
                if (typeof ready == "function") {
                    ready(data);
                }
            }).fail(function (jqxhr, settings, exception) {
                Metronic.unblockUI('.backend-wrapper');
                console.log(jqxhr, settings, exception);
                throw Error("Script loader error!");
            });
        }
    };
    this.ajax = function(url, params, done) {
        this.load(url, done, "ajax", params);
    };
    this.create_change = function(params) {
        var change_id = false;

        var changes = routerunner.get("changes");
        if (!changes) {
            changes = [];
        }
        var changes_by_model = routerunner.get("changes_by_model");
        if (!changes_by_model) {
            changes_by_model = {};
        }

        var instance_array = this.helper.json2array(this.instance());
        var label = instance_array.length;
        var section_class = false;
        if (params["section"]) {
            label = "";
            if (typeof params["section"] == "object" && params["section"]["model"] instanceof model) {
                label += "/" + params["section"]["model"].id;
            }
            section_class = this.helper.object_class(params["section"]);
            label += "/" + section_class;
        }
        if (params["field"] && typeof params["field"] == "string") {
            label += "/" + params["field"];
        }

        // check if the last change has happened on this model/section/field
        if (this.instance(label)) {
            if (section_class != "position" && changes[changes.length-1]
                && changes[changes.length-1].substr(0, label.length) == label) {
                label = changes[changes.length-1];
            } else {
                label += "/" + instance_array.length;
            }
        }
        var previous_change = this.instance(label);
        var change_obj = new changed(this, label, params, previous_change);
        if ($.inArray(label, changes) === -1) {
            changes.push(label);
        }

        if (!changes_by_model[label] && params["section"]) {
            var change_reference = {};
            if (typeof params["section"] == "object" && params["section"]["model"] instanceof model) {
                change_reference["model"] = params["section"]["model"].id;
            } else if (typeof params["section"] == "object" && params["section"] instanceof routerunner_form) {
                change_reference["model"] = params["section"].id;
            } else if (typeof params["section"] == "object" && params["section"] instanceof pageproperties) {
                change_reference["model"] = "pageproperties";
            }
            if (params["field"] && typeof params["field"] == "string") {
                change_reference["property"] = params["field"];
            }
            if (params["section"] instanceof visibility) {
                change_reference["property"] = "visibility/" + change_reference["property"];
            }
            changes_by_model[label] = change_reference;
        }

        this.instance(label, change_obj);
        if (this.states && this.states.undo
            && $.isArray(this.states.undo) && $.inArray(label, this.states.undo) === -1) {
            this.states.undo.push(label);
        }

        routerunner.set("changes", changes);
        routerunner.set("changes_by_model", changes_by_model);

        return change_obj;
    };
    this.remove_change = function(label) {
        this.instance(label, null);

        var changes = routerunner.get("changes");
        if (!changes) {
            changes = [];
        }
        var index = false;
        if ((index = $.inArray(label, changes)) > -1) {
            changes.splice(index, 1);
        }
        routerunner.set("changes", changes);

        var changes_by_model = routerunner.get("changes_by_model");
        if (!changes_by_model) {
            changes_by_model = {};
        }
        if (changes_by_model[label]) {
            delete changes_by_model[label];
        }
        routerunner.set("changes_by_model", changes_by_model);

        if (this.states && this.states.undo && (index = $.inArray(label, this.states.undo)) > -1) {
            this.states.undo.splice(index, 1);
        }
    };

};
