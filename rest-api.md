# REST API 标准

本标准的定义以实用性为主，参考了Facebook等知名网站API实现方式。

## 数据格式

以JSON作为唯一的REST API数据格式。

## HTTP动词

HTTP的动词有：

  * GET：从服务器取出资源（一项或多项）。
  * POST：在服务器新建一个资源。
  * PUT：在服务器更新资源（客户端提供改变后的完整资源）。
  * PATCH：在服务器更新资源（客户端提供改变的属性）。
  * DELETE：从服务器删除资源。
  * HEAD：获取资源的元数据。
  * OPTIONS：获取信息，关于资源的哪些属性是客户端可以改变的。

以上动词，在API中我们应只使用GET、POST、DELETE。PUT、PATCH用POST替代，因为通常人们会搞不清楚POST、PUT的区别，所以索性只使用POST这个词，来表示资源的创建/更新。

## 版本号

应当将API的版本号放入URL中，比如：
```
https://example.com/api/v1/
```

## 响应

服务器端API程序能返回的响应，状态码均应为200。非200的请求，均为异常请求，比如：API服务器宕机、服务内部错误、网络异常等。

API调用方程序应根据返回的数据中，是否含`error`这个key，来判定本次API请求操作是否成功。

### 成功的响应

单个资源的获取、创建、更新，应响应完整的资源结构体，比如创建或更新一个用户后应返回完整的用户结构体：
```
{
    "id": 1,
    "username": "admin",
    "email": "admin@example.com",
    "created_time": "2016-09-21T00:27:47+08:00"
}
```

资源结构体中的key，应均为小写字母，多个单词之间用下划线隔开。如果某个key表示时间，那么应返回ISO 8601格式标准的时间。

获取多个资源的请求，有分页的应响应如下结构体：
```
{
    "data": [
        {"key1": "value1", "key2": "value2"},
        {"key1": "value1", "key2": "value2"},
        {"key1": "value1", "key2": "value2"}
    ],
    "paging": [
        "total": 100,
        "offset": 50,
        "limit": 10
    ]
}
```

`total`表示总共有多少个资源，`offset`表示本次响应返回的结果集是从第几个资源起的序号，`limit`表次本次响应返回的结果集最大个行数。

无分页的应响应如下结构体：
```
[
    {"key1": "value1", "key2": "value2"},
    {"key1": "value1", "key2": "value2"},
    {"key1": "value1", "key2": "value2"}
]
```

### 失败的响应


API请求操作失败应返回如下的结构体：

```
{
    "error": {
        "message" : "错误描述信息。",
        "code": "错误码（值为整形）"
    }
}
```

每个接口的文档需明确指出当前接口可能会返回的每个错误码。错误码分通用错误码，及业务错误码。约定，通用错误码的取值范围应在1 ~ 100之间，100以上的为业务错误码，常用的通用错误码有：

| Code | Const              | Description               | HTTP Code |
|------|--------------------|---------------------------|-----------|
| 1    | API_NOT_FOUND      | API不存在                 | 404       |
| 2    | BAD_REQUEST        | 请求报文格式不正确：<br>1.请求体非json格式<br>2.未设置application/json头部      | 400       |
| 3    | INVALID_CREDENTIAL | Credential不正确:<br>1.Credential格式不正确 2. Credential签名不正确 | 401 |
| 4    | OVERDUE_CREDENTIAL | Credential已过期          | 401       |
| 5    | BANNED_CREDENTIALS | Credential对应的用户被禁止| 401       |
| 6    | INTERNAL_SERVER_ERROR | 服务内部错误，需联系管理员 | 500   |
| 7    | SERVICE_UNAVAILABLE| 服务暂时下线，请稍后重试:<br>1.升级维护中<br>2.过载保护中<br>3.内部服务处理超时 | 503 |
| 8    | INVALID_ARGUMENT   | 参数缺失、参数不正确      | 422       |

### 注意事项

校验类API要返回错误校验结果的，不应归类为API错误，应该按正常响应返回。

比如校验正确返回：
```
{
    "success": true
}
```

比如校验失败返回：
```
{
    "success": false,
    "reason": {
        "message" : "username is too long",
        "type": "username_invalid"
    }
}
```

## User Context

(todo)

## Batch Request

(todo)

