<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class QuityController extends BaseController
{
    /**
     * 股权价格修改
     * @method insert
     * @return [type] [description]
     */
    public function insert()
    {
        $model = D('Quity');

        if (!$model->create()) {
            $this->error($model->getError());
        }
        //如果存在则直接修改今天的价格
        $map['_string'] = 'DATE_FORMAT(`ctime`, "%Y-%m-%d") = "' . date('Y-m-d', time()) . '"';
        $info = $model->_get($map);

        if ($info) {
            $map = array();
            $map['id'] = $info['id'];
            $data['balance'] = I('post.balance');
            $result = $model->where($map)->save($data);
        } else {
            $result = $model->add();
        }

        if ($result) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 股权分红
     * @method dividend
     * 锁定股权数量*每股分红金额*锁定天数(次日到手工结算日) = 分红金额
     */
    public function dividend()
    {
        //获取锁定股权数量
        $model = D('QuityLock');

        $map['status'] = 0;
        $map['unlock_time'] = array('gt', now());

        $list = $model->where($map);
    }
}
