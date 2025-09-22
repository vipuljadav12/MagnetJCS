CKEDITOR.plugins.add( 'purple', {
    icons: 'purple',
    lang: ['en'],
    init: function( editor ) {
    	console.log('entered');

    	editor.addCommand( 'insertPurple', {
		    exec: function( editor ) {
		    	var selection = editor.getSelection();
		    	var el = selection.getStartElement();
		    	var parent = el.getParent();

		    	if (CKEDITOR.env.ie) {
				    selection.unlock(true);
				    var text = selection.getNative().createRange().text;
				} else {
				    var text = selection.getNative();
				}

				var element = CKEDITOR.dom.element.createFromHtml( '<div class="main-title-1 bg-primary text-white text-center p-5">' + text + '</div>' );
    			editor.insertElement(element);
    		//	parent.remove(true);

//		        var now = new Date();
//		        editor.insertHtml( 'The current date and time is: <em>' + now.toString() + '</em>' );
		    }
		});

		editor.ui.addButton( 'Purple', {
		    label: 'Insert Purple',
		    command: 'insertPurple',
		    toolbar: 'iwebtoolbar'
		});
        //Plugin logic goes here.
    }
});