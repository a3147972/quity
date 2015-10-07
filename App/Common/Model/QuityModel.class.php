<?php
namespace Common\Model;

use Common\Model\BaseModel;

class QuityModel extends BaseModel
{
    protected $tableName = 'quity';
    protected $selectFields = 'id,balance,ctime';

    protected $_validate = array(
        array('balance', 'require', '请输入股权单价', 1),
    );
    protected $_auto = array(
        array('ctime', 'now', 1, 'function'),
    );

    /**
     * 获取生效的股权单价
     * @method getBalance
     * @return int     股权单价
     */
    public function getBalance()
    {
        $info = $this->_get(array(), '', 'id desc');

        return $info['balance'];
    }
}
