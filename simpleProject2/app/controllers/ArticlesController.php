<?php
//namespace Application\Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class ArticlesController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for articles
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, '\Articles', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $articles = Articles::find($parameters);
        if (count($articles) == 0) {
            $this->flash->notice("The search did not find any articles");

            $this->dispatcher->forward([
                "controller" => "articles",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $articles,
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
     * Edits a article
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $article = Articles::findFirstByid($id);
            if (!$article) {
                $this->flash->error("article was not found");

                $this->dispatcher->forward([
                    'controller' => "articles",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $article->id;

            $this->tag->setDefault("id", $article->id);
            $this->tag->setDefault("titre", $article->titre);
            $this->tag->setDefault("contenu", $article->contenu);
            $this->tag->setDefault("date_publication", $article->date_publication);
            $this->tag->setDefault("tagId", $article->tagId);
            $this->tag->setDefault("userId", $article->userId);
            
        }
    }

    /**
     * Creates a new article
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "articles",
                'action' => 'index'
            ]);

            return;
        }

        $article = new Articles();
        $article->id = $this->request->getPost("id");
        $article->titre = $this->request->getPost("titre");
        $article->contenu = $this->request->getPost("contenu");
        $article->datePublication = $this->request->getPost("date_publication");
        $article->tagId = $this->request->getPost("tagId");
        $article->userId = $this->request->getPost("userId");
        

        if (!$article->save()) {
            foreach ($article->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "articles",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("article was created successfully");

        $this->dispatcher->forward([
            'controller' => "articles",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a article edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "articles",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $article = Articles::findFirstByid($id);

        if (!$article) {
            $this->flash->error("article does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "articles",
                'action' => 'index'
            ]);

            return;
        }

        $article->id = $this->request->getPost("id");
        $article->titre = $this->request->getPost("titre");
        $article->contenu = $this->request->getPost("contenu");
        $article->datePublication = $this->request->getPost("date_publication");
        $article->tagId = $this->request->getPost("tagId");
        $article->userId = $this->request->getPost("userId");
        

        if (!$article->save()) {

            foreach ($article->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "articles",
                'action' => 'edit',
                'params' => [$article->id]
            ]);

            return;
        }

        $this->flash->success("article was updated successfully");

        $this->dispatcher->forward([
            'controller' => "articles",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a article
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $article = Articles::findFirstByid($id);
        if (!$article) {
            $this->flash->error("article was not found");

            $this->dispatcher->forward([
                'controller' => "articles",
                'action' => 'index'
            ]);

            return;
        }

        if (!$article->delete()) {

            foreach ($article->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "articles",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("article was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "articles",
            'action' => "index"
        ]);
    }

}
