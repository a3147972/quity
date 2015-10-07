<?php
namespace Common\Model;

use Common\Model\BaseModel;

class GoldWithdrawModel extends BaseModel
{
    protected $tableName = 'gold_withdraw';
    protected $selectFields = 'id,member_id,gold,bank_id,remark,status,ctime';

    protected $_validate = array(
        array('gold', 'require', '请输入要提现金额', 1),
        array('bank_id', 'require', '请选择要提现到的银行卡', 1),
    );
    protected $_auto = array(
        array('status', 0, 1, 'string'),
        array('admin_id', 'auto_admin', 2, 'callback'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );

    protected function auto_admin()
    {
        return session('uid');
    }
}
