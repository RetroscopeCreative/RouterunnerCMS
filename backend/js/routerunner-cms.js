/**
 * Created by csibi on 2014.08.07..
 */

/*
$.ajax({
    url: 'RouterunnerCMS/backend/js/baserunner.js',
    dataType: "script",
    async: false
});
*/

var routerunner = new baserunner();

routerunner.version = '1.3.2.0050';

routerunner.scripts = {
    cms: {
        helper: {
            'helper.js': false
        },
        helpers: {
            'common-helper.js': false,
            'panel-helper.js': false,
            'model-helper.js': false,
            'modelpanel-helper.js': false,
            'change-helper.js': false,
            'page-helper.js': false
        },
        'thirdparty': {
            //'http://cdn.ckeditor.com/4.4.7/full/ckeditor.js': false
        },
        allframe: {
            //'baserunner.js': false,
            'ckeditor/adapters/jquery.js': false
        },
        common: {
            'common.js': false,
            'frame.css': false,
            'routerunner-cms.css': false
        },
        pointers: {
            'pointer.js': false
        },
        panel: {
            'panel.css': false,
            'action-panel.js': false,
            'menu-panel.js': false,
            'user-panel.js': false,
            //'pageproperties-panel.js': false,
            'modelselector-panel.js': false,
            'changes-panel.js': false,
            //'model-panel.js': false,
            'panel.js': false
        },
        model: {
            'property.js': false,
            'position.js': false,
            'visibility.js': false,
            'remove.js': false,
            'model.js': false
        },
        modelpanel: {
            'modelpanel.css': false,
            'properties-panel.js': false,
            'movement-panel.js': false,
            'visibility-panel.js': false,
            //'drafts-panel.js': false,
            //'history-panel.js': false,
            'remove-panel.js': false,
            'modelpanel.js': false
        },
        fault: {
            'fault.js': false
        },
        changed: {
            'changed.js': false
        },
        page: {
            'page.js': false,
            'pageproperties.js': false
        }
    },
    metronic: {
        assets: {
            //'global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js': false
        }
    },
    thirdparty: {
        ckeditor: {
            'ckeditor.js': false
        },
        uri: {
            'src/URI.min.js': false,
            //'src/jquery.URI.min.js': false
        }
    }
};
routerunner.autocreate = ["common", "pointers", "page", "panel"];
routerunner.components = routerunner.instance();
routerunner.helper = {};
routerunner.common = {};
routerunner.pointers = {};
routerunner.page = {};
routerunner.panel = {};
routerunner.affected_models = [];
routerunner.iframe = false;
routerunner.iframe_loaded = false;
routerunner.content_window = false;
routerunner.content_document = false;
routerunner.first_load = true;
routerunner.current_modelpanel = false;

routerunner.xhr = {};

routerunner.framework_ready = false;

routerunner.model_selected = false;

routerunner.states = {
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
    destroy: 202,

    revert: 800,

    force_blur: 899,
    apply: 900
};
routerunner.statenames = {};

routerunner.container = {
    started: new Date().getTime(),
    debug: true,
    delay: {
        interval: 50,
        timeout: 30,
        timers: {}
    },
    models_to_attach: {},
    models: {},
    models_by_ref: {},
    models_by_routeref: {},
    containers_to_attach: {},
    containers: {},

    new_models: {}, // attached new models by routerunner
    createds: {}, // created models

    movements: [],
    changes: false
};

routerunner.config = {};
routerunner.settings = {
    "panel_width": 560,
    "scaffold": "scaffold",
    "root": "default",
    "backend": "RouterunnerCMS/scaffold/backend"
};

