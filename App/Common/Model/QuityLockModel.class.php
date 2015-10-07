<?php
namespace Common\Model;

use Common\Model\BaseModel;

class QuityLockModel extends BaseModel
{
    protected $tableName = 'quity_lock';
    protected $selectFields = 'id,member_id,quity_count,then_gold,unlock_time,status,ctime';

    protected $_validate = array(
        array('quity_count', 'require', '请输入要锁定的股权数量', 1),
        array('unlock_time', 'require', '请选择要锁定时间', 1),
    );
    protected $_auto = array(
        array('status', 0, 1, 'string'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );
}
