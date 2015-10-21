<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class IndexController extends BaseController
{
    public function index()
    {

        $info = D('Member')->_get(array('id' => session('user_id')));
        $this->assign('info', $info);

        //最新公告列表
        $about_map['is_enable'] = 1;
        $about_list = D('About')->_list($about_map, '', '',1 ,10);

        //锁定股权数量
        $lock_map['member_id'] = session('user_id');
        $lock_map['status'] = 0;

        $lock_info = D('QuityLock')->where($lock_map)->field('sum(`quity_count`) as lock_quity_count')->find();
        $lock_quity_count = empty($lock_info['lock_quity_count']) ? 0 : $lock_info['lock_quity_count'];

        $this->assign('lock_quity_count', $lock_quity_count);
        $this->assign('about_list', $about_list);
        $this->display();
    }
}
