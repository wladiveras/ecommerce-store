@extends('template.simple')
@section('content')
<div class="container">
   <h1 class="font-weight-light text-transform-uppercase mb-3">Normas de<span class="font-weight-bold"> Envio de Arte</span> </h1>
   <fieldset>
      <strong>1. INFORMAÇÕES IMPORTANTES</strong>
      <br>    
      
      <br><strong>1.1.</strong> Os cancelamentos só poderão ser realizados através do site ou System Color na Sessão "MEUS PEDIDOS" e só será possível até 15 minutos após o envio.  
   </fieldset>
   <br>
   <fieldset>
      <strong>2. DICAS PARA O ENVIO DE SEU TRABALHO</strong>
      <br>
      <br><strong>2.1.</strong> Verifique se o arquivo contém algum preenchimento em RGB, PANTONE ou qualquer outro que não seja CMYK.
      <br><strong>2.2.</strong> Certifique-se que o Pedido esteja de acordo com a quantidade de cores e formato existente na arte.
      <br><strong>2.3.</strong> Confira se a sua arte está com 3mm de sangramento (sobra) para cada lado.
      <br><strong>2.4.</strong> Respeite a área de Segurança do corte final, que é de 4mm ao redor do corte para dentro da arte. Pois assim evitaremos risco da arte ser degolada.
      <br><strong>2.5.</strong> Verifique se os preenchimentos nos pretos estão calçados somente com 30% de azul (cyan) e 100% de preto (black).
      <br><strong>2.6.</strong> Certifique-se que todas as fontes estão convertidas em curvas.
      <br><strong>2.7.</strong> Não esqueça de converter todos os efeitos do arquivo em BITMAP para garantir o resultado desejado.
      <br><strong>2.8.</strong> Devido o processo de impressão adotado, as cores no impresso podem sofrer uma variação de 10% na carregação de tinta para cima ou para baixo.
      <br><strong>2.9.</strong> Só envie seu pedido após estar certo de que a arte está ok, pois devido a nossa linha de produção, você pode não conseguir cancelar o pedido se necessário.
   </fieldset>
   <br>
   <fieldset>
      <strong>3. ENTREGA OU RETIRADA DO MATERIAL PRONTO</strong>
      <br>    
      <br><strong>3.1.</strong> Caso escolha entrega pela Rota, seu produto será enviado para o seu endereço de cadastro (no horário comercial) e será cobrada uma taxa, cujo valor dependerá de seu endereço.
      <br><strong>3.2.</strong> Se você optar por Rota, deve ter alguém no endereço de cadastro no dia marcado, caso contrário a entrega será realizada novamente no dia útil seguinte e será cobrada nova taxa de entrega.
      <br><strong>3.3.</strong> Caso escolha uma das lojas (Mesquita, São João de Meriti ou São Pedro da Aldeia) seu produto será entregue na loja selecionada no formulário de envio de arquivo.
      <br><strong>3.4.</strong> Clientes com materiais em aberto por mais de 7 dias terão o cadastro bloqueado.
      <br><strong>3.5.</strong> Os materiais estarão disponíveis por até 60 dias, após este período, serão descartado.
   </fieldset>
   <br>
   <fieldset>
      <strong>4. MATERIAIS COM DIVERGÊNCIAS</strong>
      <br>
      <br><strong>4.1.</strong> No ato da entrega, caso o material não esteja de acordo com seu pedido, favor enviá-lo para o departamento de análise.
      <br><strong>4.2.</strong> A devolução do material deverá ser realizada mediante o preenchimento do formulário (disponível com o entregador) e encaminhado para uma de nossas lojas ou através do próprio entregador.
      <br><strong>4.3.</strong> Após a finalização do pedido, as devoluções por defeito de produção só serão aceitas dentro do prazo de 15 dias corridos. Logo não nos responsabilizaremos por problemas detectados posteriormente a este prazo.
      <br><strong>4.4.</strong> Após a devolução, o defeito será analisado em um prazo de até 48 horas.
   </fieldset>
   <br>
   <fieldset>
      <strong>5. NORMAS E PROCEDIMENTOS OFFSET PLANA</strong>
      <br>
      <br><strong>5.1. Respeite as margens de segurança:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Não nos responsabilizamos por qualquer informação, imagem ou objeto que não estejam salvaguardados pelas margens de segurança pré-estabelecidas, pois o corte pode variar, siga os gabaritos disponibilizados no site.
      <br><strong>5.2. Forma que o arquivo deve ser enviado:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Todo arquivo deverá ter o formato correto e a área de impressão (palco), deverá ser redimensionada para o mesmo formato da arte, caso possua verso, o mesmo deverá estar na pagina 2 nas mesmas condições já descritas, pois qualquer arquivo fora dessas especificações não nos responsabilizamos, mesmo que já tenha sido feito outras vezes.
      <br><strong>5.3. Converta seu arquivo em curvas:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Para que letras ou sinais, não sofram alterações em relação a arte original.
      <br><strong>5.4. Não use fontes muito pequenas e linhas muito finas:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nunca use fontes menores que 12pt, principalmente nos cartões com verniz localizado, o mesmo se aplica a linhas muito finas inferiores a 0,25mm, não nos responsabilizaremos, caso o verniz fique fora de registro.
      <br><strong>5.5. Não use preto processado nas fontes:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Para não haver problemas com o registro do impresso e possibilidade de decalque ao empilhar o material.
      <br><strong>5.6. Não use cores compostas em preenchimentos:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Para não haver manchas ou decalques nos materiais, pois não nos responsabilizaremos.
      <br><strong>5.7. Não envie preenchimentos e contornos em Pantone e RGB:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Todos os preenchimentos e contornos, devem ser convertidos em CMYK, pois os mesmos sofrem alterações após a conversão e não nos responsabilizaremos por envios dessa natureza.
      <br><strong>5.8. Overprint ou impressão sobreposta:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Overprint é o processo de impressão de uma cor sobre a outra, com o propósito de manter as cores mais sólidas, e impossibilitando o problema de registro nos textos, porém, é uma opção que o ‘design’ (quem criou a arte) deve conhecer e dominar, pois a falta de qualquer uma dessas condições, acarretará em falhas de impressão em comparação a arte criada, e não nos responsabilizaremos.
      <br><strong>5.9. Não use preto processado nas artes (C:100,M:100,Y:100,K:100,):</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O preto processado não permitirá que a tinta seque a tempo, ocasionando atraso, decalques e possíveis ‘entupimentos’ em algumas áreas do impresso, como fontes pequenas e finas. Seu trabalho é impresso em uma ‘chapa comunitária’, ou seja, junto com outros trabalhos, prejudicando os demais por uma espera maior na secagem, atrasando toda a chapa.
      <br><strong>5.10. Envio de arquivos 4/1:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ao enviar um arquivo 4/1, saiba que a nossa forma de impressão para esse tipo de arquivo é, o verso em tons de cinza, portanto, certifique-se que o mesmo esteja nessa condição ou tenha sido convertido, pois ao ignorar esse procedimento, poderá fazer com que o impresso não saia da forma criada.
      <br><strong>5.11. Imagens em baixa resolução:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Todas as imagens em bitmap jpg, devem ser enviadas em 300dpi no mínimo, não esquecendo que deverá está no modo de cor CMYK, fora dessas especificações, será de sua inteira responsabilidade.
      <br><strong>5.12. Imagens ou fotos não tratadas:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Todas as imagens e fotos, devem ser tratadas (pelo representante) antes de compor uma arte, apesar de sua visualização aparentar estar perfeita no monitor (varia pra cada monitor) geralmente as mesmas estão em RGB e em baixa resolução, e ao serem convertidas para CMYK, ficam carregadas (ficando mais escuras) não sendo possível a fidelidade da impressão, pelo fato do seu material ser impresso junto com outros trabalhos (chapa comunitária), subindo assim a carregação do impresso, por este motivo não nos responsabilizaremos por uma impressão carregada.
      <br><strong>5.13. Efeitos na arte:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Todos os efeitos criados em seu software de edição, deverão ser convertidos em Bitmap (CMYK em 300dpi), pois os mesmo podem sofrer alterações e não nos responsabilizaremos por essa não observação.
      <br><strong>5.14. Artes em vetor:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Todas as artes em vetor, deverão ser agrupadas antes do envio, pois a ocultação de qualquer objeto devido a manipulação do mesmo, não será de nossa responsabilidade.
      <br><strong>5.15. Orientação do arquivo para envio:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Todos os arquivos, independente do produto, desde que possua verso, deverão sempre seguir a orientação de pé com pé, cabeça com cabeça, como nos modelos, pois não nos responsabilizaremos por materiais que estejam de ponta a cabeça após a impressão, siga o modelo disponibilizado no site.
      <br><strong>5.16. Envio de material com faca:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Lembre-se todos os materiais que possuem corte especial, a faca nunca, poderá vir com o mesmo tamanho do arquivo (ex: arquivo 9x5 e a faca 9x5), a mesma, deverá respeitar sempre as margens para o corte e vinco (3mm no mínimo para todos os lados).
      <br><strong>5.17. Arquivos em PDF/X-1A:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nosso processo de pré-impressão, utiliza o PDF/X-1A, portanto, qualquer arquivo fechado em outra configuração, pode não ser compatível, gerando problemas no seu arquivo, não nos responsabilizaremos por problemas desta natureza.
      <br><strong>5.18. Fidelidade de impressão:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Lembre-se que pelo fato de nossas chapas serem ‘comunitárias’, a fidelidade de impressão é impossível, portanto, mesmo que já tenha impresso um material em uma outra ocasião, não existem garantias que a próxima impressão seja idêntica. O mesmo se aplica a materiais enviados no mesmo dia, porém, em horários diferentes.
      <br><strong>5.19. Cancelamento de arquivo:</strong>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Os pedidos só podem ser cancelados através do Site ou System Color em até 15 minutos após o envio do mesmo.
   </fieldset>
   <br>
   <fieldset>
      <strong>6. NORMAS E PROCEDIMENTOS COMUNICAÇÃO VISUAL</strong>
      <br>
      <br><strong>6.1.</strong> Verificar se a resolução do arquivo está correta; (72dpi para imagens acima de 1,50m²e 150dpi para imagens abaixo de 1,50m²)
      <br><strong>6.2.</strong> Todas as imagens devem estar em CMYK;
      <br><strong>6.3.</strong> No caso de arquivos enviados pelo corel draw X6 todas as fontes devem ser convertidas em curvas e as cores em CMYK.
      <br><strong>6.4.</strong> Todo arquivo que tiver preto, o mesmo deve sercalçado com 40% de cada cor (CMY) e 100% de preto (K).
      <br><strong>6.5.</strong> O banner com tubete ou madeira não deve ultrapassar o tamanho de 2,00mt de largura, pois a madeira só vai até 2,00mt.
      <br><strong>6.6.</strong> A largura máxima do nosso equipamento de impressão é 3,20mt com isso as lonas que tiverem o formato superior a 3,15mt na altura e largura vão ter emenda.
      <br><strong>6.7.</strong> Banners para serem entregues pela rota devem ter a largura máxima de 1,80m, acima deste tamanho apenas nas lojas.
      <br><strong>6.8.</strong> O adesivo brilho, fosco e transparente tem o formato máximo de 1,50mt, arquivos acima desse formato a altura e largura automaticamente vão ter emenda.
      <br><strong>6.9.</strong> O equipamento de recorte eletrônico tem largura de 1,20cm, nos casos de impressão com recorte os arquivos deverão ser montados dentro do limite de 1,10cm para serem cortados sem problema.
      <br><strong>6.10.</strong> O adesivo perfurado tem o formato de 1,25mt, qualquer arquivo que ultrapasse esse formato automaticamente terá emenda.
      <br><strong>6.11.</strong> A lona back não pode ter emenda, limite máximo de impressão é de 3,15mt.
      <br><strong>6.12.</strong> Lonas em que for solicitado o REFORÇO COM CORDA, o material terá acréscimo de R$ 2,00 por m².
      <br><strong>6.13.</strong> Toda e qualquer informação sobre o trabalho deve ser escrita no campo de observações do representante.
      <br><strong>6.14.</strong> Os pedidos só podem ser cancelados através do Site ou System Color em até 15 minutos após o envio do mesmo.
      <br><strong>6.15.</strong> Adesivo Transparente retroverso + aplicação de branco, o arquivo deve vir espelhado.
      <br><strong>6.16.</strong> A Microfibra no processo de sublimação passa por um aquecimento de 220°c e com isso pode acontecer do tecido encolher em até 10% da medida inicial.
      <br><strong>6.17</strong> Não nos responsabilizamos, por objetos ou fundos bloqueados, que não saiam na impressão.
      <br><strong>6.18</strong> Todos os materiais acima de 15m² o prazo de produção altera para 48h. Estando cinetes de que dependendo da quantidade e do tamanho final esse prazo pode estender sendo combinado com o gerente do setor.
      <br><strong>6.19</strong> Todas as lonas sem acabamento seguirão sem margem de sobra branca. As mesmas serão refiladas no tamanho enviado.
      <br><strong>6.20</strong> Ao retornar com o material para a análise de defeitos, o mesmo deverá ser enrolado e reembalado de forma em que a aparte impressa não entre em contato com ela mesma. Caso contrário, causará danos a impressão tornando impossível a análise, resultando na devolução do material para o cliente.
   </fieldset>
   <br>
   <fieldset>
      <strong>7. RESPONSABILIDADE</strong>
      <br>
      <br><strong>7.1.</strong> Qualquer procedimento diferente dos discriminados nos itens acima será de inteira responsabilidade do cliente.
      <br><strong>7.2.</strong> A empresa não responsabiliza-se pelo conteúdo produzido no site, o cliente será o único responsável por preparar o arquivo e conferir suas especificações técnicas e enviar para a empresa produzir o seu material.
      <br><strong>7.3.</strong> O cliente atesta, neste ato que é o único proprietário do conteúdo produzido no site da empresa e que será enviado para confecção. O declara que o seu pedido não viola marca, direito autoral, ou qualquer outro direito de terceiros. A empresa não responsabiliza-se pelo contúdo produzido ou comercializado pelo cliente, caso a empresa seja acusada de violar direito de terceiros, ou pela prática de atos considerados ilegais, caberá ao cliente responder pelos danos ocasionados à Empresa, incluindo, mas não se limitando, às despesas processuais, danos materiais, e perdas e danos.
      <br><strong>7.4.</strong> A empresa responsabiliza-se apenas pela confecção dos produtos enviados pelo cliente no prazo e condições ora avençadas.
      <br><strong>7.5.</strong> O cliente isenta, neste ato, a empresa por quaisquer perdas, lucros cessantes, despesas, danos, reclamações ou reivindicações, incorridos de terceiros e oriundos da prestação de serviços estabelecida entre as partes.
      <br><strong>7.6.</strong> No caso de manutenção ou atualização do sistema, o acesso do cliente ao siite poderá ser suspenso temporariamente sem a necessidade de envio de aviso ou notificação.
   </fieldset>
   <fieldset>
      <strong>7. DISPOSIÇÕES GERAIS</strong>
      <br>
      <br><strong>8.1.</strong> O Presente Termo de Uso constitui o acordo integral entre as partes, prevalecendo sobre qualquer outro entendimento firmado anteriormente.
      <br><strong>8.2.</strong> A empresa poderá divulgar através de e-mail, notificações ou mensagens enviadas através do próprio Site informações ao cliente sobre eventuais alterações em sua conta pessoal ou pedidos realizados.
      <br><strong>8.3.</strong> O presente Termo não configura ou gera qualquer forma de sociedade, vínculo, parceria de fato ou de direito, entre as partes, inexistindo solidariedade entre elas, e não estando elas autorizadas a representar ou assumir obrigações em nome da outra.
      <br><strong>8.4.</strong> Fica vedado ao cliente utilizar o nome da empresa, ou de qualquer outra empresa do grupo, para angariar clientela, sob pena de ser demandado judicialmente pelo uso indevido da marca e eventuais danos causados à empresa.
      <br><strong>8.5.</strong> Para dirimir eventuais controvérsias acerca do presente termo, fica eleito o Foro da Comarca de São João de Meriti RJ, com a renúncia a qualquer outro, por mais privilegiado que seja, para resolver qualquer dúvida, pendência ou litígio relacionado aos Termos de Uso.
   </fieldset>
</div>
@endsection