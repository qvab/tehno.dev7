jQuery(document).ready(function($){
	var lastFocusedInput;
	var lastCaretPosition;


	$('input').on('keyup mouseup', function(e) {
		inputListener(e.target)
	})

	$('textarea').on('keyup mouseup', function(e) {
		inputListener(e.target)
	})

	// keep last element and caret pos
	function inputListener(element) {
		lastFocusedInput = element;
		lastCaretPosition = element.selectionStart;
	}

    // toolbar btn setup
	tinymce.PluginManager.add( 'sticky_ads_btn_class', function( editor, url ) {
                 // Add Button to Visual Editor Toolbar
        editor.addButton('sticky_ads_btn_class', {
            title: 'Вставить прилипающую рекламу',
            image: url.replace('js', 'img') + '/icon.png',
            cmd: 'sticky_ads_btn_class',
        });

        editor.addCommand('sticky_ads_btn_class', function() {
		    // Check we have selected some text that we want to link
			$('#sticky_list_container').fadeToggle();
		    // Insert selected text back into editor, wrapping it in an anchor tag
		    //editor.execCommand('mceInsertContent', false, tag);
		});

		editor.on('focus mousedown keyup change', function(e) {
			lastFocusedInput = 'RICH_EDITOR';
		})
    });

    for (var i = sticky_posts.length - 1; i >= 0; i--) {
    	$('#sticky_list_container').append('<div class="sticky-ad-item" data-tag="[sticky-ad id='+sticky_posts[i][0]+']"><< '+sticky_posts[i][1]+'</div>');
    }

	$('.sticky-ad-item').click(function (e) {

		var tag = $(this).attr('data-tag');
		if (lastFocusedInput === "RICH_EDITOR") {
			tinymce.activeEditor.selection.setContent(tag);
		} 

		if (lastFocusedInput.tagName === "INPUT" || lastFocusedInput.tagName == "TEXTAREA") {
			let text = $(lastFocusedInput).val();
	    	var output = [text.slice(0, lastCaretPosition), tag, text.slice(lastCaretPosition)].join('');
	    	$(lastFocusedInput).val(output);
		}

	})


})