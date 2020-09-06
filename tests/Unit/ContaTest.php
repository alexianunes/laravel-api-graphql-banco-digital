<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Conta;

class ContaTest extends TestCase
{
    
    public function testSacar(): void
    {
        $graphql = <<<'GRAPHQL'
mutation Mutate($conta: Int!, $valor: Int!) {
    sacar(conta: $conta, valor: $valor){
        conta
        saldo
    }
}
GRAPHQL;
        
        $result = $this->graphql($graphql, [
            'expectErros' => true,
            'variables' => [
                'conta' => 54321,
                'valor' => 140
            ]
        ]);

        // dd($result);
        $expectedResult = [
            'data' => [
                'sacar' => [
                    'conta' => 54321,
                    'saldo' => 20.0
                ],
            ],
        ];

        // dd($expectedResult);
        $this->assertSame($expectedResult, $result);
    }

    public function testDepositar(): void
    {
        $graphql = <<<'GRAPHQL'
mutation Mutate($conta: Int!, $valor: Int!) {
    depositar(conta: $conta, valor: $valor){
        conta
        saldo
    }
}
GRAPHQL;
        
        $result = $this->graphql($graphql, [
            'expectErros' => true,
            'variables' => [
                'conta' => 54321,
                'valor' => 200
            ]
        ]);

        // dd($result);
        $expectedResult = [
            'data' => [
                'depositar' => [
                    'conta' => 54321,
                    'saldo' => 220.0
                ],
            ],
        ];

        // dd($expectedResult);
        $this->assertSame($expectedResult, $result);
    }

    public function testSaldo(): void
    {
        $query = <<<'GRAQPHQL'
{
    saldo(conta: 54321) {
        saldo
    }
}
GRAQPHQL;

        $result = $this->graphql($query);

        // dd($result);
        $expectedResult = [
            'data' => [
                'saldo' => [
                    'saldo' => 220.0
                ],
            ],
        ];

        // dd($expectedResult);
        $this->assertSame($expectedResult, $result);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('graphql.schemas.default', [
            'query' => [
                'saldo' => SaldoQuery::class,
            ],
            'mutation' => [
                SacarMutation::class,
                DepositarMutation::class,
            ]
        ]);
        $app['config']->set('graphql.types', [
            'ContaType' => ContaType::class,
        ]);
    }

}
