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
        if (C('open_status') == 0) {
            $this->error('暂时不支持提现，具体事宜请联系客服');
        }
        //判断提现时间
        $open_time = C('open_time');
        $open_time = explode('-', $open_time);
        if (date('H:i:s', time()) < $open_time[0] && date('H:i:s') > $open_time[1]) {
            $this->error('只有开盘交易时间才可以申请提现');
        }
        //银行卡列表
        $map['member_id'] = session('uid');
        $bank_list = D('Bank')->_list($map);
        if (empty($bank_list)) {
            $this->error('请先添加银行卡', U('Bank/index'));
        }
        //当前金额
        $gold = D('Member')->where(array('id' => session('uid')))->getField('gold');

        $this->assign('gold', $gold);
        $this->assign('bank_list', $bank_list);
        $this->display();
    }
    /**
     * 提交申请提现申请
     * @method insert
     */
    public function insert()
    {
        $second_password = D('Member')->where(array('id' => session('uid')))->getField('second_password');

        if (md5(I('second_password')) != $second_password) {
            $this->error('二级密码不正确');
        }

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
