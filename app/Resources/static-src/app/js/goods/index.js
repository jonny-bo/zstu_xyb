import notify from 'common/notify';

let range = 50; //距下边界长度/单位px
let stop = true;
let page = 1;
let limit = 10;
let count = $("#goods-list").data('count');

$(window).scroll(function(){ 
    let srollPos = $(window).scrollTop();
    let totalheight = parseFloat($(window).height()) + parseFloat(srollPos);

    if(($(document).height() - range) <= totalheight && stop && (Math.ceil(count/limit) >= page)) {
        stop = false;
        console.log("sss");
        $.ajax({
            url: $("#goods-list").data('url'),
            type: 'GET',
            cache: false,
            dataType:"html",
            data: {page: page+1},
        })
        .done(function(html) {
            console.log("success");
            if (html == '') {
                console.log("没有数据加载！");
            }　else {
                $(".course-list").append(html);
                page++;
            }
            stop = true;
        })
        .fail(function() {
            console.log("error");
        })
    }
});

