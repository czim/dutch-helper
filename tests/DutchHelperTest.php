<?php
namespace Czim\DutchHelper\Test;

use Czim\DutchHelper\DutchHelper;

class DutchHelperTest extends TestCase
{
    /**
     * @var DutchHelper
     */
    protected $dutch;

    public function setUp()
    {
        $this->dutch = new DutchHelper();
    }

    /**
     * @test
     */
    function it_pluralizes_and_singularizes_basic_words()
    {
        $words = [

            [ 'fiets', 'fietsen' ],
            [ 'bedrag', 'bedragen' ],
            [ 'fles', 'flessen' ],
            [ 'raaf', 'raven' ],
            [ 'hoef', 'hoeven' ],
            [ 'museum', 'musea' ],
            [ 'kanon', 'kanonnen' ],
            [ 'bal', 'ballen' ],
            [ 'bof', 'boffen' ],
            [ 'lijst', 'lijsten' ],
            [ 'baard', 'baarden' ],
            [ 'computer', 'computers' ],
            [ 'lepel', 'lepels' ],
            [ 'pdf', 'pdfs' ],
            [ 'raad', 'raden' ],
            [ 'stank', 'stanken' ],
            // exceptions
            [ 'ei', 'eieren' ],
            [ 'kalf', 'kalveren' ],
            [ 'paragraaf', 'paragrafen' ],
            [ 'pardon', 'pardons' ],
            [ 'auteur', 'auteurs' ],
            [ 'coureur', 'coureurs' ],
        ];

        foreach ($words as $forms) {

            foreach ($forms as $word) {

                $this->assertEquals(
                    $forms[0],
                    $this->dutch->singularize($word),
                    "Singular form for '$word' is incorrect."
                );

                $this->assertEquals(
                    $forms[1],
                    $this->dutch->pluralize($word),
                    "Plural form for '$word' is incorrect."
                );
            }
        }
    }

    /**
     * @test
     */
    function it_pluralizes_and_singularizes_last_words_in_longer_strings()
    {
        $words = [

            [ 'zeer lange fiets', 'zeer lange fietsen' ],
            [ 'geluksbedrag', 'geluksbedragen' ],
            [ 'lucht-fles', 'lucht-flessen' ],
            [ 'bonte_raaf', 'bonte_raven' ],
            [ 'kaartSpel', 'kaartSpellen' ],
            [ 'kippen ei', 'kippen eieren' ],
            [ 'kippen-oog', 'kippen-ogen' ],
        ];

        foreach ($words as $forms) {

            foreach ($forms as $word) {

                $this->assertEquals(
                    $forms[0],
                    $this->dutch->singularize($word),
                    "Singular form for '$word' is incorrect."
                );

                $this->assertEquals(
                    $forms[1],
                    $this->dutch->pluralize($word),
                    "Plural form for '$word' is incorrect."
                );
            }
        }
    }
}
