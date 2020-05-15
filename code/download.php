<?php
//这样点开直接是打开图片，不是下载
//echo "<a href='./imgs/5ebbfe404919b.png'>下载图片</a>";

if(!isset($_GET['file'])){
    exit("需要传递文件名称");
}
if(empty($_GET['file'])){
    exit("请传递文件名称");
}

//远程文件地址
$file = './imgs/'.$_GET['file'];

if(!file_exists($file)){
    exit("文件不存在");
}
if(!is_file($file)){
    exit("文件不存在");
}
if(!is_readable($file)){
    exit("文件不可读");
}

//清空缓冲区(目的是为更加精准计算文件大小)
ob_clean();
//打开文件（rb）
$file_handle = fopen($file,'rb');

if(!$file_handle){
    exit("打开文件失败");
}

//通知浏览器
header('Content-type: application/octet-stream; charset=utf-8');                            
header('Content-Transfer-Encoding: binary');                                                //传输时以二进制格式进行编码
header('Content-Length: ' . filesize($file));                                               //告知浏览器文件大小，从而可知下载完成所需时间
header('Content-Disposition: attachment; filename="' . urlencode(basename($file)) . '"');   //告知浏览器以附件形式打开

//读取并输出文件
while (!feof($file_handle)){
    echo fread($file_handle,10240);
}

//关闭文档流
fclose($file_handle);
