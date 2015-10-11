<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class AdminController extends BaseController
{
    public function _before_del()
    {
        $id = I('id');

        if ($id == 1) {
            $this->error('默认用户不可被删除');
        }
    }

    /**
     * 会员列表导出
     * @method export
     */
    public function export()
    {

    }
}
