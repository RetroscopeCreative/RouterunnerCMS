/**
 * Created by csibi on 2015.04.22..
 */
remove = function(caller) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.model = caller;

    this.panel = false;

    this.changes = this.instance();
    this.states = {
        "revert": { "remove": false },
        "undo": [],
        "current": { "remove": false }
    };
    this.errors = {};
    this.helper = helper.model;
    this.dontlook = false;

    /* object specific props */

    this.init = function () {
    };

    this.change = function() {
        if (!this.skip_set_change) {
            var container = false;
            if ($(this.model.inline_elem).closest(".routerunner-container").length) {
                container = $(this.model.inline_elem).closest(".routerunner-container");
            } else if ($(this.model.inline_elem).parent().length) {
                container = $(this.model.inline_elem).parent();
            }
            this.model.position.set(false, true, container);
        }
    };

    /* panel input functions */
    this.set = function() {
        this.change();

        this.panel_set();
    };
    this.get = function() {
        return (this.model.position.states.current.to === false); // true if removed
    };
    this.load = function() {
        if (this.model.panel.remove) {
            this.panel = this.model.panel.remove;
        }
        this.event();
        if (this.state() == "disabled") {
            this.panel.panel.find("#are_you_sure").bootstrapSwitch("readonly", true);
            this.panel.panel.find("#remove-button").attr("disabled", true);
        }
    };

    this.force_blur = function() {
        // blur something?
        routerunner.page.unqueue(this.model.id + ".remove", "apply_ready");
    };

    this.panel_set = function() {
        if ($("[id='routerunner-remove-" + this.model.reference + "']").length) {

            if (!this.model.position.undo) {
                this.panel.panel.find("#are_you_sure").bootstrapSwitch("state", true);

                this.panel.panel.find("#remove-button").attr("disabled", true);
            } else {
                this.panel.panel.find("#are_you_sure").bootstrapSwitch("state", false);

                this.panel.panel.find("#remove-button").attr("disabled", false);
            }
        }
    };
    this.event = function() {
        var self = this;

        this.panel.panel.find("#remove-button").on("click", function() {
            self.set();
            return false;
        });
    };

    this.init();
};