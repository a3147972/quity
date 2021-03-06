<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class QuityLockController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $this->unlock();
    }
    public function _filter()
    {
        $map['member_id'] = session('user_id');

        return $map;
    }
    public function add()
    {
        $then_balance = D('Quity')->getBalance();
        $this->assign('then_balance', $then_balance);

        $quity_count = D('Member')->where(array('id' => session('user_id')))->getField('quity');
        $this->assign('quity_count', $quity_count);

        //锁定时间
        $lock_time = C('Lock_time');
        $lock_time = explode('|', $lock_time);
        $this->assign('lock_time', $lock_time);
        $this->display();
    }
    /**
     * 股权锁定
     * @method insert
     */
    public function insert()
    {
        $second_password = D('Member')->where(array('id' => session('user_id')))->getField('second_password');

        if (md5(I('second_password')) != $second_password) {
            $this->error('二级密码不正确');
        }
        $model = D('QuityLock');

        if (!$model->create()) {
            $this->error($model->getError());
        }

        $user_info = D('Member')->_get(array('id' => session('user_id')));
        $quity_count = I('post.quity_count');
        $unlock_time = I('post.unlock_time');
        if ($quity_count > $user_info['quity']) {
            $this->error('您拥有的股权数量不足,无法锁定');
        }

        $model->member_id = session('user_id');
        $model->then_gold = D('Quity')->getBalance();
        $model->unlock_time = date('Y-m-d H:i:s', strtotime('+ ' . $unlock_time . 'month', time()));

        $model->startTrans();

        $result = $model->add();
        $delQuity_result = D('Member')->delQuity(session('user_id'), $quity_count);

        if ($result !== false && $delQuity_result !== false) {
            $model->commit();
            $this->success('操作成功', U('QuityLock/index'));
        } else {
            $model->rollback();
            $this->error('操作失败');
        }
    }

    /**
     * 股权解锁
     * @method unlock
     * @return bool 成功返回true,失败返回False
     */
    private function unlock()
    {
        $model = D('QuityLock');

        $map['member_id'] = session('user_id');
        $map['unlock_time'] = array('lt', now());
        $map['status'] = 0;

        $list = $model->_list($map);

        $quity_count = array_column($list, 'quity_count');
        $quity_count = array_sum($quity_count);

        $save_data['status'] = 1;
        $save_data['mtime'] = now();
        $model->startTrans();

        $unlock_result = $model->where($map)->save($save_data);
        $addQuity = D('Member')->addQuity(session('user_id'), $quity_count);

        if ($unlock_result !== false && $addQuity !== false) {
            $model->commit();
            return true;
        } else {
            $model->rollback();
            return false;
        }
    }
}
