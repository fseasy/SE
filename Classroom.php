<?php
	#教务处共提供了128个教室，每个教室每周7天*5节课
	#综上，共有128*7*5=4480个td
	#将其存储为128*35的二维数组，可方便进行查找
	#需要再存储这128个教室名称
	#每个教室的周N第k节课的情况表示为array[(n-1)*7+(k-1)]
	#zc:周次-number，$day：星期 ,$xq :校区{‘一校区’，‘二校区’}
	class Classroom{
		 var $classroom ;
		 var $xq_trans ;#translate the chinese to num
		function __construct($XQ,$ZC){
			$this->xq_trans=array(
				"一校区"=>1,
				"二校区"=>2
			) ;
			$data_file_name = "classroom_{$this->xq_trans[$XQ]}_{$ZC}.dat" ;
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
			$file_name = "all_classroom_{$this->xq_trans[$xq]}_{$zc}.htm" ;
			if(!file_exists($file_name)){
				$data = 'ZC='.$zc.'&XQ='.$xq ;
				#!!NOTICE 由于原网页时gb2312编码，所以post数据时必须转码！！
				$data = mb_convert_encoding($data,"gb2312","utf-8") ;
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
			#to add cache,we should store the content instead of just print it!
			$content = "" ;
			#first ,print the title
			$content.='<div class="tit_line">' ; 
			$content.='<div class="room">教室</div>';
			$content.='<div class="time">上午第一节</div>' ;
			$content.='<div class="time">上午第二节</div>' ;
			$content.='<div class="time">下午第一节</div>' ;
			$content.='<div class="time">下午第二节</div>' ;
			$content.='<div class="time">晚自习</div>' ;
			$content.='<div class="clear"></div></div>' ;
			#now,print the classroom info
			#we should group it!
			#first ,正心
			$template_h = '<div class="group"><div class="g_title">' ;
			$template_r = '</div><div class="g_content">' ;
			$g_zx_big = $template_h.'<a>正心大教室</a>'.$template_r ;
			$g_zx_small = array(
				"zx_1"=>$template_h.'<a>正心一楼</a>'.$template_r ,
				"zx_2"=>$template_h.'<a>正心二楼</a>'.$template_r ,
				"zx_3"=>$template_h.'<a>正心三楼</a>'.$template_r ,
				"zx_4"=>$template_h.'<a>正心四楼</a>'.$template_r ,
				"zx_5"=>$template_h.'<a>正心五楼</a>'.$template_r ,
				"zx_6"=>$template_h.'<a>正心六楼</a>'.$template_r ,
				"zx_9"=>$template_h.'<a>正心九楼</a>'.$template_r ,
			) ;
			#致知
			$g_zz = $template_h."<a>致知楼</a>".$template_r ;
			foreach($this->classroom as $room=>$info ){
				$startPos = ($day-1)*5 ; 
				if(strpos($room,"正心") !== false){
					#big classroom
					$str = $this->getOneClassroomInfo($room,$startPos,5) ;
					if(strlen($room) == strlen("正心00")){
						$g_zx_big.=$str ;
					}
					else{
						$index = $room[strlen("正心")] ;
						$g_zx_small['zx_'.$index].=$str ;
					}
				}
				else if(strpos($room,"致知")!==false){
					$str = $this->getOneClassroomInfo($room,$startPos,5) ;
					$g_zz.=$str ;
				}
	
			}
			#over the tag
			#let's combine it
			$g_zx_big.="</div></div>" ;
			$g_zx_small_str = "" ;
			foreach($g_zx_small as $value){
				$value.='</div></div>' ;
				$g_zx_small_str.=$value ;
			}
			$g_zz.="</div></div>" ;
			$content.=$g_zx_big ;
			$content.=$g_zx_small_str ;
			$content.=$g_zz ;
			return $content ;
		}
		#$index is the classroom name ,startOffset is the offset in the array
		function getOneClassroomInfo($index,$startOffset,$length){
			$str = "" ;
			$str.='<div class="line">';
			$str.='<div class="room">'.$index.'</div>' ;#classroom name
			for($i = 0 ; $i < $length ; $i++){
				$str.='<div class="state_container">' ;#state container
				$str.='<div class="state">' ;
				if($this->classroom[$index][$startOffset+$i] == 1){
					$str.='<img src="res/circle.png">' ;
				}else{
					$str.='<img src="res/cross.png">' ;
				}
				$str.= '</div></div>' ;
			}
			$str.='<div class="clear"></div>' ;
			$str.="</div>" ;
			return $str ;
		}
		#get a random classroom which should be empty at most time
		#we just get the day's total 
		function getRandomClassroom($day){
			$tmp = array() ;
			$startPos = ($day-1)*5 ;
			foreach($this->classroom as $room=>$info){
				#ignore the others
				if(strpos($room,"正心")===false && strpos($room,"致知") === false) continue ;
				$total = 0 ;
				for($i = 0 ; $i < 5 ; $i++){
					$total+=$info[$startPos+$i] ;
				}
				$tmp[$room] = $total ;
			}
			#sort low to hight ,the lower ,the more empty
			asort($tmp) ;
			$counter = 0 ;
			$availabel = 0 ;
			foreach($tmp as $room=>$occupy_times){
				if($counter == 10) break ;
				if($occupy_times == 0){
					$availabel++ ;
				}
			}
			#if exist the occupy times = 0,we choose it ,if not ,just select one from the top 10
			$random ;
			if($availabel > 0){
				$random = rand()%$availabel ;
			}
			else{
				$random = rand()%5 ;
			}
			$conter = 0 ;
			foreach($tmp as $room => $value){
				if($counter == $random){
					#print the result
					$back_random = rand()%4 +1;
					$content = '<div class="random_back">' ;
					$content.='<div class="random_panel">' ;
					$content.='<div class="random_classroom">'.$room.'</div>' ;#room
					$content.='<div class="random_classroom_info">' ;
					//$content.='<div class="textblock">教室详情</div>' ;
					$time_array = array(
					"上午第一节","上午第二节","下午第一节","下午第一节"	,"晚自习"
					) ;
					for($i = 0 ; $i < 5 ; $i ++){
						$content.=$this->getOneBlock($time_array[$i],$this->classroom[$room][$startPos+$i]) ;
						if($i == 1){
							$content.='<div class="clear"></div>' ;
						}
					}
					$content.='</div></div><div class="clear"></div></div>' ;
					return $content ;
				}
				$counter++ ;
			}
		}
		function getOneBlock($time,$state){
			$content = "" ;
			$content.='<div class="block"><div class="time">'.$time.'</div>' ;
			$content.='<div class="state_container"><div class="state">' ;
			if($state == 1){
				$content.='<img src="res/cross.png">' ;
			}
			else{
				$content.='<img src="res/circle.png">' ;
			}
			$content.="</div></div></div>" ;
			return $content ;
		}		
	}
?>