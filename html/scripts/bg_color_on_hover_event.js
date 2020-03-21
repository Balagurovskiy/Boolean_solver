function bg_colorize(item, r, g, b){
	$(document).ready(function(){	
		$(item).mousemove(function(e){
			 document.body.style.backgroundColor = 'rgb(' + r + ',' + g + ',' + b + ')';
		});
		$(item).mouseout(function(e){
		 document.body.style.backgroundColor = 'rgb(' + 255 + ',' + 255 + ',' + 255 + ')';
		}); 
	});
}
bg_colorize('.bg-black',200,200,200);
bg_colorize('.bg-gray',120,120,120);
bg_colorize('.bg-red',235,111,111);
bg_colorize('.bg-green',111,235,111);
bg_colorize('.bg-blue',110,180,225);
 

