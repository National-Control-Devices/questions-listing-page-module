jQuery(document).ready(function(){
	jQuery("#add-question-form").validate({
		rules: {
		  	title:{
			    required: true  		
		  	},
		  	details:{
			    required: true,
			}
		  },
		messages: {
			title:{
				required: "Please Enter title."
			},
			details:{
				required: "Please Enter details.",
			}
		  }
	});	
});
