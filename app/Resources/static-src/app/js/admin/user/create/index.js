import 'jquery-validation';
$("#user-create-form").validate({
    rules: {
      username: {
        required: true,
        rangelength: [8, 40],
        remote: {
          url: $("#username").data('url'),     //后台处理程序
          type: "post",                       //数据发送方式
          dataType: "json",                  //接受数据格式   
          data: {                           //要传递的数据
            username: function() {
                return $("#username").val();
            }
          }
        }
      },
      nickname: {
        required: true,
        rangelength: [2, 40],
      },
      password: {
        required: true,
        rangelength: [5, 20],
      },
      confirmPassword: {
        required: true,
        rangelength: [5, 20],
        equalTo: "#password"
      },
      email: {
        required: true,
        email: true,
        remote: {
          url: $("#email").data('url'),        //后台处理程序
          type: "post",                       //数据发送方式
          dataType: "json",                  //接受数据格式   
          data: {                           //要传递的数据
            email: function() {
                return $("#email").val();
            }
          }
        }
      },
    },
    messages: {
      nickname: {
        required: "请输入昵称",
        rangelength: "昵称长度为2　-　40位"
      },
      username: {
        required: "请输入用户名",
        rangelength: "用户名长度为8　-　40位",
        remote: "用户名已存在"
      },
      password: {
        required: "请输入密码",
        rangelength: "密码长度为5　-　20位"
      },
      confirmPassword: {
        required: "请输入确认密码",
        rangelength: "密码长度为5　-　20位",
        equalTo: "两次密码输入不一致"
      },
      email: {
        required: "请输入邮箱",
        email: "请输入一个正确的邮箱",
        remote: "邮箱已存在"
      }
    } 
});
