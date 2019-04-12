<?php
namespace App\Controllers;

use Zend\Diactoros\Response\RedirectResponse;

class AdminController extends BaseController{
    public function getIndex(){

        $idusuario = $_SESSION['usuarioID'] ?? null;
        $needsAuth = true;

        if($needsAuth && !$idusuario){
            return $this->renderHTML('admin/index.twig');
        }else{
            return new RedirectResponse('logout');
        }
        
    }
}