import 'jquery-validation';
import EsWebUploader from 'common/es-webuploader.js';
import notify from 'common/notify';

$("#category-form").validate({
    rules: {
      name: {
        required: true,
      },
      code: {
        required: true,
        remote: {
          url: $("#category-code-field").data('url'),
          type: "get",                       
          dataType: "json",                     
          data: {                           
            code: function() {
                return $("#category-code-field").val();
            }
          }
        }
      }
    },
    messages: {
      name: {
        required: "请输入分类名称",
      },
      code: {
        required: "请输入分类编码",
        remote: "该编码已存在"
      },
    } 
});

new EsWebUploader({
  element: '#upload-picture-btn',
  onUploadSuccess: function(file, response) {
    console.log(response);
    $('#category-icon-field').html('<img src="' + response.url + '" class="mbm" width = "132">');
    $("#category-form").find('[name=icon]').val(response.uri);
    // let url = $("#category-icon-uploader").data("gotoUrl");
    notify('success', '上传成功！', 1);
    // document.location.href = url;
  }
});
