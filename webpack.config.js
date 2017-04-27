// 配置文件

const options = {
  output: {
    path: 'web/static-dist/',       // 用于生产环境下的输出目录
    publicPath: '/static-dist/',    // 用于开发环境下的输出目录
  },
  libs: {
    vendor: ['libs/vendor.js'], //可以是一个js文件
    "fix-ie": ['html5shiv', 'respond-js'], //也可以是一个npm依赖包
    "jquery-validation": ['libs/js/jquery-validation.js'],
    "jquery-insertAtCaret": ['libs/js/jquery-insertAtCaret.js'],
    "jquery-form": ['jquery-form'],
  },
  noParseDeps: [ //使用一个dist版本加快编译速度
    'jquery/dist/jquery.js',
    'bootstrap/dist/js/bootstrap.js',
    // 'admin-lte/dist/js/app.js',
    'jquery-validation/dist/jquery.validate.js',
    'jquery-form/jquery.form.js',
    'bootstrap-notify/bootstrap-notify.js',
    // The `.` will auto be replaced to `-` for compatibility 
    'respond.js/dest/respond.src.js',
    'bootstrap-daterangepicker/daterangepicker.js',
    'moment/moment.js',
    'bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
  ],
  onlyCopys: [ //纯拷贝文件到输出的libs目录下
    {
      name: 'es-ckeditor',
      ignore: [
        '**/samples/**',
        '**/lang/!(zh-cn.js)',
        '**/kityformula/libs/**',
      ]
    },
  ]
}

import webpackDev from 'es-webpack-engine';
import webpackBuild from 'es-webpack-engine/dist/build';

let config = (process.env.NODE_ENV === 'development') ? webpackDev(options) : webpackBuild(options)

export default config;
