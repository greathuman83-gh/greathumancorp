function get_editor_wr_content()
{
    return oEditors.getById['f_content'].getIR();;
}

function put_editor_wr_content(content)
{
    oEditors.getById["f_content"].exec("SET_CONTENTS", [""]);
    //oEditors.getById["wr_content"].exec("SET_IR", [""]);
    oEditors.getById["f_content"].exec("PASTE_HTML", [content]);

    return;
}