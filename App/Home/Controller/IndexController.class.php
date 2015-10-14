<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        $info = D('Member')->_get(array('id' => session('uid')));
        $this->assign('info', $info);

        //最新公告列表
        $about_map['is_enable'] = 1;
        $about_list = D('About')->_list($about_map, '', '',1 ,10);

        $this->assign('about_list', $about_list);
        $this->display();
    }
}
