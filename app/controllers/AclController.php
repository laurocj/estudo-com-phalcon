<?php
declare(strict_types=1);

 

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model;
use App\Models\Acl;
use App\Models\Actions;
use App\Models\Resources;
use App\Models\Roles;

use App\Services\AclService;

class AclController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        //
    }

    /**
     * Searches for acl
     */
    public function searchAction()
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);
        $parameters = Criteria::fromInput($this->di, '\App\Models\Security\Acl', $_GET)->getParams();
        $parameters['order'] = "id";

        $paginator   = new Model(
            [
                'model'      => '\App\Models\Security\Acl',
                'parameters' => $parameters,
                'limit'      => 10,
                'page'       => $numberPage,
            ]
        );

        $paginate = $paginator->paginate();

        if (0 === $paginate->getTotalItems()) {
            $this->flash->notice("The search did not find any acl");

            $this->dispatcher->forward([
                "controller" => "acl",
                "action" => "index"
            ]);

            return;
        }

        $this->view->page = $paginate;
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        //
    }

    /**
     * Edits a acl
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {
            $acl = Acl::findFirstByid($id);
            if (!$acl) {
                $this->flash->error("acl was not found");

                $this->dispatcher->forward([
                    'controller' => "acl",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $acl->getId();

            $this->tag->setDefault("id", $acl->getId());
            $this->tag->setDefault("role_id", $acl->getRoleId());
            $this->tag->setDefault("action_id", $acl->getActionId());
            $this->tag->setDefault("resource_id", $acl->getResourceId());
            
        }
    }

    /**
     * Creates a new acl
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "acl",
                'action' => 'index'
            ]);

            return;
        }

        $acl = new Acl();
        $acl->setroleId($this->request->getPost("role_id", "int"));
        $acl->setactionId($this->request->getPost("action_id", "int"));
        $acl->setresourceId($this->request->getPost("resource_id", "int"));
        

        if (!$acl->save()) {
            foreach ($acl->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "acl",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("acl was created successfully");

        $this->dispatcher->forward([
            'controller' => "acl",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a acl edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "acl",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $acl = Acl::findFirstByid($id);

        if (!$acl) {
            $this->flash->error("acl does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "acl",
                'action' => 'index'
            ]);

            return;
        }

        $acl->setroleId($this->request->getPost("role_id", "int"));
        $acl->setactionId($this->request->getPost("action_id", "int"));
        $acl->setresourceId($this->request->getPost("resource_id", "int"));
        

        if (!$acl->save()) {

            foreach ($acl->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "acl",
                'action' => 'edit',
                'params' => [$acl->getId()]
            ]);

            return;
        }

        $this->flash->success("acl was updated successfully");

        $this->dispatcher->forward([
            'controller' => "acl",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a acl
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $acl = Acl::findFirstByid($id);
        if (!$acl) {
            $this->flash->error("acl was not found");

            $this->dispatcher->forward([
                'controller' => "acl",
                'action' => 'index'
            ]);

            return;
        }

        if (!$acl->delete()) {

            foreach ($acl->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "acl",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("acl was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "acl",
            'action' => "index"
        ]);
    }
    
    public function accesscontrolAction()
    {   
        $service = new AclService($this->config->application->controllersDir);
        $service->controllerToResource();
     
        $this->view->resources = Resources::find();
        $this->view->roles = Roles::find();        
    }
    
    public function saveaccesscontrolAction()
    {
        $acl = $this->request->getPost('acl');
        
        foreach ($acl['resource'] as $resourceId => $actions) {
            //delete all pre-existing access control settings for this resource;
            Acl::findByResourceId($resourceId)->delete();
            foreach ($actions['action'] as $actionId => $role ) {
                foreach ($role['role'] as $roleId => $y){
                    $acl = new Acl();
                    $acl->setActionId($actionId);
                    $acl->setResourceId($resourceId);
                    $acl->setRoleId($roleId);
                    $acl->save();
                }
            }
        }
        
        $this->flash->notice("OK");
        $this->dispatcher->forward([
            "controller" => "index",
            "action" => "index"
        ]);;
    }
}
