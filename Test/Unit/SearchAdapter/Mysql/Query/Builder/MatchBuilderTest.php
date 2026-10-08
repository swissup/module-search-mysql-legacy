<?php
declare(strict_types=1);

namespace Swissup\SearchMysqlLegacy\Test\Unit\SearchAdapter\Mysql\Query\Builder;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Helper\Mysql\Fulltext;
use Magento\Framework\Search\Request\Query\BoolExpression;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Swissup\SearchMysqlLegacy\SearchAdapter\Mysql\Field\ResolverInterface;
use Swissup\SearchMysqlLegacy\SearchAdapter\Mysql\Query\Builder\MatchBuilder;

class MatchBuilderTest extends TestCase
{
    private function prepare(string $value, string $conditionType, string $mode = 'or', ?string $maxLength = null): string
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(
            fn (string $path) => $path === 'catalog/search/max_query_length' ? $maxLength : $mode
        );
        $builder = new MatchBuilder(
            $this->createMock(ResolverInterface::class),
            $this->createMock(Fulltext::class),
            Fulltext::FULLTEXT_MODE_BOOLEAN,
            [],
            $scopeConfig
        );
        $method = new \ReflectionMethod($builder, 'prepareQuery');
        $method->setAccessible(true);

        return $method->invoke($builder, $value, $conditionType);
    }

    public static function whitespaceProvider(): array
    {
        return [
            'space' => ['red bag', '+red* +bag*'],
            'tab' => ["red\tbag", '+red* +bag*'],
            'newline' => ["red\nbag", '+red* +bag*'],
            'crlf and mixed' => ["red \r\n\t bag", '+red* +bag*'],
            'edges' => [" \t red \n", '+red*'],
        ];
    }

    #[DataProvider('whitespaceProvider')]
    public function testEveryWhitespaceSeparatesWordsInAndMode(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->prepare($input, BoolExpression::QUERY_CONDITION_SHOULD, 'and'));
    }

    public function testOperatorsAreStripped(): void
    {
        $this->assertSame(
            'bag* a* b* c*',
            $this->prepare('"bag" +a -b ~c', BoolExpression::QUERY_CONDITION_SHOULD)
        );
    }

    public static function limitProvider(): array
    {
        return [
            'default length cut to 128' => [str_repeat('a', 500), null, 129],
            'configured length' => [str_repeat('a', 500), '10', 11],
            'length not configured numeric' => [str_repeat('a', 500), 'x', 129],
        ];
    }

    #[DataProvider('limitProvider')]
    public function testQueryLengthIsLimited(string $input, ?string $maxLength, int $expectedLength): void
    {
        $result = $this->prepare($input, BoolExpression::QUERY_CONDITION_SHOULD, 'or', $maxLength);
        $this->assertSame($expectedLength, strlen($result));
    }

    public function testWordCountIsLimited(): void
    {
        $result = $this->prepare(
            implode(' ', array_fill(0, 500, 'a')),
            BoolExpression::QUERY_CONDITION_SHOULD,
            'or',
            '100000'
        );
        $this->assertSame(MatchBuilder::MAX_QUERY_WORDS, count(explode(' ', $result)));
    }
}
