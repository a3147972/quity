<?php
namespace Home\Controller;

use Home\Controller\BaseController;

class MemberController extends BaseController
{
    public function index()
    {
        $map['id'] = session('user_id');

        $info = D('Member')->_get($map);
        $this->assign('vo', $info);
        $this->display();
    }

    /**
     * 修改密码
     */
    public function changePwd()
    {
        if (IS_POST) {
            $old_password = I('post.old_password');
            $password = I('post.password');
            $rep_password = I('post.rep_password');

            if (empty($old_password)) {
                $this->error('请输入原始密码');
            }
            if (empty($password)) {
                $this->error('请输入新密码');
            }
            if (empty($rep_password)) {
                $this->error('请再次输入新密码');
            }

            if ($password != $rep_password) {
                $this->error('两次密码输入不一致');
            }
            $map['id'] = session('user_id');
            $map['password'] = md5($old_password);

            $info = D('Member')->_get($map);

            if (!$info) {
                $this->error('原始密码不正确');
            }

            $result = D('Member')->where(array('id'=>session('user_id')))->setField('password',md5($password));

            if ($result) {
                $this->success('修改密码成功');
            } else {
                $this->error('修改密码失败');
            }
        } else {
            $this->display();
        }
    }

    /**
     * 修改二级密码
     */
    function changeSecondPwd()
    {
        if (IS_POST) {
            $old_password = I('post.old_password');
            $password = I('post.password');
            $rep_password = I('post.rep_password');

            if (empty($old_password)) {
                $this->error('请输入原始密码');
            }
            if (empty($password)) {
                $this->error('请输入新密码');
            }
            if (empty($rep_password)) {
                $this->error('请再次输入新密码');
            }

            if ($password != $rep_password) {
                $this->error('两次密码输入不一致');
            }
            $map['id'] = session('user_id');
            $map['second_password'] = md5($old_password);

            $info = D('Member')->_get($map);

            if (!$info) {
                $this->error('原始密码不正确');
            }

            $result = D('Member')->where(array('id'=>session('user_id')))->setField('second_password',md5($password));

            if ($result) {
                $this->success('修改密码成功');
            } else {
                $this->error('修改密码失败');
            }
        } else {
            $this->display();
        }
    }
}
