<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class QuityController extends BaseController
{
    public function index()
    {
        $map['member_id|to_member_id'] = session('uid');
        $map['option'] = array('in', array(3,4));
        $page = I('page', 1);
        $page_size = I('page_size', 10);
        $order = I('order', '');

        $model = D('QuityDeal');

        //查询值
        $pk = $model->getPk();
        $order = empty($order) ? $pk . ' desc' : $order;

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
}
