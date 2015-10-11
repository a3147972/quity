<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class MemberController extends BaseController
{
    public function index()
    {
        $map['id'] = session('uid');

        $info = D('Member')->_get($map);
        $this->assign('vo', $info);
        $this->display();
    }
}
