/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.allowedContent = true;
	config.extraAllrowedContent = 'iframe[*]';
	config.extraPlugins = 'colorbutton,colordialog,justify,link,forms,flash,smiley,pagebreak,div,font,lineutils,clipboard,widget,dialogui,dialog,btgrid,print,save,preview,newpage,showblocks,leaflet,templates,selectall,find,slideshow,iframe,notification,notificationaggregator,autoembed,autolink,undo,embedbase,embed,collapsibleItem,accordionList,purple,curly,textindent,indentblock';

	config.toolbar = [
		{ name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
		{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField', 'purple' ] },
		'/',
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
		{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'Slideshow' ] },
		'/',
		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'tools', items: [ 'Maximize', 'ShowBlocks','btgrid' ] },
		{ name: 'iwebtoolbar', items: ['my-combo' ] },

	];

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre';
	config.enterMode = 'CKEDITOR.ENTER_DIV';
      // Pressing Shift+Enter will create a new <p> element.
    config.shiftEnterMode = 'CKEDITOR.ENTER_BR';
	config.title = false;

	// Simplify the dialog windows.
	//config.removeDialogTabs = 'image:advanced;link:advanced';
	
	// config.extraPlugins = 'leaflet'; 

	config.embed_provider = '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}';
	
	config.allowedContent = true;
	config.entities = false;
	// config.entities_processNumerical = false;
	config.fillEmptyBlocks = false;

	// config.protectedSource.push(/<\?[\s\S]*?\?>/g);
	//config.protectedSource.push(/<em[^>]*><\/em>/g);
	// config.entities_processNumerical = true;
	// config.entities_processNumerical = 'force';
};
// config.extraPlugins = 'justify';

// CKEDITOR.dtd.$removeEmpty.i = 0;
	
  $.each(CKEDITOR.dtd.$removeEmpty, function (i, value) {
        CKEDITOR.dtd.$removeEmpty[i] = false;
    });



// Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
// config.toolbar = [
// 	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
// 	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
// 	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
// 	{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
// 	'/',
// 	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
// 	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
// 	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
// 	{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
// 	'/',
// 	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
// 	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
// 	{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
// 	{ name: 'others', items: [ '-' ] },
// 	{ name: 'about', items: [ 'About' ] }
// ];

// // Toolbar groups configuration.
// config.toolbarGroups = [
// 	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
// 	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
// 	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
// 	{ name: 'forms' },
// 	'/',
// 	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
// 	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
// 	{ name: 'links' },
// 	{ name: 'insert' },
// 	'/',
// 	{ name: 'styles' },
// 	{ name: 'colors' },
// 	{ name: 'tools' },
// 	{ name: 'others' },
// 	{ name: 'about' }
// ];