<?php
/*  Gelsheet Project, version 0.0.1 (Pre-alpha)
 *  Copyright (c) 2008 - Ignacio Vazquez, Fernando Rodriguez, Juan Pedro del Campo
 *
 *  Ignacio "Pepe" Vazquez <elpepe22@users.sourceforge.net>
 *  Fernando "Palillo" Rodriguez <fernandor@users.sourceforge.net>
 *  Juan Pedro "Perico" del Campo <pericodc@users.sourceforge.net>
 *
 *  Gelsheet is free distributable under the terms of an GPL license.
 *  For details see: http://www.gnu.org/copyleft/gpl.html
 *
 */
class SpreadsheetController extends FrontController {
	
	public function loadBook($bookId) {
		$bookId = mysql_escape_string($bookId) ;
		$sql = "SELECT json FROM ".table('books')." WHERE id = $bookId  LIMIT 1";		
		$result = mysql_query($sql);	
		if ($row = mysql_fetch_object($result) ) {
			throw new Success ( null, $row->json );
		}else{
			throw new GsError(345, "Not found") ;
		}
	}
	
	
	public function saveBook($book, $inputFormat, $outputFormat) {
		// The book Controller will check if has permission depending on the bookId
		// Here we cannot see the book id.. its inside $book !		 
		// Do security checks and save the book
		

		$json = $book;
		
		$json_obj = json_decode ( $book );
		$newBook = new Book ();
		$newBook->fromJson ( $json_obj );
		
		$export = new ExportController ();
		$html = addslashes ( $export->bookToHTML ( $newBook, false ) );
		
		$sql = "INSERT INTO " . table ( "books" ) . "(html,json) VALUES ('$html','$json')";
		$result = mysql_query ( $sql );
		
		if (! $result) {
			$error = new GsError ( 345, "Error saving" );
			if ($error->isDebugging ()) {
				$error->addContentElement ( "descrption", "Saving cell" );
				$error->addContentElement ( "MySqlError", mysql_error () );
				$error->addContentElement ( "MySqlQuery", $sql );
			}
			throw $error;
		} else {
			$id = mysql_insert_id ();
			throw new Success ( 'Book saved succesfully', "{'BookId':" . $id . "}" );
		}
	
	}
	
	public function deleteBook($bookId) {
		$this->security->checkDelete ( $bookId );
		$bookController = new BookController ();
		return $bookController->deleteBook ( $bookId );
	}
	
	/*
	 * this function returns the books identification for the user
	 * when null it takes the current session user
	 */
	public function listBooks($user = null) {
		
		$id = null;
		if ($user == null) {
			$id = $this->currentUser;
		} else {
			$id = $user;
		}
		$id = 1;
		//$sql= "SELECT BookId, BookName, BookCreatedOn, UserName FROM ".table("books")." b INNER JOIN ".table("users")." u ON b.UserId = u.UserId WHERE b.UserId= $id";
		$sql = "SELECT id, created FROM " . table ( "books" );
		$result = mysql_query ( $sql );
		//echo " # ".$sql." # ";
		

		$books = array ();
		
		$xml = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<ItemSearchResponse xmlns="http://webservices.amazon.com/AWSECommerceService/2006-06-28">
	<Items>
		<TotalResults>1</TotalResults>
		<TotalPages>1</TotalPages>
XML;
		while ( $row = mysql_fetch_object ( $result ) ) {
			$id = $row->id; 
			$name = "" ; //TODO
			$created = $row->created ;
			
			$xml .= "\t<Item>\n";
			$xml .= "\t\t<ItemAttributes>\n";
			$xml .= "\t\t\t<BookId>$id</BookId>\n";
			$xml .= "\t\t\t<Name>$name</Name>\n";
			$xml .= "\t\t\t<CreationDate>$created</CreationDate>\n";
			$xml .= "\t\t</ItemAttributes>\n";
			$xml .= "\t</Item>\n";
		}
		$xml .= <<< XML
	</Items>
</ItemSearchResponse>						
XML;
		header ( 'Content-Type: text/xml' );
		$output = $xml;
		
		echo $output;
	} 
	//end listBooks
}//end class
