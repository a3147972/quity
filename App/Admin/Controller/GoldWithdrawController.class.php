<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class GoldWithDrawController extends BaseController
{
    protected $notAllowAction = 'insert, update, del';
    public function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 审核成功
     * @method pass
     */
    public function pass()
    {
        $id = I('id');
        $map['id'] = $id;
        $result = D('GoldWithDraw')->where($map)->setField('status', 1);
        if ($result) {
            $this->success('操作成功', U('GoldWithDraw/index'));
        } else {
            $this->error('操作失败');
        }
    }
    /**
     * 审核失败,返还金额
     * @method lose
     */
    public function lose()
    {
        $id = I('id');
        $map['id'] = $id;
        $model = D('GoldWithDraw');

        $info = $model->_get($map);
        $model->startTrans();

        $result = $model->where($map)->setField('status', -1);
        $add_gold_result = D('Member')->addGold($info['member_id'], $info['gold']);

        if ($result !== false && $add_gold_result) {
            $model->commit();
            $this->success('操作成功', U('GoldWithDraw/index'));
        } else {
            $this->error('操作失败');
        }
    }
}
