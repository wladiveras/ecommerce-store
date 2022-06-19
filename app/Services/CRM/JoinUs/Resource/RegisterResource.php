<?php

namespace App\Services\CRM\JoinUs\Resource;

use \Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

class RegisterResource
{
    private $fileUploadPath = "reseller-request/v1";

    public static function toArray($data, $status = 3)
    {
        return (new static)->formatData($data, $status);
    }

    public function formatData($request, $status)
    {
        $this->request = $request;
        $data = [
            "status" => $status,
            "type" => $request['reseller']['type'],
            "name" => $request['reseller']['name'],
            "email" => $request['reseller']['email'],
            "doc" => $request['reseller']['doc'],
            "phone" => $request['reseller']['phone'],
            "whatsapp" => $request['reseller']['whatsapp'],
            "cep" => $request['address']['zipcode'],
            "state" => $request['address']['state'],
            "city" => $request['address']['city'],
            "details" => [
                "password" => @$request["reseller"]['password'],
                "street" => $request['address']['street'],
                "addressNumber" => $request['address']['number'],
                "district" => $request['address']['district'],
            ]
        ];;

        $this->setUserTypeData($data);

        $this->setFiles($data);
        $this->setClients($data);

        return $data;
    }

    private function setUserTypeData(&$data)
    {
        if ($data['type'] == 1) {
            $this->setCompanyData($data, $this->request);
        } elseif ($data['type'] == 0) {
            $this->setPersonData($data, $this->request);
        }
    }

    private function setPersonData(&$data, $request)
    {
        if ($data['type'] == 0)
            $data = array_merge_recursive($data, [
                "details" => [
                    "identity" => $request['person']['identity']
                ],
            ]);
    }

    private function setCompanyData(&$data, $request)
    {
        if ($data['type'] == 1)
            $data = array_merge_recursive($data, [
                "details" => [
                    "im" => $request['company']['im'],
                    "ie" => $request['company']['ie']
                ],
            ]);
    }

    private function setFiles(&$data)
    {
        $files = @$this->request['files'];

        if (!isset($files)) return;

        foreach ($files as $type => $file) {
            $data['details']['files'][$type] = $this->uploadFile($file);
        }
    }

    private function setClients(&$data)
    {
        $clients = @$this->request['clients'];

        if (isset($clients))
            $data['details']['clients'] = $clients;
    }

    /**
     *  @return string of file url
     * */

    private function uploadFile($file)
    {
        if (is_string($file)) return $file;

        $path = $file->store($this->fileUploadPath, ['disk' => 'public']);

        return url("/storage/$path");
    }
}
