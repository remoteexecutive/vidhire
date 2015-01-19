<?php
	class HtmlWriter{
		private $title;
		private $styles;
		
//		private function addHeaderAndFooter($html){
//			return "<HTML><HEAD></HEAD><BODY>".$html."<BODY></HTML>";			 
//		}
//	
//		
//		function sheetToHtml(Book $sheet, $configs){
//			$title = $book->name;
//			$html = "<DIV>";
//			var_dump($book->fontStyles);
//			
//			$html .= "</DIV>";
//			return $this->addHeaderAndFooter($html);
//		}
//		
		private static function maxWrittenRow($sheet) {
			$max = 0;
			$rows = $sheet->getCells();
			foreach ($rows as $row => $data) {
				if((int)$row>$max){
					$max = (int)$row;	
				}
			}
			return $max;
		}

		
		/**
		 * Enter description here...
		 *
		 * @param Sheet $sheet
		 */
		private static function maxWrittenColumn($sheet) {
			$max = 0;	
		 
			$rows = $sheet->getCells();
			foreach ($rows as $row) {
				foreach ($row as $col =>$data)
					if ((int)$col > $max )
						$max = (int)$col;
			}
			return $max;
		}
		
	
		/**
		 * Enter description here...
		 *
		 * @param FontStyle $fstyle
		 * @return String
		 */
		public function fontStyleToCss($fstyle){
			$css = ".c".$fstyle->fontStyleId."{";
			$css .= "font-family:". $fstyle->getFontName().";";
			$css .= "font-size:". $fstyle->getFontSize()."pt;";
			if($fstyle->getFontBold())
				$css .= "font-weight:bold;";
			
			if($fstyle->getFontItalic())
				$css .= "font-style:italic;";
			
			if($fstyle->getFontUnderline())
				$css .= "text-decoration:underline;";
			
			if((int)$fstyle->fontHAlign) //If different from 0 "General"
				$css .= "text-align:".$fstyle->getHAlignName().";";

			$css .= "color:". $fstyle->getFontColor().";";
			
			return $css."}";	
		}
		
		/**
		 * Export a sheet to Html table
		 *
		 * @param Sheet $sheet
		 * @return String
		 */
		public static function toHTML($book, $htmlHeader=true) {
			$sheet = $book->sheets[0];
			$maxCol = HtmlWriter::maxWrittenColumn($sheet) ;
			$maxRow  = HtmlWriter::maxWrittenRow($sheet)   ;
						
			$cells = $sheet->getCells();	
			$output = "";
			$output.='<table border=1 border-collapse="collapse">';
			
			for ($i = 0 ; $i <= $maxRow ; $i++) {
				$output.="<tr>\n";
				for ($j = 0 ; $j <= $maxCol ; $j++) {
					$cell = null;
					$row = "".($i);
					//echo "Row $row";
					if(isset($cells[$row])){
						$col = "".($j);
						if(isset($cells[$row][$col])){
							$cell = $cells[$row][$col];
						}
					}
					$fsId = 0;
					if (isset($cell))	
						$fsId = $cell->getFontStyleId();
						
					$output.="<td class='c".$fsId."'>";
					if (isset($cell))	
						$output.= $cell->cellValue;
					$output.="</td>\n";
				}
				$output.="</tr>\n";
			}	
			$output.="</table>\n";
			$style  = '<style type="text/css" media="screen">';
			$style .= "table{border-collapse:collapse;}\n";
	    	$style .= "td{border:1px solid #CCC;height:18px;width:80px;position:relative;}\n";

	    	
	    	foreach($book->getFontStyles() as $fstyle){
	    		$style .= HtmlWriter::fontStyleToCss($fstyle)."\n";
	    	}
	    	
    		$style .="</style>\n";
			if($htmlHeader)
				$output="<html><head>".$style."</head>\n<body>\n".$output."</body>\n</html>\n";
			else
				$output=$style.$output;
				
			return $output; 
		}
		
	}
	
?>