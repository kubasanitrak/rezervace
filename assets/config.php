<?php 
 

  
define("SITEURL", "http://localhost:8888/NIVB/");
// define("SITEURL", "https://dev.sanitrak.cz/NTMd");
// define("SITEURL", "http://nadaceterezymaxove.cz/");

$SITE_NAME = "";
$SITE_TITLE = "";
$SITE_SUBTITLE = "";
$OG_IMG = "og-image.jpg";

$READMORE = "chci vědět víc";

$DEFLANG = "cz";
    $locale_range = array( "en", "cz" );
    
    // global  $DEFLANG, $locale_range;
    $SITELANG = '';
    $DONATE = '';
    $TIT = '';
    $DESC = '';
    $KEYWRDS = '';
    $TWITTER_SITE = '';
    $OG_URL = '';
    $OG_IMAGE = '';


// check lang param in url on page load
// A: URL PARAM IS NOT SET
if ( !isset($_GET['lang']) ) {
// check global lang cookie value:
    if ( !isset($_COOKIE["site_lang"]) ) {
        $SITELANG = $DEFLANG;
    } else {
        $SITELANG = in_array(strval($_COOKIE["site_lang"]), $locale_range ) ? strval($_COOKIE["site_lang"]) : $DEFLANG;
    }
// B: URL PARAM IS SET, IF URL PARAM IS IN AVAILABLE RANGE, OTHERVISE SET TO DEFAULT
} else {
    $SITELANG = in_array(strval($_GET['lang']), $locale_range ) ? strval($_GET['lang']) : $DEFLANG;
}

if(PHP_VERSION_ID >= 70300) { 
    setcookie("site_lang", $SITELANG, ['samesite' => 'Strict', 'secure' => true]);
} else { 
    setcookie("site_lang", $SITELANG, 0, '/; SameSite=Strict', '', true, false);
} 



?>