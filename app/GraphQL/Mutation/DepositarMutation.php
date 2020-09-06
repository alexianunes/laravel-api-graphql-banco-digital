<?php 

namespace App\GraphQL\Mutation;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

use App\Conta;
use App\GraphQL\Error\AcoesException;

class DepositarMutation extends Mutation {
    protected $attributes = [
        'name' => 'depositar'
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
            ],
            'valor' => [
                'name' => 'valor',
                'type' => Type::nonNull(Type::int())
            ]
        ];
    }


    public function resolve($root, $args)
    {
        $conta = Conta::where('conta', '=', $args['conta'])->first();


        if(isset($args['valor']) && !empty($args['valor']) && $args['valor'] > 0){

            if(isset($conta) && !empty($conta->id)){

                $data['saldo'] = $conta->saldo + $args['valor'];
                
                $conta->update($data);

                return $conta;
        
            }else{
                throw new AcoesException(
                    'Conta Inexistente'
                );
            }
        }else{
            throw new AcoesException(
                'Valor inválido'
            );
        }
    }

    
}