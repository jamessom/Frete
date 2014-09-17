<?php

namespace spec\EscapeWork\Frete\Correios;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use EscapeWork\Frete\Correios\PrecoPrazoResult;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ParseException;

class PrecoPrazoSpec extends ObjectBehavior
{

    function let(Client $client, PrecoPrazoResult $result)
    {
        $this->beConstructedWith($client, $result);
    }

    function it_throw_an_exception_with_invalid_format(Client $client, PrecoPrazoResult $result)
    {
        $this->shouldThrow('InvalidArgumentException')->during('setFormato', ['4']);
    }

    function it_throw_an_exception_with_invalid_mao_propria_value(Client $client, PrecoPrazoResult $result)
    {
        $this->shouldThrow('InvalidArgumentException')->during('setMaoPropria', ['Y']);
    }

    function it_throw_an_exception_with_invalid_aviso_recebimento(Client $client, PrecoPrazoResult $result)
    {
        $this->shouldThrow('InvalidArgumentException')->during('setAvisoRecebimento', ['Y']);
    }

    function it_can_calculate_with_error_response(Client $client, PrecoPrazoResult $result)
    {
        $response = new FakePrecoPrazoResponse('error');
        $client->get('http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&nCdServico=40010%2C41106&sCepOrigem=&sCepDestino=&nVlPeso=&nCdFormato=1&nVlComprimento=&nVlAltura=&nVlLargura=&nVlDiametro=&sCdMaoPropria=N&nVlValorDeclarado=0&sCdAvisoRecebimento=N&StrRetorno=xml')->willReturn($response);

        $this->shouldThrow('EscapeWork\Frete\FreteException')->during('calculate');
    }

    function it_throw_an_exception_with_invalid_server_response(Client $client, PrecoPrazoResult $result)
    {
        $response = new FakePrecoPrazoResponse('exception');
        $client->get('http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&nCdServico=40010%2C41106&sCepOrigem=&sCepDestino=&nVlPeso=&nCdFormato=1&nVlComprimento=&nVlAltura=&nVlLargura=&nVlDiametro=&sCdMaoPropria=N&nVlValorDeclarado=0&sCdAvisoRecebimento=N&StrRetorno=xml')->willReturn($response);

        $this->shouldThrow('EscapeWork\Frete\FreteException')->during('calculate');
    }

    function it_can_calculate_with_one_result(Client $client, PrecoPrazoResult $result)
    {
        $response = new FakePrecoPrazoResponse('preco');
        $client->get('http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&nCdServico=40010%2C41106&sCepOrigem=&sCepDestino=&nVlPeso=&nCdFormato=1&nVlComprimento=&nVlAltura=&nVlLargura=&nVlDiametro=&sCdMaoPropria=N&nVlValorDeclarado=0&sCdAvisoRecebimento=N&StrRetorno=xml')->willReturn($response);

        $result->fill(Argument::any())->shouldBeCalled();
        $this->calculate()->shouldBeAnInstanceOf('EscapeWork\Frete\Correios\PrecoPrazoResult');
    }

    function it_can_calculate_with_multiple_results(Client $client, PrecoPrazoResult $result)
    {
        $response = new FakePrecoPrazoResponse('precos');
        $client->get('http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&nCdServico=40010%2C41106&sCepOrigem=&sCepDestino=&nVlPeso=&nCdFormato=1&nVlComprimento=&nVlAltura=&nVlLargura=&nVlDiametro=&sCdMaoPropria=N&nVlValorDeclarado=0&sCdAvisoRecebimento=N&StrRetorno=xml')->willReturn($response);

        $this->calculate()->shouldBeAnInstanceOf('EscapeWork\Frete\Collection');
    }
}

class FakePrecoPrazoResponse
{

    protected $type = '';

    public function __construct($type = '')
    {
        $this->type = $type;
    }

    public function xml()
    {
        switch ($this->type) {
            case 'exception':
                throw new ParseException;

            case 'error':
                return json_decode(json_encode([
                    'cServico' => [
                        'Erro'    => '1',
                        'MsgErro' => 'fucking errors!',
                    ]
                ]));

            case 'preco':
                return json_decode(json_encode([
                    'cServico' => [
                        'Codigo' => 1,
                        'Erro'   => '0',
                    ]
                ]));

            case 'precos':
                return json_decode(json_encode([
                    'cServico' => [
                        ['Erro' => '0'],
                    ]
                ]));
        }
    }
}
