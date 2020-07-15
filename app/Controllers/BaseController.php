<?php
namespace App\Controllers;

use App\Controllers\Helpers\StatusEnum;
use Phalcon\Mvc\Controller;

class BaseController extends Controller
{
    protected function getJsonBody($status,$message=null,$data = null)
    {
        $response = ['status'=>$status];
        if ($message != null){
            $response['message'] = $message;
        }
        if ($data != null){
            $response['data'] = $data;
        }
        return $response;
    }

    protected function notFound($message = null,$data = null)
    {
        return $this->response
            ->setStatusCode(404)
            ->setJsonContent($this->getJsonBody(StatusEnum::NOT_FOUND,$message,$data));
    }

    protected function success($data = null,$message = null)
    {
        return $this->response
            ->setJsonContent($this->getJsonBody(StatusEnum::OK,$message,$data));
    }

    protected function error($message = '', $data = [])
    {
        return $this->response
            ->setJsonContent($this->getJsonBody(StatusEnum::ERROR, $message));
    }

    protected function response($status, $data, $msg)
    {
        return $this->response
            ->setJsonContent($this->getJsonBody($status, $msg, $data));
    }
}