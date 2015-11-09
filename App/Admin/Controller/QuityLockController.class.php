<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class QuityLockController extends BaseController
{
    public function _filter()
    {
        $map = array();

        $username = I('username');

        if (!empty($username)) {
            $user_info = D('Member')->_get(array('username'=>$username), 'id');
            $map['member_id'] = $user_info['id'];
        }

        return $map;
    }
}
