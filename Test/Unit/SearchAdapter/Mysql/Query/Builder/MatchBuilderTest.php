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
    private function prepare(string $value, string $conditionType, string $mode = 'or'): string
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(
            fn () => $mode
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
}
