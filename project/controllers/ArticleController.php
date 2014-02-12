<?php
use DrakkarD\Controller\DrakkarDController;

class ArticleController extends DrakkarDCOntroller{
    public function getArticleAction($id) {
        $article = Article::find($id);
        echo $this->twig->render("article.html.twig",array(
            'article' => $article
        ));
    }

    public function deleteArticleAction($id) {
        $article = new Article($id);
        $article->delete();
        $article = Article::find('all');
    }
} 