import notify from 'common/notify';

let $table = $('#thread-table');

$table.on('click', '.open-thread, .close-thread, .delete-thread', function() {
    let title = $(this).attr('title');

    if (!confirm('真的要'+title+'吗？')) {
        return;
    }

    $.post($(this).data('url'), function(html) {
        if (html == "success") {
            notify('success', title+'成功！');
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        }
        notify('success', title+'成功！');
        let $tr = $(html);
        $('#' + $tr.attr('id')).replaceWith($tr);
    }).error(function() {
        notify('danger', title+'失败!');
    });
});

$table.on('click', ".promoted-label", function() {
    let title = $(this).attr('title');
    $.post($(this).data('url'), function(html) {
        notify('success', title+'成功！');
        var $tr = $(html);
        $('#' + $tr.attr('id')).replaceWith(html);
    }).error(function() {
        notify('danger', title+'失败!');
    });
});