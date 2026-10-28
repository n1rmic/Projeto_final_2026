<?php
/**
 * Credenciais do contrato Correios (portal CWS: cws.correios.com.br).
 * Sem preencher usuario/senha, o cálculo cai automaticamente no modo ESTIMADO
 * (sem API externa), então o checkout continua funcionando em ambiente de testes.
 */
return [
    'usuario'         => '',           // usuário do "Meu Correios"
    'senha'           => '',           // senha/código de acesso à API (gerado no CWS)
    'cartao_postagem' => '',           // número do cartão de postagem
    'contrato'        => '',           // número do contrato
    'cep_origem'      => '26210240',   // CEP de onde a loja despacha — AJUSTE para o seu
    'codigo_servico'  => '03220',      // 03220 = SEDEX contrato / 04510 = PAC contrato
];