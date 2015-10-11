function DialogConfirm(dom){
    var url = $(dom).attr('_href');
    var d = dialog({
        title: '提示',
        content: '您确定继续么?',
        okValue: '确定',
        ok: function () {
            $.ajax({
                url : url,
                dataType : 'json',
                success : function (i) {
                    if (i.status == 1) {
                        window.location.href = i.url;
                    } else {
                        alert(i.info);
                    }
                }
            })
            return false;
        },
        cancelValue: '取消',
        cancel: function () {}
    });
    d.showModal();
    return false;
}

function ajaxForm(dom) {
    var url = $(dom).attr('action');
    var data = $(dom).serialize();

    $.ajax({
        url : url,
        data : data,
        type : 'post',
        dataType : 'json',
        success : function (i) {
            if (i.status == 1) {
                window.location.href = i.url;
            } else {
                alert(i.info);
            }
        }
    })
    return false;
}