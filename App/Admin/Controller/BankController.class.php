<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class BankController extends BaseController
{
    public function _before_index()
    {
        $id = I('uid');

        if (empty($id)) {
            $this->error('非法操作');
        }
    }
    public function _filter()
    {
        $map['member_id'] = I('uid', '');
        return $map;
    }
}