routerunner.init = function() {
    if (!window.settings) {
        window.settings = {};
    }
    routerunner.settings = $.extend(routerunner.settings, window.settings);

    routerunner.iframe = $(".content-iframe").get(0);

    if (routerunner.settings.version) {
        routerunner.version = routerunner.settings.version;
    }

    $.each(routerunner.states, function(statename, state_no) {
        routerunner.statenames[state_no] = statename;
    });

    routerunner.queue(function () {
        routerunner.component("cms", "thirdparty", false, "helpers");
        routerunner.component("thirdparty", "ckeditor", false, "helpers");
        routerunner.component("thirdparty", "uri", false, "helpers");
        routerunner.component("cms", "helpers", false, "helpers");
    }, "helper.loaded");
    routerunner.queue(function () {
        routerunner.component("cms", "common", false, "commons");
        routerunner.helper = $.extend({}, routerunner.components.helper);
    }, "helpers.loaded");
    routerunner.queue(function () {
        routerunner.component("cms", "allframe", false, "allframes");
        //routerunner.component("cms", "allframe", false, "allframes", routerunner.content_document);
    }, "commons.loaded");
    routerunner.queue(function () {
        routerunner.component("cms", "pointers", false, "framework");
        //routerunner.component("cms", "sidebar", false, "framework");
        routerunner.component("cms", "panel", false, "framework");
        routerunner.component("cms", "page", false, "framework");
        routerunner.component("metronic", "assets", false, "framework");
        if (routerunner.settings["LANG"] && routerunner.settings["scaffold"] && routerunner.settings["BASE"]) {
            var i18n_script = routerunner.settings["scaffold"] + "/model/i18n." + routerunner.settings["LANG"] + ".json";
            routerunner.helper.ajax(i18n_script, {}, {async: false, dataType: "json"}, function (i18n) {
                routerunner.settings.localization = i18n;
            });
        }
    }, "allframes.loaded");
    routerunner.component("cms", "helper", false, "helper");

    routerunner.queue(function () {
        routerunner.common = routerunner.instance("common");
        routerunner.page = routerunner.instance("page");
        routerunner.pointers = routerunner.instance("pointers");
        routerunner.panel = routerunner.instance("panel");

        routerunner.action("loaded");
        if (routerunner.iframe) {
            routerunner.state("browse");
        }
    }, "framework.loaded");

    $(routerunner.iframe).on("load", function() {

        routerunner.content_window = routerunner.iframe.contentWindow;
        routerunner.content_document = routerunner.content_window.document;

        routerunner.settings = $.extend(routerunner.settings, routerunner.content_window.settings);

        var selectors = [];
        while (window.routerunner_models.length) {
            var current_selector, current_model = false;
            if (current_selector = window.routerunner_models.splice(0, 1)) {
                if (current_model = $("[data-routerunner-id='" + current_selector + "']").get()) {
                    $.each(current_model, function (index, selector) {
                        if (selector && $(selector).length) {
                            routerunner.attach(selector);
                        }
                    });
                }
            }
        }

        //$(routerunner.iframe).on("load", function(evt) {
            routerunner.content_window = routerunner.iframe.contentWindow;
            routerunner.content_document = routerunner.content_window.document;

            routerunner.content_window.$ = $;

            $(routerunner.content_window).on("beforeunload", function() {
                if (routerunner.get("changes").length) {
                    return ($(routerunner.content_document).find("body").data("beforeunload")
                        ? $(routerunner.content_document).find("body").data("beforeunload")
                        : "Are you sure to leave without saving changes?");
                } else {
                    //routerunner.instance("page").clear_page();
                    //$(".page-container").hide();
                    $(routerunner.content_window).off("beforeunload");
                }
            });

            $(routerunner.content_document).on("click", ".routerunner-model", function(evt) {
                var this_model = this;
                setTimeout(function() {
                    if ($(this_model).data("model") && routerunner.page.current_model != $(this_model).data("model")) {
                        $(this_model).data("model").select();
                        if ($(this_model).data("model").state() != "browse") {
                            evt.stopImmediatePropagation();
                            evt.stopPropagation();
                            return false;
                        }
                    }
                }, 300);
            });

            //$(".page-container").slideDown();

            routerunner._delayed_call(function() {
                routerunner.instance("page").page_init();
                routerunner.first_load = false;
                routerunner.instance("panel").instance("action").init_new_button();
                var url = routerunner.content_window.location.href.replace(routerunner.settings.BASE, '');

                // todo: just load it once!!!
                routerunner.instance("page").pageproperties.update(url);

                var browser_url = "admin/" + url;
                var browser_uri = new URI(browser_url);
                browser_uri.removeQuery((routerunner.settings.backend_uri
                    ? routerunner.settings.backend_uri : "backend"));
                browser_url = browser_uri.toString();
                window.history.pushState({"url":url}, $(routerunner.content_document).find("title").text(), browser_url);

                routerunner.update_links();

                if (!$(routerunner.content_document).find("body").hasClass("editable")
                    || !$(routerunner.content_document).find(".routerunner-backend").length) {
                    $("#routerunner-action-panel").hide();
                } else {
                    $("#routerunner-action-panel").show();
                }
            }, function() {
                return (typeof routerunner.instance("page") === "object"
                    && typeof routerunner.instance("page").pageproperties === "object");
            });
        //});
    });

    /*
    if (routerunner.iframe) {
        routerunner._delayed_call(function () {
            $(routerunner.iframe).trigger("load");
        }, function () {
            return (routerunner.iframe_loaded === true);
        });
    }
    */

    window.routerunner_attach = function(selector, iframe) {
        var routerunner_document = ((iframe && iframe.document) ? iframe.document : window.document);
        var to_attach = $(routerunner_document).find("[data-routerunner-id='" + selector + "']").get();
        if ($(routerunner_document).find("#script_" + selector).length) {
            $(routerunner_document).find("#script_" + selector).remove();
        }
        if (routerunner.content_document && $(routerunner.content_document).find("#script_" + selector).length) {
            $(routerunner.content_document).find("#script_" + selector).remove();
        }
        routerunner.attach(to_attach);
    };

    $(window).resize(function() {
        routerunner.action("resize");
    });

    $(window).on("beforeunload", function(evt) {
        if (routerunner.get("changes").length) {
            return ($("body").data("beforeunload")
                ? $("body").data("beforeunload") : "Are you sure to leave without saving changes?");
        } else {
            $(window).off("beforeunload");
        }
    });
};
routerunner.links_under_backend = function(elem, evt) {
    if (routerunner.get("changes").length) {
        return false;
    }
    if (routerunner.state() != "browse") {
        return false;
    }
    evt.stopImmediatePropagation();
    evt.stopPropagation();

    if ($(elem).attr("href")) {
        var href = $(elem).attr("href");
        var uri = new URI(href);
        if (href.length < 7 || (href.substr(0, 7) != "http://" && href.substr(0, 8) != "https://")) {
            uri.href('admin/' + href);
        } else if (href.indexOf(routerunner.settings.BASE) === 0) {
            uri.href(href.replace(routerunner.settings.BASE, ''));
            uri.href('admin/' + uri.href());
        }
        uri.setQuery((routerunner.settings.backend_uri ? routerunner.settings.backend_uri : "backend"),
            routerunner.content_window.routerunner_backend);
        $(this).attr({
            "href": uri.toString(),
            "target": "_top",
        });
        top.location.href = uri.toString();

        return false;
    }
    return true;
};
routerunner.update_links = function() {
    if (routerunner.content_window && routerunner.content_document && routerunner.content_window.routerunner_backend) {
        $(routerunner.content_document).find("a[href]").each(function () {
            var uri = new URI($(this).attr("href"));
            var model = $(this).closest('.routerunner-model');
            if (model.length && model.data('url') && model.data('url') == uri.href()) {
                uri.setQuery((routerunner.settings.backend_uri ? routerunner.settings.backend_uri : "backend"),
                    routerunner.content_window.routerunner_backend);
                uri.href('admin/' + uri.href());
                $(this).attr({
                    "href": uri.toString(),
                    "target": "_top",
                });
            } else {
                $(this).on('click', function(e) {
                    routerunner.links_under_backend(this, e);
                });
            }
        });
        $(routerunner.content_document).find("body").addClass("editable");
    }
};

