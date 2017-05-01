import 'jquery-validation';
import notify from 'common/notify';
$("#transfer-group-form").validate({
    rules: {
      username: {
        required: true,
        remote: {
          url: $("#username").data('url'),     //后台处理程序
          type: "get",                       //数据发送方式
          dataType: "json",                  //接受数据格式   
          data: {                           //要传递的数据
            value: function() {
                return $("#username").val();
            }
          }
        }
      },
    },
    messages: {
      username: {
        required: "请输入用户名",
        remote: "该用户不存在"
      },
    }
});
