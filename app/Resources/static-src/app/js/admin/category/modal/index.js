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
  element: '#category-icon-uploader',
  onUploadSuccess: function(file, response) {
    console.log(file);
    // let url = $("#category-icon-uploader").data("gotoUrl");
    // notify('success', Translator.trans('上传成功！'), 1);
    // document.location.href = url;
  }
});
