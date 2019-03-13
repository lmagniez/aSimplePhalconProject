<?php
//namespace Application\Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class TagsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for tags
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, '\Tags', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $tags = Tags::find($parameters);
        if (count($tags) == 0) {
            $this->flash->notice("The search did not find any tags");

            $this->dispatcher->forward([
                "controller" => "tags",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $tags,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a tag
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $tag = Tags::findFirstByid($id);
            if (!$tag) {
                $this->flash->error("tag was not found");

                $this->dispatcher->forward([
                    'controller' => "tags",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $tag->id;

            $this->tag->setDefault("id", $tag->id);
            $this->tag->setDefault("libelle", $tag->libelle);
            
        }
    }

    /**
     * Creates a new tag
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "tags",
                'action' => 'index'
            ]);

            return;
        }

        $tag = new Tags();
        $tag->id = $this->request->getPost("id");
        $tag->libelle = $this->request->getPost("libelle");
        

        if (!$tag->save()) {
            foreach ($tag->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "tags",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("tag was created successfully");

        $this->dispatcher->forward([
            'controller' => "tags",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a tag edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "tags",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $tag = Tags::findFirstByid($id);

        if (!$tag) {
            $this->flash->error("tag does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "tags",
                'action' => 'index'
            ]);

            return;
        }

        $tag->id = $this->request->getPost("id");
        $tag->libelle = $this->request->getPost("libelle");
        

        if (!$tag->save()) {

            foreach ($tag->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "tags",
                'action' => 'edit',
                'params' => [$tag->id]
            ]);

            return;
        }

        $this->flash->success("tag was updated successfully");

        $this->dispatcher->forward([
            'controller' => "tags",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a tag
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $tag = Tags::findFirstByid($id);
        if (!$tag) {
            $this->flash->error("tag was not found");

            $this->dispatcher->forward([
                'controller' => "tags",
                'action' => 'index'
            ]);

            return;
        }

        if (!$tag->delete()) {

            foreach ($tag->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "tags",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("tag was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "tags",
            'action' => "index"
        ]);
    }

}
