# phpbase
 @[TOC](目录)
# 文件上传
## 概述
1、客户端文件通过HTTP协议复制到远程服务器
2、客户端的一切文件均可上传
## 原理
前端通过html表单控件将文件通过http协议**复制到服务器临时目录中**，服务端接收并将文件从临时目录**转移到指定目录**。
## 上传准备
### 前端
1、表单设置属性enctype="multipart/form-data"，表示不对数据进行编码
2、前端限制提交文件大小，value为文件字节大小
```html
<input type="hidden" name="MAX_FILE_SIZE" value="10240" />
```
3、选择文件表单控件
```html
<input type="file" name="name" />
```
### 后端
1、 配置php.ini
-	file_upload	【Boolean，是否开启HTTP文件上传，默认为true开启】
-	upload_tmp_dir	【指定文件上传的**临时目录**】
-	upload_max_filesize	【int，上传文件的最大大小，单位bytes，***只包含表单文件***】
-	max_file_uploads	【int，一个请求允许上传的最大文件数】
-	max_execution_time	【int，脚本在解析器终止之前运行的最长时间】
-	max_input_time	【int，脚本解析输入数据的**最长时间**（秒）】
-	post_max_size	【char/int，运行post数据的最大值，***包含表单文件和表单数据***】
-	memory_limit	【int，运行脚本分配的最大内存量（字节单位）】
几个配置的大小关系如下：
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200504014207516.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3dlaXhpbl80MjYyNTIxOA==,size_16,color_FFFFFF,t_70)

2.、通过超全局变量$_FILES获取上传文件信息并进行文件操作

> $_FILES是HTTP POST方式上传到当前脚本的项目数组

**包含键名**
|属性| 作用 |
|--|--|
|name|	原始文件名|
|type|	文件的MIME类型|
|tmp_name|	上传到服务器的临时文件名称|
|error	|	上传后发生的错误类型|
|size	|文件的实际大小（size）|

**MIME类型**
是一种用来表示文档性质和格式的标准化，浏览器通过使用MIME类型来确定如何处理文档（**注意：不是文件扩展名**）

**error类型**
|标识| 说明 |
|--|--|
|0  |UPLOAD_ERR_OK（没有错误，上传成功）  |
|1 |UPLOAD_ERR_INT_SIZE（文件大小超出配置upload_max_filesize  |
|2  |UPLOAD_ERR_FORM_SIZE（文件大小超出MAX_FILE_SIZE）  |
|3  |UPLOAD_ERR_PARTIAL（部分文件被上传，如大文件上传突然断网的情况）  |
|4  |	UPLOAD_ERR_NO_FILE（没有文件被上传）  |
|6  |UPLOAD_ERR_NO_TMP_DIR（找不到临时目录。临时目录顺序为：先找配置文件临时目录、再找系统临时目录，两个都没有才报错）  |
|7 |UPLOAD_ERR_CANT_WRITE（写入磁盘失败，一般是对应目录无写权限，特别注意Linux系统要给对应目录指定权限）  |
|8 | UPLOAD_ERR_EXTENSION（一个php扩展阻止了文件上传） |

## 上传的实现
### 相关函数
|函数名|作用  |
|--|--|
|move_uploaded_file()  | 将文件移动到新位置 |
|is_uploaded_file()|判断文件是否是HTTP POST上传|
|rename()|重命名文件或目录|
### 实现步骤
1、接收$_FILES参数
2、处理上传可能发生的错误
3、限制文件的MIME类型
4、限制文件的扩展名
5、限制上传文件的大小
6、生成新文件名（一般随机）
7、移动文件到指定目录并重命名为新生成的文件名



