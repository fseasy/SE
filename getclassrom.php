<?php	
	#���񴦹��ṩ��128�����ң�ÿ������ÿ��7��*5�ڿ�
	#���ϣ�����128*7*5=4480��td
	#����洢Ϊ128*35�Ķ�ά���飬�ɷ�����в���
	#��Ҫ�ٴ洢��128����������
	#ÿ�����ҵ���N��k�ڿε������ʾΪarray[(n-1)*7+(k-1)]
	#zc:�ܴ�-number��$day������ ,$xq :У��{��һУ����������У����}
	class Classroom{
		 var $classroom ;
		function __construct($XQ,$ZC){
			$data_file_name = "classroom_{$XQ}_{$ZC}.dat" ;
			if(!file_exists($data_file_name)){
				#create the classroom
				$all_classroom_pagecontent = $this->getAllClassroomPageContent($ZC,$XQ) ;
				$this->classroom = $this->getAllClassroomData($all_classroom_pagecontent) ;
				#store the data
				$data_file = fopen($data_file_name,'wb') ;
				fwrite($data_file,serialize($this->classroom)) ;
				fclose($data_file) ;
			}
			else{
				$data_file = fopen($data_file_name,'rb') ;
				$this->classroom = unserialize(fread($data_file,filesize($data_file_name))) ;
			}
			//print_r($this->classroom) ;
		}
		function getAllClassroomPageContent($zc,$xq){
			$file_name = "all_classroom_{$zc}.htm" ;
			if(!file_exists($file_name)){
				$data = 'ZC='.$zc.'&XQ='.$xq ;
				$base_url = "http://xscj.hit.edu.cn/HitJwgl/XS/" ;
				$action = $base_url.'kfjscx_all.asp' ;
				$curl = curl_init() ;
				curl_setopt($curl,CURLOPT_URL,$action) ;
				curl_setopt($curl,CURLOPT_POSTFIELDS,$data) ;
				curl_setopt($curl,CURLOPT_RETURNTRANSFER,true) ;
				$content = curl_exec($curl) ;
				#store the file
				$file = fopen($file_name,'w') ;
				fwrite($file,$content) ;
				fclose($file) ;
			}
			else{
				$content = file_get_contents($file_name,'r') ;
			}
			#the code is gb2312 while we need utf-8
			$content = mb_convert_encoding($content,"utf-8","gb2312") ;
			return $content ;
		}
		function getAllClassroomData($content){
			#get all classroom classinfo
			$regx = '/<td bgcolor = \'#(?P<classroom_info>\w{6})\'>[^\/]*<\/td>/i' ;
			$classroom_info_match = array() ;
			preg_match_all($regx,$content,$classroom_info_match) ;
			$classroom_info = $classroom_info_match['classroom_info'] ;
			#get all classroom name
			 #sample <td height="20">&nbsp;1031</td>
			$regx = '/<td height="20">&nbsp;(?P<classroom_name>[^\/]+)<\/td>/' ;
			$classroom_name_match = array() ;
			preg_match_all($regx,$content,$classroom_name_match);
			$classroom_name = $classroom_name_match['classroom_name'] ;
			//var_dump($classroom_name_match['classroom_name']) ;
			#create the array
			$classroom = array() ;
			for($i = 0 ; $i < count($classroom_name) ; $i++){
				$classroom[$classroom_name[$i]] = array() ;
				$startPos = $i*35 ;
				for($k = 0  ;$k < 35 ; $k++ ){
					# 1 stands for occupy while 0 stands for empty
					if($classroom_info[$startPos+$k] == "FFFFFF"){
						$classroom[$classroom_name[$i]][$k] = 0 ;
					}
					else{
						$classroom[$classroom_name[$i]][$k] = 1 ;
					}
				}
			}
			#now classroom has store the all classroom info at all week
			return $classroom ;
		}
		#day,1~7
		function getClassroomInfoAtOneDay($day){
			foreach($this->classroom as $room=>$info ){
				echo $room ;
				$startPos = ($day-1)*7 ; 
				for($i = 0 ; $i < 7 ; $i++){
					printf("\t%s\t",$info[$startPos+$i]) ;
				}
				echo '<br/>' ;
			}
		}
	}
	$classroom = new Classroom("һУ��",11) ;
	$classroom->getClassroomInfoAtOneDay(1) ;
?>
