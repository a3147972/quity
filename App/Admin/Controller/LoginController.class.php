<?php
namespace Admin\Controller;

use Admin\Controller\BaseController;

class LoginController extends BaseController
{
    public function login()
    {
        $this->display();
    }
    /**
     * 检测登录
     * @method checkLogin
     * @return [type]     [description]
     */
    public function checkLogin()
    {
        if (!IS_POST) {
            $this->error('非法访问');
        }
        $username = I('post.username');
        $password = I('post.password');

        if (empty($username)) {
            $this->error('请输入用户名');
        }

        if (empty($password)) {
            $this->error('请输入密码');
        }
        $model = D('Admin');
        $result = $model->login($username, $password);

        if ($result) {
            session('uid', $result['id']);
            session('nickname', $result['nickname']);
            $this->success('登录成功', U('Index/index'));
        } else {
            $this->error($model->getError());
        }
    }

    /**
     * 退出
     * @method logout
     */
    public function logout()
    {
        session(null);
        session_regenerate_id();
        redirect(U('Login/logout'));
    }
}