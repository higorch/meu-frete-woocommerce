<?php

if (!defined('ABSPATH')) {
    exit;
}

class Correios
{
    public $nCdEmpresa = '';
    public $sDsSenha = '';
    public $sCepOrigem;
    public $sCepDestino;
    public $nCdServico;
    public $nCdFormato = 1; // 1 –Formato caixa/pacote, 2 –Formato rolo/prisma, 3 -Envelope
    public $nVlPeso;
    public $nVlComprimento;
    public $nVlAltura;
    public $nVlLargura;
    public $nVlDiametro = 0;
    public $sCdMaoPropria = 'N'; // S ou N   (S –Sim, N –Não)
    public $nVlValorDeclarado = 0;
    public $sCdAvisoRecebimento = 'N'; // S ou N   (S –Sim, N –Não)
    public $itens;

    public function setCodigoEmpresa($codigo)
    {
        $this->nCdEmpresa = $codigo;

        return $this;
    }

    public function setSenhaEmpresa($senha)
    {
        $this->sDsSenha = $senha;

        return $this;
    }

    public function setCepOrigem($cepOrigem)
    {
        $this->sCepOrigem = preg_replace('/[^0-9]/', '', $cepOrigem);;

        return $this;
    }

    public function setCepDestino($cepDestino)
    {
        $this->sCepDestino = preg_replace('/[^0-9]/', '', $cepDestino);;

        return $this;
    }

    public function setCodigoServico($cdigoServico)
    {
        $this->nCdServico = $cdigoServico;

        return $this;
    }

    public function setFormatoEncomenda($formato)
    {
        $this->nCdFormato = $formato;

        return $this;
    }

    public function setMaoPropria($maoPropria)
    {
        $this->sCdMaoPropria = $maoPropria;

        return $this;
    }

    public function setValorDeclarado($valorDeclarado)
    {
        $this->nVlValorDeclarado = $valorDeclarado;

        return $this;
    }

    public function setAvisoRecebimento($avisoRecebimento)
    {
        $this->sCdAvisoRecebimento = $avisoRecebimento;

        return $this;
    }

    public function setItens(array $itens)
    {
        $this->itens = $itens;

        return $this;
    }

    public function calc()
    {
        $total_peso = 0;
        $total_cm_cubico = 0;

        foreach ($this->itens as $item) :
            if ($item['peso'] != '' && $item['altura'] != '' && $item['largura'] != '') {
                $row_peso = $item['peso'] * $item['quantidade'];
                $row_cm = ($item['altura'] * $item['largura'] * $item['comprimento']) * $item['quantidade'];

                $total_peso += $row_peso;
                $total_cm_cubico += $row_cm;
            }
        endforeach;

        $raiz_cubica = round(pow($total_cm_cubico, 1 / 3), 2);

        $this->nVlComprimento = $raiz_cubica < 16 ? 16 : $raiz_cubica;
        $this->nVlAltura = $raiz_cubica < 2 ? 2 : $raiz_cubica;
        $this->nVlLargura = $raiz_cubica < 11 ? 11 : $raiz_cubica;
        $this->nVlPeso = $total_peso < 0.3 ? 0.3 : $total_peso;
        // $this->nVlDiametro = hypot($comprimento, $largura); // opcional
    }

    public function webService()
    {
        $this->calc();

        $params = array(
            'nCdEmpresa' => $this->nCdEmpresa,
            'sDsSenha' => $this->sDsSenha,
            'sCepOrigem' => $this->sCepOrigem,
            'sCepDestino' => $this->sCepDestino,
            'nVlPeso' => $this->nVlPeso,
            'nCdFormato' => $this->nCdFormato,
            'nVlComprimento' => $this->nVlComprimento,
            'nVlAltura' => $this->nVlAltura,
            'nVlLargura' => $this->nVlLargura,
            'sCdMaoPropria' => $this->sCdMaoPropria,
            'nVlValorDeclarado' => $this->nVlValorDeclarado,
            'sCdAvisoRecebimento' => $this->sCdAvisoRecebimento,
            'nCdServico' => $this->nCdServico,
            'nVlDiametro' => $this->nVlDiametro,
            'StrRetorno' => 'xml',
            'nIndicaCalculo' => 3
        );

        $query = http_build_query($params);
        $url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?{$query}";

        $xml = simplexml_load_file($url);

        $result = [
            'codigo' => current($xml->cServico->Codigo),
            'valor' => strip_number_format(current($xml->cServico->Valor)),
            'prazoEntrega' => current($xml->cServico->PrazoEntrega),
            'valorSemAdicionais' => current($xml->cServico->ValorSemAdicionais),
            'valorMaoPropria' => current($xml->cServico->ValorMaoPropria),
            'valorAvisoRecebimento' => current($xml->cServico->ValorAvisoRecebimento),
            'valorValorDeclarado' => current($xml->cServico->ValorValorDeclarado),
            'entregaDomiciliar' => current($xml->cServico->EntregaDomiciliar),
            'entregaSabado' => current($xml->cServico->EntregaSabado),
            'erro' => current($xml->cServico->Erro) == 0 ? false : current($xml->cServico->Erro)
        ];

        return $result;
    }

    public function run()
    {
        return $this->webService();
    }
}
