<?php
	class ZC{
		var $zc_options ;
		function __construct(){
			$range_file_name = "range.dat" ;
			if(!file_exists($range_file_name)){
				#is the html exit
				$range_html = 'range.htm' ;
				if(!file_exists($range_html)){
						$url = "http://xscj.hit.edu.cn/HitJwgl/XS/KFJscx.ASP" ;
						$range_content = file_get_contents($url) ;
						$range_file = fopen($range_html,'w') ;
						fwrite($range_file,$range_content) ;
						fclose($range_file) ;
					}
					#open the range_html file
					$range_file = fopen($range_html,'r') ;
					$range_content = file_get_contents($range_html) ;
					$regx = '/<option value="(?P<options>[^"]+)"[^<]+<\/option>/i' ;
					$res = array() ;
					preg_match_all($regx,$range_content,$res) ;
					#get useful options
					foreach($res['options'] as $index=>$value){
						if($index == 3) break ;
						$this->zc_options[$index] = $value ;
					}
					#write to file
					$range_file = fopen($range_file_name,'w') ;
					fwrite($range_file,serialize($this->zc_options)) ;
					fclose($range_file) ;
			}
			else{
				$this->zc_options = unserialize(file_get_contents($range_file_name)) ;
			}
		}
	
	}
?>