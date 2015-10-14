<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class AboutController extends BaseController
{
    public function _filter()
    {
        $map['is_enable'] = 1;
        return $map;
    }
    public function content()
    {
        $id = I('id');
        $map['id'] = $id;
        $info = D('About')->_get($map);

        $this->assign('vo', $info);
        $this->display();
    }
}
