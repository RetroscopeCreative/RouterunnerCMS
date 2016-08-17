/**
 * Created by csibi on 2014.10.16..
 */
helper.panel = $.extend({}, routerunner.components.helper, {
    get_changes_content_row: function(icon, model, property, timestamp, errors) {
        timestamp = parseInt(timestamp);
        var diff = new Date().getTime() / 1000 - timestamp;
        var date = "Just now";
        if (diff > 60) {
            date = Math.floor(diff / 60) + " min" + (Math.floor(diff / 60) > 1 ? "s" : "");
        }
        if (diff > 60 * 60) {
            date = Math.floor(diff / (60 * 60)) + " hour" + (Math.floor(diff / (60 * 60)) > 1 ? "s" : "");
            date += Math.floor(diff / 60) + " min" + (Math.floor(diff / 60) > 1 ? "s" : "");
        }

        var ret = $('' +
        '   <div class="task-checkbox label label-sm label-info">' +
        '        <i class="' + icon + '"></i>' +
        '    </div>' +
        '    <div class="task-title">' +
        '       <span class="task-title-sp">' + model + '</span>' +
        '       <span class="label label-sm label-success">' + property + '</span>' +
        '   </div>' +
        '    <div class="task-date">' +
        '        <i class="fa fa-clock-o"></i>' +
        '       <span class="task-date">' + date + '</span>' +
        '   </div>');

        if (errors) {
            ret.siblings(".task-title").append(errors);
        }

        return ret;
    }
});