<?php
use DrakkarD\Controller\DrakkarDController;


class PouetController extends DrakkarDController {

    public function biscuitAction($numero){
        // create some people
        $jax = new Person(array('name' => 'Jax', 'state' => 'CA'));
        $jax->save();

        // compact way to create and save a model
        $tito = Person::create(array('name' => 'Tito', 'state' => 'VA'));

        echo $this->twig->render('test.html.twig', array('numero' => $numero));
    }

} 