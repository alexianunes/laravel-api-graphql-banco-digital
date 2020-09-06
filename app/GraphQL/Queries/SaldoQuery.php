<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

use App\Conta;
use App\GraphQL\Error\AcoesException;

class SaldoQuery extends Query
{
    protected $attributes = [
        'name' => 'saldo',
    ];

    public function type(): Type
    {
        return GraphQL::type('Conta');
    }

    public function args(): array
    {
        return [
            'conta' => [
                'name' => 'conta',
                'type' => Type::nonNull(Type::int())
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $conta = Conta::where('conta', '=', $args['conta'])->first();

        if(isset($conta) && !empty($conta->id)){

            return $conta;
    
        }else{
            throw new AcoesException(
                'Conta Inexistente'
            );
        }
    }
}