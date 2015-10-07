<?php
namespace Common\Model;

use Common\Model\BaseModel;

class GoldToQuityModel extends BaseModel
{
    protected $tableName = 'gold_to_quity';
    protected $selectFields = 'id,member_id,quity_count,then_balance,ctime';

    protected $_validate = array(
        array('quity_count', 'require', '请输入股权金额', 1),
    );
    protected $_auto = array(
        array('member_id', 'auto_member', 1, 'callback'),
        array('status', 0, 1, 'string'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );

    protected function auto_member()
    {
        return session('uid');
    }
}
