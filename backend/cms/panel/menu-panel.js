/**
 * Created by csibi on 2014.10.17..
 */

menu_panel = function(caller, selector) {
    var _base = new baserunner(caller);
    $.extend(this, _base);

    this.selector = selector;
    this.panel = false;
    this.helper = helper;

    this.init = function () {
        var self = this;
        this.panel = $(this.selector);

        this.helper.delayed_call(function() {
            self.panel.find(".page-sidebar-hider").slideDown(200);
        }, function() {
            return routerunner.framework_ready;
        });

        var self = this;
        this.panel.find("[data-state]").bind("click", function(){
            if (routerunner.framework_ready) {
                switch ($(this).data("state")) {
                    case "edit":
                    default:
                        routerunner.state(routerunner.states[$(this).data("state")]);
                        break;
                }
                self.select($(this).closest("li"));
            }
            return false;
        });
    };

    this.deselect = function() {
        this.panel.find("li.active span.selected").remove();
        this.panel.find("li.active").removeClass("active");
    };
    this.select = function(elem) {
        if ($(elem) != this.panel.find("li.active")) {
            this.deselect();

            var selected_span = $("<span></span>").addClass("selected");
            $(elem).find("a").append(selected_span);
            $(elem).addClass("active");
        }
    };

    this.init();
};