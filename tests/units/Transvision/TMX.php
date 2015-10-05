<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\TMX as _TMX;

require_once __DIR__ . '/../bootstrap.php';

class TMX extends atoum\test
{
    public function createDP()
    {
        return [
            [
                [
                    'fr' => [
                            'shared/date/date.properties:month-7-genitive'                             => 'août',
                            'shared/download/download.properties:unsupported_file_type_download_title' => 'Ouverture impossible',
                    ],
                    'en-US' => [
                            'shared/date/date.properties:month-7-genitive'                             => 'August',
                            'shared/download/download.properties:unsupported_file_type_download_title' => 'Unable to open',
                    ],
                ],
                'fr',
                'en-US',
                '<?xml version="1.0" encoding="UTF-8"?>
<tmx version="1.4">
<header o-tmf="plain text" o-encoding="UTF8" adminlang="en" creationdate="' . date('c') . '" creationtoolversion="0.1" creationtool="Transvision" srclang="en-US" segtype="sentence" datatype="plaintext">
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
</tmx>' . "\n",
            ],
        ];
    }

    /**
     * @dataProvider createDP
     */
    public function testCreate($a, $b, $c, $d)
    {
        $obj = new _TMX();
        $this->string($obj->create($a, $b, $c))
                ->isEqualTo($d);
    }

    public function createOmegatDP()
    {
        return [
            [
                [
                    'fr' => [
                            'shared/date/date.properties:month'                                        => '',
                            'shared/date/date.properties:month-7-genitive'                             => 'août',
                            'shared/download/download.properties:unsupported_file_type_download_title' => 'Ouverture impossible',
                    ],
                    'en-US' => [
                            'shared/date/date.properties:month'                                        => 'Open',
                            'shared/date/date.properties:month-7-genitive'                             => 'August',
                            'shared/download/download.properties:unsupported_file_type_download_title' => 'Unable to open',
                    ],
                ],
                'fr',
                'en-US',
                '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE tmx SYSTEM "tmx11.dtd">
<tmx version="1.1">
<header creationtool="Transvision" o-tmf="OmegaT TMX" o-encoding="UTF8" adminlang="EN-US" datatype="plaintext" creationtoolversion="0.1" segtype="paragraph" creationdate="' . date('c') . '" srclang="en-US"></header>
<body>'
. "\n\t" . '<tu>'
. "\n\t\t" . '<prop type="file">shared/date/date.properties</prop>'
. "\n\t\t" . '<prop type="id">month-7-genitive</prop>'
. "\n\t\t" . '<tuv lang="en-US">'
. "\n\t\t\t" . '<seg>August</seg>'
. "\n\t\t" . '</tuv>'
. "\n\t\t" . '<tuv lang="fr">'
. "\n\t\t\t" . '<seg>août</seg>'
. "\n\t\t" . '</tuv>'
. "\n\t" . '</tu>'
. "\n\t" . '<tu>'
. "\n\t\t" . '<prop type="file">shared/download/download.properties</prop>'
. "\n\t\t" . '<prop type="id">unsupported_file_type_download_title</prop>'
. "\n\t\t" . '<tuv lang="en-US">'
. "\n\t\t\t" . '<seg>Unable to open</seg>'
. "\n\t\t" . '</tuv>'
. "\n\t\t" . '<tuv lang="fr">'
. "\n\t\t\t" . '<seg>Ouverture impossible</seg>'
. "\n\t\t" . '</tuv>'
. "\n\t" . '</tu>
</body>
</tmx>' . "\n",
            ],
        ];
    }

    /**
     * @dataProvider createOmegatDP
     */
    public function testCreateOmegat($a, $b, $c, $d)
    {
        $obj = new _TMX();
        $this->string($obj->createOmegat($a, $b, $c))
                ->isEqualTo($d);
    }
}
