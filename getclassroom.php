<?php	
	include('Classroom.php') ;
	$content ;
	if(!empty($_GET['zc'])&&!empty($_GET['xq'])&&!empty($_REQUEST['day'])){
		$zc = $_GET['zc'] ;
		$xq = $_GET['xq'] ;
		$day = $_GET['day'] ;
		$classroom = new Classroom($xq,$zc) ;
		if(empty($_GET['random'])){
			$content = $classroom->getClassroomInfoAtOneDay($day) ;
		}
		else{
			$content = $classroom->getRandomClassroom($day) ;
		}
	}
	else{
		$content = 'bad request' ;
	}
?>
<!doctype html>
<head>
<meta charset="utf-8">
<meta name="viewport"content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="classroom.css">
</head>
<body>
	<?php
		echo $content ;
	?>
	<script>
		var divs = document.getElementsByTagName('div') ;
		function getClass(claName){
			var res  = new Array() ;
			for(var i = 0 ; i  < divs.length ; i++){
				if(divs[i].className == claName){
					res.push(divs[i]) ;
				}	
			}
			if(res.length == 1) res = res[0] ;
			return res ; 
		}
		var groups = getClass('g_title') ;
		//console.log(groups) ;
		bind(groups,"onclick",function(){
			var g_content = this.nextSibling ;
				if(g_content.style.display == 'none'|| g_content.style.display == ""){
					this.nextSibling.style.display="block" ;
				}
				else{
					this.nextSibling.style.display="none" ;
				}
		}) ;
		var lines = getClass('line') ;
		bind(lines,"onmousedown",function(){
			this.style.background = "#ccc" ;
		}) ;
		bind(lines,"onmouseup",function(){
			this.style.background = "#fff" ;
		}) ;
		function bind(obj,type,fun){
			for(var i  = 0 ; i < obj.length ; i++){
				if(type == "onclick"){
					obj[i].onclick = fun ;
				}
				else if(type == "onmousedown"){
					obj[i].onmousedown = fun ;
				}
				else if(type == "onmouseup"){
					obj[i].onmouseup = fun ;
				}
			}	
		}
		//
		var random_back = getClass('random_back') ;
		if(random_back.lenth != 0){
			var random_value = Math.floor(Math.random()*4+1) ;
			document.body.style.background="url(res/random_"+random_value+".jpg) center 0 no-repeat" ;
		}
	</script>
</body>