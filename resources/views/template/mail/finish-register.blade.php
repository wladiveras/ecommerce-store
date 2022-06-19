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

        .btn_store {
            padding: 10px;
            background-color: #1B4E01;
            color: white!important;
            cursor: pointer;
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
            Seja bem-vindo à Loja da Padrão Color!
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
                <td style="background-color: #ffffff; padding: 0 34px;">
                    <h1 style="font-weight: 100; text-align: center; margin-top: 41px; margin-bottom: 13px; font-size: 32px; color: #313131;">Quase lá!<span style="font-weight: 700; color: #1B4E01; font-style: italic;"></span></h1>
                </td>
            </tr>
            <tr>
                <td style="background-color: #ffffff; padding: 0 34px;  margin-top: 10px; margin-bottom: 64px;">
                    <p style="font-weight: 400; text-align: center; margin-top: 10px; margin-bottom: 5px;  font-size: 18px; color: #313131;">
                        Olá {{explode(" ",$reseller['name'])[0]}}, só falta mais um passo para concluirmos seu cadastro.    
                    </p>
                    <p style="font-weight: 400; text-align: center; margin-top: 5px; margin-bottom: 10px; font-size: 18px; color: #313131;">
                        Clique no link abaixo para confirmar seu e-mail e preencher os dados.
                    </p>
                    <p style="font-weight: 400; text-align: center; margin-top: 50px; margin-bottom: 30px; font-size: 18px; color: #313131;">
                        <a class="btn_store" target="_BLANK" href="{{$url}}">Finalizar Cadastro</a>
                    </p>
                </td>
            </tr>
            <!-- Frist Block : FIM -->

            <!-- 1 Column Text : INICIO -->
            <tr>
                <td style="background-color: #F2F2F2;padding: 15px 34px 0 34px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="font-weight: 400; text-align: left; font-size: 10px; color: #313131; line-height: 20px;">
                                <p style="color: #313131;"><a href="#" style="text-decoration: underline; color: #313131;">FAQ</a> <span style="font-size: 14px;">|</span> <a href="#" style="text-decoration: underline; color: #313131;">Politica de privacidade</a></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
	        <!-- 1 Column Text : FIM-->

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
