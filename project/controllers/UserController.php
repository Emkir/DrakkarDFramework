<?php
use DrakkarD\Controller\DrakkarDController;

class UserController extends DrakkarDController {
    public function loginAction ($id, $password) {
        $user = User::find_by_login($id);
        if ($user->password == sha1($password)) {
            $_SESSION['id'] = $user->id;
            echo "vous êtes connecté<br><br>";
        } $article = Article::find('all');
    }

    public function logoutAction () {
        if (!empty($_SESSION['id']))
            unset($_SESSION['id']);
    }
} 