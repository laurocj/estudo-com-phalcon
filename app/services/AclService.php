<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use App\Models\Resources;
use App\Models\Acl;
use App\Models\Roles;
use App\Models\Actions;

/**
 * Description of AclService
 *
 * @author lauro
 */
class AclService {
    
    protected $_dir;
    
    public function __construct(String $_dir = '../app/controllers/' )
    {
        $this->_dir = $_dir;
    }
    
     /**
     * 
     * @return bool
     */
    public function controllerToResource()
    {
        foreach ($this->getFileInPath() as $controller)
        {
            $controller = $this->removeExtencion($controller);
            $nameResource = $this->nameResource($controller);
            $resource = $this->getResource($nameResource);
            
            if(is_null($resource)){
                return;
            }
            $functions = get_class_methods($controller);            
            if(is_null($this->createActions($resource, $functions))){
                return;
            }
        }
    }
    
    /**
     * @param Closure $filter
     * @return Array
     */
    private function getFileInPath(Closure $filter = null)
    {
        if(is_null($filter)) {
            $filter = function($controller) {
                return strpos($controller,'.php') !== false;
            };
        }
        return array_filter(scandir($this->_dir), $filter);
    }
    
    /**
     * @param String $clazz
     * @return String
     */
    private function removeExtencion(String $clazz) : String
    {
        return strstr($clazz, ".",true);
    }
    
    /**
     * @param String $controller
     * @return String
     */
    private function nameResource(String $controller) : String
    {
        return str_replace("Controller", "", $controller);
    }
    
    /**
     * @param \App\Services\String $nameResource
     * @return Resources
     */
    private function getResource(String $nameResource) : Resources
    {
        $resource = Resources::findFirstByResource($nameResource);
        if(is_null($resource))
        {
            $resource = new Resources();
            $resource->setResource($nameResource);
            if (!$resource->save()) {
                throw \UI\Exception\RuntimeException("Resource $nameResource not save.");
            }
        }
        
        return $resource;
    }
 
    /**
     * @param Resources $resource
     * @param array $functions
     * @return bool
     * @throws type
     */
    private function createActions(Resources $resource, Array $functions) : bool
    {                   
        foreach($functions as $func) {
            if(strpos($func,"Action")){
                $actionName = substr($func,0,-6);
                $action = Actions::findFirstByAction($actionName);
                if(is_null($action)) {
                    $action = new Actions();
                    $action->setResourceId($resource->getId());
                    $action->setAction($actionName);
                    if (!$action->save()) {
                        throw \UI\Exception\RuntimeException("Action $actionName not save.");
                    }
                }
            }
        }
        return true;
    }
}
