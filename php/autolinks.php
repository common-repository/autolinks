<?php
class AutoLinks{

	var $_imgTitlePrefix;
	var $_imgAltPrefix;
	var $_onlyForFistImg;
	
	function AutoLinks($imgTitlePrefix,$imgAltPrefix,$onlyForFistImg){
		$this->_imgTitlePrefix=$imgTitlePrefix;
		$this->_imgAltPrefix=$imgAltPrefix;
		$this->_onlyForFistImg=$onlyForFistImg;
	}

	function handleImgLinks(&$text,$title,$link){
		if(empty($link)){
			return $text;
		}
		$regex="/<img(.*?)>\s*(<\/img>)?/ ";

		$imgTitle=$title;
		$imgAlt=$title;
		if(!empty($this->_imgTitlePrefix)){
			$imgTitle=$this->_imgTitlePrefix.' '.$title;
		}
		if(!empty($this->_imgAltPrefix)){
			$imgAlt=$this->_imgAltPrefix.' '.$title;
		}
		$this->_replaceImg(NULL,$link,$imgTitle,$imgAlt);

		if($this->_onlyForFistImg){
			$text= preg_replace_callback($regex,array( &$this, '_replaceImg'), $text,1);
		}else{
			$text= preg_replace_callback($regex,array( &$this, '_replaceImg'), $text);
		}
		return $text;

	}
	function _replaceImg($matches,$link=NULL,$title=NULL,$alt=NULL){	
		static $_link;
		static $_title;
		static $_alt;
		if(isset($link)&&isset($title)){
			$_link=$link;
			$title=str_replace("'",' ',$title);
			$title=str_replace('"',' ',$title);
			$alt=str_replace("'",' ',$alt);
			$alt=str_replace('"',' ',$alt);
			$_alt=$alt;
			$_title=$title;
		}else{
			$img=$matches[0];
			if(strpos($img,' title=')==false){
				$img=str_replace('<img ','<img title="'.$_title.'" ',$img);
			}
			if(strpos($img,' alt=')==false){
				$img=str_replace('<img ','<img alt="'.$_alt.'" ',$img);
			}
			return '<a href="'.$_link.'" alt="'.$_alt.'" title="'.$_title.'" >'.$img.'</a>';
		}

	}
}
?>