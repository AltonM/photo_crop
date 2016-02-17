<?php

?>
<head>
<?php
	/*
		index
	*/
	if( isset( $_POST['image_upload']  ) ):
		/*
			create an image from temporary the show to use
		*/
		$image = $_FILES['image'];
		list($width, $height) = getimagesize($image['tmp_name']);
	endif;
	
?>
	<style>
		<!--
		body{
			margin:0px;
			padding:1px;
		}
		
		.clear{
			clear:both;
		}
		
		.submitImage{
			padding:10px 20px;
			border:solid 1px #0052cc;
			background-color:#0066ff;
			color:white;
			text-align:center;
			display:inline-block;
			font-size:12px;
			cursor:pointer;
		}
		
		#cropping_holder{
			max-width:100%;
			max-height:100%;
			margin:auto;
			text-align:center;
			position:relative;
		}
		
		#cropping_holder > img{
			max-width:100%;
			max-height:100%;
		}
		
		#cropping_holder > .overlay{
			background-color:black;
			opacity:.3;
			position:absolute;
			top:0;
			left:0;
			right:0;
			bottom:0;
			border:solid 1px black;
		}
		
		#cropping_holder > .overlay >div{
			position:absolute;
			/*background-color:white;*/
			
		}
		#cropping_holder > .overlay >#top{
			top:0px;
			left:0px;
			right:0px;
			height:25px;
			cursor:n-resize;
			border-top:dashed 2px grey;
		}
		
		#cropping_holder > .overlay >#right{
			top:0px;
			bottom:0px;
			right:0px;
			width:25px;
			cursor:e-resize;
			border-right:dashed 2px grey;
		}
		
		#cropping_holder > .overlay >#bottom{
			left:0px;
			bottom:0px;
			right:0px;
			height:25px;
			cursor:n-resize;
			border-bottom:dashed 2px grey;
		}
		
		#cropping_holder > .overlay >#left{
			left:0px;
			bottom:0px;
			top:0px;
			width:25px;
			cursor:e-resize;
			border-left:dashed 2px grey;
		}
		
		#cropping_holder > .overlay >#center{
			left:25px;
			bottom:25px;
			top:25px;
			right:25px;
			cursor:move;
			/*border:dashed 2px white;*/
		}
		
		#body > .cont{
			max-width:1100px;
			padding:10px;
			margin:auto;
			text-align:center;
		}
		
		#body > .cont > #floater{
			float:left;
			width:200px;
			height:200px;
			margin-right:20px;
			padding:2px;
			border:solid 1px lightgrey;
		}
		
		#body > .cont > #floater #result{
			width:195px;
			height:195px;
			border:solid 1px black;
			overflow:hidden;
		}
		
		#body > #header{
			background-color: lightgrey;
			color: grey;
			font-size: 13px;
			padding: 10px;
			text-transform: uppercase;
			margin-bottom: 20px;
			border: solid 1px gray;
			text-align:center;
		}
		-->
	</style>
	<script type="text/javascript">
		window.onload = function(){
			<?php if(  isset(  $_POST['image_upload']  )  ): ?>
				cropping(document.getElementById('cropping_holder'));
			<?php else: ?>
				var f = document.forms[0];

				f['image'].addEventListener('change',function(){
					f.submit();
				});
				
				document.getElementsByClassName('submitImage')[0].addEventListener('click',function(){
					f['image'].click();
				});
			<?php endif; ?>
		}
		<?php if(  isset(  $_POST['image_upload']  )  ): ?>
		cropping = function(element){
			var obj = [];
			obj['obj'] = this;
			obj['container_width'] = parseInt(window.getComputedStyle(element).getPropertyValue("width"));
			obj['container_height'] = parseInt(window.getComputedStyle(element).getPropertyValue("height"));
			obj['build'] = false;
			obj['draggable'] = false;
			obj['moveable'] = false;
			obj['current'] = [0,0,0,0];
			obj['start'] = [0,0,0,0];
			obj['center_pos'] = [0,0];
			obj['result_cont'] = document.getElementById('result');
			obj['result_image'] = element.getElementsByTagName('IMG')[0];
			obj['crop_results'] = [];
			
			obj['dimensions'] = [<?php echo $width;?>,<?php echo $height;?>];
			//element.
			
			this.build = function(){
				if(obj.build) return false;
				obj.build = true;
				
				//set the image size
				
				obj['result_image'].style.maxWidth = returnCss(element.parentNode,"width")+'px';
				obj['result_image'].style.maxHeight = (returnCss(element.parentNode,"height")-2)+'px';
				
				
				var overlay = document.createElement('DIV');
				overlay.className = 'overlay';
				overlay.innerHTML = '<div id="top"></div><div id="right"></div><div id="bottom"></div><div id="left"></div><div id="center"></div>';
				obj['overlay'] = overlay;
				
				element.appendChild(obj['overlay']);
				if(obj['result_cont']){
					var o = obj['result_cont'];
					obj['result_max_w'] = returnCss(o,'width'),obj['result_max_h'] = returnCss(o,'height');
				
					obj['result_cont'].innerHTML = '<img src="'+obj['result_image'].src+'" style="max-width:'+obj['result_max_w']+'px;max-height:'+obj['result_max_h']+'px;" />';
				}
				
				
				obj['puller_array'] = [
					obj['top_pull'] = obj['overlay'].childNodes[0],
					obj['right_pull'] = obj['overlay'].childNodes[1],
					obj['bottom_pull'] = obj['overlay'].childNodes[2],
					obj['left_pull'] = obj['overlay'].childNodes[3],
					obj['center_pull'] = obj['overlay'].childNodes[4]
				]
				
				for(i=0;i<obj['puller_array'].length;i++){
					obj['puller_array'][i].addEventListener('mousemove',function(event){
						var e = i,event = event;
						return function(event){
							obj['obj'].drag(event,obj['puller_array'][e].id);
						}
					}(obj,i));
					
					obj['puller_array'][i].addEventListener('mousedown',function(event){
						var e = i;
						return function(event){
							event.preventDefault();
							obj['draggable'] = true;
							if(e == 0){
								obj['current'][e] = returnCss(obj['puller_array'][e].parentNode,'top');
								obj['start'][e] =  event.clientY;
								
							}else if(e == 1){
								obj['current'][e] = returnCss(obj['puller_array'][e].parentNode,'right');
								obj['start'][e] =  event.clientX;
							}else if(e == 2){
								obj['current'][e] = returnCss(obj['puller_array'][e].parentNode,'bottom');
								obj['start'][e] =  event.clientY;
							}else if(e == 3){
								obj['current'][e] = returnCss(obj['puller_array'][e].parentNode,'left');
								obj['start'][e] =  event.clientX;
							}else if(e == 4){
								obj['center_pos'] = [event.clientX,event.clientY];
								obj['current'][0] = returnCss(obj['puller_array'][e].parentNode,'top');
								obj['current'][1] = returnCss(obj['puller_array'][e].parentNode,'right');
								obj['current'][2] = returnCss(obj['puller_array'][e].parentNode,'bottom');
								obj['current'][3] = returnCss(obj['puller_array'][e].parentNode,'left');
								
							}
						}
					}(obj,i));
					
					
					obj['puller_array'][i].addEventListener('mouseup',function(event){
						var e = i;
						return function(event){
							
							obj['obj'].setPlace();
							
						}
					}(obj,i));
					
					obj['puller_array'][i].addEventListener('mouseout',function(){
						var e = i;
						return function(){
							obj['obj'].setPlace();
						}
					}(obj,i));
				}
				
				//Initialize the result
				returnResult();
			}
			
			this.returnResult = function(){
				if(obj['result_cont']){
					var w = obj['overlay'].offsetWidth,h = obj['overlay'].offsetHeight,sw,sh,ratio;
					if(w>h){
						ratio = h/w;
						obj['result_cont'].style.width = obj['result_max_w']+'px';
						obj['result_cont'].style.height = (ratio*obj['result_max_h'])+'px';
						obj['result_cont'].style.marginTop = ((obj['result_max_h']-(ratio*obj['result_max_h']))/2)+'px';
					}else{
						ratio = w/h;
						obj['result_cont'].style.height = obj['result_max_h']+'px';
						obj['result_cont'].style.width = (ratio*obj['result_max_w'])+'px';
						obj['result_cont'].style.marginTop = '0px';
					}
					
					var img_ratio_w = obj['result_max_w']/obj['container_width'];
					
					//obj['result_cont'].childNodes[0].style.width = parseInt(window.getComputedStyle(obj['result_cont']).getPropertyValue("width"))*img_ratio_w+'px';
				}
				
			}
			
			this.setPlace = function(){
			
				obj['draggable'] = false;
				obj['crop_results'] = []
				returnResult();
			}
			
			this.returnCss = function(element,css){
				if(isNaN(parseInt(window.getComputedStyle(element).getPropertyValue(css)))){
					return window.getComputedStyle(element).getPropertyValue(css);
				}else{
					return parseInt(window.getComputedStyle(element).getPropertyValue(css));
				}
			}
			
			this.drag = function(event,id){
				if(!obj['draggable']) return false;
				var c = obj['current'],min = 150;
				switch(id){
					case'top':
						var pos = 0,change;
						if(returnCss(obj['overlay'],'height') > min){
							change = c[pos]+(event.clientY - obj['start'][pos]);
							if((change)>=0){
								obj['overlay'].style.top = change+'px';
								//obj['overlay'].style.height = (c[2]-change)+'px';
							}
						}else{
							obj['overlay'].style.top = (returnCss(obj['overlay'],'top')-1)+'px';
						}
						
					break;
					case'right':
						var pos = 1,change;
						if(returnCss(obj['overlay'],'width') > min){
							c = c[pos];
							change = c-(event.clientX - obj['start'][pos]);
							if((change)>=0){
								obj['overlay'].style.right = change+'px';
							}
						}else{
							obj['overlay'].style.right = (returnCss(obj['overlay'],'right')-1)+'px';
						}
					break;
					case'bottom':
						var pos = 2,change;
						if(returnCss(obj['overlay'],'height') > min){
							change = c[pos]-(event.clientY - obj['start'][pos]);
							if((change)>=0){
								obj['overlay'].style.bottom = change+'px';
								//obj['overlay'].style.height = (c[2]-change)+'px';	
							}
						}else{
							obj['overlay'].style.bottom = (returnCss(obj['overlay'],'bottom')-1)+'px';
						}
					break;
					case'left':
						var pos = 3,change;
						if(returnCss(obj['overlay'],'width') > min){
							c = c[pos];
							change = c+(event.clientX - obj['start'][pos]);
							if((change)>=0){
								obj['overlay'].style.left = change+'px';
							}
						}else{
							obj['overlay'].style.left = (returnCss(obj['overlay'],'left')-1)+'px';
						}
					break;
					
					case 'center':
						<?php //change in the xpos ?>
						
						
						var x,y,xchange = event.clientX - obj['center_pos'][0],ychange = event.clientY - obj['center_pos'][1];
						<?php /*x = ( ( xchange ) > 0)? 'right':'left';
						y = ( ( ychange ) > 0)? 'bottom':'top';*/ ?>
						if(c[0] + ychange > 0 && c[2] - ychange > 0){
							obj['overlay'].style.top = ( c[0] + ychange )+'px';
							obj['overlay'].style.bottom = ( c[2] - ychange)+'px';
						}
						
						if(c[1] - xchange >= 0 && c[3] + xchange >= 0){
							obj['overlay'].style.right = ( c[1] - xchange )+'px';
							obj['overlay'].style.left = ( c[3] + xchange )+'px';
						}
						
					break;
				}
				
			}
			
			
			
			
			
			build();
		}
		<?php endif; ?>
	</script>
