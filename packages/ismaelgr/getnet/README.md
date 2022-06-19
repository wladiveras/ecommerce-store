# PACKAGE API GETNET

# documentação API 
https://developers.getnet.com.br/api

#sandBox

https://developers.getnet.com.br/login



## Faça upload de arquivos na sua app laravel

Esta biblioteca é uma helper auxiliar para upload de arquivos, classificação do mesmo em categorias e relacionamento com outros models.

### Instalação
##### Pacotes Requeridos

  - [guzzlehttp/guzzle](http://docs.guzzlephp.org/en/stable/overview.html#installation)

Instale as dependêcias e inicie o serve

$ composer require marcusvbda/uploader

adicione a config/app , em provider a linha abaixo

 ismaelgr\getnet\GetnetClassProvider::class

execute no dash

$ php artisan vendor:publish


### Configurações

#### Dentro do config do seu app, crie um nosso config chamado getnet.php , igual o exemplo a seguir: 


<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Seller ID
    |--------------------------------------------------------------------------
    |
    | Essa é a identificação do e-commerce.
    |
    */
    'seller_id' => '',

    /*
    |--------------------------------------------------------------------------
    | Client ID
    |--------------------------------------------------------------------------
    |
    | Esse é o ID para identificação da API.
    |
    */
    'client_id' => '',

    /*
    |--------------------------------------------------------------------------
    | Client Secret
    |--------------------------------------------------------------------------
    */

    'client_secret' => '',
 
];


##### Vincular arquivo a um model, segue exemplo:


use ismaelgr\getnet\GetnetClass; 

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {

            $transaction =  GetnetClass::makeTransaction($data);
            return response()->json(['success' => true, 'message' => null, 'data' => $order]);
      
        } catch (\Exception $e) {
            @DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }
}


### Classes, Métodos e Funcionamento 

#### Environment 

Essa classe é responsável por criar o objeto que identifica o ambiente no qual o API será usado(Sandbox ou Produção).


#### AuthGetNet

Essa Classe é responsável por criar o objeto que faz as autenticações no API.   

O método makeAuth() é responsável por chamar os demais métodos da classe, fazer a autenticação e retornar um objeto com todas as informações para construir o acessToken.  

### Payment 


Essa Classe é responsável por criar o objeto que recebe as requisiçoes de pagamento e também o tipo de transação.  
O método makePayment recebe os dados via request e partir da action cria o objeto necessário, para iniciar a transação a partir das Classes  #CreditCard, #DebitCard ou #BankSlip




campos obrigatórios no request

 amount - valor da compra
 orderId - identificação da ordem de cliente
 customerId - identificação do cliente
 

(informações somente para cartão de crédito)

 numberInstallments - número de parcelas
 transaction_type - Tipo de transação
 cardNumber - número de cartão  
 cardholderName  - nome do proprietário do cartão         
 expirationMonth - mês de expiração do cartão           
 expirationYear - ano de expiração do cartão
 verifyCard - Realiza uma transação que Verifica se o cartão informado, não está cancelado, bloqueado ou com restrições.
 securityCode  - código de segurança     
            
            
(informações somente para boleto)

 name - nome do cliente
 street - endereço 
 number - número 
 complement - complemento 
 district - distríto 
 city - cidade
 country - país
 state - sigla do Estado
 documentType - tipo de documento (CPF ou CNPJ),
 documentNumber - número do documento
 postalCode - cep
              

action - recebe o tipo de transação que será efeutada. 

    0 - Cartão de Crédito
    1 - Cartão de Débito 
    2 - Boleto Bancário