routerunner.component = function (mainclass, subclass, success_fn, namespace, doc) {
    var self = this;
    var script_array = this.scripts;
    mainclass = (mainclass == undefined ? 'cms' : mainclass);
    subclass = (subclass == undefined ? 'common' : subclass);
    var url = routerunner.settings["BACKEND_DIR"] + '/' + (mainclass !== 'metronic' ? 'backend/' : '') + mainclass + '/' + subclass + '/';
    if (script_array[mainclass] != undefined) {
        script_array = script_array[mainclass];
    }
    if (script_array[subclass] != undefined) {
        script_array = script_array[subclass];
    }
    if (!namespace) {
        namespace = mainclass + "/" + subclass;
    }
    if (!script_array.loaded) {
        $.each(script_array, function (script, is_loaded) {
            if (is_loaded) {
                return false;
            }
            var script_to_load = (script.substr(0, 1) == '/' || script.substr(0, 4) == 'http')
                ? script : url + script + '?v=' + self.version;
            var ready_fn = function() {
                script_array[script] = new Date().getTime();

                var _loaded = true;
                $.each(script_array, function(_script, is_loaded) {
                    if (!is_loaded) {
                        _loaded = false;
                    }
                });
                if (_loaded) {
                    var _subclass = false;
                    if (typeof window[subclass] == "function" && $.inArray(subclass, routerunner.autocreate) !== -1) {
                        _subclass = new window[subclass]();
                    } else if (typeof window[subclass] == "object") {
                        _subclass = window[subclass];
                    }
                    if (_subclass) {
                        self.instance(subclass, _subclass);
                    }
                }
                if (typeof success_fn == "function" && self.ready(namespace)) {
                    success_fn();
                } else if (namespace && self.ready(namespace) && self.queue(false, namespace + ".loaded")) {
                    $.each(self.queue(false, namespace + ".loaded"), function(fn_index, fn) {
                        self.unqueue(fn, namespace + ".loaded");
                        if (fn && typeof fn == "function") {
                            fn();
                        }
                    });
                }
            };
            self.load(script_to_load, ready_fn, namespace, false, doc);
        });
    } else if (success_fn != undefined) {
        success_fn();
    }
};
routerunner.session_open = function() {
    var session_id = false;
    $.ajax({
        url: routerunner.settings["BACKEND_DIR"] + "/backend/ajax/session/open.php",
        async: false,
        dataType: "json",
        type: "get"
    }).done(function(data) {
        if (data.session_id) {
            session_id = data.session_id;
            routerunner.session_set(data.session_id, data.session_open_date);
        }
    });
    return session_id;
};
routerunner.session_set = function(session_id, session_open_date) {
    routerunner.set("session_id", session_id);
    routerunner.set("session_open_date", session_open_date);
};
routerunner.session = function() {
    if (session_opened = routerunner.get("session_id")) {
        return session_opened;
    } else if (session_created = routerunner.session_open()) {
        return session_created;
    }
};

routerunner.edit = function() {
    if (!routerunner.session()) {
        alert("Error in session opening method!");
        routerunner.state("browse");
    }
};

routerunner.attach = function(elem) {
    var elem_id = (!$(elem).data("routerunner-id")) ? "routerunner_id_" + new Date().getTime() : $(elem).data("routerunner-id");
    this.set('models_to_attach/' + elem_id, elem);
};

routerunner.refresh = function() {
    window.location.reload();
};


$(window).unbind("resize").bind("resize", function() {
    routerunner.action("resize");
});

$(document).ready(function(){
    if (document.getElementById("routerunner-content-iframe")) {
        document.getElementById("routerunner-content-iframe").onload = function () {
            routerunner.iframe_loaded = true;
        };
    }
    routerunner.init();
});
