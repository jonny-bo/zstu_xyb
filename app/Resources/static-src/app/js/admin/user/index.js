// import notify from 'common/notify';

// notify('danger', '这里是用户管理模块');
let $start_time = $('#startDate');
let $end_time = $('#endDate');

$start_time.datepicker({
  autoclose: true,
  clearBtn: true,
  language: "en",
  todayBtn: "linked",
  format: "yyyy-mm-dd",
  endDate: $end_time.val()
}).on('changeDate',function(e){
  if($(this).val()){
    $end_time.datepicker('setStartDate', new Date(e.date));
  }else{
    $end_time.datepicker('setStartDate', new Date());
  }
});

$end_time.datepicker({
  language: "en",
  autoclose: true,
  clearBtn: true,
  todayBtn: "linked",
  format: "yyyy-mm-dd",
  endDate: $end_time.val()
}).on('changeDate',function(e){
  if($(this).val()){
    $start_time.datepicker('setEndDate', new Date(e.date));
  }
});