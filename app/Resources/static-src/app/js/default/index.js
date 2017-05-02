console.log('web-default-index');

$("body").on("mouseenter","li.nav-hover",function(event){
    $(this).addClass("open");
}).on("mouseleave","li.nav-hover",function(event) {
    $(this).removeClass("open");
});