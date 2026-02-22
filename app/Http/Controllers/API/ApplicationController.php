<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Repositories\ApplicationRepository;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationRepository $applicationRepo
    ){}

    public function index(){
        $applications = $this->applicationRepo->getFullApplicationsPaginated();
        return ApplicationResource::collection($applications);

    }

    public function show(Application $application){
        $application->load('attributeValues.attribute', 'attachments', 'user', 'applicationCategory');
        return new ApplicationResource($application);
    }

}
