<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class QuityController extends BaseController
{
    public function index()
    {
        $this->display();
    }
    public function add()
    {
        $page = I('page', 1);
        $page_size = I('page_size', 10);
        $order = I('order', '');

        $model = D(CONTROLLER_NAME);

        //查询值
        $pk = $model->getPk();
        $order = empty($order) ? $pk . ' desc' : $order;
        $map = method_exists($this, '_filter') ? $this->_filter() : array();

        //查询数据
        if (method_exists($model, 'lists')) {
            $list = $model->lists($map, '', $order, $page, $page_size);
        } else {
            $list = $model->_list($map, '', $order, $page, $page_size);
        }

        $count = $model->_count($map);

        //分页处理
        $page_list = $this->page($count, $page, $page_size);

        $this->assign('page_list', $page_list);
        $this->assign('count', $count);
        $this->assign('list', $list);
        $this->display();
    }
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
        $share_amount = C('SHARE_AMOUNT'); //每股分红金额
        //查询所有还在锁定的股权
        $lock_map['status'] = 0;

        $lock_list = D('QuityLock')->_list($lock_map, $lock_field);

        if (empty($lock_list)) {
            $this->error('没有在锁定中的股权');
        }

        $list = array();
        $member_id = array();
        foreach ($lock_list as $_k => $_v) {

            $member_id = array_merge($member_id, array($_v['member_id']));

            $lock_day = round((time() - strtotime($_v['ctime']) - 1) / 86400) + 1;

            $list[$_v['member_id']] = $list[$_v['member_id']] + ($_v['quity_count'] * $share_amount * $lock_day);
        }
        $MemberModel = D('Member');
        $member_map['id'] = array('in', $member_id);
        $member_list = $MemberModel->_list($member_map, 'id,name,admin_id,username,password,second_password,gold,quity,quity_gold,phone,id_number,is_enable,ctime,mtime');
        $dividend_data = array();
        foreach ($member_list as $_k => $_v) {
            $member_list[$_k]['quity_gold'] = $_v['quity_gold'] + $list[$_v['id']];
            $dividend_data['member_id'] = $_v['id'];
            $dividend_data['gold'] = $list[$_v['id']];
            $dividend_data['create_time'] = now();
            $dividend_data['modify_time'] = now();
        }

        $MemberModel->startTrans();

        $result = $MemberModel->addAll($member_list, array(), true);
        $dividend_result = D('QuityGoldDividend')->addAll($dividend_data);
        if ($result) {
            $MemberModel->commit();
            $this->success('已完成分红');
        } else {
            $MemberModel->rollback();
            $this->error('更新分红数据失败');
        }
    }
}
