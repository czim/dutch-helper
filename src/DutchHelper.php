<?php
namespace Czim\DutchHelper;

/**
 * Helper class to deal with the impracticalities of using
 * Dutch names in an English oriented (Laravel) setup. Mainly to help
 * with pluralization / singularization of commonly used module
 * and field names.
 *
 * This is far from perfect and only intended as a simple quick-fix tool
 * mainly aimed at simple database table and attribute naming changes.
 */
class DutchHelper
{
    const CONSONANT         = '[bcdfghjklmnpqrstvwxyz]';
    const VOWEL             = '[aeiou]';
    const VOWEL_EXCEPT_I    = '[aeou]';
    const DOUBLE_SAME_VOWEL = 'aa|ee|oo|uu';
    //const DIPHTHONG         = 'oe|eu|ui|ie|ei|ij';

    // V = VOWEL
    // V!I = VOWEL but not I
    // C = CONSONANT
    // DSV = DOUBLE SAME VOWEL


    protected $endings = [

        // common special
        'ties?'   => ['tie', 'ties'],
        'ie([eë]n)?'   => ['ie', 'ieën'],


        // english
        'ea([dlmr])s?' => [ 'ea\\2', 'ea\\2s'],

        // french
        'pardons?' => [ 'pardon', 'pardons' ],
        '(nn|V[cpqt])uis?' => [ '\\1ui', '\\1uis' ],
        'eaus?' => [ 'eau', 'eaus' ],

        // wortel -> wortels
        // partner -> partners
        '(V)(C{1,3})e([rlm])s?' => [ '\\1\\2e\\3', '\\1\\2e\\3s' ],

        // lade -> laden
        // bode -> bodes
        'ade' => ['ade', 'aden'],
        'ode' => ['ode', 'odes'],

        // boe -> boes
        // koe -> koeien
        // groei -> groeien
        '(boe)(s)?'  => ['\\1', '\\1s'],
        '(oe)(ien)?' => ['\\1', '\\1ien'],
        '(oei)(en)?' => ['\\1', '\\1en'],

        // pagina -> pagina's
        'ina(\'s)?' => ['ina', 'ina\'s'],
        // video -> videos
        '([aeo])s?' => ['\\1', '\\1s'],

        // exception: paragrafen / parafen
        'paragra(af|ven)' => ['paragraaf', 'paragrafen'],
        'para(af|ven)'    => ['paraaf', 'parafen'],

        // hoes -> hoezen
        // graaf -> graven
        // aas -> azen
        '(ie|oe|eu|ui|ei|ij)(f|ven)' => ['\\1f', '\\1ven'],
        '(ie|oe|eu|ui|ei|ij|iel)(s|zen)' => ['\\1s', '\\1zen'],
        '(DSV)f' => ['\\1f', ':SINGLE:ven'],
        '(V)ven' => ['\\1\\1f', '\\1ven'],
        '(DSV)s' => ['\\1s', ':SINGLE:zen'],
        '(V)zen' => ['\\1\\1s', '\\1zen'],

        // groet -> groeten
        // kleur -> kleuren
        '(oe|eu|ie)(C)(en)?' => ['\\1\\2', '\\1\\2en'],

        // aap -> apen etc, loop -> lopen
        '(DSV)([dgklmnprt])' => ['\\1\\2', ':SINGLE:\\2en'],
        '(C)(V!I)([dgklmnprt])en'  => ['\\1\\2\\2\\3', '\\1\\2\\3en'],
        '^a([gklpr])en'  => ['\\1\\1\\2', 'a\\2\\3en'],
        '^o([gr])en'  => ['\\1\\1\\2', 'o\\2\\3en'],

        // graf -> graven

        // bedrag -> bedragen
        '(V)([g])(en)?'  => ['\\1\\2', '\\1\\2en'],
        // vis -> vissen
        // kanon -> kanonnen
        // do not include 'en' matches here
        '(V)([bdfklmprst])(en)?'  => ['\\1\\2', '\\1\\2\\2en'],
        '(V)([bdfklmnprst])([aoui])n'  => ['\\1\\2\\3n', '\\1\\2\\3nnen'],
        // bon -> bonnen
        '^(C)([aeoui])n'  => ['\\1\\2n', '\\1\\2nnen'],

        // ..en fallback assume plural
        '(C)en'  => ['\\1', '\\1en'],
    ];


    /**
     * Pluralizes a string (Dutch-aware)
     *
     * @param string $string
     * @return mixed
     */
    public function pluralize($string)
    {
        dd( $this->findEndingBasedMatch($string) );


        // detect commonly used terms and exceptions
        // detect typical word-endings
        // fallback

        if ( ! preg_match('#en$#i', $string)) {
            return $string . 'en';
        }

        return $string;
    }

    /**
     * Singularizes a string (Dutch-aware)
     *
     * @param string $string
     * @return mixed
     */
    public function singularize($string)
    {

        if (preg_match('#^(.*)en$#i', $string, $matches)) {
            return $matches[1];
        }

        return $string;
    }

    /**
     * Returns matching plural/singular version based on ending match
     * or false if no matches found.
     *
     * @param string $string
     * @return array|bool
     */
    protected function findEndingBasedMatch($string)
    {
        foreach ($this->endings as $ending => $forms) {

            // keep track of some special cases, so we
            // may better handle replacements
            $specialCase = null;

            if (strstr($ending, 'DSV')) {
                $specialCase  = static::DOUBLE_SAME_VOWEL;
            }

            $ending = str_replace(
                [
                    'DSV',
                    'C',
                    'V!I',
                    'V',
                ],
                [
                    static::DOUBLE_SAME_VOWEL,
                    static::CONSONANT,
                    static::VOWEL_EXCEPT_I,
                    static::VOWEL,
                ],
                $ending
            );

            var_dump( '#^(.*)' . $ending . '$#i' );

            if (preg_match('#^(.*)' . $ending . '$#i', $string, $matches)) {

                $fixedForms = [
                    'singular' => preg_replace('#' . $ending . '$#i', $forms[0], $string),
                    'plural'   => preg_replace('#' . $ending . '$#i', $forms[1], $string),
                ];

                // handle exceptions
                if ($specialCase == static::DOUBLE_SAME_VOWEL) {

                    $single = '';

                    // find the double same vowel match, get a single character from it
                    for ($x = count($matches) - 1; $x > 0; $x--) {
                        if (preg_match('#' . static::DOUBLE_SAME_VOWEL . '#i', $matches[$x])) {
                            $single = substr($matches[$x], 0, 1);
                            break;
                        }
                    }

                    $fixedForms['plural'] = str_replace(':SINGLE:', $single, $fixedForms['plural']);
                }

                return $fixedForms;
            }
        }

        return false;
    }

}
