<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class MemberController extends BaseController
{
    /**
     * 启用或禁用会员
     * @method is_enable
     */
    public function is_enable()
    {
        $status = I('status', 1);
        $id = I('id');

        if (empty($id)) {
            $this->error('非法操作');
        }

        if (!in_array($status, array(1, 0))) {
            $this->error('传值错误');
        }

        $map['id'] = $id;

        $result = D('Member')->where($map)->setField('is_enable', $status);

        if ($result) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}
