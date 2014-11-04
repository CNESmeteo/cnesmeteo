<?php namespace CnesMeteo\Localization\Updates;

use October\Rain\Database\Updates\Seeder;
use CnesMeteo\Localization\Models\Language;
use CnesMeteo\Localization\Models\Country;
use CnesMeteo\Localization\Models\State;
use CnesMeteo\Localization\Models\Province;

class SeedAllTables extends Seeder
{

    public function run()
    {
        /*
         * LANGUAGES
         */
        Language::insert([
            ["enabled" => true,  "code" => "en" , "name" =>  'English'],
            ["enabled" => false, "code" => "aa" , "name" =>  'Afar'],
            ["enabled" => false, "code" => "ab" , "name" =>  'Abkhazian'],
            ["enabled" => false, "code" => "af" , "name" =>  'Afrikaans'],
            ["enabled" => false, "code" => "am" , "name" =>  'Amharic'],
            ["enabled" => false, "code" => "ar" , "name" =>  'Arabic'],
            ["enabled" => false, "code" => "as" , "name" =>  'Assamese'],
            ["enabled" => false, "code" => "ay" , "name" =>  'Aymara'],
            ["enabled" => false, "code" => "az" , "name" =>  'Azerbaijani'],
            ["enabled" => false, "code" => "ba" , "name" =>  'Bashkir'],
            ["enabled" => false, "code" => "be" , "name" =>  'Byelorussian'],
            ["enabled" => false, "code" => "bg" , "name" =>  'Bulgarian'],
            ["enabled" => false, "code" => "bh" , "name" =>  'Bihari'],
            ["enabled" => false, "code" => "bi" , "name" =>  'Bislama'],
            ["enabled" => false, "code" => "bn" , "name" =>  'Bengali/Bangla'],
            ["enabled" => false, "code" => "bo" , "name" =>  'Tibetan'],
            ["enabled" => false, "code" => "br" , "name" =>  'Breton'],
            ["enabled" => false, "code" => "ca" , "name" =>  'Catalan'],
            ["enabled" => false, "code" => "co" , "name" =>  'Corsican'],
            ["enabled" => false, "code" => "cs" , "name" =>  'Czech'],
            ["enabled" => false, "code" => "cy" , "name" =>  'Welsh'],
            ["enabled" => false, "code" => "da" , "name" =>  'Danish'],
            ["enabled" => false, "code" => "de" , "name" =>  'German'],
            ["enabled" => false, "code" => "dz" , "name" =>  'Bhutani'],
            ["enabled" => false, "code" => "el" , "name" =>  'Greek'],
            ["enabled" => false, "code" => "eo" , "name" =>  'Esperanto'],
            ["enabled" => true,  "code" => "es" , "name" =>  'Spanish'],
            ["enabled" => false, "code" => "et" , "name" =>  'Estonian'],
            ["enabled" => false, "code" => "eu" , "name" =>  'Basque'],
            ["enabled" => false, "code" => "fa" , "name" =>  'Persian'],
            ["enabled" => false, "code" => "fi" , "name" =>  'Finnish'],
            ["enabled" => false, "code" => "fj" , "name" =>  'Fiji'],
            ["enabled" => false, "code" => "fo" , "name" =>  'Faeroese'],
            ["enabled" => true,  "code" => "fr" , "name" =>  'French'],
            ["enabled" => false, "code" => "fy" , "name" =>  'Frisian'],
            ["enabled" => false, "code" => "ga" , "name" =>  'Irish'],
            ["enabled" => false, "code" => "gd" , "name" =>  'Scots/Gaelic'],
            ["enabled" => false, "code" => "gl" , "name" =>  'Galician'],
            ["enabled" => false, "code" => "gn" , "name" =>  'Guarani'],
            ["enabled" => false, "code" => "gu" , "name" =>  'Gujarati'],
            ["enabled" => false, "code" => "ha" , "name" =>  'Hausa'],
            ["enabled" => false, "code" => "hi" , "name" =>  'Hindi'],
            ["enabled" => false, "code" => "hr" , "name" =>  'Croatian'],
            ["enabled" => false, "code" => "hu" , "name" =>  'Hungarian'],
            ["enabled" => false, "code" => "hy" , "name" =>  'Armenian'],
            ["enabled" => false, "code" => "ia" , "name" =>  'Interlingua'],
            ["enabled" => false, "code" => "ie" , "name" =>  'Interlingue'],
            ["enabled" => false, "code" => "ik" , "name" =>  'Inupiak'],
            ["enabled" => false, "code" => "in" , "name" =>  'Indonesian'],
            ["enabled" => false, "code" => "is" , "name" =>  'Icelandic'],
            ["enabled" => false, "code" => "it" , "name" =>  'Italian'],
            ["enabled" => false, "code" => "iw" , "name" =>  'Hebrew'],
            ["enabled" => false, "code" => "ja" , "name" =>  'Japanese'],
            ["enabled" => false, "code" => "ji" , "name" =>  'Yiddish'],
            ["enabled" => false, "code" => "jw" , "name" =>  'Javanese'],
            ["enabled" => false, "code" => "ka" , "name" =>  'Georgian'],
            ["enabled" => false, "code" => "kk" , "name" =>  'Kazakh'],
            ["enabled" => false, "code" => "kl" , "name" =>  'Greenlandic'],
            ["enabled" => false, "code" => "km" , "name" =>  'Cambodian'],
            ["enabled" => false, "code" => "kn" , "name" =>  'Kannada'],
            ["enabled" => false, "code" => "ko" , "name" =>  'Korean'],
            ["enabled" => false, "code" => "ks" , "name" =>  'Kashmiri'],
            ["enabled" => false, "code" => "ku" , "name" =>  'Kurdish'],
            ["enabled" => false, "code" => "ky" , "name" =>  'Kirghiz'],
            ["enabled" => false, "code" => "la" , "name" =>  'Latin'],
            ["enabled" => false, "code" => "ln" , "name" =>  'Lingala'],
            ["enabled" => false, "code" => "lo" , "name" =>  'Laothian'],
            ["enabled" => false, "code" => "lt" , "name" =>  'Lithuanian'],
            ["enabled" => false, "code" => "lv" , "name" =>  'Latvian/Lettish'],
            ["enabled" => false, "code" => "mg" , "name" =>  'Malagasy'],
            ["enabled" => false, "code" => "mi" , "name" =>  'Maori'],
            ["enabled" => false, "code" => "mk" , "name" =>  'Macedonian'],
            ["enabled" => false, "code" => "ml" , "name" =>  'Malayalam'],
            ["enabled" => false, "code" => "mn" , "name" =>  'Mongolian'],
            ["enabled" => false, "code" => "mo" , "name" =>  'Moldavian'],
            ["enabled" => false, "code" => "mr" , "name" =>  'Marathi'],
            ["enabled" => false, "code" => "ms" , "name" =>  'Malay'],
            ["enabled" => false, "code" => "mt" , "name" =>  'Maltese'],
            ["enabled" => false, "code" => "my" , "name" =>  'Burmese'],
            ["enabled" => false, "code" => "na" , "name" =>  'Nauru'],
            ["enabled" => false, "code" => "ne" , "name" =>  'Nepali'],
            ["enabled" => false, "code" => "nl" , "name" =>  'Dutch'],
            ["enabled" => false, "code" => "no" , "name" =>  'Norwegian'],
            ["enabled" => false, "code" => "oc" , "name" =>  'Occitan'],
            ["enabled" => false, "code" => "om" , "name" =>  '(Afan)/Oromoor/Oriya'],
            ["enabled" => false, "code" => "pa" , "name" =>  'Punjabi'],
            ["enabled" => false, "code" => "pl" , "name" =>  'Polish'],
            ["enabled" => false, "code" => "ps" , "name" =>  'Pashto/Pushto'],
            ["enabled" => false, "code" => "pt" , "name" =>  'Portuguese'],
            ["enabled" => false, "code" => "qu" , "name" =>  'Quechua'],
            ["enabled" => false, "code" => "rm" , "name" =>  'Rhaeto-Romance'],
            ["enabled" => false, "code" => "rn" , "name" =>  'Kirundi'],
            ["enabled" => false, "code" => "ro" , "name" =>  'Romanian'],
            ["enabled" => false, "code" => "ru" , "name" =>  'Russian'],
            ["enabled" => false, "code" => "rw" , "name" =>  'Kinyarwanda'],
            ["enabled" => false, "code" => "sa" , "name" =>  'Sanskrit'],
            ["enabled" => false, "code" => "sd" , "name" =>  'Sindhi'],
            ["enabled" => false, "code" => "sg" , "name" =>  'Sangro'],
            ["enabled" => false, "code" => "sh" , "name" =>  'Serbo-Croatian'],
            ["enabled" => false, "code" => "si" , "name" =>  'Singhalese'],
            ["enabled" => false, "code" => "sk" , "name" =>  'Slovak'],
            ["enabled" => false, "code" => "sl" , "name" =>  'Slovenian'],
            ["enabled" => false, "code" => "sm" , "name" =>  'Samoan'],
            ["enabled" => false, "code" => "sn" , "name" =>  'Shona'],
            ["enabled" => false, "code" => "so" , "name" =>  'Somali'],
            ["enabled" => false, "code" => "sq" , "name" =>  'Albanian'],
            ["enabled" => false, "code" => "sr" , "name" =>  'Serbian'],
            ["enabled" => false, "code" => "ss" , "name" =>  'Siswati'],
            ["enabled" => false, "code" => "st" , "name" =>  'Sesotho'],
            ["enabled" => false, "code" => "su" , "name" =>  'Sundanese'],
            ["enabled" => false, "code" => "sv" , "name" =>  'Swedish'],
            ["enabled" => false, "code" => "sw" , "name" =>  'Swahili'],
            ["enabled" => false, "code" => "ta" , "name" =>  'Tamil'],
            ["enabled" => false, "code" => "te" , "name" =>  'Tegulu'],
            ["enabled" => false, "code" => "tg" , "name" =>  'Tajik'],
            ["enabled" => false, "code" => "th" , "name" =>  'Thai'],
            ["enabled" => false, "code" => "ti" , "name" =>  'Tigrinya'],
            ["enabled" => false, "code" => "tk" , "name" =>  'Turkmen'],
            ["enabled" => false, "code" => "tl" , "name" =>  'Tagalog'],
            ["enabled" => false, "code" => "tn" , "name" =>  'Setswana'],
            ["enabled" => false, "code" => "to" , "name" =>  'Tonga'],
            ["enabled" => false, "code" => "tr" , "name" =>  'Turkish'],
            ["enabled" => false, "code" => "ts" , "name" =>  'Tsonga'],
            ["enabled" => false, "code" => "tt" , "name" =>  'Tatar'],
            ["enabled" => false, "code" => "tw" , "name" =>  'Twi'],
            ["enabled" => false, "code" => "uk" , "name" =>  'Ukrainian'],
            ["enabled" => false, "code" => "ur" , "name" =>  'Urdu'],
            ["enabled" => false, "code" => "uz" , "name" =>  'Uzbek'],
            ["enabled" => false, "code" => "vi" , "name" =>  'Vietnamese'],
            ["enabled" => false, "code" => "vo" , "name" =>  'Volapuk'],
            ["enabled" => false, "code" => "wo" , "name" =>  'Wolof'],
            ["enabled" => false, "code" => "xh" , "name" =>  'Xhosa'],
            ["enabled" => false, "code" => "yo" , "name" =>  'Yoruba'],
            ["enabled" => false, "code" => "zh" , "name" =>  'Chinese'],
            ["enabled" => false, "code" => "zu" , "name" =>  'Zulu']
        ]);







        /*
         * PROVINCES
         */
        // TODO

    }

}
