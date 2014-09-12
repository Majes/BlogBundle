Blog = {}

Blog.Common = {
	init: function(){

		$("a.editArticle").click(function(e){
    		e.preventDefault();
	
    		var blog = $(event.target).data('blog');
    		var article = $(event.target).data('article');
	
    		$.ajax({
    			url: $(this).attr('href'),
    			data: {blog: blog, article: article},
    			success: function(response){
    				$("#ajaxEditBlock").html(response);
    				$('#editModal').modal('show');
    			}
    		});
    		
    	});
	}
}

$(document).ready(function(){
	Blog.Common.init();
});