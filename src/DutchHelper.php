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
    const CONSONANT             = '[bcdfghjklmnpqrstvwxyz]';
    const VOWEL                 = '[aeiou]';
    const VOWEL_EXCEPT_I        = '[aeou]';
    const DOUBLE_SAME_VOWEL     = 'aa|ee|oo|uu';
    const DOUBLE_SAME_CONSONANT = 'bb|dd|ff|kk|ll|mm|nn|pp|rr|ss|tt';
    //const DIPHTHONG         = 'oe|eu|ui|ie|ei|ij';

    // V = VOWEL
    // V!I = VOWEL but not I
    // C = CONSONANT
    // DSV = DOUBLE SAME VOWEL
    // DSV = DOUBLE SAME CONSONANT

    protected $endings = [

        // common special
        'ties?'      => ['tie', 'ties'],
        'ie([eë]n)?' => ['ie', 'ieën'],

        // english
        '(ea|ai|ia)([dlmr])s?' => ['\\1\\2', '\\1\\2s'],
        'ayout(s)?' => ['ayout', 'ayouts'],
        '(V)ys?'    => ['\\1y', '\\1ys'],
        'ss(es)?'   => ['ss', 'sses'],
        '(C)end'    => ['\\1end', '\\1ends'],
        'chats?'    => ['chat', 'chats'],
        'shops?'    => ['shop', 'shops'],
        'tags?'     => ['tag', 'tags'],

        // french
        '(pardon|coupon)s?' => ['\\1', '\\1s'],
        '(nn|V[cpqt])uis?'  => ['\\1ui', '\\1uis'],
        'eaus?' => [ 'eau', 'eaus' ],

        // wortel -> wortels
        // partner -> partners
        '(V)(C{1,3})e([rlm])s?' => [ '\\1\\2e\\3', '\\1\\2e\\3s' ],

        // adres -> adressen
        '(adres|bordes|les)(sen)?' => ['\\1', '\\1sen'],

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

        // exception: paragrafen / parafen / typen
        'paragra(af|ven)' => ['paragraaf', 'paragrafen'],
        'para(af|ven)'    => ['paraaf', 'parafen'],
        'auteurs?'        => ['auteur', 'auteurs'],
        'typen?'          => ['type', 'typen'],

        // hoes -> hoezen
        // graaf -> graven
        // aas -> azen
        '(ie|oe|eu|ui|ei|ij)(f|ven)'     => ['\\1f', '\\1ven'],
        '(ie|oe|eu|ui|ei|ij|iel)(s|zen)' => ['\\1s', '\\1zen'],
        '(DSV)f' => ['\\1f', ':SINGLE:ven'],
        '(V)ven' => ['\\1\\1f', '\\1ven'],
        '(DSV)s' => ['\\1s', ':SINGLE:zen'],
        '(V)zen' => ['\\1\\1s', '\\1zen'],

        // groet -> groeten
        // kleur -> kleuren
        '(oe|eu|ie|ij|ou)(C)(en)?' => ['\\1\\2', '\\1\\2en'],

        // aap -> apen etc, loop -> lopen
        // materiaal -> materialen
        '(DSV)([dgklmnprt])'      => ['\\1\\2', ':SINGLE:\\2en'],
        '(C)(V!I)([dgklmnprt])en' => ['\\1\\2\\2\\3', '\\1\\2\\3en'],
        'i(V!I)([dgklmnprt])en'   => ['i\\1\\1\\2', 'i\\1\\2en'],
        '^a([gklpr])en'           => ['aa\\1', 'a\\1en'],
        '^o([gr])en'              => ['oo\\1', 'o\\1en'],


        // graf -> graven
        'gra(f|ven)'  => ['graf', 'graven'],

        // bedrag -> bedragen
        '(V)([g])(en)?'  => ['\\1\\2', '\\1\\2en'],
        // vis -> vissen
        // kanon -> kanonnen
        // do not include 'en' matches here
        '(V)([bdfklmprst])(en)?'      => ['\\1\\2', '\\1\\2\\2en'],
        '(V)([bdfklmnprst])([aoui])n' => ['\\1\\2\\3n', '\\1\\2\\3nnen'],
        '(V)(DSC)en'                  => ['\\1:SINGLE:', '\\1\\2en'],
        // bon -> bonnen
        '^(C)([aeoui])n'  => ['\\1\\2n', '\\1\\2nnen'],

        // ..en fallback assume plural
        '(C)en'  => ['\\1', '\\1en'],

        // tekst -> teksten
        '(V)kst(en)?' => [ '\\1kst', '\\1ksten' ],
        'ijst(en)?' => [ 'ijst', 'ijsten' ],

        // abbreviations and oddities
        '([bcdfghjklmnpqrtvwxyz]{1,3})s?' => [ '\\1', '\\1s' ],
    ];


    /**
     * Pluralizes a string (Dutch-aware)
     *
     * @param string $string
     * @return mixed
     */
    public function pluralize($string)
    {
        $pluralized = $this->findEndingBasedMatch($string);

        if ( $pluralized !== false) return $pluralized;

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
        $singularized = $this->findEndingBasedMatch($string);

        if ( $singularized !== false) return $singularized;

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

            if (strstr($ending, 'DSC')) {
                $specialCase  = static::DOUBLE_SAME_CONSONANT;
            } elseif (strstr($ending, 'DSV')) {
                $specialCase  = static::DOUBLE_SAME_VOWEL;
            }

            $ending = str_replace(
                [
                    'DSC',
                    'DSV',
                    'C',
                    'V!I',
                    'V',
                ],
                [
                    static::DOUBLE_SAME_CONSONANT,
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
                if (    $specialCase == static::DOUBLE_SAME_CONSONANT
                    ||  $specialCase == static::DOUBLE_SAME_VOWEL
                ) {
                    $single = '';

                    // find the double same vowel match, get a single character from it
                    for ($x = count($matches) - 1; $x > 0; $x--) {
                        if (preg_match('#' . $specialCase . '#i', $matches[$x])) {
                            $single = substr($matches[$x], 0, 1);
                            break;
                        }
                    }

                    $fixedForms['singular'] = str_replace(':SINGLE:', $single, $fixedForms['singular']);
                    $fixedForms['plural']   = str_replace(':SINGLE:', $single, $fixedForms['plural']);
                }

                return $fixedForms;
            }
        }

        return false;
    }

}
