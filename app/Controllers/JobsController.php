<?php
namespace App\Controllers;

use App\Models\Job;
use Respect\Validation\Validator as validator;

class JobsController extends BaseController{
    public function getAddJob($request){
        $responseMessage = null;
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();
            $jobValidator = validator::key('title', validator::stringType()->length(1, 60)->notEmpty())
                  ->key('description', validator::stringType()->length(1, 120)->notEmpty());

            try {
                $jobValidator->assert($postData);
                $postData = $request->getParsedBody();

                $files = $request->getUploadedFiles(); //   Obtiene archivos en post
                $thumbnail = $files['thumbnail'];

                if ($thumbnail->getError() == UPLOAD_ERR_OK) {
                    $fileName = $thumbnail->getClientFileName();
                    $routeThumbnail = "uploads/{$fileName}";
                    $thumbnail->moveTo($routeThumbnail);
                }


                $job = new Job();
                $job->title = $_POST['title'];
                $job->description = $_POST['description'];
                $job->thumbnail = $routeThumbnail;
                $job->visible = 0;
                $job->months = 0;
                $job->save();
                $responseMessage = 'Save';
            } catch (\Exception $e){
                $responseMessage = $e->getMessage();
            }
        }

        return $this->renderHTML('addJob.twig', [
            'responseMessage' => $responseMessage
        ]);
    }
}