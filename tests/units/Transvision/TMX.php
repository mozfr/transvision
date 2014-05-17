<?php
namespace Transvision\tests\units;
use atoum;

require_once __DIR__ . '/../bootstrap.php';

class TMX extends atoum\test
{
    public function dataProviderCreate()
    {
        return array(
            array(
                array('fr' => array(
                            'shared/date/date.properties:month-7-genitive' => 'août',
                            'shared/download/download.properties:unsupported_file_type_download_title' => 'Ouverture impossible'
                        ),
                      'en-US' => array(
                            'shared/date/date.properties:month-7-genitive' => 'August',
                            'shared/download/download.properties:unsupported_file_type_download_title' => 'Unable to open'
                        )
                    ),
                'fr',
                'en-US',
                '<?xml version="1.0" encoding="UTF-8"?>
<tmx version="1.4">
<header o-tmf="plain text" o-encoding="UTF8" adminlang="en" creationdate="'. date('c') . '" creationtoolversion="0.1" creationtool="Transvision" srclang="en-US" segtype="sentence" datatype="plaintext">
</header>
<body>
    <tu tuid="shared/date/date.properties:month-7-genitive" srclang="en-US">
        <tuv xml:lang="en-US"><seg>August</seg></tuv>
        <tuv xml:lang="fr"><seg>août</seg></tuv>
    </tu>
    <tu tuid="shared/download/download.properties:unsupported_file_type_download_title" srclang="en-US">
        <tuv xml:lang="en-US"><seg>Unable to open</seg></tuv>
        <tuv xml:lang="fr"><seg>Ouverture impossible</seg></tuv>
    </tu>
</body>
</tmx>'
            )
        );
    }


    /**
     * @dataProvider dataProviderCreate
     */
    public function testCreate($a, $b, $c, $d) {
        $obj = new \Transvision\TMX();
        $this->string($obj->create($a, $b, $c))
                ->isEqualTo($d);
    }
}
