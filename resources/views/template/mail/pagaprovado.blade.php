<!DOCTYPE html>
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,700,700i" rel="stylesheet">
    <!-- CSS Reset : INICIO -->
    <style>

        /* O que faz: Remove espaços gerados pelo client de email. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        /* O que faz: Ajuste para client de email não reduzirem os textos. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        /* O que faz: Centraliza o email no Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        /* O que faz: Para Outlook de gerar espaços no email */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* O que faz: Conserta alinhamento do webkit do yahoo */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }

        /* O que faz: Previne Windows 10 Mail sublinhar links */
        a {
            text-decoration: none;
            color: #1B4E01;
            font-weight: 700;
        }

        /* O que faz: RFIMeriza melhor imagens no IE */
        img {
            -ms-interpolation-mode:bicubic;
        }

        /* O que faz: Previne intromissão dos links nos Clients de email */
        *[x-apple-data-detectors],  /* iOS */
        .unstyle-auto-detected-links *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* O que faz:  Previne Gmail de alterar cor dos links nas conversas. */
        .im {
            color: inherit !important;
        }

        /*  O que faz:  Previne Gmail de exibir botao de download para imagens de links*/
        .a6S {
           display: none !important;
           opacity: 0.01 !important;
		}
		/*  O que faz: Se o de cima nao funcionar, adicionar a classe .g-img as imagens. */
		img.g-img + div {
		   display: none !important;
		}

        /* O que faz: Remove o espaço da direita no gmail do IOS */

        /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u ~ div .email-container {
                min-width: 320px !important;
            }
        }
        /* iPhone 6, 6S, 7, 8, and X */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u ~ div .email-container {
                min-width: 375px !important;
            }
        }
        /* iPhone 6+, 7+, and 8+ */
        @media only screen and (min-device-width: 414px) {
            u ~ div .email-container {
                min-width: 414px !important;
            }
        }

    </style>
    <!-- CSS Reset : FIM -->
	<!--[if mso]>
	<style type="text/css">
		ul,
		ol {
			margin: 0 !important;
		}
		li {
			margin-left: 30px !important;
		}
		li.list-item-first {
			margin-top: 0 !important;
		}
		li.list-item-last {
			margin-bottom: 10px !important;
		}
	</style>
	<![FIMif]-->

    <!-- Progressive Enhancements : INICIO -->
    <style>

        /*  O que faz: Hover dos buttons */
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
	    .button-td-primary:hover,
	    .button-a-primary:hover {
	        background: #555555 !important;
	        border-color: #555555 !important;
	    }
        .circleStatus::after {
            width: 60px;
            height: 1px;
            background-color: #d8f03f;
            position: absolute;
            content: "";
            margin-left: 16px;
            margin-top: 8px;
        }
        .circleStatusLast::after {
            background-color: #ffffff;
        }
        .circleStatus {
           background-color: #D6EF63; 
           width: 16px; 
           height: 16px; 
           border-radius: 16px; 
           margin: 0 auto; 
        }
        .circleStatusLastgreen {
           background-color: #D6EF63; 
           width: 16px; 
           height: 16px; 
           border-radius: 16px; 
           margin: 0 auto; 
        }
        .circleStatusLastgreen::after {
            width: 60px;
            height: 1px;
            background-color: #F2F2F2;
            position: absolute;
            content: "";
            margin-left: 16px;
            margin-top: 8px
        }
        .circleStatusLast {
           background-color: #F2F2F2; 
           width: 16px; 
           height: 16px; 
           border-radius: 16px; 
           margin: 0 auto; 
        }
        .circleStatusV {
           background-color: #F2F2F2; 
           width: 16px; 
           height: 16px; 
           border-radius: 16px; 
           margin: 0 auto; 
        }
        .circleStatusV::after {
            width: 60px;
            height: 1px;
            background-color: #F2F2F2;
            position: absolute;
            content: "";
            margin-left: 16px;
            margin-top: 8px
        }

        /* Media Queries */
        @media screen and (max-width: 600px) {

            .email-container {
                width: 100% !important;
                margin: auto !important;
            }

            /*  O que faz:  Força os elementos a redimencionarem para full width do container, útil para redimencionar imagens além da largura maxima delas*/
            .fluid {
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            /*  O que faz: Força as celulas da tabela a full-width da linha */
            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            /*  O que faz: justifica no centro as celulas */
            .stack-column-center {
                text-align: center !important;
            }

            /*  O que faz:  Classe generica para centralizar, útil para imagens, buttons e tabelas */
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }

            /*  O que faz: Ajusta tipografia em telas pequenas */
            .email-container p {
                font-size: 14px !important;
            }
        }

    </style>
    <!-- Progressive Enhancements : FIM -->
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![FIMif]-->