</head>
<body>
<div id="body">
	<div id="header">
		Cropping Photo Tool
	</div>
	<div class="cont">
	<?php if( isset( $_POST['image_upload']  ) ): ?>

		<?php
			ob_start();
				//header("Content-type: image/jpeg");
				if($image['type'] == 'image/png'):
					$type = 'png';
					$img = imagecreatefrompng($image['tmp_name']);
					imagepng($img);
					
				else:
					$type = 'jpeg';
					$img = imagecreatefromjpeg($image['tmp_name']);
					imagejpeg($img);
				
				endif;
			$i = ob_get_clean();
			$i = base64_encode($i);
		?>
				
				<!--<div id="floater">
					<center>
						<div id="result"></div>
					</center>
				</div>-->
				<div style="max-height:500px;max-width:900px;overflow-y:hidden;display:inline-block;">
					<div id="cropping_holder">
						<img src='data:image/<?php echo $type;?>;base64, <?php echo $i;?>'  class='cropping_image' />
					</div>
				</div>
				<div class="clear"></div>
			

	<?php else: ?>

		<div>
			<form action="" method="post" enctype="multipart/form-data">
				<input type="file" name="image" id="image" style="display:none;"/>
				<input type="hidden" name="image_upload" />
				<div style="text-align:center;">
					<div class="submitImage">Upload an image</div>
				</div>
			</form>
		</div>

	<?php endif; ?>
	</div>
</div>
</body>
