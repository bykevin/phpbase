<?php
$key = 'myfile';
$allowmime = ['image/jpeg', 'image/png', 'image/gif'];
$allowext = ['jpeg', 'jpg', 'png', 'gif'];
$allowsize = 1024*1024*2;
$basedir = './file/';
foreach ($_FILES[$key]['tmp_name'] as $k=>$v){
    if(is_uploaded_file($v)){
        $name = $_FILES[$key]['name'][$k];
        $tmp_name = $_FILES[$key]['tmp_name'][$k];
        $error = $_FILES[$key]['error'][$k];
        $type = $_FILES[$key]['type'][$k];
        $size = $_FILES[$key]['size'][$k];
        if($error>UPLOAD_ERR_OK){
            switch ($error){
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_EXTENSION:
                    exit("上传错误");
            }
        }
        if(!in_array($type,$allowmime)){
            exit("文件类型".$type."不允许");
        }
        $ext = pathinfo($name,PATHINFO_EXTENSION);
        if(!in_array($ext,$allowext)){
            exit("文件后缀".$ext."不被允许");
        }
        if($size>$allowsize){
            exit("文件大小".$size."大于允许文件大小".$allowsize);
        }
        $newname = uniqid().".".$ext;
        if(!is_dir($basedir)){
            mkdir($basedir);
        }
        if(move_uploaded_file($tmp_name,$basedir.$newname)){
            echo "文件".$name."上传成功<br />";
        }else{
            echo "文件".$name."上传失败<br />";
        }
    }else{
        exit("错误");
    }
}
//1、获取$_FILES
//2、处理上传错误
//3、判断文件mine类型
//4、判断文件后缀
//5、判断文件大小
//6、重命名文件
//7、将文件从缓冲区转移到新目录