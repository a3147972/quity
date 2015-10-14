<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class QuityDealController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $overdue_result = $this->overdue_order(); //处理过期订单
    }
    public function _filter()
    {
        $map['option'] = array('in', array(1, 2));
        $map['status'] = 0;
        return $map;
    }
    /**
     * 股权历史价格数据
     */
    public function quityBalanceList()
    {
        //股权单价纪录
        $quity_balance_list = D('Quity')->_list(array(), '', 'ctime asc');
        $quity_balance_date = array_column($quity_balance_list, 'ctime');
        foreach ($quity_balance_date as $_k => $_v) {
            $quity_balance_date[$_k] = date('Y-m-d', strtotime($_v));
        }
        $quity_balance = array_column($quity_balance_list, 'balance');

        $this->assign('quity_balance_date', json_encode($quity_balance_date));
        $this->assign('quity_balance', json_encode($quity_balance));
    }

    public function _before_index()
    {
        $model = D('QuityDeal');

        $deal_map['option'] = array('in', array(1, 2));
        $deal_map['status'] = 0;
        $deal_list = $model->_list($deal_map, '', 'id desc', 1, 10);

        $this->assign('deal_list', $deal_list);

        $this->quityBalanceList();
    }

    public function _before_add()
    {
        $then_balance = D('Quity')->getBalance();
        $this->assign('then_balance', $then_balance);
    }

    /**
     * 发起交易
     * @method insert
     */
    public function insert()
    {
        //二级密码验证
        $second_password = D('Member')->where(array('id' => session('user_id')))->getField('second_password');

        if (md5(I('second_password')) != $second_password) {
            $this->error('二级密码不正确');
        }

        $model = D('QuityDeal');

        if (!$model->create()) {
            $this->error($model->getError());
        }
        $option = I('post.option');
        $quity_count = I('post.quity_count');

        $user_info = D('Member')->_get(array('id' => session('user_id')));
        $then_balance = D('Quity')->getBalance();

        if (in_array($option, array(2, 4))) {
            $user_quity = $user_info['quity'];
            if ($quity_count > $user_quity) {
                $this->error('您股权数量不足');
            }
        }
        if (in_array($option, array(1, 3))) {
            $user_gold = $user_info['gold'];
            $total_balance = $quity_count * $then_balance;

            if ($total_balance > $user_gold) {
                $this->error('您的余额不足');
            }
        }

        $overdue_day = I('post.overdue_day', 3);

        $model->overdue_time = date('Y-m-d H:i:s', strtotime('+' . $overdue_day . ' days', time()));
        $model->member_id = session('user_id');
        $model->then_balance = $then_balance;

        $model->startTrans();
        $result = $model->add();

        if (in_array($option, array(2, 4))) {
            $user_result = D('Member')->delQuity(session('user_id'), $quity_count);
        }
        if (in_array($option, array(1, 3))) {
            $user_result = D('Member')->delGold(session('user_id'), $total_balance);
        }
        if ($result !== false && $user_result !== false) {
            $model->commit();
            $this->success('操作成功', U('QuityDeal/index'));
        } else {
            $model->rollback();
            $this->error('操作失败');
        }
    }

    /**
     * 开始支付订单
     * @method buy
     */
    public function buy()
    {
        $id = I('id');
        $map['id'] = $id;
        $map['status'] = 0;
        $map['overdue_time'] = array('gt', now());

        $model = D('QuityDeal');

        $info = $model->_get($map);

        if (empty($info)) {
            $this->error('交易信息不存在,请重新选择');
        }
        $user_info = D('Member')->_get(array('id' => session('user_id')));
        $model->startTrans(); //开启事务处理

        if (in_array($info['option'], array(3, 4)) && session('user_id') != $info['to_member_id']) {
            if (session('user_id') != $info['to_member_id']) {
                $this->error('此定向单您不可购买');
            }
        }
        switch ($info['option']) {
            case 1:
            case 3:
                //买入操作,减少A的金额,增加A的股权数,同时减少B的股权数,增加B的金额
                if ($user_info['quity'] < $info['quity_count']) {
                    $this->error('您的股权不足');
                }
                $delQuity_result = D('Member')->delQuity(session('user_id'), $info['quity_count']);

                $addQuity_result = D('Member')->addQuity($info['member_id'], $info['quity_count']);
                $addGold_result = D('Member')->addGold(session('user_id'), $info['quity_count'] * $info['then_balance']);
                $delGold_result = true;
                break;
            case 2:
            case 4:
                //卖出操作,减少A的股权增加A的金额,同时减少B的金额,增加B的股权
                if ($user_info['gold'] < $info['quity_count'] * $info['the_balance']) {
                    $this->error('您的余额不足以购买股权');
                }
                $addQuity_result = D('Member')->addQuity(session('user_id'), $info['quity_count']);
                $addGold_result = D('Member')->addGold($info['member_id'], $info['quity_count'] * $info['then_balance']);
                $delGold_result = D('Member')->delGold(session('user_id'), $info['quity_count'] * $info['then_balance']);
                $delQuity_result = true;
                break;
        }
        $data['to_member_id'] = session('user_id');
        $data['status'] = 1;
        $data['type'] = 1;
        $data['mtime'] = now();
        $result = $model->where($map)->save($data);

        if ($addQuity_result !== false && $addGold_result !== false && $delQuity_result !== false && $delGold_result !== false && $result !== false) {
            $model->commit();
            $this->success('操作成功');
        } else {
            $model->rollback();
            $this->error('操作失败');
        }
    }

    /**
     * 过期订单处理
     * @method overdue_order
     */
    private function overdue_order()
    {
        $model = D('QuityDeal');

        $map['overdue_time'] = array('lt', now());
        $map['status'] = 0;
        $map['member_id'] = session('user_id');

        $list = $model->_list($map);

        if (empty($list)) {
            return true;
        }
        $gold = 0;
        $quity = 0;
        foreach ($list as $_k => $_v) {
            switch ($_v['option']) {
                case 1:
                case 3:
                    $gold += $_v['quity_count'] * $_v['then_balance'];
                    break;
                case 2:
                case 4:
                    $quity += $_v['quity_count'];
                    break;
            }
        }
        $save_data['status'] = -1;
        $model->startTrans();

        if (!empty($gold)) {
            $addGold = D('Member')->addGold(session('user_id'), $gold);
        } else {
            $addGold = true;
        }

        if (!empty($quity)) {
            $addQuity = D('Member')->addQuity(session('user_id'), $quity);
        } else {
            $addQuity = true;
        }
        $result = $model->where($map)->save($save_data);
        if ($addGold !== false && $addQuity !== false) {
            $model->commit();
            return true;
        } else {
            $model->rollback();
            return false;
        }

    }
}
