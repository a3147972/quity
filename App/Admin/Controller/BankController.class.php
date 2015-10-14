<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class BankController extends BaseController
{

    public function _filter()
    {
        $map['member_id'] = I('member_id', '');
        return $map;
    }
}
