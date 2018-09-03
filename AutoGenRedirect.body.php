<?php

class AutoGenRedirect {

	// Hook : "PageContentSaveComplete"
	public static function createRedirectFromTitle( &$wikiPage, &$user, $content, $summary, $isMinor, $isWatch, $section, &$flags, $revision, &$status, $baseRevId, $undidRevId  )  {

	   	$title = $wikiPage->getTitle();
	 	$titleText = $title->getText();

		preg_match( '/^.+?\((.+?)\)$/', $titleText, $matches );
		if (!$matches)  return true  ;

		$newPageTitle = Title::newFromText( $matches[1] );
		// create redirect page
		self::createRedirect( $newPageTitle, $title );
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

		/* ---------------------------------------------------------- */

	// Hook : "PageContentSaveComplete"

	public static function updatePageOnSaveComplete( &$wikiPage, &$user, $content, $summary, $isMinor, $isWatch, $section, &$flags, $revision, &$status, $baseRevId, $undidRevId  ){

		$editMessage = "Page updated automatically by AutoGenRedirect";
		if($summary == $editMessage) {
			$status->fatal( new RawMessage( "message same" ) );
			return true;
		}

		$title = $wikiPage->getTitle();

		$text = ContentHandler::getContentText( $content );
		$newPageContentText = Tools::mergeChr( $text );
		$newPageContent = ContentHandler::makeContent( $newPageContentText, $title );
		$wikiPage->doEditContent( $newPageContent, $editMessage );

		return true;
	}
	
	
	/* ---------------------------------------------------------- */

	// Hook : "PageContentSave"
	// Return false to cancel a save and use $status to provide an error message.

	public static function updatePageOnSave( &$wikiPage, &$user, &$content, &$summary, $isMinor, $isWatch, $section, &$flags, &$status ){

		/*
		$editMessage = "Page updated automatically by AutoGenRedirect";
		if($summary == $editMessage) {
			$status->fatal( new RawMessage( "message same" ) );
			return false;
		}

		$title = $wikiPage->getTitle();

		$text = ContentHandler::getContentText( $content );
		$newPageContentText = Tools::mergeChr( $text );
		$newPageContent = ContentHandler::makeContent( $newPageContentText, $title );
		$wikiPage->doEditContent( $newPageContent, $editMessage );

		// $pageUpdater = $wikiPage->newPageUpdater();
		// $pageUpdater->setContent("main", $newPageContent);

		if ( true ) {
			$status->fatal( new RawMessage( "$newPageContentText" ) );
		}
		*/
		return true;
	}

	/* ---------------------------------------------------------- */

	// Hook : "EditPage::attemptSave"
	
	public static function updatePageOnEditPage( EditPage $editpage ) {

	// https://doc.wikimedia.org/mediawiki-core/master/php/classEditPage.html


		$title = $editpage->getTitle();
		$article = $editpage->getArticle();
		

		$wikipage = $article->getPage();
		$content = $wikipage->getContent( Revision::RAW );
		$contentText = ContentHandler::getContentText( $content );

		$newContentText = Tools::mergeChr( $contentText );
		$newContent = ContentHandler::makeContent( $newPageContentText, $title );
		/*
		$status = $wikipage->doEditContent( $newContent, $summary );

		if($status){
			$status->getMessage()->text();
		}
		*/
		return true;

	}
	
	
	
	
	/*
	public static function updatePageTMP( $wikiPage, $user, $summary ){


		$content = $wikiPage->getContent( Revision::RAW );
		$editMessage = "Page updated automatically by AutoGenRedirect";

		// $summary = $content->getTextForSummary();
		if($summary == $editMessage) return true;

		$title = $wikiPage->getTitle();
		$pageUpdater = $wikiPage->newPageUpdater();

		$text = ContentHandler::getContentText( $content );
		$newPageContentText = Tools::mergeChr( $text );
		$newPageContent = ContentHandler::makeContent( $newPageContentText, $title );
		$pageUpdater->setContent("main", $newPageContent);

		// $wikiPage->doEditContent( $newPageContent, $editMessage );

	}
	*/

}

class Tools {

	public static $chrSetBf = array("가", "나", "다", "라");

	public static $chrSetAf = array("家", "裸", "多", "羅");

	public static function mergeChr( $text ){
		return str_replace( self::$chrSetBf, self::$chrSetAf, $text ) ;
	}

}
