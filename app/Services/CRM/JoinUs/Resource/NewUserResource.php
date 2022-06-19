<?php

namespace App\Services\CRM\JoinUs\Resource;

class NewUserResource
{
    public static function toArray($data)
    {
        return (new static)->formatData($data);
    }

    public function formatData($request)
    {
        $this->request = $request;
        $reseller = $request['reseller'];
        $data = [
            "NOM_REPR" => $this->getName(),
            "LOG_REPR" => $reseller['business_name'],
            "DES_REPR" => $this->getName(),
            "email" => $reseller['email'],
            "SEN_REPR" => $reseller["password"],
            "provider" => @$reseller["provider"] ?  @$reseller["provider"] : null,
            "provider_id" => @$reseller["provider_id"] ? @$reseller["provider_id"] : null,
            "CPF_CNPJ" => only_numbers($reseller['doc']),
            "TL1_REPR" => only_numbers($reseller['phone']),
            "TL2_REPR" => only_numbers($reseller['whatsapp'])
        ];

        $this->setAddresses($data);

        return $data;
    }

    public function getName()
    {
        $reseller = $this->request['reseller'];
        $reseller['type'] ? $field = 'business_name' : $field = 'name';


        return explode(" ", $reseller[$field])[0];
    }

    public function setInscriptions(&$data)
    {
        $emptyVal = "";
        $hasInscription = "";


        $company = $this->request['company'];

        $data["InscEstadual"] = $hasInscription ? only_numbers($company['ie']) : $emptyVal;
        $data["InscMunicipal"] = $hasInscription && !$company['no_im'] ? only_numbers($company['im']) : $emptyVal;
    }

    private function setAddresses(&$data)
    {
        $data['PAI_REPR'] = $data['pais'] = "BRASIL";
        $baseAddress = $this->request['address'];

        $billingAddress = $this->setBillingAddress($baseAddress);

        $this->setShippingAddress($billingAddress);

        $data = array_merge($data, $billingAddress);
    }
    private function setBillingAddress($address)
    {
        return [
            "END_REPR" => $address['street'],
            "NRO_REPR" => only_numbers($address['number']),
            "CMP_REPR" => $address['complement'],
            "BAI_REPR" => $address['district'],
            "CID_REPR" => $address['city'],
            "CEP_REPR" => only_numbers($address['zipcode']),
            "EST_REPR" => $address['state']
        ];
    }

    private function setShippingAddress(&$address)
    {
        $keyPrefix = "entrega_";
        $bAddress = $address;
        foreach ($bAddress as $key => &$value) {
            $shippingKey = $keyPrefix . ucfirst($key);
            $address[$shippingKey] = $value;
        }
    }
}
