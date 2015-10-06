<?php
namespace Common\Model;

use Common\Model\BaseModel;

class AboutModel extends BaseModel
{
    protected $tableName = 'about';
    protected $selectFields = 'id,title,content,is_enable,ctime';

    protected $_validate = array(
        array('title', 'require', '请输入公告标题', 1),
        array('content', 'require', '请输入公告内容', 1),
    );
    protected $_auto = array(
        array('is_enable', 1, 1, 'string'),
        array('ctime', 'now', 1, 'function'),
        array('mtime', 'now', 3, 'function'),
    );
}
