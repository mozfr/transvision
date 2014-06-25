<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\TMX as _TMX;

require_once __DIR__ . '/../bootstrap.php';

class TMX extends atoum\test
{
    public function dataProviderCreate()
    {
        return [
            [
                [
                    'fr' => [
                            'shared/date/date.properties:month-7-genitive' => 'août',
                            'shared/download/download.properties:unsupported_file_type_download_title' => 'Ouverture impossible'
                    ],
                    'en-US' => [
                            'shared/date/date.properties:month-7-genitive' => 'August',
                            'shared/download/download.properties:unsupported_file_type_download_title' => 'Unable to open'
                    ]
                ],
                'fr',
                'en-US',
                '<?xml version="1.0" encoding="UTF-8"?>
<tmx version="1.4">
<header o-tmf="plain text" o-encoding="UTF8" adminlang="en" creationdate="'. date('c') . '" creationtoolversion="0.1" creationtool="Transvision" srclang="en-US" segtype="sentence" datatype="plaintext">
</header>
<body>'
. "\n\t" . '<tu tuid="shared/date/date.properties:month-7-genitive" srclang="en-US">'
. "\n\t\t" . '<tuv xml:lang="en-US"><seg>August</seg></tuv>'
. "\n\t\t" . '<tuv xml:lang="fr"><seg>août</seg></tuv>'
. "\n\t" . '</tu>'
. "\n\t" . '<tu tuid="shared/download/download.properties:unsupported_file_type_download_title" srclang="en-US">'
. "\n\t\t" . '<tuv xml:lang="en-US"><seg>Unable to open</seg></tuv>'
. "\n\t\t" . '<tuv xml:lang="fr"><seg>Ouverture impossible</seg></tuv>'
. "\n\t" . '</tu>
</body>
</tmx>'. "\n"
            ]
        ];
    }

    /**
     * @dataProvider dataProviderCreate
     */
    public function testCreate($a, $b, $c, $d) {
        $obj = new _TMX();
        $this->string($obj->create($a, $b, $c))
                ->isEqualTo($d);
    }
}
