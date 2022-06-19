<?php

function isFactoryClosed($now = null){
  if(!($now instanceof \Carbon)){
    $now = \Carbon::now();
  }
  $close_time = \Carbon::createMidnightDate($now->year,$now->month,$now->day)->set('hour',17);
  return (int) ($now > $close_time && $now->isWeekday());
}
function isFactoryClosing(){
  $now = \Carbon::now();
  $close_time = \Carbon::createMidnightDate()->set('hour',16)->set('minute',45);
  return (int) ($now > $close_time && $now->isWeekday());
}

function only_numbers($s){
  return preg_replace('/[^0-9]/',"",$s);
}

function customRequestCaptcha(){
    return new \ReCaptcha\RequestMethod\Post();
}
function formatMoney($money){
  return 'R$'.number_format($money, 2, ',', '.');
}

function getNextBusinessDay($days = 0, $date = null,$format = "Y-m-d"){
  if(!($date instanceof \Carbon)){
    $date = \Carbon::now();
  }
  
  $days += (int) !$date->isWeekday();
  $date = $date->addWeekdays($days);
  
  $holydays =  getHolydays(); 
  
  foreach($holydays as $holyday)
  {
    $timestamp =  date('Y').$holyday." ".date('H:i:s');
    $holyday = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, 'America/Sao_Paulo');  
    if($date == $holyday)
    {  
      $date->add(1, 'day');
      // if($date->format('D') == 'Sat' || $date->format('D') == 'Sun') $days -= 2;  
      // $date = getNextBusinessDay($days,$date);
      return $date; 
    }
  }

  return $date->format($format);
}

function getHolydays()
{ 
  return ["-11-20","-12-24","-12-25","-12-30","-12-31","-01-01", "-02-24", "-02-25", "-02-26", "-04-10", "-04-21", "-04-23", "-05-01","-06-11","-09-07","-10-12","-06-03"];
}

function hasRule($n){
  if(strstr($n,'<') || strstr($n,'>')){
    return true;
  }
}

function debug_log(string $path, string $message, $context = []){
  @mkdir(storage_path("logs/".$path,0755,true));
  \Log::channel('debug')->debug("\{$path} $message",$context);
}

function normalize( $s)
{
    // Normalizer-class missing!
    if (! class_exists("Normalizer", $autoload = false))
        return $s;


    // maps German (umlauts) and other European characters onto two characters before just removing diacritics
    $s    = preg_replace( '@\x{00c4}@u'    , "AE",    $s );    // umlaut Ä => AE
    $s    = preg_replace( '@\x{00d6}@u'    , "OE",    $s );    // umlaut Ö => OE
    $s    = preg_replace( '@\x{00dc}@u'    , "UE",    $s );    // umlaut Ü => UE
    $s    = preg_replace( '@\x{00e4}@u'    , "ae",    $s );    // umlaut ä => ae
    $s    = preg_replace( '@\x{00f6}@u'    , "oe",    $s );    // umlaut ö => oe
    $s    = preg_replace( '@\x{00fc}@u'    , "ue",    $s );    // umlaut ü => ue
    $s    = preg_replace( '@\x{00f1}@u'    , "ny",    $s );    // ñ => ny
    $s    = preg_replace( '@\x{00ff}@u'    , "yu",    $s );    // ÿ => yu


    // maps special characters (characters with diacritics) on their base-character followed by the diacritical mark
        // exmaple:  Ú => U´,  á => a`
    $s    = Normalizer::normalize( $s, Normalizer::FORM_D );


    $s    = preg_replace( '@\pM@u'        , "",    $s );    // removes diacritics


    $s    = preg_replace( '@\x{00df}@u'    , "ss",    $s );    // maps German ß onto ss
    $s    = preg_replace( '@\x{00c6}@u'    , "AE",    $s );    // Æ => AE
    $s    = preg_replace( '@\x{00e6}@u'    , "ae",    $s );    // æ => ae
    $s    = preg_replace( '@\x{0132}@u'    , "IJ",    $s );    // ? => IJ
    $s    = preg_replace( '@\x{0133}@u'    , "ij",    $s );    // ? => ij
    $s    = preg_replace( '@\x{0152}@u'    , "OE",    $s );    // Œ => OE
    $s    = preg_replace( '@\x{0153}@u'    , "oe",    $s );    // œ => oe

    $s    = preg_replace( '@\x{00d0}@u'    , "D",    $s );    // Ð => D
    $s    = preg_replace( '@\x{0110}@u'    , "D",    $s );    // Ð => D
    $s    = preg_replace( '@\x{00f0}@u'    , "d",    $s );    // ð => d
    $s    = preg_replace( '@\x{0111}@u'    , "d",    $s );    // d => d
    $s    = preg_replace( '@\x{0126}@u'    , "H",    $s );    // H => H
    $s    = preg_replace( '@\x{0127}@u'    , "h",    $s );    // h => h
    $s    = preg_replace( '@\x{0131}@u'    , "i",    $s );    // i => i
    $s    = preg_replace( '@\x{0138}@u'    , "k",    $s );    // ? => k
    $s    = preg_replace( '@\x{013f}@u'    , "L",    $s );    // ? => L
    $s    = preg_replace( '@\x{0141}@u'    , "L",    $s );    // L => L
    $s    = preg_replace( '@\x{0140}@u'    , "l",    $s );    // ? => l
    $s    = preg_replace( '@\x{0142}@u'    , "l",    $s );    // l => l
    $s    = preg_replace( '@\x{014a}@u'    , "N",    $s );    // ? => N
    $s    = preg_replace( '@\x{0149}@u'    , "n",    $s );    // ? => n
    $s    = preg_replace( '@\x{014b}@u'    , "n",    $s );    // ? => n
    $s    = preg_replace( '@\x{00d8}@u'    , "O",    $s );    // Ø => O
    $s    = preg_replace( '@\x{00f8}@u'    , "o",    $s );    // ø => o
    $s    = preg_replace( '@\x{017f}@u'    , "s",    $s );    // ? => s
    $s    = preg_replace( '@\x{00de}@u'    , "T",    $s );    // Þ => T
    $s    = preg_replace( '@\x{0166}@u'    , "T",    $s );    // T => T
    $s    = preg_replace( '@\x{00fe}@u'    , "t",    $s );    // þ => t
    $s    = preg_replace( '@\x{0167}@u'    , "t",    $s );    // t => t

    // remove all non-ASCii characters
    $s    = preg_replace( '@[^\0-\x80]@u'    , "",    $s );


    // possible errors in UTF8-regular-expressions
    if (empty($s))
        return $s;
    else
        return $s;
}


function str_query_binding($query)
{
    $sql = $query->toSql();
    $binding = $query->getBindings();
    return vsprintf(str_replace("?","%s",$sql),$binding);
}

