<head>
	<meta charset="utf-8" >
</head>
<style>
　　#pagelist{position:relative;lelft:360px;top:0}
　　#pagelist ul li { float:left;border:1px solid #e0691a; height:20px; font-weight:bold; line-height:20px; margin:0px 2px; list-style:none;}

　　.current { background:#FFB27A; display:block; padding:0px 6px; font-weight:bold;}
	#test{color:red;}
	ul{color:red;}
</style>
<div id="test">111</div>
<?php print_r($data);?>

<div id="pagelist">
　　	<ul>
		<?php echo $page; ?>
	</ul>
</div>