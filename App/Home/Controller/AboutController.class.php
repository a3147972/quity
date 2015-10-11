<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class AboutController extends BaseController
{
    public function content()
    {
        $id = I('id');
        $map['id'] = $id;
        $info = D('About')->_get($map);

        $this->assign('vo', $info);
        $this->display();
    }
}
