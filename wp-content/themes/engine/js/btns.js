(function() {
        tinymce.PluginManager.add('tinymce_dropbutton', function( editor, url ) {
           editor.addButton( 'tinymce_dropbutton', {
                 text: 'Blocs',
                 icon: false,
                 type: 'menubutton',
                 menu: [
   
                       {
                        text: 'Plus',
                        onclick: function() {
                          var text = editor.selection.getContent({
                            'format': 'html'
                          });
 
                          editor.execCommand('mceReplaceContent', false, '[advantages]' + text + '[/advantages]');
                         }
                       },
 
                       {
                        text: 'Minus',
                        onclick: function() {
                          var text = editor.selection.getContent({
                            'format': 'html'
                          });
 
                          editor.execCommand('mceReplaceContent', false, '[disadvantages]' + text + '[/disadvantages]');
                         }
                       },
 
                       {
                        text: 'Mesta',
                        onclick: function() {
                          var text = editor.selection.getContent({
                            'format': 'html'
                          });
 
                          editor.execCommand('mceReplaceContent', false, '[badge]' + text + '[/badge]');
                         }
                       }
                       ]
              });
        });
})();