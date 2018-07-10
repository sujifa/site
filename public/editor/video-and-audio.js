(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['tui-editor'], factory);
    } else if (typeof exports === 'object') {
        factory(require('tui-editor'));
    } else {
        factory(root['tui']['Editor']);
    }
})(this, function(Editor) {
    // define video extension
    Editor.defineExtension('video', function() {
        Editor.codeBlockManager.setReplacer('video', function(src) {
            var wrapperId = 'yt' + Math.random().toString(36).substr(2, 10);
            setTimeout(renderVideo.bind(null, wrapperId, src), 0);
            return '<div id="' + wrapperId + '"></div>';
        });
    });
    // define audio extension
    Editor.defineExtension('audio', function() {
        Editor.codeBlockManager.setReplacer('audio', function(src) {
            var wrapperId = 'au' + Math.random().toString(36).substr(2, 10);
            setTimeout(renderAudio.bind(null, wrapperId, src), 0);
            return '<div id="' + wrapperId + '"></div>';
        });
    });

    function renderVideo(wrapperId, src) {
        var el = document.querySelector('#' + wrapperId);
        el.innerHTML = "<video  playsinline webkit-playsinline preload='auto' controls src='"+src+"' width='100%'  webkit-playsinline playsinline>如果你看到这段文字，说明视频挂掉。</video>";
    }

    function renderAudio(wrapperId, src) {
        var el = document.querySelector('#' + wrapperId);
        el.innerHTML = "<audio  controls='controls' preload='auto' ><br><source src='"+src+"' type='audio/mpeg'>如果你看到这段文字，说明音乐挂掉<br></audio>";
    }

});

function newButton(editor){
    const toolbar = editor.getUI().getToolbar();

    editor.eventManager.addEventType('Event1');
    editor.eventManager.listen('Event1', () => {
        var html = '';
        if(editor.isMarkdownMode()){
            html = "``` video \n\n```";
        }

    editor.insertText(html);
});
    toolbar.addButton({
        name: 'customize',
        className: 'fab fa-accessible-icon',
        event: 'Event1',
        tooltip: '视频',
        $el: $('<div class="our-button-class" style="display: inline-block;float:left;padding: 5px;"><i class="fas fa-video"></i></div>')
    }, 1);


    editor.eventManager.addEventType('Event2');
    editor.eventManager.listen('Event2', () => {
        var html = '';
        if(editor.isMarkdownMode()){
            html = "``` audio \n\n```";
        }

    editor.insertText(html);
});
    toolbar.addButton({
        name: 'customize',
        className: 'fab fa-accessible-icon',
        event: 'Event2',
        tooltip: '音频',
        $el: $('<div class="our-button-class" style="display: inline-block;float:left;padding: 5px;"><i class="fas fa-headphones"></i></div>')
    }, 1);
}
