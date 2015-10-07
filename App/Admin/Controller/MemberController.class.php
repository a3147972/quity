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

    /**
     * 充值
     */
    public function recharge()
    {
        $gold = I('post.gold');
        $member_id = I('post.member_id');

        if (empty($gold)) {
            $this->error('请输入要操作金额');
        }
        if (empty($member_id)) {
            $this->error('请选择要操作会员');
        }

        $map['id'] = $member_id;
        $data['gold'] = array('exp', 'gold+'. $gold);

        $model = D('Member');
        $model->startTrans();
        $result = $model->where($map)->save($data);
        $write_log = D('RechargeLog')->write($gold, $member_id);

        if ($result !== false && $write_log !== false) {
            $model->commit();
            $this->success('操作成功', U('Member/index'));
        } else {
            $model->rollback();
            $this->error('操作失败');
        }
    }
}
