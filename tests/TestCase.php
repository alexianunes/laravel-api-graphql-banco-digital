<?php

declare(strict_types=1);

namespace Tests;

use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPUnit\Framework\ExpectationFailedException;
use Rebing\GraphQL\GraphQLServiceProvider;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Symfony\Component\Console\Tester\CommandTester;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getEnvironmentSetUp($app)
    {
        if (env('TESTS_ENABLE_LAZYLOAD_TYPES') === '1') {
            $app['config']->set('graphql.lazyload_types', true);
        }

        $app['config']->set('app.debug', true);
    }

    protected function assertGraphQLSchema($schema): void
    {
        $this->assertInstanceOf(Schema::class, $schema);
    }

    protected function assertGraphQLSchemaHasQuery($schema, $key): void
    {
        // Query
        $query = $schema->getQueryType();
        $queryFields = $query->getFields();
        $this->assertArrayHasKey($key, $queryFields);

        $queryField = $queryFields[$key];
        $queryListType = $queryField->getType();
        $queryType = $queryListType->getWrappedType();
        $this->assertInstanceOf(FieldDefinition::class, $queryField);
        $this->assertInstanceOf(ListOfType::class, $queryListType);
        $this->assertInstanceOf(ObjectType::class, $queryType);
    }

    protected function assertGraphQLSchemaHasMutation($schema, $key): void
    {
        // Mutation
        $mutation = $schema->getMutationType();
        $mutationFields = $mutation->getFields();
        $this->assertArrayHasKey($key, $mutationFields);

        $mutationField = $mutationFields[$key];
        $mutationType = $mutationField->getType();
        $this->assertInstanceOf(FieldDefinition::class, $mutationField);
        $this->assertInstanceOf(ObjectType::class, $mutationType);
    }

    protected function getPackageProviders($app): array
    {
        $providers = [
            GraphQLServiceProvider::class,
        ];

        return $providers;
    }

    protected function getPackageAliases($app): array
    {
        return [
            'GraphQL' => GraphQL::class,
        ];
    }

    /**
     * The `CommandTester` is directly returned, use methods like
     * `->getDisplay()` or `->getStatusCode()` on it.
     *
     * @param Command $command
     * @param array $arguments The command line arguments, array of key=>value
     *   Examples:
     *   - named  arguments: ['model' => 'Post']
     *   - boolean flags: ['--all' => true]
     *   - arguments with values: ['--arg' => 'value']
     * @param array $interactiveInput Interactive responses to the command
     *   I.e. anything the command `->ask()` or `->confirm()`, etc.
     * @return CommandTester
     */
    protected function runCommand(Command $command, array $arguments = [], array $interactiveInput = []): CommandTester
    {
        $command->setLaravel($this->app);

        $tester = new CommandTester($command);
        $tester->setInputs($interactiveInput);

        $tester->execute($arguments);

        return $tester;
    }

    /**
     * Helper to dispatch an internal GraphQL requests.
     *
     * @param  string  $query
     * @param  array  $options
     *   Supports the following options:
     *   - `expectErrors` (default: false): if no errors are expected but present, let's the test fail
     *   - `variables` (default: null): GraphQL variables for the query
     * @return array GraphQL result
     */
    protected function graphql(string $query, array $options = []): array
    {
        $expectErrors = $options['expectErrors'] ?? false;
        $variables = $options['variables'] ?? null;

        $result = GraphQL::query($query, $variables);

        $assertMessage = null;

        if (! $expectErrors && isset($result['errors'])) {
            $appendErrors = '';
            if (isset($result['errors'][0]['trace'])) {
                $appendErrors = "\n\n".$this->formatSafeTrace($result['errors'][0]['trace']);
            }

            $assertMessage = "Probably unexpected error in GraphQL response:\n"
                .var_export($result, true)
                .$appendErrors;
        }
        unset($result['errors'][0]['trace']);

        if ($assertMessage) {
            throw new ExpectationFailedException($assertMessage);
        }

        return $result;
    }

    /**
     * Helper to dispatch an HTTP GraphQL requests.
     *
     * @param  string  $query
     * @param  array  $options
     *   Supports the following options:
     *   - `httpStatusCode` (default: 200): the HTTP status code to expect
     * @return array GraphQL result
     */
    protected function httpGraphql(string $query, array $options = []): array
    {
        $expectedHttpStatusCode = $options['httpStatusCode'] ?? 200;

        $response = $this->call('GET', '/graphql', [
            'query' => $query,
        ]);

        $httpStatusCode = $response->getStatusCode();

        if ($expectedHttpStatusCode !== $httpStatusCode) {
            $result = $response->getData(true);
            $msg = var_export($result, true)."\n";
            $this->assertSame($expectedHttpStatusCode, $httpStatusCode, $msg);
        }

        return $response->getData(true);
    }

    /**
     * Converts the trace as generated from \GraphQL\Error\FormattedError::toSafeTrace
     * to a more human-readable string for a failed test.
     *
     * @param array $trace
     * @return string
     */
    private function formatSafeTrace(array $trace): string
    {
        return implode(
            "\n",
            array_map(function (array $row, int $index): string {
                $line = "#$index ";
                $line .= $row['file'] ?? '';
                if (isset($row['line'])) {
                    $line .= "({$row['line']}) :";
                }
                if (isset($row['call'])) {
                    $line .= ' '.$row['call'];
                }
                if (isset($row['function'])) {
                    $line .= ' '.$row['function'];
                }

                return $line;
            }, $trace, array_keys($trace))
        );
    }
}