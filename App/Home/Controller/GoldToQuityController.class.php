<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class GoldToQuityController extends BaseController
{
    public function _filter()
    {
        $map['member_id'] = session('user_id');

        return $map;
    }

    public function add()
    {
        $quity_balance = D('Quity')->getBalance();
        $this->assign('quity_balance', $quity_balance);
        $this->display();
    }
    /**
     * 奖金币转股权
     * @method insert
     */
    public function insert()
    {
        $model = D('GoldToQuity');

        if (!$model->create()) {
            $this->error($model->getError());
        }
        $quity_count = I('post.quity_count');
        $quity_balance = D('Quity')->getBalance();
        $total_balance = $quity_count * $quity_balance;

        $member_balance = D('Member')->where(array('id' => session('user_id')))->getField('gold');

        if ($total_balance > $member_balance) {
            $this->error('您账户的奖金币不足');
        }

        $model->then_balance = $quity_balance;

        $model->startTrans();
        $result = $model->add();
        $del_gold = D('Member')->delGold(session('user_id'), $total_balance);
        $add_quity = D('Member')->addQuity(session('user_id'), $quity_count);

        if ($result !== false && $del_gold !== false) {
            $model->commit();
            $this->success('操作成功', U('GoldToQuity/index'));
        } else {
            $model->rollback();
            $this->error('操作失败');
        }
    }
}
