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
    public function _filter()
    {
        $map = array();
        $status = I('status');
        if (in_array($status, array(0, 1, -1)) && $status !== '') {
            $status = I('status');
            $map['status'] = $status;
        }

        return $map;
    }
    /**
     * 审核成功
     * @method pass
     */
    public function pass()
    {
        $model = D('GoldWithdraw');
        if (IS_POST) {
            if (!$model->create()) {
                $this->error($model->getError());
            }
            $pic = I('post.pic');
            if (empty($pic)) {
                $this->error('请上传转账截图');
            }
            $model->status = 1;
            $id = I('id');
            $map['id'] = $id;
            $result = $model->where($map)->save();

            if ($result) {
                $this->success('操作成功', U('GoldWithdraw/index'));
            } else {
                $this->error('操作失败');
            }
        } else {
            $id = I('id');
            $map['id'] = $id;
            $info = $model->lists($map);
            $this->assign('vo', $info[0]);

            $this->display();
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
        $model = D('GoldWithdraw');

        $info = $model->_get($map);
        $model->startTrans();

        $result = $model->where($map)->setField('status', -1);
        $add_gold_result = D('Member')->addGold($info['member_id'], $info['gold']);

        if ($result !== false && $add_gold_result) {
            $model->commit();
            $this->success('操作成功', U('GoldWithdraw/index'));
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 导出
     */
    public function export()
    {
        $map = $this->_filter();

        $list = D('GoldWithdraw')->lists($map);
        $title['id'] = '编号';
        $title['username'] = '用户名';
        $title['name'] = '姓名';
        $title['gold'] = '提现金额';
        $title['deposit_bank'] = '银行名称';
        $title['deposit_code_number'] = '银行卡号';
        $title['deposit_name'] = '户名';
        $title['deposit_address'] = '开户行';
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
                ->setCellValue('D' . $num, $v['deposit_bank'])
                ->setCellValue('E' . $num, $v['deposit_code_number'])
                ->setCellValue('F' . $num, $v['deposit_name'])
                ->setCellValue('G' . $num, $v['deposit_address']);
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
