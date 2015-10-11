<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class GoldTransferController extends BaseController
{
    /**
     * 金币转账
     * @method insert
     */
    public function insert()
    {
        $model = D('GoldTransfer');

        if (!$model->create()) {
            $this->error($model->getError());
        }

        $gold = I('post.gold');
        $to_member_id = I('post.to_member_id');

        $to_member_info = D('Member')->_get(array('id' => $to_member_id));

        if (empty($to_member_info)) {
            $this->error('会员不存在');
        }

        $member_balance = D('Member')->where(array('id' => session('uid')))->getField('gold');

        if ($gold > $member_balance) {
            $this->error('您的余额不足');
        }

        $model->startTrans();
        $add_balance_result = D('Member')->addGold($to_member_id, $gold);
        $del_balance_result = D('Member')->delGold(session('uid'), $gold);
        $result = $model->add();

        if ($add_balance_result !== false && $del_balance_result !== false && $result !== false) {
            $model->commit();
            $this->success('操作成功');
        } else {
            $model->rollback();
            $this->error('操作失败');
        }
    }
}