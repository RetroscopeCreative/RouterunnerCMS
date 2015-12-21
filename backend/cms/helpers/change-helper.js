/**
 * Created by csibi on 2014.10.15..
 */
helper.change = $.extend({}, routerunner.components.helper, {
    noop: function() {
        return false;
    },
    log_change: function(change_obj, fn) {
        var url = "Routerunner/backend/ajax/action/log_change.php";
        var data = {
            "change_id": change_obj.id,
            "reference": change_obj.data.reference,
            "resource": change_obj.data.resource,
            "changes": change_obj.changes,
            "state": change_obj.section
        };
        var params = {
            dataType: 'json',
            type: 'post'
        };
        if (!change_obj.id || routerunner.set("synchronized")) {
            params.async = false;
        }
        this.ajax(url, data, params, function(data){
            if (data.success && data.change_id) {
                change_obj.id = data.change_id;
                change_obj.data.date = data.date;
                change_obj.data.approved = data.approved;
                change_obj.data.approved_session = data.approved_session;
                change_obj.data.state = data.state;
            }
            if (fn && typeof fn == "function") {
                fn();
            }
        }, function(){
            alert("error in log changes");
        });
    },

    apply_change: function(change_obj, fn) {
        var success = false;
        var url = false;
        var data = { change_id: change_obj.id };
        if (change_obj.section == "routerunner-page-properties") {
            url = 'Routerunner/backend/ajax/action/apply_pageprops.php';
            data.resource = routerunner.page.pageproperties.resource;
        } else {
            url = 'Routerunner/backend/ajax/action/apply_change.php';
            data.route = change_obj.model.route;
        }
        if (change_obj.caller instanceof property) {
            var apply_fn, ret_fn;
            if ((apply_fn = change_obj.caller.control_get("apply", "global", ""))
                && typeof change_obj.caller[apply_fn] == "function") {
                var params = {
                    change: change_obj.data,
                    value: change_obj.changes
                }
                ret_fn = change_obj.caller[apply_fn](params);
                if (ret_fn != undefined) {
                    $.each(ret_fn, function(field_name, field_value) {
                        if (change_obj.model && change_obj.model.property[field_name]) {
                            change_obj.model.property[field_name].change(field_name, field_value);
                            change_obj.model.property[field_name].set(field_value);
                        }
                    });
                }
            }
        }
        var params = {
            dataType: 'json',
            async: false,
            type: 'post'
        };
        if (url) {
            this.ajax(url, data, params, function (data) {
                if (data && data.success && data.change_id) {
                    change_obj.id = data.change_id;
                    change_obj.data.date = data.date;
                    change_obj.data.approved = data.approved;
                    change_obj.data.approved_session = data.approved_session;
                    change_obj.data.state = data.state;
                    success = data.success;
                }
                if (fn && typeof fn == "function") {
                    fn();
                }
            }, function () {
                alert("error in apply changes");
            });
        }
        return success;
    },

    delete_change: function(change_obj) {
        var success = false;
        var url = 'Routerunner/backend/ajax/action/delete_change.php';
        var data = {
            change_id: change_obj.id
        };
        var params = {
            dataType: 'json',
            async: false,
            type: 'post'
        };
        this.ajax(url, data, params, function(data){
            success = data.success;
        }, function(){
            alert("error in delete changes");
        });
        return success;
    }
});