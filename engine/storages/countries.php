<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class CountriesData
{
	static public $data = array(
		'AF' => 'Afghanistan (افغانستان)',
		'AX' => 'Åland Islands',
		'AL' => 'Albania (Shqipëria)',
		'DZ' => 'Algeria (الجزائر)',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua and Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia (Հայաստան)',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria (Österreich)',
		'AZ' => 'Azerbaijan (Azərbaycan)',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain (البحرين)',
		'BD' => 'Bangladesh (বাংলাদেশ)',
		'BB' => 'Barbados',
		'BY' => 'Belarus (Белару́сь)',
		'BE' => 'Belgium (België)',
		'BZ' => 'Belize',
		'BJ' => 'Benin (Bénin)',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan (འབྲུག་ཡུལ)',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia and Herzegovina (Bosna i Hercegovina)',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil (Brasil)',
		'IO' => 'British Indian Ocean Territory',
		'VG' => 'British Virgin Islands',
		'BN' => 'Brunei (Brunei Darussalam)',
		'BG' => 'Bulgaria (България)',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi (Uburundi)',
		'KH' => 'Cambodia (Kampuchea)',
		'CM' => 'Cameroon (Cameroun)',
		'CA' => 'Canada',
		'CV' => 'Cape Verde (Cabo Verde)',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic (Centrafricaine)',
		'TD' => 'Chad (Tchad)',
		'CL' => 'Chile',
		'CN' => 'China (中国)',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos [Keeling] Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros (Comores)',
		'CD' => 'Congo [DRC]',
		'CG' => 'Congo [Republic]',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Côte d’Ivoire',
		'HR' => 'Croatia (Hrvatska)',
		'CU' => 'Cuba',
		'CY' => 'Cyprus (Κυπρος)',
		'CZ' => 'Czech Republic (Česko)',
		'DK' => 'Denmark (Danmark)',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt (مصر)',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea (Guinea Ecuatorial)',
		'ER' => 'Eritrea (Ertra)',
		'EE' => 'Estonia (Eesti)',
		'ET' => 'Ethiopia (Ityop\'iya)',
		'FK' => 'Falkland Islands [Islas Malvinas]',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland (Suomi)',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia (საქართველო)',
		'DE' => 'Germany (Deutschland)',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece (Ελλάς)',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea (Guinée)',
		'GW' => 'Guinea-Bissau (Guiné-Bissau)',
		'GY' => 'Guyana',
		'HT' => 'Haiti (Haïti)',
		'HM' => 'Heard Island and McDonald Islands',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary (Magyarország)',
		'IS' => 'Iceland (Ísland)',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran (ایران)',
		'IQ' => 'Iraq (العراق)',
		'IE' => 'Ireland',
		'IM' => 'Isle of Man',
		'IL' => 'Israel (ישראל)',
		'IT' => 'Italy (Italia)',
		'JM' => 'Jamaica',
		'JP' => 'Japan (日本)',
		'JE' => 'Jersey',
		'JO' => 'Jordan (الاردن)',
		'KZ' => 'Kazakhstan (Қазақстан)',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KW' => 'Kuwait (الكويت)',
		'KG' => 'Kyrgyzstan (Кыргызстан)',
		'LA' => 'Laos (ນລາວ)',
		'LV' => 'Latvia (Latvija)',
		'LB' => 'Lebanon (لبنان)',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libya (ليبيا)',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania (Lietuva)',
		'LU' => 'Luxembourg (Lëtzebuerg)',
		'MO' => 'Macau',
		'MK' => 'Macedonia [FYROM] (Македонија)',
		'MG' => 'Madagascar (Madagasikara)',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives (ގުޖޭއްރާ ޔާއްރިހޫމްޖ)',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania (موريتانيا)',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico (México)',
		'FM' => 'Micronesia',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia (Монгол Улс)',
		'ME' => 'Montenegro (Црна Гора)',
		'MS' => 'Montserrat',
		'MA' => 'Morocco (المغرب)',
		'MZ' => 'Mozambique (Moçambique)',
		'MM' => 'Myanmar [Burma] (Myanmar (Burma))',
		'NA' => 'Namibia',
		'NR' => 'Nauru (Naoero)',
		'NP' => 'Nepal (नेपाल)',
		'NL' => 'Netherlands (Nederland)',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'KP' => 'North Korea (조선)',
		'NO' => 'Norway (Norge)',
		'OM' => 'Oman (عمان)',
		'PK' => 'Pakistan (پاکستان)',
		'PW' => 'Palau (Belau)',
		'PS' => 'Palestinian Territories',
		'PA' => 'Panama (Panamá)',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru (Perú)',
		'PH' => 'Philippines (Pilipinas)',
		'PN' => 'Pitcairn Islands',
		'PL' => 'Poland (Polska)',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar (قطر)',
		'RE' => 'Réunion',
		'RO' => 'Romania (România)',
		'RU' => 'Russia (Россия)',
		'RW' => 'Rwanda',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts and Nevis',
		'LC' => 'Saint Lucia',
		'PM' => 'Saint Pierre and Miquelon',
		'VC' => 'Saint Vincent and the Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'São Tomé and Príncipe',
		'SA' => 'Saudi Arabia (المملكة العربية السعودية)',
		'SN' => 'Senegal (Sénégal)',
		'RS' => 'Serbia (Србија)',
		'CS' => 'Serbia and Montenegro (Србија и Црна Гора)',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore (Singapura)',
		'SK' => 'Slovakia (Slovensko)',
		'SI' => 'Slovenia (Slovenija)',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia (Soomaaliya)',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia and the South Sandwich Islands',
		'KR' => 'South Korea (한국)',
		'ES' => 'Spain (España)',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan (السودان)',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard and Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden (Sverige)',
		'CH' => 'Switzerland (Schweiz)',
		'SY' => 'Syria (سوريا)',
		'TW' => 'Taiwan (台灣)',
		'TJ' => 'Tajikistan (Тоҷикистон)',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand (ราชอาณาจักรไทย)',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad and Tobago',
		'TN' => 'Tunisia (تونس)',
		'TR' => 'Turkey (Türkiye)',
		'TM' => 'Turkmenistan (Türkmenistan)',
		'TC' => 'Turks and Caicos Islands',
		'TV' => 'Tuvalu',
		'UM' => 'U.S. Minor Outlying Islands',
		'VI' => 'U.S. Virgin Islands',
		'UG' => 'Uganda',
		'UA' => 'Ukraine (Україна)',
		'AE' => 'United Arab Emirates (الإمارات العربيّة المتّحدة)',
		'GB' => 'United Kingdom',
		'US' => 'United States',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan (O\'zbekiston)',
		'VU' => 'Vanuatu',
		'VA' => 'Vatican City (Città del Vaticano)',
		'VE' => 'Venezuela',
		'VN' => 'Vietnam (Việt Nam)',
		'WF' => 'Wallis and Futuna',
		'EH' => 'Western Sahara (الصحراء الغربية)',
		'YE' => 'Yemen (اليمن)',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	);

	static public function get($key)
	{
		if (!isset(self::$data[$key]))
		{
			return false;
		}
		
		return self::$data[$key];
	}
}
