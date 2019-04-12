<?php
namespace App\Controllers;

use App\Models\Projects;

class ProjectsController extends BaseController{
    public function getAddProject($request){
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();
            $project = new Job();
            $project->title = $_POST['title'];
            $project->description = $_POST['description'];
            $project->save();
        }
        return $this->renderHTML('addProject.twig');
    }
}