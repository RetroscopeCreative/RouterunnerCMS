/**
 * Created by csibi on 2014.10.15..
 */
helper.model = $.extend({}, routerunner.components.helper, {
    recycle: {},

    get_input_elem: function(elem, field) {
        var input = false;
        if (field.selector != undefined) {
            if (field.selector == "=" || field.selector == "self") {
                input = $(elem);
            } else if ($(elem).find(field.selector).length > 0) {
                input = $(elem).find(field.selector);
            }
        }
        return input;
    },

    get_input_controller: function(script_object) {
        var elem = script_object.model.inline_elem;
        var input_ctrl = false;
        if (!script_object.route.length && script_object.model && script_object.model.route) {
            script_object.route = script_object.model.route.split("/");
        }

        if (script_object.route.length) {
            var wndw = ((routerunner && routerunner.iframe && routerunner.iframe.contentWindow)
                ? routerunner.iframe.contentWindow : window);
            var obj = wndw;
            $.each(script_object.route, function(index, obj_name){
                if (obj[obj_name] != undefined) {
                    obj = obj[obj_name];
                }
            });
            if (!obj || wndw === obj) {
                wndw = window;
                obj = wndw;
                $.each(script_object.route, function(index, obj_name){
                    if (obj[obj_name] != undefined) {
                        obj = obj[obj_name];
                    }
                });
            }
            if (obj && obj[script_object.model.class_name] != undefined
                && obj[script_object.model.class_name][script_object.field_data.type] != undefined
                && typeof obj[script_object.model.class_name][script_object.field_data.type] == "function") {
                obj = obj[script_object.model.class_name];
            }

            if (obj && obj[script_object.field_data.type] != undefined
                && typeof obj[script_object.field_data.type] == "function") {
                input_ctrl = new obj[script_object.field_data.type](script_object.model, script_object.input, script_object.field_data);
            } else if (window["input"] != undefined && window["input"][script_object.field_data.type] != undefined
                && typeof window["input"][script_object.field_data.type] == "function") {
                input_ctrl = new window["input"][script_object.field_data.type](script_object.model, script_object.input, script_object.field_data);
            } else if (window[script_object.field_data.type] != undefined
                && typeof window[script_object.field_data.type] == "function") {
                input_ctrl = new window[script_object.field_data.type](script_object.model, script_object.input, script_object.field_data);
            }
        } else {
            if (window["input"] != undefined && window["input"][script_object.field_data.type] != undefined
                && typeof window["input"][script_object.field_data.type] == "function") {
                input_ctrl = new window["input"][script_object.field_data.type](script_object.model, script_object.input, script_object.field_data);
            } else if (window[script_object.field_data.type] != undefined
                && typeof window[script_object.field_data.type] == "function") {
                input_ctrl = new window[script_object.field_data.type](script_object.model, script_object.input, script_object.field_data);
            }
        }
        if (input_ctrl) {
            return input_ctrl;
        //} else {
        //    throw Error('Input controller js not found (' + script_object.script + ')!');
        } else {
            return {};
        }
    },

    script_loader: function(url_data, done) {
        var url = 'Routerunner/backend/ajax/common/load_script.php?ver=' + routerunner.version;
        var data = { url : url_data };
        var params = {
            dataType: "json",
            method: "post",
            async: false
        };
        this.ajax(url, data, params, function(data) {
            if (done) {
                done(data);
            }
        });
    },

    get_script_object: function(input_elem, model, field_name, field_data) {
        var self = this;

        var script_object = {
            input: input_elem,
            model: model,
            field_name: field_name,
            field_data: field_data,
            script: false,
            route: false
        };
        var script_to_load = false;
        var version = '?ver=' + routerunner.version;
        var _route = [];
        if (self.recycle[$(model.inline_elem).data('route') + "/" + field_name] != undefined) {
            var recycle = self.recycle[$(model.inline_elem).data('route') + "/" + field_name];
            script_object["script"] = recycle["script"];
            script_object["route"] = recycle["route"];
        } else {
            _route = $(model.inline_elem).data('route').split('/');
            var first_route = '';
            if ((first_route = _route.shift()) != '') {
                _route.unshift(first_route);
            }
            var route_script;

            var path = routerunner.settings.scaffold + '/input/' + _route.join('/');
            var url_data = [{
                path: path,
                file: [ field_name + '.js' + version, field_data.type + '.js' + version ],
                class: model.class_name
            }];
            var response = {};
            this.script_loader(url_data, function(data) {
                response = data;
            });

            script_object = $.extend(script_object, response);
            if (script_object["script"]) {
                self.recycle[$(model.inline_elem).data('route') + "/" + field_name] = script_object;
            }
        }
        return script_object;
    }
});