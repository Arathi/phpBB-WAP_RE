<?php
/***************************************************************************
 *                          functions_translit.php
 *                            -------------------
 *  	Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *            2010 год
 *		简体中文：爱疯的云
 ***************************************************************************/

function translit($text_to_translit)
{
$trans = array(
		"Sch" => "Щ", 	"sch" => "щ",
		"Yu" => "Ю",	"yu" => "ю",
		"Ju" => "Ю",	"ju" => "ю",
		"Ya" => "Я",	"ya" => "я",
		"Ja" => "Я",	"ja" => "я",
		"Yo" => "Ё",	"yo" => "ё",
		"Jo" => "Ё",	"jo" => "ё",
		"Zh" => "Ж",	"zh" => "ж",
		"Ch" => "Ч",	"ch" => "ч",
		"Sh" => "Ш",	"sh" => "ш",
		"Ts" => "Ц",	"ts" => "ц",
		"E\'" => "Э",	"e\'" => "э",
		"A"=>"А",	"a"=>"а",
		"B"=>"Б",	"b"=>"б",
		"C"=>"Ц",	"c"=>"ц",
		"D"=>"Д",	"d"=>"д",
		"E"=>"Е",	"e"=>"е",
		"F"=>"Ф",	"f"=>"ф",
		"G"=>"Г",	"g"=>"г",
		"H"=>"Х",	"h"=>"х",
		"I"=>"И",		"i"=>"и",
		"J"=>"Й",		"j"=>"й",
		"K"=>"К",	"k"=>"к",
		"L"=>"Л",	"l"=>"л",
		"M"=>"М",	"m"=>"м",
		"N"=>"Н",	"n"=>"н",
		"O"=>"О",	"o"=>"о",
		"P"=>"П",	"p"=>"п",
		"R"=>"Р",	"r"=>"р",
		"S"=>"С",	"s"=>"с",
		"T"=>"Т",	"t"=>"т",
		"U"=>"У",	"u"=>"у",
		"V"=>"В",	"v"=>"в",
		"W"=>"В",	"w"=>"в",
		"Y"=>"Ы",	"y"=>"ы",
		"Z"=>"З",	"z"=>"з",
		"''"=>"Ь",	"'"=>"ь",
		"``"=>"Ъ",	"`"=>"ъ",

		"~"=>"");

foreach ($trans as $lat => $rus) { $text_to_translit = str_replace($lat, $trans[$lat], $text_to_translit); }
return $text_to_translit;
}
?>