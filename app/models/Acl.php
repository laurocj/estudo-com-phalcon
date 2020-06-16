<?php

namespace App\Models;

class Acl extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var integer
     */
    protected $role_id;

    /**
     *
     * @var integer
     */
    protected $action_id;

    /**
     *
     * @var integer
     */
    protected $resource_id;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field role_id
     *
     * @param integer $role_id
     * @return $this
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;

        return $this;
    }

    /**
     * Method to set the value of field action_id
     *
     * @param integer $action_id
     * @return $this
     */
    public function setActionId($action_id)
    {
        $this->action_id = $action_id;

        return $this;
    }

    /**
     * Method to set the value of field resource_id
     *
     * @param integer $resource_id
     * @return $this
     */
    public function setResourceId($resource_id)
    {
        $this->resource_id = $resource_id;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field role_id
     *
     * @return integer
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * Returns the value of field action_id
     *
     * @return integer
     */
    public function getActionId()
    {
        return $this->action_id;
    }

    /**
     * Returns the value of field resource_id
     *
     * @return integer
     */
    public function getResourceId()
    {
        return $this->resource_id;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("phalcon");
        $this->setSource("acl");
        $this->belongsTo('action_id', 'App\Models\Actions', 'id', ['alias' => 'Actions']);
        $this->belongsTo('resource_id', 'App\Models\Resources', 'id', ['alias' => 'Resources']);
        $this->belongsTo('role_id', 'App\Models\Roles', 'id', ['alias' => 'Roles']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Acl[]|Acl|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Acl|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
