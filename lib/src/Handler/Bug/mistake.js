function mistake(){}

(function () {

    var imgs = document.querySelectorAll('.ms-nav-menu > .ms-menu-item img');

    var menu_info = document.querySelectorAll('.ms-nav-info > span');

    var content_info = document.querySelectorAll('.ms-info-content>.ms-block');

    var contents = document.querySelectorAll('section.ms-content');

    var linos = document.querySelectorAll('.lino');

    mistake.showInfoContent = function (item) {
        remove(menu_info, 'ms-mi-color');
        remove(content_info, 'ms-block-show');
        item.classList.add('ms-mi-color');
        var content = document.querySelector('[data-content="'+item.getAttribute('data-referer')+'"]');
        content.classList.add('ms-block-show');
    }

    mistake.showContent = function(item) {
        remove(contents, 'ms-block-show');
        var content = document.querySelector('[data-content="'+ item.getAttribute('data-referer')  +'"]');
        if (content) content.classList.add('ms-block-show');
    }

    function each(arr,func){
        for(var x = 0; x < arr.length; x++) {
            func(arr[x], x);
        }
    }

    function remove(items, cl) {
        each(items, function (item) {
            item.classList.remove(cl);
        });
    }
console.log(imgs, menus)
    each(imgs, function (img, i) {
        img.setAttribute('src', menus[i][1]);
        img.setAttribute('alt', menus[i][0]);
        var mi = img.parentNode.parentNode;
        mi.setAttribute('data-referer', menus[i][2]);
        mi.setAttribute('onclick', 'mistake.showContent(this)');
        if (i == 0) {
            document.querySelector('[data-content="'+ menus[i][2]  +'"]')
                .classList.add('ms-block-show');
        }
    });

    var scroller = (document.querySelectorAll('[data-scroll]'));

    each(scroller, function(scroll, x) {
        scroll = scroll.parentNode; // div line
        var bline = scroll.parentNode,
            wrapper = bline.parentNode,
            bpre = bline.nextSibling,
            spancode = bpre.firstChild,
            span = spancode.firstChild.childNodes;
        spancode.firstChild.removeChild(span[0]);
        spancode.removeChild(spancode.childNodes[1]);
        scroll.style.background = 'red';
        wrapper.scrollTop = scroll.offsetTop - wrapper.offsetTop - 100;
    });
    menu_info[0].classList.add('ms-mi-color');
}());