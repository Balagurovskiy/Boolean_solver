
var SUB = 10;
function bg_slide(item){
	$(document).ready(function(){
		$(item).mousemove(function(e){
			var x = (window.screen.width/2 - e.pageX ) / SUB;
			var y = (window.screen.height/2 - e.pageY ) / SUB;
			document.body.style.backgroundPosition = x + 'px ' + y + 'px';
		});    
	});
}
 

bg_slide('.middle');
bg_slide('.left');
bg_slide('.right');

