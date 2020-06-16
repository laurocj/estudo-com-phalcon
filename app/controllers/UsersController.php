<?php
declare(strict_types=1);

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model;
use App\Models\Users;

class UsersController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        //
    }

    /**
     * Searches for users
     */
    public function searchAction()
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);
        $parameters = Criteria::fromInput($this->di, '\App\Models\Users', $_GET)->getParams();
        $parameters['order'] = "id";

        $paginator   = new Model(
            [
                'model'      => '\App\Models\Users',
                'parameters' => $parameters,
                'limit'      => 10,
                'page'       => $numberPage,
            ]
        );

        $paginate = $paginator->paginate();

        if (0 === $paginate->getTotalItems()) {
            $this->flash->notice("The search did not find any users");

            $this->dispatcher->forward([
                "controller" => "users",
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
     * Edits a user
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {
            $user = Users::findFirstByid($id);
            if (!$user) {
                $this->flash->error("user was not found");

                $this->dispatcher->forward([
                    'controller' => "users",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $user->getId();

            $this->tag->setDefault("id", $user->getId());
            $this->tag->setDefault("password", $user->getPassword());
            $this->tag->setDefault("name", $user->getName());
            $this->tag->setDefault("email", $user->getEmail());
            $this->tag->setDefault("role", $user->getRole());
            $this->tag->setDefault("validationkey", $user->getValidationkey());
            $this->tag->setDefault("status", $user->getStatus());
            $this->tag->setDefault("createdat", $user->getCreatedat());
            $this->tag->setDefault("updatedat", $user->getUpdatedat());
            
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $user = new Users();
        $user->setpassword($this->request->getPost("password", "string"));
        $user->setname($this->request->getPost("name", "string"));
        $user->setemail($this->request->getPost("email", "email"));
        $user->setpassword($this->security->hash($this->request->getPost("password")));
        $user->setrole("Registered User");
        $user->setstatus("Active");
        $user->setvalidationkey(md5($this->request->getPost("email") . uniqid()));
        $user->setcreatedat((new DateTime())->format("Y-m-d H:i:s"));
        
        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message->getMessage());
            }

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("user was created successfully");

        $this->dispatcher->forward([
            'controller' => "users",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $user = Users::findFirstByid($id);

        if (!$user) {
            $this->flash->error("user does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $user->setpassword($this->request->getPost("password", "int"));
        $user->setname($this->request->getPost("name", "int"));
        $user->setemail($this->request->getPost("email", "int"));
        $user->setrole($this->request->getPost("role", "int"));
        $user->setvalidationkey($this->request->getPost("validationkey", "int"));
        $user->setstatus($this->request->getPost("status", "int"));
        $user->setcreatedat($this->request->getPost("createdat", "int"));
        $user->setupdatedat($this->request->getPost("updatedat", "int"));
        

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'edit',
                'params' => [$user->getId()]
            ]);

            return;
        }

        $this->flash->success("user was updated successfully");

        $this->dispatcher->forward([
            'controller' => "users",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a user
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $user = Users::findFirstByid($id);
        if (!$user) {
            $this->flash->error("user was not found");

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("user was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "users",
            'action' => "index"
        ]);
    }
    
    public function loginAction()
    {

    }
    
    public function logoutAction()
    {
        $this->session->destroy();
        return $this->dispatcher->forward(["controller" => "member","action" => "search"]);
    }
    
    public function authorizeAction()
    {
        $email = $this->request->getPost('email');
        $pass = $this->request->getPost('password');
        $user = Users::findFirstByEmail($email);
        if ($user) {
            if ($this->security->checkHash($pass, $user->getPassword())) {
                $this->session->set('auth',
                            [
                                'userName' => $user->getEmail(), 
                                'role' => $user->getRole()
                            ]
                        );
                $this->session->set('user', $user);
                $this->flash->success("Welcome back " . $user->getName());
                
                return $this->dispatcher->forward(["controller" => "member", "action" => "search"]);
            }
            else {
                $this->flash->error("Your password is incorrect - try again");
                return $this->dispatcher->forward(["controller" => "users", "action" => "login"]);
            }
        }
        else {
            $this->flash->error("That email was not found - try again");
            return $this->dispatcher->forward(["controller" => "users", "action" => "login"]);
        }
        return $this->dispatcher->forward(["controller" => "index", "action" => "index"]);
    }
}
