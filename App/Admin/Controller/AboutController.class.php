<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class AboutController extends BaseController
{
    public function is_enable()
    {
        $id = I('id');
        $status = I('status');

        $map['id'] = $id;

        $result = D('About')->where($map)->setField('is_enable', $status);

        if ($result) {
            $this->success('成功', U('About/index'));
        } else {
            $this->error('失败');
        }
    }
}
