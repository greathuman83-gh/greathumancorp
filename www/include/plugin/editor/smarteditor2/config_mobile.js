document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.smarteditor2').forEach(function(el) {
        var get_id = el.getAttribute('id');

        if (!get_id || el.nodeName !== 'TEXTAREA') return;

        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: get_id,
            sSkinURI: g5_editor_url + "/SmartEditor2Skin_mobile.html",
            htParams: {
                bUseToolbar: true,
                bUseVerticalResizer: true,
                bUseModeChanger: true,
                fOnBeforeUnload: function() {}
            },
            fOnAppLoad: function() {
                var oEd = oEditors.getById[get_id];
                if (!oEd) {
                    return;
                }
                try {
                    oEd.exec("CHECK_STYLE_CHANGE", []);
                } catch (e) {}
            },
            fCreator: "createSEditor2"
        });
    });
});
