<?php 

namespace App\GraphQL\Types;

use App\Conta;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;


class ContaType extends GraphQLType
{

    protected $attributes = [
        'name' => 'Conta',
        'description' => 'Contas bancárias',
        'model' => Conta::class
    ];


    public function fields(): array{
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID da Conta Bancaria',
            ],
            'agencia' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Agência responsável pela Conta Bancaria',
            ],
            'conta' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Conta da Conta Bancaria',
            ],
            'saldo' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'Saldo da Conta Bancaria',
            ]
        ];
    }

    
}