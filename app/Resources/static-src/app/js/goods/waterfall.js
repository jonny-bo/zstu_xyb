/*
    parend 父级id
    pin 元素id
*/
const waterfall = (parent, pin) =>{
    var $aPin = $( ".course-list > div" );
    var iPinW = $aPin.eq( 0 ).width();// 一个块框pin的宽
    console.log("iPinW" + iPinW);
    var num = Math.floor( $(".course-list").width() / iPinW );
    console.log("num" + num);
    //每行中能容纳的pin个数【窗口宽度除以一个块框宽度】
    //oParent.style.cssText='width:'+iPinW*num+'px;ma rgin:0 auto;';//设置父级居中样式：定宽+自动水平外边距
    // $( "#goods-list" ).css({
    //     'width:' : iPinW * num,
    //     'margin': '0 auto'
    // });

    var pinHArr=[];//用于存储 每列中的所有块框相加的高度。

    $aPin.each( function( index, value ){
        var pinH = $aPin.eq( index ).height();
        if( index < num ){
            pinHArr[ index ] = pinH; //第一行中的num个块框pin 先添加进数组pinHArr
            console.log("pinHArr" + pinHArr);
        }else{
            var minH = Math.min.apply( null, pinHArr );//数组pinHArr中的最小值minH
            console.log("minH" + minH);
            var minHIndex = $.inArray( minH, pinHArr );
            console.log("minHIndex" + minHIndex);
            $( value ).css({
                'position': 'absolute',
                'top': minH + 15,
                'left': $aPin.eq( minHIndex ).position().left
            });
            //数组 最小高元素的高 + 添加上的aPin[i]块框高
            pinHArr[ minHIndex ] += $aPin.eq( index ).height() + 15;//更新添加了块框后的列高
        }
    });
}

export default waterfall;