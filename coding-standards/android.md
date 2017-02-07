# Android项目架构（重构）之一：代码规范

每个程序员的编码习惯和风格都有非常大的差异。特别在一个大项目中，时间久了肯定会乱，导致代码可维护性、可阅读性降低。更恶劣的情况是项目中的开发人员对继续开发这个项目产生厌恶感，编写的代码质量降低，最后就是bug无处不在。
定义好规范，才能统一风格，才可提高代码可读性，同时也提高了维护性，还能提高开发效率。
Android开发并没有业界统一的标准，这里我只是罗列下一些常用的规范。只要做到项目内开发人员编写的代码都遵循一个规范，每一句代码看上去都像一个人写的。

## 代码书写规范

#### 1.代码文件编码：UTF-8

![图片1](http://7xi85h.com1.z0.glb.clouddn.com/utf8.png)


#### 2.代码缩进为4个空格。

![图片2](http://7xi85h.com1.z0.glb.clouddn.com/tab.png)
    
分别是Tab的空格数，缩进空格数，再次缩进空格数


#### 3.大括号的使用，遵循Kernighan和Ritchie风格(包括类，接口，方法中大括号的使用) ([Egyptian brackets](http://blog.codinghorror.com/new-programming-jargon/)):
  

    public void method() {
        // good
    }
    
        
  如果右大括号是一个语句、函数体或类的终止，则右大括号后换行; 否则不换行。例如，如果右大括号后面是else或逗号，则不换行。
  
    if (condition) {
        // do something
    } else {
        // do something
    }
        
  左大括号前不换行

    public void method()
    {
        // bad
    }

        
  大括号与`if`, `else if`, `else`语句一起使用，即使只有一行代码也要把大括号加上
  
    //good
    if(condition) {
        // do something
    }
        
    //bad
    if( condition ) 
        // do something


#### 4.空格的使用

if、else、for、switch、while等逻辑关键字与后面的语句留一个空格隔开。

    while (condition) {
        // good
    }
            
    while(condition){
        // bad
    }
        
运算符之间需要有空格隔开
    
    int result = a + b; // Good
    int result=a+b; // Bad
        
标点符号`;`, `,`, `<`需要有空格隔开
    
    public void method(String param1, String param2); //good
    method(param1,param2); // bad   
    for (int i=0;i<10;i++) { // bad
    
        // do something
    }
    
#### 5.代码过长换行需缩进8个空格（相对于上一行代码）

    public void method(String expression1, 
            String expression1, 
            String expression1,
            String expression1) {  // good
    }

![0D828D4A-AF19-48C5-A3B8-0BAB17F61B86](http://7xi85h.com1.z0.glb.clouddn.com/codestyle3.png)

    

#### 6.一个方法的代码不要超过50行
#### 7.文字大小代为sp，元素大小的单位统一用dp。
#### 8.颜色统一在colors.xml中定义，不要使用系统的颜色，如：`@android:color/black`
#### 9.使用快捷键进行代码格式化，默认：
    **Windows：CTRL + ALT + L**
    **Mac：OPTION + COMMAND + L**
    
也可以自定义：
![8CD2EE24-75E7-4243-BAD0-20E152F46AFE](http://7xi85h.com1.z0.glb.clouddn.com/codestyle4.png)

## 代码命名规范

#### 1.包名
全部小写连续的单词只是简单地连接起来，不使用下划线。

#### 2.类和接口声明
使用`UpperCamelCase`风格，首字母大写。
> 当一个类有多个构造函数，或是多个同名方法，这些函数/方法应该按顺序出现在一起，中间不要放进其它函数/方法。

Android中相关类的命名：

* activity类：命名+Activity，如：LoginActivity
* fragment类，命名+Fragment，如：DialogFragment
* service类，命名+Service，如：DownloadService
* adapter类，命名+Adapter，如：CouponListAdapter
* 工具类，命名+Utils，如：FileUtils，TimeUtils
* 自定义组件，命名+Wigdet，如：TextViewWigdet

#### 3.方法名
* 方法名都以`lowerCamelCase`风格编写。
* 获取类的方法以get开头，`getUserInfo`, `getCourseInfo`

#### 4.常量名
常量名命名模式为`CONSTANT_CASE`，全部字母大写，用下划线分隔每个单词

#### 5.非常量字段名
非常量字段名以`lowerCamelCase`风格编写

#### 6.全局变量名
全局变量命名m开头（javabean 类不需要），`private List<User> mUserList`

#### 7.参数名
参数名以`lowerCamelCase`风格编写。

#### 8.代码中控件命名
采用[匈牙利命名法](https://zh.wikipedia.org/wiki/%E5%8C%88%E7%89%99%E5%88%A9%E5%91%BD%E5%90%8D%E6%B3%95)，`btnLogin`，`tvNickname`

|控件名称|命名开头|控件名称|命名开头|
|:-:|:-:|:-:|:-:|
|Button|btn|RadioButton|rbtn|
|ImageButton|ibtn|TextView|tv|
|ProgressBar|progress|EditText|et|
|ScrollView|scroll|CheckBox|chk|
|RelativeLayout|rlayout|LinearLayout|llayout|
|ListView|lv|FrameLayout|flayout|
|RadioGroup|group|WebView|webview|

#### 9.方法、变量之间的顺序

在一个源码文件中，变量的循序是：
1. 常量
2. private
3. protected
4. public

各个类型之间需要换行。

```
public class CodeStyle{
     public static final String TAG = "ChatActivity";
     public static final int MAX = 10;
     public static final int MIN = 1;
     
     private ...
     private ...
     
     protected ...
     
     public ...
}
```

方法的顺序是：
1. 如果是Activity，Service，必须先写重写生命周期（不一定全部要重写，但重写了必须要要按照生命周期排列）
2. 父类重写
3. 自己定义的方法


```
public class YourActivity{
     ...
     @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        ...
    }

    @Override
    protected void onResume() {
        super.onResume();
        ...
    }
    
    @Override
    protected void onDestroy() {
        onDestroy();
        ...
    }
    
    @Override
    protected void baseFuntion1() {
    
    }
    
    @Override
    protected void baseFuntion1() {
    
    }
     ...
}
```

## 资源文件中的命名

#### 1.Layout布局文件命名

{activity}_{module_name}[_名称]

|组件|简写|例子|
|:-:|:-:|:-:|:-:|
|activity|{activity}_{module_name}[_名称]|`activity_login`|
|fragment|{fragment}_{module_name}[_名称]|`fragment_login`|
|dialog|{dialog}_{module_name}[_名称]|`dialog_login_loading`|
|adapter中的item|{item}_{module_name}[_名称]|`item_login_username`|

#### 2.layout控件id命名规范

命名模式为：{控件缩写}_{module_name}_{view的业务逻辑名称}，如：tv_login_username
常见的控件缩写可参考上面==8.代码中控件命名==

#### 3.图片资源命名

* 背景图片：{module_name}_{控件缩写}_bg，例如：`login_tv_bg`
* 按钮图片：{module_name}_{控件缩写}_normal[press][select]，例如：`login_btn_normal`,`login_btn_press`

#### 4.strings.xml中的命名

命名模式为：{module_name}_{控件}_{意义}, `login_btn_username`，`register_toast_sumbit`

#### 7.colors.xml
静态类：

* 背景，bg开头
* 分隔，div开头
* 图标，icon开头

状态类：

* 默认状态，normal结尾
* 按下时的状态，pressed结尾，`login_btn_pressed`
* 选中时的状态，selected结尾，`course_rbtn_selected`
* 不可用时的状态，添加disabled后缀, `homework_submit_disabled`

#### 6.drawable.xml中的命名

静态类：

* 背景，bg开头
* 分隔，div开头
* 图标，icon开头

状态类：

* 默认状态，normal结尾
* 按下时的状态，pressed结尾，`login_btn_pressed`
* 选中时的状态，selected结尾，`course_rbtn_selected`
* 不可用时的状态，添加disabled后缀, `homework_submit_disabled`

## git代码管理规范

#### 分支解释

|分支名称|作用|备注|
|:-:|:-:|:----|
|master|一直保持产品稳定|永远处在即将发布(production-ready)状态|
|develop|最新的开发状态||
|release|发布版本分支|在develop新功能迭代上发布新版本，分支建立发布后merge到master|
|fix|修复bug分支||
|feature|迭代某一新功能|在develop分支上分出来，比如：feature/replace_new_find_module|



#### 迭代功能开发流程

1. 当前的迭代任务从develop上建分支，如feature/xxxx
2. 如果条件允许可以要求其他人review
3. 自测完毕merge入develop


#### bug修复
一般从master分支上去新建fix/xxx

1. 从master上建新分支，如fix/login_error，
2. 同时merge到develop分支上

#### 版本发布
1. 如果是紧急修复bug，并无新功能，修复分支fix/xxx1,fix/xxx2...merge到master，从master中建立release/x.x.x发布，并在master上打版本号tag
2. 如果是正常迭代发布，从develop中建立release/x.x.x发布，merge到master并在master上打版本号tag

[A successful Git branching model
](http://nvie.com/posts/a-successful-git-branching-model/)
