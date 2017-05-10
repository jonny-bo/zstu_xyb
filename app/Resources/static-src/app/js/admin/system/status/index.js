
$('[data-toggle="popover"]').popover();
let $table = $('#direcory-check-table');
$.post($table.data('url'), function(html) {
    $table.find('tbody').html(html);
});