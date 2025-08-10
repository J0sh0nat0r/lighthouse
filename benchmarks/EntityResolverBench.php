<?php declare(strict_types=1);

namespace Benchmarks;

use Nuwave\Lighthouse\Federation\FederationServiceProvider;

/**
 * @phpstan-type Representation array{__typename: 'Foo', id: int}
 */
class EntityResolverBench extends QueryBench
{
    protected string $schema /** @lang GraphQL */ = <<<'GRAPHQL'
type Foo @key(fields: "id") {
  id: ID! @external
  foo: String!
}
GRAPHQL;

    /** @var string */
    private const QUERY /** @lang GraphQL */ = <<<'GRAPHQL'
query ($representations: [_Any!]!) {
    _entities(representations: $representations) {
        __typename
        ... on Foo {
            id
        }
    }
}
GRAPHQL;

    /** @var list<Representation> */
    private array $representations;

    /** @return  list<Representation> */
    private function generateRepresentations(int $count): array
    {
        $representations = [];

        for ($i = 0; $i < $count; ++$i) {
            $representations[] = [
                '__typename' => 'Foo',
                'id' => $i,
            ];
        }

        return $representations;
    }

    protected function getPackageProviders($app): array
    {
        return array_merge(
            parent::getPackageProviders($app),
            [FederationServiceProvider::class],
        );
    }

    /**
     * @Warmup(1)
     *
     * @Revs(10)
     *
     * @Iterations(10)
     *
     * @ParamProviders({"providePerformanceTuning"})
     *
     * @BeforeMethods("setPerformanceTuning")
     */
    public function benchmark1(): void
    {
        $this->representations ??= $this->generateRepresentations(1);
        $this->graphQL(self::QUERY, [
            'representations' => $this->representations,
        ]);
    }

    /**
     * @Warmup(1)
     *
     * @Revs(10)
     *
     * @Iterations(10)
     *
     * @ParamProviders({"providePerformanceTuning"})
     *
     * @BeforeMethods("setPerformanceTuning")
     */
    public function benchmark100(): void
    {
        $this->representations ??= $this->generateRepresentations(100);
        $this->graphQL(self::QUERY, [
            'representations' => $this->representations,
        ]);
    }

    /**
     * @Warmup(1)
     *
     * @Revs(10)
     *
     * @Iterations(10)
     *
     * @ParamProviders({"providePerformanceTuning"})
     *
     * @BeforeMethods("setPerformanceTuning")
     */
    public function benchmark10k(): void
    {
        $this->representations ??= $this->generateRepresentations(10000);
        $this->graphQL(self::QUERY, [
            'representations' => $this->representations,
        ]);
    }
}
