<?php
namespace Common\Model;

use Common\Model\BaseModel;

class GoldTransferModel extends BaseModel
{
    protected $tableName = 'gold_transfer';
    protected $selectFields = 'id,member_id,to_member_id,gold,remark,status,ctime';

    protected $_validate = array(
        array('gold', 'require', '请输入要转账金额', 1),
        array('to_member_id', 'require', '请输入要转给的会员编号', 1),
    );
    protected $_auto = array(
        array('status', 0, 1, 'string'),
        array('member_id', 'auto_member', 1, 'callback'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );

    protected function auto_member()
    {
        return session('uid');
    }
}
