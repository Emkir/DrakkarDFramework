<?php
use DrakkarD\Controller\DrakkarDController;


class IndexController extends DrakkarDController {

    public function indexAction(){
        $article = Article::find('all');
        echo $this->twig->render("index.html.twig",array(
            'articles' => $article
        ));
    }

} 