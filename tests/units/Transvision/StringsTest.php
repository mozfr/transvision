<?php
namespace tests\Transvision;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Transvision\Strings;

require_once __DIR__ . '/../bootstrap.php';

class StringsTest extends TestCase
{
    public static function mtrimDP()
    {
        return [
            ['Le cheval  blanc ', 'Le cheval blanc '],
            ['  Le cheval  blanc', ' Le cheval blanc'],
            ['  Le cheval  blanc  ', ' Le cheval blanc '],
            ['Le cheval  blanc', 'Le cheval blanc'],
        ];
    }

    #[DataProvider('mtrimDP')]
    public function testMtrim($a, $b)
    {
        $obj = new Strings();
        $this
            ->assertEqualsCanonicalizing($obj->mtrim($a), $b);
    }

    public static function startsWithDP()
    {
        return [
            ['it is raining', 'it', true],
            [' foobar starts with a nasty space', 'foobar', false],
            ['multiple matches test', ['horse', 'multiple'], true],
            ['multiple matches test', ['not', 'there'], false],
        ];
    }

    #[DataProvider('startsWithDP')]
    public function testStartsWith($a, $b, $c)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->startsWith($a, $b), $c);
    }

    public static function endsWithDP()
    {
        return [
            ['it is raining', 'ing', true],
            ['foobar ends with a nasty space ', 'space', false],
            ['multiple matches test', ['horse', 'test'], true],
            ['multiple matches test', ['not', 'there'], false],
        ];
    }

    #[DataProvider('endsWithDP')]
    public function testEndsWith($a, $b, $c)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->endsWith($a, $b), $c);
    }

    public static function inStringWithDP()
    {
        return [
            ['La maison est blanche', 'blanche', false, true],
            ['La maison est blanche', 'blanche', true, true],
            ['La maison est blanche', ['blanche', 'maison'], true, true],
            ['La maison est blanche', ['blanche', 'maison'], false, true],
            ['La maison est blanche', ['blanche', 'noir'], true, false],
            ['La maison est blanche', ['blanche', 'noir'], false, true],
            ['Le ciel est bleu', 'noir', false, false],
            ['Le ciel est bleu', 'Le', false, true],
        ];
    }

    #[DataProvider('inStringWithDP')]
    public function testInString($a, $b, $c, $d)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->inString($a, $b, $c), $d);
    }

    public static function multipleStringReplaceDP()
    {
        return [
            [
                [
                    ' '        => '<span class="highlight-special highlight-gray" title="Non breakable space"> </span>', // Nbsp highlight
                    ' '        => '<span class="highlight-special highlight-red" title="Thin space"> </span>', // Thin space highlight
                    '…'        => '<span class="highlight-special highlight-gray">…</span>', // Right ellipsis highlight
                    '&hellip;' => '<span class="highlight-special highlight-gray">…</span>', // Right ellipsis highlight
                ],
                '&hellip;  …',
                '<span class="highlight-special highlight-gray">…</span><span class="highlight-special highlight-gray" title="Non breakable space"> </span><span class="highlight-special highlight-red" title="Thin space"> </span><span class="highlight-special highlight-gray">…</span>',
            ],
        ];
    }

    #[DataProvider('multipleStringReplaceDP')]
    public function testmultipleStringReplace($a, $b, $c)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->multipleStringReplace($a, $b), $c);
    }

    public function testHighlightSpecial()
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->highlightSpecial('Foo is bar ; Bar is Foo…'), 'Foo is bar<span class="highlight-special highlight-gray" title="Non breakable space"> </span>;<span class="highlight-special highlight-gray" title="Non breakable space"> </span>Bar is Foo<span class="highlight-special highlight-gray" title="Real ellipsis">…</span>');
        $this
            ->assertSame($obj->highlightSpecial('Foo is bar ; Bar is Foo…', false), 'Foo<span class="highlight-special highlight-space" title="White space"> </span>is<span class="highlight-special highlight-space" title="White space"> </span>bar<span class="highlight-special highlight-gray" title="Non breakable space"> </span>;<span class="highlight-special highlight-gray" title="Non breakable space"> </span>Bar<span class="highlight-special highlight-space" title="White space"> </span>is<span class="highlight-special highlight-space" title="White space"> </span>Foo<span class="highlight-special highlight-gray" title="Real ellipsis">…</span>');
    }

    public static function markStringDP()
    {
        return [
            ['in', '@@missing@@', '@@missing@@'],
            ['cronologia', 'cronologia di navigazione', '←cronologia→ di navigazione'],
            ['cronologia', 'Cronologia di navigazione', '←Cronologia→ di navigazione'],
            ['test', 'Cronologia di navigazione', 'Cronologia di navigazione'],
            ['Überdeckende', 'Überdeckende Popups öffnen', '←Überdeckende→ Popups öffnen'],
            ['überdeckende', 'Überdeckende Popups öffnen', '←Überdeckende→ Popups öffnen'],
            ['Überdeckende', 'überdeckende Popups öffnen', '←überdeckende→ Popups öffnen'],
        ];
    }

    #[DataProvider('markStringDP')]
    public function testMarkString($a, $b, $c)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->markString($a, $b), $c);
    }

    public static function highlightStringDP()
    {
        return [
            ['@@missing@@', '@@missing@@'],
            ['←cronologia→ di navigazione', '<span class=\'highlight\'>cronologia</span> di navigazione'],
            ['←Cronologia→ di navigazione', '<span class=\'highlight\'>Cronologia</span> di navigazione'],
            ['←servi←ce→→', '<span class=\'highlight\'>service</span>'],
            ['Cronologia di navigazione', 'Cronologia di navigazione'],
            ['←←A→dd→ more ←se←a→rch→ ←engine→s…', '<span class=\'highlight\'>Add</span> more <span class=\'highlight\'>search</span> <span class=\'highlight\'>engine</span>s…'],
        ];
    }

    #[DataProvider('highlightStringDP')]
    public function testHighlightString($a, $b)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->highlightString($a), $b);
    }

    public static function getLengthDP()
    {
        return [
            ['Le cheval  blanc ', 17],
            ['મારુ ઘર પાનું બતાવો', 19],
        ];
    }

    #[DataProvider('getLengthDP')]
    public function testGetLength($a, $b)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->getLength($a), $b);
    }

    public static function getSimilarDP()
    {
        return [
            [
                'maison',
                ['maçon', 'melon', 'blanche', 'navet'],
                1,
                ['maçon'],
            ],
            [
                'toto',
                ['maçon', 'melon', 'blanche', 'navet'],
                2,
                ['navet', 'melon'],
            ],
        ];
    }

    #[DataProvider('getSimilarDP')]
    public function testGetSimilar($a, $b, $c, $d)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->getSimilar($a, $b, $c), $d);
    }

    public static function getLevenshteinUTF8DP()
    {
        return [
            ['notre', 'nôtre', 1],
            ['웹', '으', 1],
            ['हिस्सा', 'हमारे', 5],
            ['hello', 'melon', 3],
            ['കട', 'കടല', 1],
            ['കട', 'കല', 1],
            ['കട', 'കടി', 1],
        ];
    }

    #[DataProvider('getLevenshteinUTF8DP')]
    public function testLevenshteinUTF8($a, $b, $c)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->LevenshteinUTF8($a, $b), $c);
    }

    public static function getLevenshteinQualityDP()
    {
        // We use divisions so as to have real precise numbers for float comparison
        return [
            ['notre', 'nôtre', (float) 80],
            ['웹', '으', (float) 0],
            ['हिस्सा', 'हमारे', (float) round(100 / 6, 5)],
            ['hello', 'melon', (float) 40],
            ['കട', 'കടല', (float) round(100 / 1.5, 5)],
            ['കട', 'കല', (float) 50],
            ['കട', 'കടി', (float) round(100 / 1.5, 5)],
        ];
    }

    #[DataProvider('getLevenshteinQualityDP')]
    public function testLevenshteinQuality($a, $b, $c)
    {
        $obj = new Strings();
        $this
            ->assertSame($obj->levenshteinQuality($a, $b), $c);
    }
}
