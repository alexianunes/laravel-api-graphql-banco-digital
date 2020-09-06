
## Sobre

- API em Laravel com GraphQL para movimentação (quitação de dívida) de uma conta bancaria, também foi utilizado PHPUnit para realização dos testes unitários.

## Requisitos
- PHP 7.2+
- Laravel 6+
- MSYQL 5.7+
- Composer

## Orientações
- 1) Renomeie o arquivo ".env.example" para ".env" e configure os campos APP_URL com a URL completa do projeto, DB_DATABASE com o nome do banco de dados criado, DB_USERNAME e DB_PASSWORD com login e senha do banco de dados.
- 2) Em seguida utilize "composer install" para que seja instalado todas as dependências do Projeto.
- 3) Para finalizar, utilize o "php artisan migrate --seed" para que executar todos os Migrations (criação de todas as tabelas) e popular as mesmas.