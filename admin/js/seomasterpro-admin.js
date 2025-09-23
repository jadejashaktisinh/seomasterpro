jQuery( document ).ready(function($){
console.log($('#all_auto_generate'));
	
	$("#all_auto_generate").on('change',function(){
		let isCheked = $(this).prop("checked");
		console.log(isCheked);
		
		isCheked ? $('#geneate_select_post_type_container').show() : $('#geneate_select_post_type_container').hide() 

	})
})