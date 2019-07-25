<?php 

function getLocaleFolder( $langFolder )
{
	$localeFolder = $langFolder . "locale/en_US/LC_MESSAGES";
	// create the folder if it not exists
	if ( !file_exists( $localeFolder ) )
	{
		mkdir( $localeFolder, 0777, true );
	}
	return $localeFolder . "/";
}

function getLocaleFiles( $langCode, $langFolder )
{
	// just file names without path
	$localeMatch = array_map( 'basename', glob( getLocaleFolder( $langFolder ) . $langCode . "-*.mo" ) );
	return $localeMatch;
}

function getLastLocaleFile( $langCode, $langFolder )
{
	// getting all files
	$lastFile = getLocaleFiles( $langCode, $langFolder );
	// last one
	rsort( $lastFile );
	if ( !empty( $lastFile ) )
	{
		return $lastFile[0];
	}
	return false;
}

function getLastLocaleFileTime( $langCode, $langFolder )
{
	// get last modification time from the file name, as it's encoded in the file name
	$lastFile = getLastLocaleFile( $langCode, $langFolder );
	if ( !empty( $lastFile ) )
	{
		$lastTime = preg_replace( "|" . $langCode . "\-(\d+).+|is", "$1", $lastFile );
		return $lastTime;
	}
	return 0;
}

function langIsNewerThanLocale( $langCode, $langFolder )
{
	// locale time 
	$localeFileTime = getLastLocaleFileTime( $langCode, $langFolder );
	// lang file name
	$langFile = $langFolder . $langCode . ".mo";
	// compare time 
	if( file_exists( $langFile ) ){
		$langFileTime = filemtime( $langFile );
		if( $langFileTime > $localeFileTime )
		{
			return true;
		}
	}
	return false;
}

function moveLangtoLocaleFolder( $langCode, $langFolder )
{
	$langFile = $langFolder . $langCode . ".mo";
	if( file_exists( $langFile ) ){
		$langFileTime = filemtime( $langFile );
		// delete all old locales
		$locales = getLocaleFiles( $langCode, $langFolder );
		foreach ($locales as $locale)
		{
			unlink( getLocaleFolder( $langFolder ) . $locale );
		}
		// copy new lang to locale
		copy( $langFile, getLocaleFolder( $langFolder ) . $langCode . "-". $langFileTime . ".mo" );
	}
}

function loadTextDomain( $langCode, $langFolder )
{
	// set env
	putenv('LC_ALL=en_US');
	setlocale(LC_ALL, 'en_US');
	
	// newer is always better
	$langFile = $langFolder . $langCode . ".mo";
	if ( file_exists( $langFile ) )
	{
		if ( langIsNewerThanLocale( $langCode, $langFolder ) )
		{
			moveLangtoLocaleFolder( $langCode, $langFolder );
		}
	}
	
	// get last locale time, generate textdomain 
	$lastLocaleTime = getLastLocaleFileTime( $langCode, $langFolder );
	$textDomain = $langCode . "-" . $lastLocaleTime;
	
	// bind the text domain from the locale folder
	bindtextdomain( $textDomain, $langFolder.'locale' );
	bind_textdomain_codeset( $textDomain, 'UTF-8' );
}

function setTextDomain( $langCode, $langFolder )
{
	$lastLocaleFileTime = getLastLocaleFileTime( $langCode, $langFolder );
	if ( !empty( $lastLocaleFileTime ) )
	{
		textdomain($langCode . "-" . $lastLocaleFileTime);
	}
}

function loadLanguage( $langCode, $langFolder )
{
	loadTextDomain( $langCode, $langFolder );
	setTextDomain( $langCode, $langFolder );
}

/**
 * shortcut functions
 */
function _e($text){
	echo gettext($text);
}

?>