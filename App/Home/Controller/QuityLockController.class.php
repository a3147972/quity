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
    /**
     * 股权锁定
     * @method insert
     * @return [type] [description]
     */
    public function insert()
    {
        $model = D('QuityLock');

        if (!$model->create()) {
            $this->error($model->getError());
        }

        $user_info = D('Member')->_get(array('id' => session('uid')));
        $quity_count = I('post.quity_count');
        $unlock_time = I('post.unlock_time');
        if ($quity_count > $user_info['quity']) {
            $this->error('您拥有的股权数量不足,无法锁定');
        }

        $model->member_id = session('uid');
        $model->then_gold = D('Quity')->getBalance();
        $model->unlock_time = date('Y-m-d H:i:s', strtotime('+ ' . $unlock_time . 'month', time()));

        $model->startTrans();

        $result = $model->add();
        $delQuity_result = D('Member')->delQuity(session('uid'), $quity_count);

        if ($result !== false && $delQuity_result !== false) {
            $model->commit();
            $this->success('操作成功');
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

        $map['member_id'] = session('uid');
        $map['unlock_time'] = array('lt', now());
        $map['status'] = 0;

        $list = $model->_list($map);

        $quity_count = array_column($list, 'quity_count');
        $quity_count = array_sum($quity_count);

        $save_data['status'] = 1;
        $save_data['mtime'] = now();
        $model->startTrans();

        $unlock_result = $model->where($map)->save($save_data);
        $addQuity = D('Member')->addQuity(session('uid'), $quity_count);

        if ($unlock_result !== false && $addQuity !== false) {
            $model->commit();
            return true;
        } else {
            $model->rollback();
            return false;
        }
    }
}
