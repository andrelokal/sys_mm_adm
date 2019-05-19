$(function(){

	/*$(".multiple_tamanhos").multiple({
		prefix : "tam",
		input: $('#tamanhos'),
		loaded: function( elem ){

			

		},
		afterDuplicate : function( elem ){
			
			ApplyJsInput()

		}
	})*/

	$('.form-actions .bkpt').after(" <button type='button' class='btn purple duplicate'><i class='fa fa-copy'></i> Duplicar Produto</button>")
	$('.btn.duplicate').unbind('click').click(function(){
		Duplicar()
		return false;
	})


	$('#categoria_id').change(function(){
		LoadProdVinc( $(this).val() )
	}).change()
	
	$('.tooltips').tooltip();
})

function LoadProdVinc( categ ){

	$('.select2-container').remove();
	$('#sel_prod_vinculado').remove()
	
	if( !categ ) return; 

	var sel = $('<select>')
	sel.attr({
		name : 'sel_prod_vinculado',
		id : 'sel_prod_vinculado',
		'data-type': 'select-range',
		class : 'form-control'
	})

	var prod = new $PHP('product');
	prod.loaded = function(){
		prod.limit = 1000;
		prod.categoria_id = categ;
		prod.call('get',[], function( ret ){

			if( prod.data.grid.data.length ){
				sel.append("<option value=''> Vincular produto </option>")

				var data = prod.data.grid.data;
				for( var i in data ){

					if( $('#id').val() == data[i].id ){
						continue;
					}

					var option = $('<option>')
					var cod = (data[i].codigo ? data[i].codigo + " - " : '');
					option.html( cod + data[i].nome );
					option.prop('value', data[i].id )
					option.attr('unid', data[i].unidade_id )
					sel.append( option )
				}

				$('#prod_vinculado').after(sel)	;
				sel.change(function(){
					$('#prod_vinculado').val( $(this).val() )
					NomeVinculo( $(this).val() )
				})
				sel.val( $('#prod_vinculado').val() ).change()

				InitJs()
			}
			
		})
	}
} 

function NomeVinculo( id ){

	$(".vincName").remove()

	if( id ){
		
		var unid = $('#sel_prod_vinculado > option[value='+ id +']').attr('unid');
		$('#unidade_id').val( unid )
		$('#unidade_id').attr('readonly','readonly');

		var html = '<div class="input-group vincName" style="float:left; width: 270px">'+
					'<span class="input-group-addon">'+
					'<i class="fa fa-link"></i>'+
					'</span>'+
					'<input type="text" id="nome_vinculo_tmp" name="nome_vinculo_tmp" class="form-control" required=required />'+
					'</div>';

		$('#nome').attr('readonly','readonly').css({ 'width':'auto', 'float':'left'}).after( html );
			
		$('#nome_vinculo_tmp').val( $('#nome_vinculo').val() ).keyup(function(){
			$('#nome_vinculo').val($('#nome_vinculo_tmp').val())
		})

		if( !$('#nome_vinculo_tmp').val() )  $('#nome_vinculo_tmp').focus()
		

	} else {

		$('#nome').removeAttr('readonly').removeAttr('style');
		//$('#unidade_id').val('')
		$('#unidade_id').removeAttr('readonly');

	}

}

function Duplicar(){

	$('#categoria_id').change();
	$('#codigo').val('');
	$('#codbar').val('');
	$('#id').val('');
	$('#valor').val('');
	$('#nome').focus()

}