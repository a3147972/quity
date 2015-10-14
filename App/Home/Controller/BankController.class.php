<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class BankController extends BaseController
{
    public function _filter()
    {
        $map['member_id'] = session('user_id');

        return $map;
    }
    public function _before_add()
    {
        $map['member_id'] = session('user_id');

        $count = D('Bank')->_count($map);

        if ($count >= 5) {
            $this->error('最多只可添加5张银行卡');
        }
    }
    public function insert()
    {
        $model = D(CONTROLLER_NAME);
        $data = $_POST;
        $data['member_id'] = session('user_id');

        if (!$model->create($data)) {
            $this->error($model->getError());
        }

        $insert_result = $model->add();

        if ($insert_result) {
            $this->success('新增成功', U(CONTROLLER_NAME . '/index'));
        } else {
            $this->error('新增失败');
        }
    }
}
