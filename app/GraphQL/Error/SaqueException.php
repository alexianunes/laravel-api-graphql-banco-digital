<?php 

namespace App\GraphQL\Error;

use GraphQL\Error\ClientAware;

class SaqueException extends \Exception implements ClientAware
{
    public function isClientSafe()
    {
        return true;
    }

    public function getCategory()
    {
        return 'graphql';
    }
}