<?php
namespace Common\Model;

use Common\Model\BaseModel;

class BankModel extends BaseModel
{
    protected $tableName = 'bank';
    protected $selectFields = 'id,member_id,deposit_bank,deposit_name,deposit_code_number,deposit_address,ctime';

    protected $_validate = array(
        array('deposit_bank', 'require', '请选择开户行', 1),
        array('deposit_name', 'require', '请输入开户人', 1),
        array('deposit_code_number', 'require', '请输入开户卡号', 1),
        array('deposit_address', 'require', '请输入开户地址', 1),
        array('member_id', 'require', '请选择会员后操作', 1),
    );
    protected $_auto = array(
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );
}
