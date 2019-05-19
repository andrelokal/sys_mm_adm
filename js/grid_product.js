$(function(){

	$('.btn.copy').unbind('click').click(function(){

		blockWait( $('#datagrid') );
		var id = $(this).parents('tr:eq(0)').data('id');
		
		var clt = new $PHP( "product" );
		clt.loaded = function(){
			OpenPage("/form", '#page-content', id, function(){
				OpenFormController( ctl, id, {}, null, function(){
					alert('oi')
				})
			});			
		}
		
		
		return false;
	})
	
})