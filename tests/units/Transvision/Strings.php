<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Strings as _Strings;

require_once __DIR__ . '/../bootstrap.php';

class Strings extends atoum\test
{
    public function mtrimDP()
    {
        return [
            ['Le cheval  blanc ', 'Le cheval blanc '],
            ['  Le cheval  blanc', ' Le cheval blanc'],
            ['  Le cheval  blanc  ', ' Le cheval blanc '],
            ['Le cheval  blanc', 'Le cheval blanc'],
        ];
    }

    /**
     * @dataProvider mtrimDP
     */
    public function testMtrim($a, $b)
    {
        $obj = new _Strings();
        $this
            ->string($obj->mtrim($a))
                ->isEqualTo($b);
    }

    public function startsWithDP()
    {
        return [
            ['it is raining', 'it', true],
            [' foobar starts with a nasty space', 'foobar', false],
            ['multiple matches test', ['horse', 'multiple'], true],
            ['multiple matches test', ['not', 'there'], false],
        ];
    }

    /**
     * @dataProvider startsWithDP
     */
    public function testStartsWith($a, $b, $c)
    {
        $obj = new _Strings();
        $this
            ->boolean($obj->startsWith($a, $b))
                ->isEqualTo($c);
    }

    public function endsWithDP()
    {
        return [
            ['it is raining', 'ing', true],
            ['foobar ends with a nasty space ', 'space', false],
            ['multiple matches test', ['horse', 'test'], true],
            ['multiple matches test', ['not', 'there'], false],
        ];
    }

    /**
     * @dataProvider endsWithDP
     */
    public function testEndsWith($a, $b, $c)
    {
        $obj = new _Strings();
        $this
            ->boolean($obj->endsWith($a, $b))
                ->isEqualTo($c);
    }

    public function inStringWithDP()
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

    /**
     * @dataProvider inStringWithDP
     */
    public function testInString($a, $b, $c, $d)
    {
        $obj = new _Strings();
        $this
            ->boolean($obj->inString($a, $b, $c))
                ->isEqualTo($d);
    }

    public function multipleStringReplaceDP()
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

    /**
     * @dataProvider multipleStringReplaceDP
     */
    public function testmultipleStringReplace($a, $b, $c)
    {
        $obj = new _Strings();
        $this
            ->string($obj->multipleStringReplace($a, $b))
                ->isEqualTo($c);
    }

    public function testHighlightSpecial()
    {
        $obj = new _Strings();
        $this
            ->string($obj->highlightSpecial('Foo is bar ; Bar is Foo…'))
                ->isEqualTo('Foo is bar<span class="highlight-special highlight-gray" title="Non breakable space"> </span>;<span class="highlight-special highlight-gray" title="Non breakable space"> </span>Bar is Foo<span class="highlight-special highlight-gray" title="Real ellipsis">…</span>');
        $this
            ->string($obj->highlightSpecial('Foo is bar ; Bar is Foo…', false))
                ->isEqualTo('Foo<span class="highlight-special highlight-space" title="White space"> </span>is<span class="highlight-special highlight-space" title="White space"> </span>bar<span class="highlight-special highlight-gray" title="Non breakable space"> </span>;<span class="highlight-special highlight-gray" title="Non breakable space"> </span>Bar<span class="highlight-special highlight-space" title="White space"> </span>is<span class="highlight-special highlight-space" title="White space"> </span>Foo<span class="highlight-special highlight-gray" title="Real ellipsis">…</span>');
    }

    public function markStringDP()
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

    /**
     * @dataProvider markStringDP
     */
    public function testMarkString($a, $b, $c)
    {
        $obj = new _Strings();
        $this
            ->string($obj->markString($a, $b, $c))
                ->isEqualTo($c);
    }

    public function highlightStringDP()
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

    /**
     * @dataProvider highlightStringDP
     */
    public function testHighlightString($a, $b)
    {
        $obj = new _Strings();
        $this
            ->string($obj->highlightString($a))
                ->isEqualTo($b);
    }

    public function getLengthDP()
    {
        return [
            ['Le cheval  blanc ', 17],
            ['મારુ ઘર પાનું બતાવો', 19],
        ];
    }

    /**
     * @dataProvider getLengthDP
     */
    public function testGetLength($a, $b)
    {
        $obj = new _Strings();
        $this
            ->integer($obj->getLength($a))
                ->isEqualTo($b);
    }

    public function getSimilarDP()
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

    /**
     * @dataProvider getSimilarDP
     */
    public function testGetSimilar($a, $b, $c, $d)
    {
        $obj = new _Strings();
        $this
            ->array($obj->getSimilar($a, $b, $c))
                ->isEqualTo($d);
    }

    public function getLevenshteinUTF8DP()
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

    /**
     * @dataProvider getLevenshteinUTF8DP
     */
    public function testLevenshteinUTF8($a, $b, $c)
    {
        $obj = new _Strings();
        $this
            ->integer($obj->LevenshteinUTF8($a, $b))
                ->isEqualTo($c);
    }

    public function getLevenshteinQualityDP()
    {
        // We use divisions so as to have real precise numbers for float comparizon
        return [
            ['notre', 'nôtre', (float) 80],
            ['웹', '으', (float) 0],
            ['हिस्सा', 'हमारे', (float) 100 / 6],
            ['hello', 'melon', (float) 40],
            ['കട', 'കടല', (float) 100 / 1.5],
            ['കട', 'കല', (float) 50],
            ['കട', 'കടി', (float) 100 / 1.5],
        ];
    }

    /**
     * @dataProvider getLevenshteinQualityDP
     */
    public function testLevenshteinQuality($a, $b, $c)
    {
        $obj = new _Strings();
        $this
            ->float($obj->levenshteinQuality($a, $b))
                ->isNearlyEqualTo($c);
    }
}
