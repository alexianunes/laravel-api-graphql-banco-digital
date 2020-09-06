<?php

namespace App\GraphQL\Queries;

use App\Conta;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ContasQuery extends Query
{
    protected $attributes = [
        'name' => 'contas',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Conta'));
    }

    public function resolve($root, $args)
    {
        return Conta::all();
    }
}