</head>
<!--

A cor de fundo do email (# 222222) é definida em três locais:
1. tag body: para a maioria dos clientes de email
2. marca central: para aplicativos móveis do Gmail e do Inbox e versões da Web do Gmail, GSuite, Caixa de entrada, Yahoo, AOL, Líbero, Comcast, freenet, Mail.ru, Orange.fr
3. mso condicional: para o Windows 10 Mail
-->
<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #ffffff; font-family: 'Roboto', sans-serif;">
	<center style="width: 100%; background-color: #ffffff;">
    <!--[if mso | IE]>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff;">
    <tr>
    <td>
    <![FIMif]-->

        <!--  Hidden Preheader Text : INICIO -->
        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
            (Optional) This text will appear in the inbox preview, but not the email body. It can be used to supplement the email subject line or even summarize the email's contents. ExtFIMed text preheaders (~490 characters) seems like a better UX for anyone using a screenreader or voice-command apps like Siri to dictate the contents of an email. If this text is not included, email clients will automatically populate it using the text (including image alt text) at the start of the email's body.
        </div>
        <!--  Hidden Preheader Text : FIM -->

        <!-- Espaço do texto Hack : INICIO -->
        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
	        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
        </div>
        <!-- Espaço do texto Hack : FIM -->

        <!-- Email Body : INICIO -->
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto;" class="email-container">
	        <!-- Email Header : INICIO -->
            <tr>
                <td style="padding: 18px 0; text-align: center; background: #D6EF63;">
                    <img src="{{asset('assets/mail/LOGO-padrao-color.png')}}" width="150" height="39" alt="Grupo Padrão Color" border="0" style="height: auto; font-family: 'Roboto', sans-serif; font-size: 15px; line-height: 15px; color: #000000;">
                </td>
            </tr>
	        <!-- Email Header : FIM -->

            <!-- Frist Block : INICIO -->
            <tr>
                <td style="background-color: #ffffff; padding: 0 45px;">
                    <h1 style="font-weight: 100; text-align: center; margin-top: 41px; margin-bottom: 13px; font-size: 32px; color: #313131;">Olá <span style="font-weight: 700; color: #1B4E01; font-style: italic;">{{$name}}</span></h1>
                </td>
            </tr>
            <tr>
                <td style="background-color: #ffffff; padding: 0 45px;  margin-top: 10px; margin-bottom: 64px;">
                    <p style="font-weight: 400; text-align: center; margin-top: 10px; margin-bottom: 5px;  font-size: 14px; color: #313131;">Em primeiro lugar, gostariamos de lhe dar as boas-vindas à <span style="color: #1B4E01; font-weight: 700;">Padão Color!</span></p>
                    <p style="font-weight: 400; text-align: center; margin-top: 5px; margin-bottom: 10px; font-size: 14px; color: #313131;">Estamos analisando seu cadastro assim que finalizarmos enviamos um e-mail.</p>
                </td>
            </tr>
            <tr>
                <td style="background-color: #ffffff; padding: 0 45px;  margin-top: 10px; margin-bottom: 64px;">
                    <p style="font-weight: 400; text-align: center; font-size: 26px; color: #1B4E01;"><img {{asset('assets/mail/checked.svg')}} width="32" height="25" alt="Pagamento Aprovado" border="0" > Pagamento Aprovado</p>
                </td>
            </tr>
            <!-- Frist Block : FIM -->

            <!-- 1 Column Text : INICIO -->
            <tr>
                <td style="background-color: #ffffff;padding: 10px 46px;">
                    <div style="border: 1px solid #CCCCCC;border-radius: 5px;padding: 24px 27px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="font-weight: 400; text-align: left; font-size: 9px; color: #313131; line-height: 13px;">
                                <div class="circleStatus"></div>
                                <p style="text-align: center;">PEDIDO REALIZADO</p>
                            </td>
                            <td style="font-weight: 400; text-align: left; font-size: 9px; color: #313131; line-height: 13px;">
                                <div class="circleStatusLastgreen"></div>
                                <p style="text-align: center;">PAGAMENTO CONFIRMADO</p>
                            </td>
                            <td style="font-weight: 400; text-align: left; font-size: 9px; color: #313131; line-height: 13px;">
                                <div class="circleStatusV"></div>
                                <p style="text-align: center;">ARQUIVO(S) PROCESSADO(S)</p>
                            </td>
                            <td style="font-weight: 400; text-align: left; font-size: 9px; color: #313131; line-height: 13px;">
                                <div class="circleStatusV"></div>
                                <p style="text-align: center;">ITEM(S) EM PRODUÇÃO</p>
                            </td>
                            <td style="font-weight: 400; text-align: left; font-size: 9px; color: #313131; line-height: 13px;">
                                <div class="circleStatusV"></div>
                                <p style="text-align: center;">ITEM(S) EM TRANSPORTE</p>
                            </td>
                            <td style="font-weight: 400; text-align: left; font-size: 9px; color: #313131; line-height: 13px;">
                                <div class="circleStatusLast"></div>
                                <p style="text-align: center;">PEDIDO ENTREGUE</p>
                            </td>
                        </tr>
                    </table>
                    </div>
                </td>
            </tr>
	        <!-- 1 Column Text : FIM-->
            <!-- 2 Column Text : INICIO -->
            <tr>
                <td style="background-color: #ffffff;padding: 10px 46px;">
                    <div style="border: 1px solid #CCCCCC;border-radius: 5px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">

                        <tr>
                            <td style="background-color: #F2F2F2;padding: 12px 14px 9px 14px;border-top-right-radius: 5px;border-top-left-radius: 5px; border-bottom: 1px solid #CCCCCC;" colspan="5">
                                <img {{asset('assets/mail/pagamentos.svg')}} width="24" height="24" alt="pagamentos" border="0" style="vertical-align: middle;top: -1px;position: relative;"><span style="font-weight: 700; text-align: left; font-size: 16px; color: #313131;"> Pagamentos</span>
                            </td>
                        </tr>
                        <tr style="text-align: left;">
                            <td style="padding: 19px 0 21px 14px" colspan="2">
                                <p style="font-size:12px; font-weight: 700;margin: 0;">Nº Cartão</p>
                                <p style="font-size:12px; font-weight: 400;margin: 0;">xxxx.xxxx.xxxx.2342</p>
                            </td>
                            <td style="padding: 19px 0 21px 0px">
                                <p style="font-size:12px; font-weight: 700;margin: 0;">Data</p>
                                <p style="font-size:12px; font-weight: 400;margin: 0;">20/12/2018</p>
                            </td>
                            <td style="padding: 19px 0 21px 0px">
                                <p style="font-size:12px; font-weight: 700;margin: 0;">Método</p>
                                <p style="font-size:12px; font-weight: 400;margin: 0;">Boleto</p>
                            </td>
                            <td style="padding: 21px 0 21px 19px">
                                <p style="font-size:12px; font-weight: 700;margin: 0;">Valor</p>
                                <p style="font-size:12px; font-weight: 400;margin: 0;">R$ 90,00</p>
                            </td>
                        </tr>
                    </table>
                    </div>
                </td>
            </tr>
            <!-- 2 Column Text : FIM-->
            <!-- 3 Column Text : INICIO -->
            <tr>
                <td style="background-color: #ffffff;padding: 10px 46px;">
                    <div style="border: 1px solid #CCCCCC;border-radius: 5px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">

                        <tr>
                            <td style="background-color: #F2F2F2;padding: 12px 14px 9px 14px;border-top-right-radius: 5px;border-top-left-radius: 5px; border-bottom: 1px solid #CCCCCC;" colspan="5">
                                <img {{asset('assets/mail/cart-pedidos.svg')}} width="24" height="24" alt="pedidos" border="0" style="vertical-align: middle;top: -1px;position: relative;"><span style="font-weight: 700; text-align: left; font-size: 16px; color: #313131;"> Meus Pedidos</span>
                            </td>
                        </tr>

                        <!-- primeiro Produto INICIO -->
                        <tr>
                            <td colspan="5">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-bottom: 1px solid #cecece;">
                                    <tr style="text-align: left;">
                                        <td style="padding: 19px 0 21px 14px" colspan="4">
                                            <p style="font-size:14px; font-weight: 700;margin: 0;">Cartão de visita 250 un. - Fosco - 9x4 - Lorem ipsum dolor sit amet. Lorem ipsum dolor sit.</p>
                                        </td>
                                        <td style="padding: 19px 14px 0 0px;text-align: right;">
                                            <img {{asset('assets/mail/alerta-info.svg')}} width="24" height="24" alt="informacoes" border="0">
                                        </td>
                                    </tr>
                                    <tr style="text-align: left;">
                                        <td style="padding: 3px 0 0 14px" colspan="2">
                                            <p style="font-size:12px; font-weight: 700;margin: 0;">Data da compra</p>
                                            <p style="font-size:12px; font-weight: 400;margin: 0;">20/12/2018</p>
                                        </td>
                                        <td style="padding: 3px 0 0 0px">
                                            <p style="font-size:12px; font-weight: 700;margin: 0;">Valor total</p>
                                            <p style="font-size:12px; font-weight: 400;margin: 0;">R$ 45,00</p>
                                        </td>
                                        <td style="padding: 3px 0 0 19px">
                                            <p style="font-size:12px; font-weight: 700;margin: 0;">Previsão de entrega</p>
                                            <p style="font-size:12px; font-weight: 400;margin: 0;">2 dias úteis</p>
                                        </td>
                                    </tr>
                                    <tr style="text-align: left;">
                                        <td style="padding: 24px 0 21px 14px" colspan="5">
                                            <p style="font-size:12px; font-weight: 700;margin: 0;">Status</p>
                                            <p style="font-size:12px; font-weight: 400;margin: 0;">Aguardando</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- Primeiro Produto FIM -->
                        <!-- Segundo Produto INICIO -->
                        <tr>
                            <td colspan="5">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-bottom: 1px solid #cecece;">
                                    <tr style="text-align: left;">
                                        <td style="padding: 19px 0 21px 14px" colspan="4">
                                            <p style="font-size:14px; font-weight: 700;margin: 0;">Cartão de visita 250 un. - Fosco - 9x4 - Lorem ipsum dolor sit amet. Lorem ipsum dolor sit.</p>
                                        </td>
                                        <td style="padding: 19px 14px 0 0px;text-align: right;">
                                            <img {{asset('assets/mail/alerta-info.svg')}} width="24" height="24" alt="informacoes" border="0">
                                        </td>
                                    </tr>
                                    <tr style="text-align: left;">
                                        <td style="padding: 13px 0 0 14px" colspan="2">
                                            <p style="font-size:12px; font-weight: 700;margin: 0;">Data da compra</p>
                                            <p style="font-size:12px; font-weight: 400;margin: 0;">20/12/2018</p>
                                        </td>
                                        <td style="padding: 13px 0 0 0px">
                                            <p style="font-size:12px; font-weight: 700;margin: 0;">Valor total</p>
                                            <p style="font-size:12px; font-weight: 400;margin: 0;">R$ 45,00</p>
                                        </td>
                                        <td style="padding: 3px 0 0 19px">
                                            <p style="font-size:12px; font-weight: 700;margin: 0;">Previsão de entrega</p>
                                            <p style="font-size:12px; font-weight: 400;margin: 0;">2 dias úteis</p>
                                        </td>
                                    </tr>
                                    <tr style="text-align: left;">
                                        <td style="padding: 24px 0 21px 14px" colspan="5">
                                            <p style="font-size:12px; font-weight: 700;margin: 0;">Status</p>
                                            <p style="font-size:12px; font-weight: 400;margin: 0;">Aguardando</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- Segundo Produto FIM -->
                        
                    </table>
                    </div>
                </td>
            </tr>
            <!-- 3 Column Text : FIM-->
            <!-- 4 Column Text : INICIO -->
            <tr>
                <td style="background-color: #ffffff;padding: 10px 46px;">
                    <div style="border: 1px solid #CCCCCC;border-radius: 5px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">

                        <tr>
                            <td style="background-color: #F2F2F2;padding: 12px 14px 9px 14px;border-top-right-radius: 5px;border-top-left-radius: 5px; border-bottom: 1px solid #CCCCCC;" colspan="3">
                                <img {{asset('assets/mail/truck-envio.svg')}} width="24" height="24" alt="pagamentos" border="0" style="vertical-align: middle;top: -1px;position: relative;"><span style="font-weight: 700; text-align: left; font-size: 16px; color: #313131;"> Pagamentos</span>
                            </td>
                        </tr>
                        <tr style="text-align: left;">
                            <td style="padding: 19px 0 21px 14px">
                                <p style="font-size:12px; font-weight: 700;margin: 0;">Método</p>
                                <p style="font-size:12px; font-weight: 400;margin: 0;">Retirada</p>
                            </td>
                            <td style="padding: 19px 0 21px 0px" >
                                <p style="font-size:12px; font-weight: 700;margin: 0;">Endereço</p>
                                <p style="font-size:12px; font-weight: 400;margin: 0;">Rua João Cesarino, Nº 211 Centro – RJ</p>
                            </td>
                            <td style="padding: 19px 14px 21px 0px">
                                <p style="font-size:12px; font-weight: 700;margin: 0;">Telefone</p>
                                <p style="font-size:12px; font-weight: 400;margin: 0;">(21) 3668-1563</p>
                                <p style="font-size:12px; font-weight: 400;margin: 0;">(21) 97174-3168</p>
                            </td>
                        </tr>
                        <tr style="text-align: left;">
                            <td style="padding: 3px 0 21px 14px">
                                <p style="font-size:12px; font-weight: 700;margin: 0;">E-mail</p>
                                <p style="font-size:12px; font-weight: 400;margin: 0;">lojaalcantara@padraocolor.com.br</p>
                            </td>
                        </tr>
                    </table>
                    </div>
                </td>
            </tr>
            <!-- 4 Column Text : FIM-->
            <!-- 5 Column Text : INICIO-->
            <tr>
                <td style="background-color: #ffffff; padding: 32px 0 0px 0;">
                    <p style="text-align: center;"><a href="#" style="color: #ffffff;background-color: #1B4E01;border-radius: 5px;padding: 17px 15%;font-size: 14px;letter-spacing: 1px;">IR PARA MINHA CONTA</a></p>
                </td>
            </tr>
            <tr>
                <td style="background-color: #ffffff; padding: 12px 0 32px 0;">
                    <p style="text-align: center; font-size: 14px; margin: 0; font-weight: 400;">Dúvidas? Fale com a nossa <a href="#" style="color: #1B4E01;">Central de Atendimento</a></p>
                </td>
            </tr>
            <!-- 5 Column Text : FIM-->
            <!-- 6 Column Text : INICIO -->
            <tr>
                <td style="background-color: #F2F2F2; padding: 24px 34px 0 34px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="font-weight: 400; text-align: left; font-size: 10px; color: #313131;line-height: 20px;">
                                <p style="">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec scelerisque egestas enim vel finibus. Phasellus faucibus ante id odio venenatis scelerisque. Maecenas dictum rutrum sem, eget pretium lorem tincidunt ac. Quisque ullamcorper, ipsum auctor sollicitudin aliquam, urna est placerat augue, sit amet pharetra dui turpis eu libero. Curabitur vitae ligula vitae velit feugiat finibus. Sed nec pretium urna, eget luctus ex. Mauris ut ipsum sed tortor porta egestas ut ut sem. Nullam sed sem et est consectetur gravida vitae et quam. Ut gravida magna sed tortor vehicula auctor. Praesent at mi urna. </p>
                                <p>Duis quis suscipit nisl, et convallis eros. Nam ut mollis sapien, sit amet vulputate lectus. Cras quis ante facilisis, gravida tortor ac, venenatis lacus. Sed convallis, orci fringilla vestibulum egestas, sapien purus congue elit, ut dictum justo mi ut felis. Suspendisse vehicula leo eu justo tempor, eleifend vehicula metus scelerisque. Aliquam commodo urna a ipsum tincidunt tristique. Pellentesque lacus diam, ultricies sit amet tempor sit amet, viverra at urna. Pellentesque rhoncus, tortor ac posuere tristique, dui Leo bibendum dui, quis pellentesque nulla felis vitae lacus. Nunc lacus augue, auctor et nunc id, pellentesque elementum est. In hac habitasse platea dictumst. Nulla facilisi. Ut vel lacinia turpis, gravida accumsan urna. Maecenas vitae diam scelerisque, viverra ante nec, pharetra diam. Duis sem libero, rhoncus sed tincidunt eu, venenatis in elit. Etiam auctor dolor at nunc hendrerit accumsan.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: 400; text-align: left; font-size: 10px; color: #313131; line-height: 20px;">
                                <p style="color: #313131;"><a href="#" style="text-decoration: underline; color: #313131;">FAQ</a> <span style="font-size: 14px;">|</span> <a href="#" style="text-decoration: underline; color: #313131;">Politica de privacidade</a></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- 6 Column Text : FIM-->
	       
	    </table>
	    <!-- Email Body : FIM -->
    <!--[if mso | IE]>
    </td>
    </tr>
    </table>
    <![FIMif]-->
    </center>
</body>
</html>