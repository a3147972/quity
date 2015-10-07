<?php
namespace Common\Model;

use Common\Model\BaseModel;

class QuityDealModel extends BaseModel
{
    protected $tableName = 'quity_deal';
    protected $selectFields = 'id,member_id,quity_count,then_balance,option,to_member_id,type,overdue_time';

    protected $_validate = array(
        array('quity_count', 'require', '请输入要交易的股权数量', 1),
        array('option', 'require', '请选择要进行的操作', 1),
        array('to_member_id', 'validate_to_member', '请填写要交易会员编号', 1, 'callback'),
    );
    protected $_auto = array(
        array('status', 0, 1, 'string'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );

    protected function validate_to_member()
    {
        $option = I('post.option');
        $to_member_id = I('post.to_member_id');
        if (in_array($option, array(3, 4)) && empty($to_member_id)) {
            return false;
        }
        return true;
    }
}
