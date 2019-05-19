function OpenFormController_training( phpjs, id, cfg){
	OpenFormController( phpjs, id, cfg, function(){
		if( $("#training_id").val()){
			FormCadatroExercicios()				
		}		
	})
}