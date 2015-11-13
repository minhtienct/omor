var lastTabIndex = 1;

$(document).ready(function () {
    $('#delete-yes').click(function(){
        var tableId = 'listId';
        refreshLastPage(tableId, '/omolink/user/delete/' + $(this).attr('uid'), {'tab' : lastTabIndex}, function (response) {
            $('.alert').hide();
            if (response.error != null) {
                $('#error-message').html(response.error);
                $('#error-message').parent().show();
            } else if (response.message != null) {
                $('#info-message').html(response.message);
                $('#info-message').parent().show();                
            }
            refreshDelayMessage();
        });
    });
    
    $('#btnRegister').click(function(){
        showPopup('registerComfirmPopup');
    });
    
    onlyInputNumber('postalCode');
    onlyInputNumber('roles');
    
    if (typeof insertSuccess !== 'undefined' && insertSuccess) {
        showPopup('registerResultPopup');
    }
});

function userFilterTab(id, page) {
    $('#UserFilterTabBar > li').removeClass('active');
    $('#UserFilterTabBar > li[key=' + id + ']').addClass('active');
    var tableId = 'listId';
    lastTabIndex = id;
    
    lastPage = page != null ? page : 1;    
    refreshLastPage(tableId, '/omolink/user/paging', {'tab' : id, 'page' : lastPage}, function () {
        //change header
        $('#' + tableId + ' > thead > tr').html('');
        if (id !== 3) {
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">ユーザーID</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">企業・団体名</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">担当者氏名</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">オーナー名</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">削除</th>');
        } else {
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">ユーザーID</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">ユーザー名</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">メールアドレス</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">削除</th>');
        }
        
        switch(id) {
            case 1:
                $('#title-user').html('チャネルオーナーの一覧');
                break;
            case 2:
                $('#title-user').html('正規コンテンツオーナーの一覧');
                break;
            case 3:
                $('#title-user').html('一般ユーザーの一覧');
                break;
            case 4:
                $('#title-user').html('登録申請中ユーザーの一覧');
                break;
            default:
                break;
        }
        
        //save to history
        if (typeof(Storage) !== "undefined") {
            var currentURL = sessionStorage.getItem("currentURL");
            var data = 'userFilterTab|' + id + '|' + currentURL;

            var links = sessionStorage.getItem("prevLinks");
            if (links == null || links == '') {
                links = data;
            } else {
                var arrLinks = links.split(';;');
                if (arrLinks[arrLinks.length - 1] !== data) {
                    links += ';;' + data;
                }
            }
            sessionStorage.setItem("prevLinks", links);
            $('.back-link').show();
        }
    });
}

function registerSubmit() {
    $('#userRegisterForm').submit();    
}

function onDeleteUserClick(obj) {
    $('#delete-yes').attr('uid', $(obj).attr("uid"));
    showPopup('deleteComfirmPopup');
}