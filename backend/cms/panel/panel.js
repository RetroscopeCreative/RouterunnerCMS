/**
 * Created by csibi on 2014.10.10..
 */
panel = function(caller) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.action = {};
    this.changes = {};
    this.menu = {};
    this.modelselector = {};
    this.user = {};

    this.init = function() {
        var self = this;

        //this.instance("model", new model_panel(this, "#routerunner-model-panel"));
        //this.instance("pageproperties", new pageproperties_panel(this, "#routerunner-pageproperties-panel"));
        this.action = this.instance("action", new action_panel(this, "#routerunner-action-panel"));
        this.changes = this.instance("changes", new changes_panel(this, "#routerunner-changes-panel"));
        this.menu = this.instance("menu", new menu_panel(this, "#routerunner-menu-panel"));
        this.modelselector = this.instance("modelselector", new modelselector_panel(this, "#routerunner-modelselector-panel"));
        this.user = this.instance("user", new user_panel(this, "#routerunner-user-panel"));
    };

    this.edit = function() {
        return true;
    };

    this.init();
};