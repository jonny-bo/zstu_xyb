console.log('web-default-index');
import waterfall from '../goods/waterfall';

$("body").on("mouseenter","li.nav-hover",function(event){
    $(this).addClass("open");
}).on("mouseleave","li.nav-hover",function(event) {
    $(this).removeClass("open");
});
waterfall();