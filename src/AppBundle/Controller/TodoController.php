<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends Controller
{
    /**
     * @Route("/todos", name="todo_list")
     */
    public function indexAction()
    {
        // replace this example code with whatever you need
        return $this->render('todo/index.html.twig');
    }

    /**
     * @Route("/todos/create", name="todo_create")
     */
    public function createAction($id, Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('todo/index.html.twig');
    }

    /**
     * @Route("/todos/edit{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('todo/index.html.twig');
    }

        /**
     * @Route("/todos/edit{id}", name="todo_edit")
     */
    public function detailsAction($id)
    {
        // replace this example code with whatever you need
        return $this->render('todo/index.html.twig');
    }



}


