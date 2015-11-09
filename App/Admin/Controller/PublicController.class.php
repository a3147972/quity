<?php
namespace Admin\Controller;

use Think\Controller;
use Common\Tools\Upload;
class PublicController extends Controller
{
    public function upload()
    {
        $upload = new Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

        $info   =   $upload->upload($_FILES);
        $_info = array();

        if ($info) {
            foreach ($info as $_k => $_v) {
                array_push($_info, $_v);
            }

            $filename = './Uploads/'.$_info[0]['savepath'] . $_info[0]['savename'];
            $result['status'] = 1;
            $result['info'] = $filename;
        } else {
            $result['status'] = 0;
            $result['info'] = $upload->getError();
        }
        die(json_encode($result));
    }
}