<?php
namespace App\Controllers;

use App\Models\Job;
use App\Models\Project;

class IndexController extends BaseController{
    public function getIndex(){
        $jobs = Job::all();
        $projects = Project::all();
        $name = 'Yunuen Moncada';
        return $this->renderHTML('index.twig', [
            'name' => $name,
            'jobs' => $jobs,
            'projects' => $projects
        ]);
    }
}