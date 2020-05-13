<?php

//封装文件上传类
//支持单文件、多文件上传

class Upload
{
    const UPLOAD_ERROR = [
        UPLOAD_ERR_INI_SIZE   => '文件大小超出了php.ini当中的upload_max_filesize的值',
        UPLOAD_ERR_FORM_SIZE  => '文件大小超出了MAX_FILE_SIZE的值',
        UPLOAD_ERR_PARTIAL    => '文件只有部分被上传',
        UPLOAD_ERR_NO_FILE    => '没有文件被上传',
        UPLOAD_ERR_NO_TMP_DIR => '找不到临时目录',
        UPLOAD_ERR_CANT_WRITE => '写入磁盘失败',
        UPLOAD_ERR_EXTENSION  => '文件上传被扩展阻止',
    ];
    protected $key;         //前端文件的name属性
    protected $savedir;     //文件保存路径
    protected $savename;    //保存文件名
    protected $allowsize;   //允许上传最大文件字节
    protected $allowmine;   //允许上传的文件mine类型
    protected $allowext;    //允许上传的文件后缀
    protected $error;       //文件上传的错误

    protected $file_name;
    protected $file_tmp_name;
    protected $file_error;
    protected $file_size;
    protected $file_type;

    protected $newname;
    protected $ext;

    public function __construct($savename, $key = "file", $savedir = "./file/", $allowsize = 1024 * 1024, $allowmine = ['image/jpeg', 'image/png', 'image/gif'], $allowext = ['jpeg', 'jpg', 'png', 'gif'])
    {
        $this->key = $key;
        $this->savedir = $savedir;
        $this->savename = $savename;
        $this->allowsize = $allowsize;
        $this->allowmine = $allowmine;
        $this->allowext = $allowext;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function setSaveDir($savedir)
    {
        $this->savedir = $savedir;
    }

    public function setFileName($filename)
    {
        $this->savename = $filename;
    }

    public function setMaxSize($maxsize)
    {
        $this->allowsize = $maxsize;
    }

    public function setAllowMine(array $allowmine)
    {
        $this->allowmine = $allowmine;
    }

    public function setAllowExt(array $allowext)
    {
        $this->allowext = $allowext;
    }

    protected function setError($index, $error)
    {
        exit($this->error[$index] = $error);
    }

    public function upload()
    {
        $files = [];
        if (is_array($_FILES[$this->key]['name'])) {
            foreach ($_FILES[$this->key]['name'] as $k => $v) {
                $files[$k]['name'] = $v;
                $files[$k]['type'] = $_FILES[$this->key]['type'][$k];
                $files[$k]['error'] = $_FILES[$this->key]['error'][$k];
                $files[$k]['tmp_name'] = $_FILES[$this->key]['tmp_name'][$k];
                $files[$k]['size'] = $_FILES[$this->key]['size'][$k];
            }
        } else {
            $files[] = $_FILES[$this->key];
        }

        foreach ($files as $index => $file) {
            //1、获取$_FILES
            $this->fileInfo($index, $file);
            //2、处理上传可能的错误
            $this->checkError($index);
            //3、判断上传文件的mine类型
            $this->checkMine($index, $file);
            //4、判断上传文件的后缀
            $this->checkExt($index, $file);
            //5、判断上传文件的大小
            $this->checkSize($index, $file);
            //6、重命名文件
            $this->toRename($index);
            //7、将文件从服务器缓冲区转移到指定目录
            $this->moveFile($index, $file);
        }

    }

    protected function fileInfo($index, $file)
    {
        $this->file_name[$index] = $file['name'];
        $this->file_size[$index] = $file['size'];
        $this->file_type[$index] = $file['type'];
        $this->file_error[$index] = $file['error'];
        $this->file_tmp_name[$index] = $file['tmp_name'];
    }

    protected function checkError($index)
    {
        if ($this->file_error[$index] > UPLOAD_ERR_OK) {
            switch ($this->file_error[$index]) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_EXTENSION:
                    $this->setError($index, self::UPLOAD_ERROR[$this->file_error]);
                    return false;
            }
        }
        return true;
    }

    protected function checkMine($index, $file)
    {
        if (!in_array($file['type'], $this->allowmine)) {
            $this->setError($index, "mine类型" . $file['type'] . "不被允许");
            return false;
        }
        return true;
    }

    protected function checkExt($index, $file)
    {
        $this->ext[$index] = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array($this->ext[$index], $this->allowext)) {
            $this->setError($index, '文件后缀' . $this->ext[$index] . '不被允许');
            return false;
        }
        return true;
    }

    protected function checkSize($index, $file)
    {
        if ($file['size'] > $this->allowsize) {
            $this->setError($index, "文件大小" . $file['size'] . "超过允许大小" . $this->allowsize);
            return false;
        }
        return true;
    }

    protected function toRename($index)
    {
        $this->newname[$index] = uniqid() . '.' . $this->ext[$index];
    }

    protected function moveFile($index, $file)
    {
        if(!is_dir($this->savedir)){
            mkdir($this->savedir,0777,true);
        }
        $savepath = rtrim($this->savedir,'/').'/'.$this->newname[$index];
        if(is_uploaded_file($file['tmp_name'])&&move_uploaded_file($file['tmp_name'],$savepath)){
            echo "文件".$file['name']."上传成功<br />";
            return true;
        }
        echo "文件上传失败";
        return false;
    }
}

$a = new Upload('img', 'myfile');
$a->setSaveDir('./abcdd/');
$a->upload();
