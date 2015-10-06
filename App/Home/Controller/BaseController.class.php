<?php
namespace Home\Controller;

use Think\Controller;

class BaseController extends Controller
{
    public function _initialize()
    {

    }

    /**
     * 默认首页
     */
    public function index()
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
        $list = $model->_list($map, '', $order, $page, $page_size);
        $count = $model->_count($map);

        //分页处理
        $page_list = $this->page($count, $page, $page_size);
        dump($list);
        $this->assign('page_list', $page_list);
        $this->assign('count', $count);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 分页处理
     * @param  int  $count     总条数
     * @param  integer $page      当前页
     * @param  integer $page_size 每页条数
     * @return array              分页数据
     */
    public function page($count, $page = 1, $page_size = 10)
    {
        $Page = new Page($count, $page, $page_size);

        $page_list = $Page->show();
        $get = I('get.');
        foreach ($page_list as $_k => $_v) {
            $page_list[$_k]['url'] = U(CONTROLLER_NAME . '/index', array_merge($get, array('page' => $_v['page'], 'page_size' => $page_size, 'order' => $order)));
        }

        return $page_list;
    }

    public function add()
    {
        $this->assign('action', 'add');
        $this->display();
    }

    /**
     * 默认新增
     */
    public function insert()
    {
        $model = D(CONTROLLER_NAME);

        if (!$model->create()) {
            $this->error($model->getError());
        }

        $insert_result = $model->add();

        if ($insert_result) {
            $this->success('新增成功', U(CONTROLLER_NAME . '/index'));
        } else {
            $this->error('新增失败');
        }
    }

    public function _empty()
    {
        redirect(U('Index/index'));
        exit();
    }
}
