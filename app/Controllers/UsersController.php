<?php
namespace App\Controllers;

use App\Models\User;
use Respect\Validation\Validator as validator;

class UsersController extends BaseController{
    public function getAddUser(){
        return $this->renderHTML('addUser.twig');
    }

    public function saveUser($request){
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();
            $userValidator = validator::key('username', validator::stringType()->length(1, 50)->notEmpty())
                  ->key('password', validator::notEmpty())
                  ->key('email', validator::notEmpty());

            try {
                $userValidator->assert($postData);
                $postData = $request->getParsedBody();
                $user = new User();
                $user->username = $_POST['username'];
                $user->password = password_hash($_POST['password'],PASSWORD_DEFAULT);
                $user->email = $_POST['email'];
                $user->save();
                $responseMessage = 'Save';
            } catch(\Exception $e){
                $responseMessage = $e->getMessage();
            }
            
        }

        return $this->renderHTML('addUser.twig', [
            'responseMessage' => $responseMessage
        ]);
    }

}