function switch_display(item, block_item, hide_item){
	$(document).ready(function(){
		$(item).mousemove(function(e){  
			$(block_item).css({			
				display: 'block'
			});
			$(hide_item).css({
				display: 'none'
			});
		});
		$(item).mouseout(function(e){ 
			$(block_item).css({
				display: 'none'
			});
			$(hide_item).css({
				display: 'block'
			});
		});
	});
}
switch_display('.bg-red','.notok','.hide');
switch_display('.bg-green','.ok','.hide');
switch_display('.bg-gray','.up','.hide');
switch_display('.bg-black','.upload','.hide');
switch_display('.bg-blue','.helptext','.hide');

