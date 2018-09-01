<?php

class AutoGenRedirect {

	public static function createRedirectFromTitle( &$wikiPage, &$user, $content, $summary, $isMinor, $isWatch, $section, &$flags, $revision, &$status, $baseRevId, $undidRevId  )  {

	   	$title = $wikiPage->getTitle();
	 	$titleText = $title->getText();
		
		preg_match( '/^.+?\((.+?)\)$/', $titleText, $matches );
		if (!$matches)  return true  ;

		$newPageTitle = Title::newFromText( $matches[1] );
		// create redirect page
		self::createRedirect( $newPageTitle, $title );

		// update oldpage
		self::updatePage( $wikiPage, $summary );
		
	    return true;
	}
	
	public static function createRedirect( $newPageTitle, $destinationTitle ){
		
		if ( is_null( $newPageTitle ) || $newPageTitle->isKnown() || !$newPageTitle->canExist() ) return true;

		$destinationTitleText = $destinationTitle->getText();
		$newPageContentText = "#REDIRECT [[$destinationTitleText]]";		
		$editMessage = "Page created automatically by AutoGenRedirect on page [[$destinationTitleText]]";
		self::createPage( $newPageTitle, $newPageContentText, $editMessage );
		
	}
	
	private function createPage( $newPageTitle, $newPageContentText, $editMessage ){
		$newWikiPage = new WikiPage( $newPageTitle );
		$newPageContent = ContentHandler::makeContent( $newPageContentText, $newPageTitle );
		$newWikiPage->doEditContent( $newPageContent, $editMessage ); 
	}


	public static function updatePage( $wikiPage, $user, $summary ){
	

		$content = $wikiPage->getContent( Revision::RAW );
		$editMessage = "Page updated automatically by AutoGenRedirect";
		
		// $summary = $content->getTextForSummary();
		if($summary == $editMessage) return true;
		
		$title = $wikiPage->getTitle();
		$pageUpdater = $wikiPage->newPageUpdater();

		$text = ContentHandler::getContentText( $content );
		$newPageContentText = Tools::mergeChr( $text );
		$newPageContent = ContentHandler::makeContent( $newPageContentText, $title );
		$pageUpdater->setContent("main", $newPageContent)		
		
		// $wikiPage->doEditContent( $newPageContent, $editMessage ); 

	}
	
}

class Tools {

	public static $chrSetBf = array("°¡", "³ª", "´Ù", "¶ó");
	
	public static $chrSetAf = array("Ê«", "Ñß", "Òý", "Ôþ");

	public static function mergeChr( $text ){
		return str_replace( self::$chrSetBf, self::$chrSetAf, $text ) ;
	}
	
}
