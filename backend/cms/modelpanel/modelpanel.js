/**
 * Created by csibi on 2014.10.15..
 */
modelpanel = function(caller, selector, ready_fn) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.ready_fn = (ready_fn != undefined ? ready_fn : false);
    this.model = caller;

    this.panel = $("#routerunner-panel");
    this.modelpanel = $(selector);
    this.content = $(".routerunner-content");

    this.properties = {};
    this.movement = {};
    this.visibility = {};
    this.remove = {};

    this.panels = this.instance();
    this.helper = helper.modelpanel;

    this.init = function () {
        var self = this;

        if (this.model) {
            this.queue("properties_panel");
            this.queue("movement_panel");
            this.queue("visibility_panel");
            this.queue("remove_panel");

            this.properties = this.instance("properties", new properties_panel(this, "routerunner-properties-" + this.model.reference));
            this.movement = this.instance("movement", new movement_panel(this, "routerunner-movement-" + this.model.reference));
            this.visibility = this.instance("visibility", new visibility_panel(this, "routerunner-visibility-" + this.model.reference));
            this.remove = this.instance("remove", new remove_panel(this, "routerunner-remove-" + this.model.reference));

            self.helper.delayed_call(function(){
                self.ready_fn(self);
            }, function(){
                return self.ready();
            }, false, false, function() {
                console.log("exception", self);
            });

            this.helper.scrollspied($(".routerunner-panel"));
            $(window).trigger("scroll");

        }
    };

    this.clear = function() {
        var self = this;
        $.each(this.instance(), function(panel_name, panel) {
            if (typeof panel.destroy == "function") {
                panel.destroy();
                self.instance("properties", null);
            }
        });
        routerunner.components.panel.instance("modelselector").select();
        routerunner.page.current_model = false;
    };

    this.disabled = function() {
        this.panel.find("#routerunner-model").fadeTo(200, 0.5);
    };
    this.edit = function() {
        this.panel.find("#routerunner-model").fadeTo(200, 1);
    };

    this.init();
};