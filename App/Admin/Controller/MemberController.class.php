<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class MemberController extends BaseController
{
    public function _filter()
    {
        $map = array();

        $username = I('username');

        if (!empty($username)) {
            $map['username'] = $username;
        }

        return $map;
    }
    /**
     * 启用或禁用会员
     * @method is_enable
     */
    public function is_enable()
    {
        $status = I('status', 1);
        $id = I('id');

        if (empty($id)) {
            $this->error('非法操作');
        }

        if (!in_array($status, array(1, 0))) {
            $this->error('传值错误');
        }

        $map['id'] = $id;

        $result = D('Member')->where($map)->setField('is_enable', $status);

        if ($result) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 充值
     */
    public function recharge()
    {
        if (IS_POST) {
            $gold = I('post.gold');
            $member_id = I('post.id');

            if (empty($gold)) {
                $this->error('请输入要操作金额');
            }
            if (empty($member_id)) {
                $this->error('请选择要操作会员');
            }

            $map['id'] = $member_id;
            $data['gold'] = array('exp', 'gold+' . $gold);

            $model = D('Member');
            $model->startTrans();
            $result = $model->where($map)->save($data);
            $write_log = D('RechargeLog')->write($gold, $member_id);

            if ($result !== false && $write_log !== false) {
                $model->commit();
                $this->success('操作成功', U('Member/index'));
            } else {
                $model->rollback();
                $this->error('操作失败');
            }
        } else {
            $this->display();
        }
    }

    public function quity_recharge()
    {
        if (IS_POST) {
            $quity = I('post.quity_count');
            $member_id = I('post.id');

            if (empty($quity)) {
                $this->error('请输入要充值股权数');
            }
            if (empty($member_id)) {
                $this->error('请选择要操作会员');
            }

            $map['id'] = $member_id;
            $data['quity'] = array('exp', 'quity+' . $quity);

            $model = D('Member');
            $model->startTrans();
            $result = $model->where($map)->save($data);
            $write_log = D('QuityRechargeLog')->write($member_id, $quity);

            if ($result !== false && $write_log !== false) {
                $model->commit();
                $this->success('操作成功', U('Member/index'));
            } else {
                $model->rollback();
                $this->error('操作失败');
            }
        } else {
            $this->display();
        }
    }

    /**
     * 导出
     */
    public function export()
    {
        $map = $this->_filter();

        $list = D('Member')->_list($map);
        $title['id'] = '编号';
        $title['username'] = '用户名';
        $title['name'] = '姓名';
        $title['gold'] = '奖金币';
        $title['quity'] = '股权数';
        $title['quity_gold'] = '股权奖金';
        $title['phone'] = '联系方式';
        $title['id_number'] = '身份证号';
        array_unshift($list, $title);
        import('Common.Tools.PHPExcel.PHPExcel');

        $objPHPExcel = new \PHPExcel();
        $name = time();
        foreach ($list as $k => $v) {
            $num = $k + 1;
            $objPHPExcel->setActiveSheetIndex(0)
            //Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A' . $num, $v['id'])
                ->setCellValue('B' . $num, $v['username'])
                ->setCellValue('B' . $num, $v['name'])
                ->setCellValue('C' . $num, $v['gold'])
                ->setCellValue('D' . $num, $v['quity'])
                ->setCellValue('E' . $num, $v['quity_gold'])
                ->setCellValue('F' . $num, $v['phone'])
                ->setCellValue('G' . $num, $v['id_number']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.csv"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}
