/**
 * Created by csibi on 2014.10.15..
 */
helper.page = $.extend({}, routerunner.components.helper, {
    create_model: function(model) {
        var model_return = false;
        var params = {
            type: "POST",
            async: false,
            dataType: "json"
        };
        this.ajax('Routerunner/backend/ajax/action/create.php', model, params, function(returned) {
            model_return = returned;
        });
        return model_return;
    },

    check: function() {

    }
});
