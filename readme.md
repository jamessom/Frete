# EscapeWork/Frete

[![Build Status](https://secure.travis-ci.org/EscapeWork/Frete.png)](http://travis-ci.org/EscapeWork/Frete) [![Latest Stable Version](https://poser.pugx.org/escapework/frete/v/stable.png)](https://packagist.org/packages/escapework/frete) [![Total Downloads](https://poser.pugx.org/escapework/frete/downloads.png)](https://packagist.org/packages/escapework/frete)

## Instalação

Instalação disponível via Composer.

```
{
    "require": {
        "escapework/frete": "0.2.*"
    }
}
```

## Calculando preço e prazo

```php
use EscapeWork\Frete\Correios\PrecoPrazo;
use EscapeWork\Frete\Correios\Data;
use EscapeWork\Frete\FreteException;

$frete = new PrecoPrazo();
$frete->setCodigoServico(Data::SEDEX)
      ->setCodigoEmpresa('Codigo')      # opcional
      ->setSenha('Senha')               # opcional
      ->setCepOrigem('cep de origem')   # apenas numeros, sem hifen(-)
      ->setCepDestino('cep de destino') # apenas numeros, sem hifen(-)
      ->setComprimento(30)              # obrigatorio
      ->setAltura(30)                   # obrigatorio
      ->setLargura(30)                  # obrigatorio
      ->setDiametro(30)                 # obrigatorio
      ->setPeso(0.5);                   # obrigatorio

try {
    $result = $frete->calculate();

    echo $result['cServico']['Valor'];
    echo $result['cServico']['PrazoEntrega'];

    var_dump($result); // debugge o resultado!
}
catch (FreteException $e) {
    echo $e->getMessage();
}
```

#### Tipos de frete disponíveis

```php
EscapeWork\Frete\Correios\Data::SEDEX;          # sedex
EscapeWork\Frete\Correios\Data::SEDEX_A_COBRAR; # sedex a cobrar
EscapeWork\Frete\Correios\Data::SEDEX_10;       # sedex 10
EscapeWork\Frete\Correios\Data::SEDEX_HOJE;     # sedex hoje
EscapeWork\Frete\Correios\Data::PAC;            # pac
```

#### Buscando múltiplos serviços

Também é possível obter um array com vários serviços (Sedex e PAC, por exemplo) utilizando a classe `PrecoPrazo`.

```php
$frete = new PrecoPrazo();
$frete->setCodigoServico(Data::SEDEX)
      ... // todo os setters igual a chamada acima

try {
    $results = $frete->calculate();

    foreach ($results as $result) {
        echo $result['cServico']['Valor'];
        echo $result['cServico']['PrazoEntrega'];

        var_dump($result); // debugge o resultado!
    }
}
catch (FreteException $e) {
    echo $e->getMessage();
}
```

## Rastreamento de encomendas

Precisa de documentação.

***

### Referências

* [Cálculo de preço e prazo](http://www.correios.com.br/para-voce/correios-de-a-a-z/pdf/calculador-remoto-de-precos-e-prazos/manual-de-implementacao-do-calculo-remoto-de-precos-e-prazos)
* [Rastreamento online](http://www.correios.com.br/para-voce/correios-de-a-a-z/pdf/rastreamento-de-objetos/Manual_SROXML_28fev14.pdf)

### License

The MIT License (MIT)

Copyright (c) 2013 Escape Criativação LTDA

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
