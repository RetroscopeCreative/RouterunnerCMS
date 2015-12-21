/**
 * Created by csibi on 2014.10.17..
 */

modelselector_panel = function(caller, selector) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.caller = caller;
    this.selector = selector;
    this.panel = false;
    this.model_selector = false;

    this.selectable = {};

    this.init = function () {
        var self = this;
        this.panel = $(this.selector);
        this.model_selector = this.panel.find("#routerunner-model-selector");
        this.model_selector.bind("change", function() {
            if ($($("#" + $(this).val()).get(0)).find("[contenteditable=true]").length) {
                $($("#" + $(this).val()).get(0)).find("[contenteditable=true]").caret(0);
            }
            $($(routerunner.content_document).find("[data-routerunner-id='" + $(this).val() + "']").get(0)).data("model").select();
        });
    };

    this.on = function() {
        //routerunner.page.current_model.instance(false, false, "panel").open();
        //this.caller.open();
    };
    this.off = function() {
        //routerunner.page.current_model.instance(false, false, "panel").close();
        //this.caller.close();
    };

    this.browse = function() {
        this.off();
    };
    this.add = function(model, isNew) {
        if (isNew) {
            this.add_to_selector($(model.inline_elem).data("routerunner-id"), model);
            this.select(model.inline_elem);
            this.edit();
        } else {
            this.add_to_selector($(model.inline_elem).data("routerunner-id"), model);
        }

    };
    this.remove = function(model_id) {
        this.model_selector.children("#modelselector_" + model_id).remove();
    };
    this.edit = function() {
        var self = this;
        this.on();
        $.each(routerunner.page.models, function(model_id, model) {
            self.add_to_selector(model_id, model);
        });
    };
    this.refresh = function() {
        var self = this;
        var models = routerunner.queue(false, "refresh");
        $.each(models, function() {
            var option = self.model_selector.children("#modelselector_" + $(this.inline_elem).data("routerunner-id"));
            if (option.length) {
                option.html(this.get_label(20, true));
            }
            routerunner.unqueue(this, "refresh");
        });
    };
    this.add_to_selector = function(model_id, obj) {
        if (!this.model_selector.children("#modelselector_" + model_id).length && obj.id != "id_null" && obj instanceof model) {
            var label = obj.get_label(20, true);
            if (label) {
                var option = $("<option></option>");
                option.attr("id", "modelselector_" + model_id).val(model_id).html(label);
                this.model_selector.append(option);
            }
        }
    };

    this.select = function(elem) {
        if (elem) {
            this.model_selector.val($(elem).data("routerunner-id"));
        } else {
            this.model_selector.val("");
        }
        //this.model_selector.trigger("focus");
    };

    this.init();
};