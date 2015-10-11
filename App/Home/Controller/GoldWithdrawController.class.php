<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class GoldWithdrawController extends BaseController
{
    public function _filter()
    {
        $map['member_id'] = session('uid');

        return $map;
    }
    public function add()
    {
        $bank_map['member_id'] = session('uid');
        $bank_list = D('Bank')->_list($bank_map);
        if (empty($bank_list)) {
            $this->error('请先添加银行卡', U('Bank/index'));
        }
        $this->assign('bank_list', $bank_list);
        $this->display();
    }
    /**
     * 提交申请提现申请
     * @method insert
     */
    public function insert()
    {
        $model = D('GoldWithdraw');

        if (!$model->create()) {
            $this->error($model->getError());
        }
        $gold = I('post.gold');
        $user_gold = D('Member')->where(array('id' => session('uid')))->getField('gold');

        if ($gold > $user_gold) {
            $this->error('您账户的奖金币不足');
        }

        $model->startTrans();
        $insert_result = $model->add();
        $del_gold_result = D('Member')->delGold(session('uid'), $gold);

        if ($insert_result !== false && $del_gold !== false) {
            $model->commit();
            $this->success('申请提现成功', U('GoldWithdraw/index'));
        } else {
            $model->rollback();
            $this->error('申请提现失败');
        }
    }
}
