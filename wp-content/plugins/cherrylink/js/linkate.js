jQuery(document).ready(function($){
    // cl_ prefix for variables to prevent any conflicts
    // fcl_ prefix for functions to prevent any conflicts
    const T_WAIT_EDITOR_INPUT = 200; // look timeOutChecker() 
    const T_WAIT_FILTER = 200; // delay on filter input
    const T_WAIT_FILTER_CB = 0; // delay on checkbox change
    const T_WAIT_TOTAL_LINKS = 100; // delay because function called twice
    const T_WAIT_SHOW_PANEL = 100; // delay on open panel

    var cl_total_links = 0; // total links from list or all from text
    var cl_allow_multilink = $('#multilink').attr('data-value') == "checked" ? true : false;
    var cl_exists_class = cl_allow_multilink ? 'link-exists-multi' : 'link-exists';

    var cl_list_links = $('div[class*="linkate-link"]:not(.link-term)');
    var cl_list_terms = $('div[class*="link-term"]');
    var cl_open_button = $('#linkate-button');
    var cl_editor_textarea = $("#content")[0];

    var cl_wp_ver_old = $('#wp_ver').attr('data-value');

    $('#linkate-box').removeClass('postbox').addClass('linkate-custom-box');
    $('#linkate-box').find('button').remove();    
    $('#linkate-box').append('<div class="linkate-close-btn">&#x2716;</div>'); // close btn
    $('#linkate-box').find('h2').after('<div class="linkate-filter-bar"><div><div><input id="hide_that_exists" type="checkbox"><label class="'+cl_exists_class+'" for="hide_that_exists">LINK</label></div><div><input id="show_that_exists" type="checkbox"><label for="show_that_exists" class="linkate-link">LINK</label></div></div><div><input id="filter_by_title" type="text" placeholder="Фильтр"></div></div><div class="linkate-tabs"><div class="tab tab-articles linkate-tab-selected">Записи</div><div class="tab tab-taxonomy">Таксономии</div></div>');  
    $('#linkate-box').append('<div class="linkate-total-links"><div class="total-links-header">ИСХОДЯЩИХ ССЫЛОК</div><div class="total-links-counter"><div>Из списка</div><div id="links-count-from-list">0</div><div>всего в тексте</div><div id="links-count-total">0</div></div></div>');
    
    $('#show_that_exists').prop('checked', true);
    $('#hide_that_exists').prop('checked', true);

    $('.link-preview').click(function() {
        let url = $(this).parent().parent().find('div.linkate-link').attr('data-url');
        window.open(url,'_blank');
    });

    $('.link-counter').click(function() {
        if ($(this).hasClass('link-counter-good') || $(this).hasClass('link-counter-bad')) {
            let parent = $(this).parent()[0];
            let url = '';
            if (parent.tagName == 'LI') {
                url = $(parent).find('div.linkate-link').attr('data-url');
            } else {
                url = $(parent).parent().find('div.linkate-link').attr('data-url');
            }
            
            if(fcl_isTinyMCE()) {
                selectExistingTinyMCE(url);
            }
            else {
                selectExistingTextarea(url); 
            }
        }
    });


    /* =================== Tabs ==================== */
    
    $('.container-articles').show();
    $('.container-taxonomy').hide();

    $('div.tab').click(function() {
        if($(this).hasClass('tab-articles')) {
            $('.container-articles').show();
            $('.container-taxonomy').hide();
            $('div.tab-articles').addClass('linkate-tab-selected');
            $('div.tab-taxonomy').removeClass('linkate-tab-selected');
        } else {
            $('.container-articles').hide();
            $('.container-taxonomy').show();
            $('div.tab-articles').removeClass('linkate-tab-selected');
            $('div.tab-taxonomy').addClass('linkate-tab-selected');
        }
    });

    /* =================== END Tabs ==================== */


    /* =================== Input/change listeners ==================== */

    $('#hide_that_exists').change(function() {
        timeOutLinksFilter(T_WAIT_FILTER_CB);
    });    
    $('#show_that_exists').change(function() {
        timeOutLinksFilter(T_WAIT_FILTER_CB);
    });
    $('#filter_by_title').on('input propertychange', function() {
        timeOutLinksFilter(T_WAIT_FILTER);
    });
    
    if (cl_editor_textarea)
        fcl_setListeners(); // if there is no editor - don't bother to load shit

    cl_open_button.click(function () {
        // Looking for links in text
        timeOutLinksChecker(T_WAIT_SHOW_PANEL);
        $('#linkate-box').toggleClass('hide-or-show');

        
    });
    
    var cl_timerCheck;
    function timeOutLinksChecker (delay) { // wait after input some time, if input repeats - null timer and wait again, then call func
        if (cl_timerCheck) {
            clearTimeout(cl_timerCheck);
        }        
        cl_timerCheck = setTimeout(function() { 
            cl_total_links = 0;
            console.time('checkTextLinks');
            fcl_checkTextLinks(cl_list_links); 
            fcl_checkTextLinks(cl_list_terms); 
            console.timeEnd('checkTextLinks');
        }, delay);
    }

    var cl_timerFilter;
    function timeOutLinksFilter (delay) { // wait after input some time, if input repeats - null timer and wait again, then call func
        if (cl_timerFilter) {
            clearTimeout(cl_timerFilter);
        }        
        cl_timerFilter = setTimeout(function() { 
            console.time('filterLinks');
            fcl_filterLinks(cl_list_links); 
            fcl_filterLinks(cl_list_terms); 
            console.timeEnd('filterLinks');
        }, delay);
    }

    var cl_timerTotalLinks;
    function timeOutTotalCount (delay, content) { // to prevent second call of the function (it's called for links and terms separately)
        if (cl_timerTotalLinks) {
            clearTimeout(cl_timerTotalLinks);
        }        
        cl_timerTotalLinks = setTimeout(function() { 
            $("#links-count-total").html(fcl_getAllIndexes(content, 'href=', 0, 0));
        }, delay);
    }

    function fcl_setListeners() {

        $("#content").on('input propertychange',function(e){
            timeOutLinksChecker(T_WAIT_EDITOR_INPUT);
            
        });
        if(typeof tinymce !== 'undefined') {        
            tinymce.on('SetupEditor', function (editor) {
                console.log(editor.editor);
                if (cl_wp_ver_old == 1) {
                    editor.on('ExecCommand change', function (event) {
                        timeOutLinksChecker(T_WAIT_EDITOR_INPUT);
                    });
                } else {
                    editor.editor.on('ExecCommand change', function (event) {
                        timeOutLinksChecker(T_WAIT_EDITOR_INPUT);
                    });
                }
            });
        }

        $(".linkate-close-btn").on('click', function () {
            $('#linkate-box').toggleClass('hide-or-show');
        });

    }

    /* =================== END listeners ==================== */
    
    function fcl_isTinyMCE() {
        return !($('#wp-content-wrap').hasClass('html-active'));
    }

    function fcl_getEditorContent() {
        let content = "";
        if (fcl_isTinyMCE()) {
            content = tinymce.get('content').getContent();
        } else {
            content = cl_editor_textarea.value;
        }
        return content;
    }
    
    // check if some links already exist
    function fcl_checkTextLinks(list) {
        let content = fcl_getEditorContent();
        let url;
        let limit = list.length;

        let hide_exist = $('#hide_that_exists').is(':checked');
        let show_exist = $('#show_that_exists').is(':checked');
        let filter_word = $('#filter_by_title').val().toUpperCase();

        let el, text, contains, hide;
        for (let i = limit - 1; i >= 0; i--) {
            el = list[i];
            if (el.classList.contains('linkate-link')) {
                url = el.getAttribute('data-url');
                let count = fcl_getAllIndexes(content, url, 0, 0); 
                cl_total_links = cl_total_links + count;
                fcl_markNumber(el, count);

                if (fcl_hideItem(el,hide_exist,show_exist,filter_word)) {
                        el.parentElement.classList.add('link-hidden');
                } else {
                    el.parentElement.classList.remove('link-hidden');
                }   
            }    
        }
        $("#links-count-from-list").html(cl_total_links);
        timeOutTotalCount(T_WAIT_TOTAL_LINKS, content);
    }

    function fcl_filterLinks(list) {
        let el;

        let hide_exist = $('#hide_that_exists').is(':checked');
        let show_exist = $('#show_that_exists').is(':checked');
        let filter_word = $('#filter_by_title').val().toUpperCase().replace(/Ё/g, 'Е');

        for (let i = list.length - 1; i >= 0; i--) {
            el = list[i];
            if (el.classList.contains('linkate-link') && !el.classList.contains('linkate-terms-devider')) {     
                if (fcl_hideItem(el,hide_exist,show_exist,filter_word)) {
                        el.parentElement.classList.add('link-hidden');
                } else {
                    el.parentElement.classList.remove('link-hidden');
                }      
            }       
        }
    }

    function fcl_hideItem(el,hide_exist,show_exist,filter_word){
        let text, contains, hide, hide_not_exist;
        text = el.querySelector('.link-title').innerHTML.toUpperCase().replace(/Ё/g, 'Е');
        // if (!filter_word && !hide_exist) { // if there are no filters
        //     return false;
        // }
        hide = !hide_exist && el.querySelector('.link-title').classList.contains(cl_exists_class); // if we checked hide cb and link exists in text
        if (hide) { // hide by checkbox, if exists in text
            return true;
        }
        contains = text.indexOf(filter_word) !== -1; // if we are using quick filtering and found smth       
        if (!contains) {
            return true;
        }

        hide_not_exist = !show_exist && !el.querySelector('.link-title').classList.contains(cl_exists_class);
        if (hide_not_exist) {
            return true;
        }
        return false;            
    }

    function fcl_markNumber(el,count) {
        let num = el.parentElement.querySelector('.link-counter');
        let title = el.querySelector('.link-title');

        if (count > 0) {
            if (!title.classList.contains(cl_exists_class)) {
                title.classList.add(cl_exists_class);
            }
            num.innerText = '[ ' +count+ ' ] ' ;   
            if (count > 1) {
                num.classList.remove('link-counter-good');
                num.classList.add('link-counter-bad');
            } else {
                num.classList.remove('link-counter-bad');
                    num.classList.add('link-counter-good');
            }                 
        } else {
            if (title.classList.contains(cl_exists_class)) {
                num.innerText = '[ 0 ]' ;
                title.classList.remove(cl_exists_class);
                num.classList.remove('link-counter-bad');
                num.classList.remove('link-counter-good');
            }
        }
    }
    
    // When pressed link which is already in text, it's url will be found and selected in the editor
    function selectExistingTextarea(url) {
        let start = cl_editor_textarea.value.indexOf('href="'+url+'"');
        if (start !== -1) start = start + 6;
        let end = start + url.length;
        
        cl_editor_textarea.setSelectionRange(start, end);
        
        let charsPerRow = cl_editor_textarea.cols;
        let selectionRow = (start - (start % charsPerRow)) / charsPerRow;
        let lineHeight = cl_editor_textarea.clientHeight / cl_editor_textarea.rows;
        
        // scroll !!
        cl_editor_textarea.scrollTop = lineHeight * selectionRow;
        cl_editor_textarea.focus();
    }
    
    function selectExistingTinyMCE(url) {
        let selection = tinyMCE.activeEditor.dom.select('a[href="'+ url +'"]')[0]; 
        tinyMCE.activeEditor.selection.select(selection);
        selection.scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"});
    }
    
    function replaceSelectedLink(url, title, title_seo, categorynames, date, author, postid, imagesrc, anons, event) {

        // decode from base64 link template
        let temp_before = decodeURIComponent(atob($('#link_template').attr('data-before')))
            .replace(/{url}/g, url)
            .replace(/{title}/g, title)
            .replace(/{title_seo}/g, title_seo)
            .replace(/{categorynames}/g, categorynames)
            .replace(/{date}/g, date)
            .replace(/{author}/g, author)
            .replace(/{postid}/g, postid)
            .replace(/{imagesrc}/g, imagesrc)
            .replace(/{anons}/g, anons)
            .replace(/\+/g, ' ')
            .replace(/\\/g, '');
        let temp_after = decodeURIComponent(atob($('#link_template').attr('data-after')))            
            .replace(/{url}/g, url)
            .replace(/{title}/g, title)
            .replace(/{title_seo}/g, title_seo)
            .replace(/{categorynames}/g, categorynames)
            .replace(/{date}/g, date)
            .replace(/{author}/g, author)
            .replace(/{postid}/g, postid)
            .replace(/{imagesrc}/g, imagesrc)
            .replace(/{anons}/g, anons)
            .replace(/\+/g, ' ')
            .replace(/\\/g, '');
        
        if (fcl_isTinyMCE()) {
            replaceSelectedTinyMCE(temp_before, temp_after, event, title);
            tinymce.activeEditor.fire('change');
        } else {
            replaceSelectedTextarea(temp_before, temp_after, event, title);
            $("#content").trigger('propertychange');
        }
    }
        
    function replaceSelectedTerm(url, title, taxonomy, event) {
        // decode from base64 link template
        let temp_before = atob($('#term_template').attr('data-before'))
            .replace(/{url}/g, url)
            .replace(/{title}/g, title)
            .replace(/{taxonomy}/g, taxonomy)
            .replace(/\\/g, '');
        let temp_after = atob($('#term_template').attr('data-after'))            
            .replace(/{url}/g, url)
            .replace(/{title}/g, title)
            .replace(/{taxonomy}/g, taxonomy)
            .replace(/\\/g, '');
        
        if (fcl_isTinyMCE()) {
            replaceSelectedTinyMCE(temp_before, temp_after, event, title);
            tinymce.activeEditor.fire('change');
        } else {
            replaceSelectedTextarea(temp_before, temp_after, event, title);
            $("#content").trigger('propertychange');
        }
    }
    
    function replaceSelectedTextarea(temp_before, temp_after, event, title) {
        let start = cl_editor_textarea.selectionStart;
        // obtain the index of the last selected character
        let finish = cl_editor_textarea.selectionEnd;
        
        let before = cl_editor_textarea.value.substring(0, start);
        let between = cl_editor_textarea.value.substring(start, finish);
        let after = cl_editor_textarea.value.substring(finish, cl_editor_textarea.value.length);
        
        let arr = trimSelection(between);
        let text;

        if (event.ctrlKey) { // IF CTRL+CLICK - insert <a %template%>title</a>
            text = before + temp_before + title + temp_after  + after;
        } else {
            if (arr['hasSpaces'] == true) {
                text = before + arr['first'] + temp_before + arr['selection'] + temp_after + arr['last'] + after;
            } else {
                text = before + temp_before + between + temp_after  + after;
            }
        }

        cl_editor_textarea.value = text;
    }
    
    function replaceSelectedTinyMCE(temp_before, temp_after, event, title) {
        let selection = tinymce.activeEditor.selection.getContent();

        if (event.ctrlKey) { // IF CTRL+CLICK - insert <a %template%>title</a>
            tinymce.activeEditor.selection.setContent(temp_before + title + temp_after);
        } else {        
            if (selection) {
                let arr = trimSelection(selection);
                if (arr['hasSpaces'] == true) {
                    tinymce.activeEditor.selection.setContent(arr['first'] + temp_before + arr['selection'] + temp_after + arr['last']);
                } else {
                    tinymce.activeEditor.selection.setContent(temp_before + selection + temp_after);
                }
            }
            else {
                tinymce.activeEditor.execCommand('mceInsertContent', false, temp_before+ 'ТЕКСТ_ССЫЛКИ' +temp_after);
            }
        }
    }

    // check for spaces before/after
    function trimSelection (selection) {
        let arr = [];
        if (selection) {
            selection.charAt(0) === ' ' ? arr['first'] = ' ' : arr['first'] = ''
            selection.charAt(selection.length-1) === ' ' ? arr['last'] = ' ' : arr['last'] = ''
            arr['hasSpaces'] = false;
            if (arr['first'] == " " || arr['last'] == " ") {
                arr['hasSpaces'] = true;
                arr['selection'] = selection.trim();
            }
            return arr;
        }
        arr['hasSpaces'] = false;
        return arr; 
    }
        
    cl_list_links.click(function (e) {
        prepareLinkTemplate(e);
    });    

    cl_list_terms.click(function (e) {
        prepareTermTemplate(e);
    });

    function prepareLinkTemplate(e) {
        let url = getAttr(e.target, 'data-url');
        let title = getAttr(e.target, 'data-title');
        let title_seo = getAttr(e.target, 'data-titleseo');
        let categorynames = getAttr(e.target, 'data-category');
        let date = getAttr(e.target, 'data-date');
        let author = getAttr(e.target, 'data-author');
        let postid = getAttr(e.target, 'data-postid');
        let imagesrc = getAttr(e.target, 'data-imagesrc');
        let anons = getAttr(e.target, 'data-anons');
        let exists = fcl_hasClassExists(e.target);
        
        if (exists && !cl_allow_multilink) {
            if(fcl_isTinyMCE()) {
                selectExistingTinyMCE(url) 
            }
            else {
                selectExistingTextarea(url); 
            }
        } else {
            replaceSelectedLink(url, title, title_seo, categorynames, date, author, postid, imagesrc, anons, e);
        }
    }

    function prepareTermTemplate(e) {
        let url = getAttr(e.target, 'data-url');
        let title = getAttr(e.target, 'data-title');
        let taxonomy = getAttr(e.target, 'data-taxonomy');
        let exists = fcl_hasClassExists(e.target);

        if (exists && !cl_allow_multilink) {
            if(fcl_isTinyMCE()) {
                selectExistingTinyMCE(url) 
            }
            else {
                selectExistingTextarea(url); 
            }
        } else {
            replaceSelectedTerm(url, title, taxonomy, e);
        }
    }
    
    function fcl_hasClassExists(element) {
        let exists;
        if (element.classList.contains('linkate-link')) {
            exists = element.querySelector('.link-title').classList.contains(cl_exists_class);
        } else {
            exists = fcl_hasClassExists(element.parentElement);
        }
        return exists;
    }

    function getAttr(element, attr) {
        let val;
        if (element.classList.contains('linkate-link')) {
            val = element.getAttribute(attr);
        } else {
            val = getAttr(element.parentElement, attr);
        }
        return val;
    }

    //func getAllIndexes counts all links from the links if 'url' provided, or counts every link (internal&external) except documents and images (doc, jpg...) if ('href=') provided.
    
    function fcl_getAllIndexes(text, url, offset, count) {
        let off = text.indexOf(url, offset);
        let cnt = count;
        //let page =  false; // check if page of img,doc,xlcx
        if (off > -1 && (off+url.length) < text.length) {
            let skip = false;
            let sa,da;
            if (url === 'href=') { // finding index of ' or "
                sa = text.indexOf('\'', off+url.length+1);
                da = text.indexOf('\"', off+url.length+1);
            } else {
                sa = text.indexOf('\'', off);
                da = text.indexOf('\"', off);
            }

            let urls_end = -1;
            if (sa == -1 && da != -1) { // choosing the nearest apostrafe (' or ")
                urls_end = da;
            } else if (sa != -1 && da == -1) {
                urls_end = sa;
            } else if (sa == -1 && da == -1) {
                urls_end = -1;
            } else {
                if (sa < da) {
                    urls_end = sa;
                } else {
                    urls_end = da;
                }
            }
            let url_address;
            if (url === 'href=') { // taking out the whole url in the text between ""
                url_address = text.slice(off+url.length, urls_end);
            } else {
                url_address = text.slice(off, urls_end);
            }
            // console.log(url_address);
            if (url === 'href=' && fcl_fileTypeChecker(url_address)) { // skipping images and docs
                console.log('found media');
                skip = true;
            }


            if (url !== 'href=' && url.length < url_address.length) { // skipping subcategories, if there is no exact match
                skip = true;
            }

            if (!skip) { 
                cnt++;
            }
            cnt = fcl_getAllIndexes(text, url, off+url.length, cnt); // adding offset and going on to check the rest of content
            
        }
        return cnt;
    }
    
    function fcl_fileTypeChecker(url) { // cuz we don't want to count media as int/ext links
        let prohibited = ['.jpg','.jpeg','.tiff','.bmp','.psd', '.png', '.gif','.webp', '.doc', '.docx', '.xlsx', '.xls', '.odt', '.pdf', '.ods','.odf', '.ppt', '.pptx', '.txt', '.rtf', '.mp3', '.mp4', '.wav', '.avi', '.ogg', '.zip', '.7z', '.tar', '.gz', '.rar'];

        for (let i = prohibited.length - 1; i >= 0; i--) {
            if (url.indexOf(prohibited[i]) != -1) {
                return true;
            }
        }
        return false;
    }
});

