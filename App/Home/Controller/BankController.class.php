<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class BankController extends BaseController
{
    public function _filter()
    {
        $map['member_id'] = session('uid');

        return $map;
    }

    public function insert()
    {
        $model = D(CONTROLLER_NAME);
        $data = $_POST;
        $data['member_id'] = session('uid');

        if (!$model->create($data)) {
            $this->error($model->getError());
        }

        $insert_result = $model->add();
        echo $model->_sql();
        if ($insert_result) {
            $this->success('新增成功', U(CONTROLLER_NAME . '/index'));
        } else {
            $this->error('新增失败');
        }
    }
}
