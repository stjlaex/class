function activateCommentEditor(){
	tinymce.init({
	inline: true,
	selector : ".htmleditorarea",
	menubar: false,
	plugins:["paste"],
	paste_as_text: true,
	browser_spellcheck : true,
    toolbar: "bold italic bullist | undo redo selectall link",
	setup: function(editor) {
		editor.on('blur', function(event){
			if (editor.isDirty()) {
				editor.save();
				var elementObj = $(editor.getElement());
				var form = elementObj.closest('form');
				form.find('.flash-message .saving').fadeIn();
				
				$('textarea[name="' + elementObj.attr('name') + '"]').val(elementObj.html());
				
				var jqXHR = $.post(form.attr('action'), form.serialize(), function( data ){})
				.done(function(data) {
					data = JSON.parse(data);
					form.find('input[name="inmust"]').val(data.inmust);
					form.find('.flash-message .saving').fadeOut('slow');
				})
				.fail(function(data) {
					form.find('.flash-message .saving').text("An error occured saving this comment");
				})
			}
		});
		editor.on('keydown', function(evt){
			var elementObj = $(editor.getElement());
			var form = elementObj.closest('form');
			var lenid='#textlen'+elementObj.attr('name');
			var maxlenid='#maxtextlen'+elementObj.attr('name');
			var char_counter=form.find(maxlenid).val();
			var count=editor.getContent().replace(/<[^>]+>/g, '').length;
			if(char_counter==0){
				return; //no max length set
				}
			else{
				form.find(lenid).val(char_counter - count);
				}
			if(form.find(lenid).val()<=0){
				}
			else{
				  char_overflow_flag=0;
				}
			if(form.find(lenid).val()<=0){
					  alert("Comment should not be longer than " + char_counter + " characters");
			  }
			});
		editor.addCommand('post', function(){
			var elementObj = $(editor.getElement());
			var form = elementObj.closest('form');
			form.find('.flash-message .saving').fadeIn();
				
			$('textarea[name="' + elementObj.attr('name') + '"]').val(elementObj.html());
				
			var jqXHR = $.post(form.attr('action'), form.serialize(), function( data ){})
			.done(function(data) {
				data = JSON.parse(data);
				form.find('input[name="inmust"]').val(data.inmust);
				form.find('.flash-message .saving').fadeOut('slow');
			})
			.fail(function(data) {
				form.find('.flash-message .saving').text("An error occured saving this comment");
			})
		})
	}
	})
	$('.htmleditorarea')
}
