/**
 * Featured on SitePoint.com
 * Developed by Craig Buckler (@craigbuckler) of OptimalWorks.net
 *
 * Extended and adapted for ClaSS
 *
 */
function documentdropInit(){
	/**
	 * Some general useful stuff
	 */
	var filesThatWereDropped = new Array();
	/* getElementById */
	function $id(id) {
		return document.getElementById(id);
		}

	/* output information */
	function Output(msg) {
		var m = $id('messages');
		m.innerHTML = msg + m.innerHTML;
		}


	/* file drag hover */
	function FileDragHover(e) {
		e.stopPropagation();
		e.preventDefault();
		e.target.className = (e.type == 'dragover' ? 'hover' : '');
		}
       
	/* file selection */
	function FileSelectHandler(e) {

		/* cancel event and hover styling */
		FileDragHover(e);

		/* fetch FileList object */
		var files = e.target.files || e.dataTransfer.files;

		/* process all File objects */
		for (var i = 0, f; f = files[i]; i++) {
			ParseFile(f);
			if($id('FILECONTEXT').value!='icon') UploadFile(f);
			}

		

		}


	/* output file information */
	function ParseFile(file) {
	   if($id('FILECONTEXT').value!='icon'){
		Output(
			'<p>File: <strong>' + file.name +
			'</strong> type: <strong>' + file.type +
			'</strong> size: <strong>' + file.size +
			'</strong> bytes</p>'
			);
	   }
	   if($id('FILECONTEXT').value=='icon'){
		// update info by cropping (onChange and onSelect events handler)
		function updateInfo(e) {
			$('#x1').val(e.x);
			$('#y1').val(e.y);
			$('#x2').val(e.x2);
			$('#y2').val(e.y2);
			$('#w').val(e.w);
			$('#h').val(e.h);
		};

		// clear info by cropping (onRelease event handler)
		function clearInfo() {
			$('.info #w').val('');
			$('.info #h').val('');
		};
		/* display an image*/
		if(file.type.indexOf('image')==0){
			var reader = new FileReader();
			reader.onload=function(e){
				//TODO: Refresh the preview image for crop when the user tries to preview another one
				$id('DRAG').value='true';
				$id('dragbutton').style.display='block';
				$id('submitbutton').style.display='none';

				// display the image for resizing
				$id('preview').src=e.target.result;
				
				// display step 2
				$('.step2').fadeIn(500);

				// Create variables (in this scope) to hold the Jcrop API and image size
				var jcrop_api, boundx, boundy;

				// destroy Jcrop if it is existed
				if (typeof jcrop_api != 'undefined') 
				jcrop_api.destroy();

				// initialize Jcrop
				$('#preview').Jcrop({
					minSize: [28.5,32], // min crop size
					aspectRatio : 0.88, // keep aspect ratio 1:1
					bgFade: true, // use fade effect
					bgOpacity: .3, // fade opacity
					setSelect:[ 160, 180, 0, 0 ],
					onChange: updateInfo,
					onSelect: updateInfo,
					onRelease: clearInfo
				}, function(){
					// use the Jcrop API to get the real image size
					var bounds = this.getBounds();
					boundx = bounds[0];
					boundy = bounds[1];

					// Store the Jcrop API in the jcrop_api variable
					jcrop_api = this;
				});
			}
			reader.readAsDataURL(file);
		}
		filesThatWereDropped.push(file); 
	   }
	}


 	function UploadIconFiles(e) {
		var lid = $id('FILESID').value;
		var ownertype = $id('OWNERTYPE').value;

		/* process all File objects*/
 		while(filesThatWereDropped.length > 0){
 			var f = filesThatWereDropped.pop();
			/* uploads the file */
 			UploadFile(f);
			}
	 	}


	function UploadFile(file) {
		var xhr = new XMLHttpRequest();
		var owner = $id('FILEOWNER').value;
		var context = $id('FILECONTEXT').value;
		var linkedid = $id('FILELINKEDID').value;
		if(context=='icon'){
			var lid = $id('FILESID').value;
			var ownertype = $id('OWNERTYPE').value;
			var drag_var = $id('DRAG').value;
			var x1 = $id('x1').value;
			var y1 = $id('y1').value;
			var x2 = $id('x2').value;
			var y2 = $id('y2').value;
			var w = $id('w').value;
			var h = $id('h').value;
			}
		else{
			var drag_var = 'true';
			}

		var scriptpath=$id('formdocumentdrop').action;

		// limit upload by filesize
		if(xhr.upload && file.size <= $id('MAX_FILE_SIZE').value){
			if(context!='icon'){
				// create progress bar
				var o = $id('progress');
				var progress = o.appendChild(document.createElement('p'));
				progress.appendChild(document.createTextNode('upload ' + file.name));

				// progress bar
				xhr.upload.addEventListener('progress',function(e){
					var pc = parseInt(100 - (e.loaded / e.total * 100));
					progress.style.backgroundPosition = pc + '% 0';
					}, false);
				// file received/failed
				xhr.onreadystatechange=function(e){
					if(xhr.readyState==4){
						if(xhr.status==200){
							progress.className='success';
							}
						else{
							progress.className='failure';
							}
						}
					}
				}
			else if(context=='icon'){
				xhr.onreadystatechange=function(e){
					/* Redirects to staff or student profile page*/
					xhr.upload.addEventListener('load',function(e){
						if(ownertype=='staff'){
							window.location = "admin.php?current=staff_details.php&seluid="+lid;
							}
						else{
							window.location = "infobook.php?current=student_view.php&sid="+lid;
							}
						});
					}
				}

			// start upload
			xhr.open('POST',scriptpath,true);
			xhr.setRequestHeader('FILENAME',file.name);
			xhr.setRequestHeader('FILEOWNER',owner);
			xhr.setRequestHeader('FILECONTEXT',context);
			xhr.setRequestHeader('FILELINKEDID',linkedid);
			xhr.setRequestHeader('DRAG',drag_var);
			if(context=='icon'){
				xhr.setRequestHeader('FILESID',lid);
				xhr.setRequestHeader('OWNERTYPE',ownertype);
				xhr.setRequestHeader('X1',x1);
				xhr.setRequestHeader('Y1',y1);
				xhr.setRequestHeader('X2',x2);
				xhr.setRequestHeader('Y2',y2);
				xhr.setRequestHeader('W',w);
				xhr.setRequestHeader('H',h);
				}
			
			xhr.send(file); //TODO: Refresh left div to display the new documents
			}
		else{
			if(context=='icon') {
				if(ownertype=='staff'){javascript:location.href="admin.php?current=staff_photo.php&cancel=staff_details.php";}
				else{javascript:location.href="infobook.php?current=student_photo.php&cancel=student_view.php";}
				}
			alert('File size is too big.');
			}
		}



	function FileDelete() {

		var xhr = new XMLHttpRequest();
		var owner = $id('FILEOWNER').value;
		var context = $id('FILECONTEXT').value;
		var form = $id('formfiledelete');
		var scriptpath=form.action;
		var data = new FormData(form);

		var answer=confirmAction('Delete');

		if(answer){
			/* delete succeeded/failed */
			xhr.onreadystatechange=function(e){
				if(xhr.readyState==4){
					if(xhr.status==200){
						progress.className='success';
						xmlRecord=xhr.responseXML;
						var recordId=xmlRecord.getElementsByTagName("id_db").item(0).firstChild.data;
						var containerDiv=document.getElementById('filecontainer'+recordId);
						while(containerDiv.hasChildNodes()){
							containerDiv.removeChild(containerDiv.childNodes[0]);
							}
						}
					else{
						progress.className='failure';
						}
					}
				};

			/* start upload */
			xhr.open('POST',scriptpath,true);
			xhr.send(data);
			
			}
		}


	/* Initliaise */
	var fileselect = $id('fileselect'),
	filedrag = $id('filedrag'),
	deletebutton = $id('deletebutton');
	submitbutton = $id('dragbutton');


	// file select
	fileselect.addEventListener('change', FileSelectHandler, false);
	fileselect.style.display = 'none';
	
	// file drop
	filedrag.addEventListener('dragover', FileDragHover, false);
	filedrag.addEventListener('dragleave', FileDragHover, false);
	filedrag.addEventListener('drop', FileSelectHandler, false);
	filedrag.style.display = 'block';
	
	// file upload
	if($id('FILECONTEXT').value=='icon'){submitbutton.addEventListener("click", UploadIconFiles, false);}

	// file delete
	if(deletebutton){
		deletebutton.addEventListener('click', FileDelete, false);
	}

}
