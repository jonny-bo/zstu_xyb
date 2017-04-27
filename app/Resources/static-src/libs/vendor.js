import './vendor.less';

import 'jquery';
import 'bootstrap';
import 'bootstrap-datepicker';
import 'common/script';
// import 'common/bootstrap-modal-hack';

$(document).ajaxSend(function(a, b, c) {
  if (c.type == 'POST') {
    b.setRequestHeader('X-CSRF-Token', $('meta[name=csrf-token]').attr('content'));
  }
});

$('#modal').on('show.bs.modal', function (e) {
  let url = $(e.relatedTarget).data('url');
  let $this = $(this);

  if(!$this.html()) {
    localStorage.removeItem("modalUrl");
  }
  
  if(localStorage.getItem("modalUrl") != url ) {
    $this.empty().load(url);
    localStorage.setItem("modalUrl",url);
  }
})