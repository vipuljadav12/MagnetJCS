CKEDITOR.plugins.add( 'curly', {
    icons: 'curly',
    lang: ['en'],
    init: function( editor ) {
    	console.log('entered');

    	editor.addCommand( 'insertCurly', {
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

				var element = CKEDITOR.dom.element.createFromHtml( '<div class="sub-title-1">' + text + '</div>' );
    			editor.insertElement(element);
    		//	parent.remove(true);

//		        var now = new Date();
//		        editor.insertHtml( 'The current date and time is: <em>' + now.toString() + '</em>' );
		    }
		});

		editor.ui.addButton( 'Curly', {
		    label: 'Insert Curly',
		    command: 'insertCurly',
		    toolbar: 'iwebtoolbar'
		});
        //Plugin logic goes here.
    }
});