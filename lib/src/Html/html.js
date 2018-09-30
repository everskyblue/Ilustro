(function () {
    var menu_img = {
        0: ['ic_message.png', 'mensaje'],
        1: ['ic_info.png', 'informacion'],
        2: ['ic_bug_report.png', 'errores'],
        3: ['ic_perm_data_setting.png', 'configuraciones']
    };

    var imgs = document.querySelectorAll('.menu > .item img');

    for(var x = 0; x < imgs.length; x++) {
        imgs[x].setAttribute('src', menu_img[x][0]);
        imgs[x].setAttribute('alt', menu_img[x][1]);
    }
}());