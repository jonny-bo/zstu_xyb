import starEndTime from './star-end-time';
import notify from 'common/notify';

starEndTime();

let $table = $('#user-table');

$table.on('click', '.lock-user, .unlock-user', function() {
    let title = $(this).attr('title');

    if (!confirm('真的要'+title+'吗？')) {
        return;
    }

    $.post($(this).data('url'), function(html) {
        notify('success', title+'成功！');
        let $tr = $(html);
        $('#' + $tr.attr('id')).replaceWith($tr);
    }).error(function() {
        notify('danger', title+'失败!');
    });
});