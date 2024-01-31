<?php
namespace App\Http;

class ApiResponse{
    
    private $data=[];
    private $message=[];
    private $stautsCode=200;




    public function addData($key, $data){
        $this->data[$key]=$data;
    }

    public function getStatusCode(){
        return $this->stautsCode;
    }

    private function addMessage($reason, $message, $type){
        $this->message=[
            'type'=>$type,
            'reason'=>$reason,
            'message'=>$message,
        ];
    }

    public function addSuccessMessage($reason, $message, $stautsCode=200){
        $this->stautsCode=$stautsCode;
        $this->addMessage($reason, $message, 'success');
    }

    public function addErrorMessage($reason, $message, $stautsCode=500){
        $this->stautsCode=$stautsCode;
        $this->addMessage($reason, $message, 'error');

    }

    public function result(){
        return [
            'data'=>$this->data,
            'message'=>$this->message,
        ];
    }

}