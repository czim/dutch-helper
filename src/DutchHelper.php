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
    const DOUBLE_SAME_CONSONANT = 'bb|dd|ff|gg|kk|ll|mm|nn|pp|rr|ss|tt';
    //const DIPHTHONG         = 'oe|eu|ui|ie|ei|ij';

    // V = VOWEL
    // V!I = VOWEL but not I
    // C = CONSONANT
    // DSV = DOUBLE SAME VOWEL
    // DSV = DOUBLE SAME CONSONANT

    protected $endings = [

        // special exceptions
        'ei(eren)?'                => ['ei', 'eieren'],
        'vlo(oi|oien)?'            => ['vlo', 'vlooien'],
        'kal(f|veren)'             => ['kalf', 'kalveren'],
        'media'                    => ['media', 'media'],
        'cris(is|es)'              => ['crisis', 'crises'],
        'tikel(s|en)?'             => ['tikel', 'tikelen'],
        'catalog(us|i)'            => ['catalogus', 'catalogi'],
        'geni(e|us|i[eë]n)'        => ['genius', 'genieën'],
        'aanb(od|iedingen)'        => ['aanbod', 'aanbiedingen'],
        'gel(id|ederen)'           => ['gelid', 'gelederen'],
        'gedrag(ingen)'            => ['gedrag', 'gedragingen'],
        'gen(ot|ietingen)'         => ['genot', 'genietingen'],
        '(adres|bordes)(sen)?'     => ['\\1', '\\1sen'],
        '^(les)(sen)?'             => ['\\1', '\\1sen'],
        '^lof'                        => ['lof', 'lofbetuigingen'],
        'lof((uiting|betuiging)(en))' => ['lof\\2', 'lof\\2en'],

        // common special
        '(\d)s?'        => [ '\\1', '\\1s' ],
        'ties?'         => ['tie', 'ties'],
        'ie([eë]n)?'    => ['ie', 'ieën'],
        'taxi\'?s?'     => ['taxi', 'taxi\'s'],
        // vrede uitzondering!
        '^rede(nen)?'   => ['\\1ede', '\\1edenen'],
        '(C)ende(nen)?' => ['\\1ende', '\\1endenen'],

        // english
        '(url|set|uence|che|age)s?' => [ '\\1', '\\1s' ],
        '(ea|ai|ia)([dlmr])s?' => ['\\1\\2', '\\1\\2s'],
        'ngles?'    => [ 'ngle', 'ngles' ],
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

        // lade -> laden
        // bode -> bodes
        'ade' => ['ade', 'aden'],
        'ode' => ['ode', 'odes'],

        // museum -> musea
        'se(a|um)' => ['seum', 'sea'],

        // boe -> boes
        // koe -> koeien
        // groei -> groeien
        '(boe)(s)?'  => ['\\1', '\\1s'],
        '(oe)(ien)?' => ['\\1', '\\1ien'],
        '(oei)(en)?' => ['\\1', '\\1en'],

        // medium -> mediums
        'iums?' => ['ium', 'iums'],
        // pagina -> pagina's
        'ina(\'s)?' => ['ina', 'ina\'s'],
        // video -> videos
        '([aeo]{2})s?' => ['\\1', '\\1s'],

        // exception: paragrafen / parafen / typen
        'paragra(af|fen)' => ['paragraaf', 'paragrafen'],
        'para(af|fen)'    => ['paraaf', 'parafen'],
        'auteurs?'        => ['auteur', 'auteurs'],
        'coureurs?'       => ['coureur', 'coureurs'],
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

        // bedrag -> bedragen
        'edrag(en)?'  => ['edrag', 'edragen'],

        // dak -> daken
        // pad -> paden (forget about the toad)
        '(dal|dak|pad|slot|vat|weg|aardappel)(en)?' => [ '\\1', '\\1en' ],
        // aap -> apen etc, loop -> lopen
        // materiaal -> materialen
        '(DSV)([dgklmnprt])'      => ['\\1\\2', ':SINGLE:\\2en'],
        '(C)(V!I)([dgklmnprt])en' => ['\\1\\2\\2\\3', '\\1\\2\\3en'],
        'i(V!I)([dgklmnprt])en'   => ['i\\1\\1\\2', 'i\\1\\2en'],
        '^a([gklpr])en'           => ['aa\\1', 'a\\1en'],
        '^o([gr])en'              => ['oo\\1', 'o\\1en'],

        // graf -> graven
        'gra(f|ven)'  => ['graf', 'graven'],

        // vis -> vissen
        // kanon -> kanonnen
        // do not include 'en' matches here
        '(V)([bdfgklmprst])(en)?'      => ['\\1\\2', '\\1\\2\\2en'],
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
        '([bcdfghjklmnpqrtvwxyz]{3})s?' => [ '\\1', '\\1s' ],

        '([aeo])s?' => ['\\1', '\\1s'],
    ];

    /**
     * Whether the string was detected to be camelCased
     * @var bool
     */
    protected $camelCased = false;

    /**
     * Whether debug mode is enabled
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Pluralizes a string (Dutch-aware)
     *
     * @param string $string
     * @return mixed
     */
    public function pluralize($string)
    {
        $this->camelCased = $this->isStringCamelCased($string);

        $matches = $this->findEndingBasedMatch($string);

        if ( $matches !== false) return $matches['plural'];

        if (preg_match('#en$#i', $string)) return $string;

        return $string . 'en';
    }

    /**
     * Singularizes a string (Dutch-aware)
     *
     * @param string $string
     * @return mixed
     */
    public function singularize($string)
    {
        $this->camelCased = $this->isStringCamelCased($string);

        $matches = $this->findEndingBasedMatch($string);

        if ( $matches !== false) return $matches['singular'];

        if (preg_match('#^(.*)en$#i', $string, $matches)) {
            return $matches[1];
        }

        return $string;
    }

    /**
     * Returns the last word/part of the string
     *
     * @param string $string
     * @return string[]     first part, separator, last part
     */
    protected function splitLastWord($string)
    {
        // see if we're dealing with a camelCase string
        if ($this->camelCased && preg_match('#^(.*)([A-Z].*)$#', $string, $matches)) {
            return [ $matches[1], '', $matches[2] ];
        }

        // otherwise, attempt to split at anything that might indicate a word separator
        if (preg_match('#^(.*)([_ \s-])([^_ \s-]+)$#', $string, $matches)) {
            return [ $matches[1], $matches[2], $matches[3] ];
        }

        return [ '', '', $string ];
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
        list($firstPart, $separator, $lastPart) = $this->splitLastWord($string);

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

            if ($this->debug) {
                var_dump('#^(.*)' . $ending . '$#i');
            }

            if (preg_match('#^(.*)' . $ending . '$#i', $lastPart, $matches)) {

                $newSingularEnd  = preg_replace('#' . $ending . '$#i', $forms[0], $lastPart);
                $newPluralizeEnd = preg_replace('#' . $ending . '$#i', $forms[1], $lastPart);

                if ($this->camelCased && strlen($firstPart)) {
                    $newSingularEnd  = ucfirst($newSingularEnd);
                    $newPluralizeEnd = ucfirst($newPluralizeEnd);
                }

                $fixedForms = [
                    'singular' => $firstPart . $separator . $newSingularEnd,
                    'plural'   => $firstPart . $separator . $newPluralizeEnd,
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

    protected function isStringCamelCased($string)
    {
        return (bool) preg_match('#^([a-z0-9]+[A-Z])*[a-z0-9]+$#', $string);
    }

    /**
     * Enables debug mode
     *
     * @param bool $enable
     */
    public function debug($enable = true)
    {
        $this->debug = (bool) $enable;
    }
}
