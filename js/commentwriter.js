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
				console.log(form.find('.flash-message .saving'))
				form.find('.flash-message .saving').fadeIn();
				
				$('input[name="' + elementObj.attr('name') + '"]').val(elementObj.html());
				
				var jqXHR = $.post(form.attr('action'), form.serialize(), function( data ) {
					//console.log(data);
				})
					.done(function(event) {
					form.find('.flash-message .saving').fadeOut('slow');
				})
					.fail(function(event) {
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
				  if(char_overflow_flag<3) {
					  alert("Comment should not be longer than " + char_counter + " characters");
					  char_overflow_flag++;
				  }
			  }
			});
		}
	})
	$('.htmleditorarea')
}
