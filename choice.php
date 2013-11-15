<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>选择</title>
	<style>
		body{
			font-size:2em ;
			height:100% ;
		}
		.center_panel{
			width:60% ;
			height:50% ;
			margin:0 auto ;
			border-radius:0.2em ;
			border:0.3em solid #ccc ;
		}
		.title{
			border-radius:5px 5px 0 0 ;
			background:#ddd ;
			height:3em ;
			line-height:3em ;
			text-align:center ;
			font-weight:bold ;
		}
		.content{
			margin-top:1em ;
			padding-top:1em ;
			margin-bottom:1em ;
		}
		.line{
			width:80% ;
			margin:0.4em auto ;
			height:2em ;
			text-align:center ;
			border-radius:0.2em ;
			border:0.1em solid #ccc ;
			line-height:2em ;
			cursor:pointer ;
		}
	</style>
</head>
<body>
	<div class="center_panel">
		<div class="title">查询选项</div>
		<div class="content">
			<div class="line" id="current"><a>查看今日教室情况</a></div>
			<div class="line" id="fixed"><a>查看指定日期教室情况</a></div>
		</div>
	</div>
	<script>
		var screenHeight = window.screen.height ;
		var divs = document.getElementsByTagName('div') ;
		var centerPanel = getClass('center_panel') ;
		var c_height = centerPanel[0].offsetHeight ;
		var marginTopV = (screenHeight-c_height)/2 ;
		if(marginTopV < 150){
			marginTopV = 150 ;
		}
		centerPanel[0].style.marginTop = marginTopV+"px" ;
		var lines = getClass('line') ;
		//make the click effect
		for(var i = 0 ; i < lines.length ; i++){
			lines[i].onmousedown = function(){
				this.style.background="#ccc" ;
			}
			lines[i].onmouseup = function(){
				this.style.background="#fff" ;
			}
		}
		//get the avalible zc 
		var zc = 11 ;
		var xq = "一校区" ;
		//process the click 
		var current = document.getElementById('current') ;
		current.onclick = function(){
			window.location.href="getclassroom.php?zc="+zc+"&xq="+xq ;
		}
		function getClass(className){
			var results = new Array() ;
			var index = 0 ;
			for(var i = 0 ; i < divs.length ; i++){
				if(divs[i].className == className){
					results[index] = divs[i] ;
					index++ ;
				}
			}
			return results ;
		}
	</script>
</body>