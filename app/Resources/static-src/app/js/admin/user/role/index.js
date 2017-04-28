import notify from 'common/notify';

let $form = $("#user-roles-form"),
    currentUser = $form.data('currentuser'),
    editUser = $form.data('edituser');

if (currentUser == editUser) {
    $form.find('input[value=ROLE_SUPER_ADMIN]').attr('disabled', 'disabled');
};

$form.find('input[value=ROLE_USER]').on('change', function(){
    if ($(this).prop('checked') === false) {
        $(this).prop('checked', true);
        notify('info', '用户必须拥有用户角色');
    }
});

$form.on('submit', function() {
    let roles = [];

    let $modal = $('#modal');

    $form.find('input[name="roles[]"]:checked').each(function(){
        roles.push($(this).val());
    });

    if ($.inArray('ROLE_USER', roles) < 0) {
        notify('danger', '用户必须拥有用户角色');
        return false;
    }

    $form.find('input[value=ROLE_SUPER_ADMIN]').removeAttr('disabled');
    $('#change-user-roles-btn').button('submiting').addClass('disabled');
    $.post($form.attr('action'), $form.serialize(), function(html) {
        $modal.modal('hide');
        notify('success', '用户组保存成功');
        var $tr = $(html);
        $('#' + $tr.attr('id')).replaceWith($tr);
    }).error(function(){
        notify('danger', '操作失败');
    });

    return false;
});