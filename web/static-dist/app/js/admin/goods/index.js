webpackJsonp(["app/js/admin/goods/index"],[function(module,exports,__webpack_require__){"use strict";function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{"default":obj}}var _starEndTime=__webpack_require__("b90bd4430e1a5dd1fde5"),_starEndTime2=_interopRequireDefault(_starEndTime),_notify=__webpack_require__("b334fd7e4c5a19234db2"),_notify2=_interopRequireDefault(_notify);(0,_starEndTime2["default"])();var $table=$("#goods-table");$table.on("click",".publish-goods, .close-goods",function(){var title=$(this).attr("title");confirm("真的要"+title+"吗？")&&$.post($(this).data("url"),function(html){(0,_notify2["default"])("success",title+"成功！");var $tr=$(html);$("#"+$tr.attr("id")).replaceWith($tr)}).error(function(){(0,_notify2["default"])("danger",title+"失败!")})})}]);