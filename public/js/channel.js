$(document).ready(function () {
    onImageSelected('fileBannerImgPhoto', 'bannerImage', 'txtBannerImage');
    onImageSelected('fileFrameOnImagePhoto', 'frameOnImage', 'txtFrameOnImage');
    onImageSelected('fileFrameUnderImagePhoto', 'frameUnderImage', 'txtFrameUnderImage');
    onImageSelected('fileFrameIconImagePhoto', 'frameIconImage', 'txtIconImage');
    
    $('#btnRegister').click(function(){
        showPopup('registerComfirmPopup');
    });
});

/**
 * Set on change event for file input element
 * @param {type} fileElementId
 * @param {type} imgElementId
 * @returns {undefined}
 */
function onImageSelected(fileElementId, imgElementId, fileNameId) {
    //:::: Display local images without going to server
    var $fileElement = $('input[id="' + fileElementId + '"]');
    var $fileName = $('input[id="' + fileNameId + '"]');
    var $image = $('img[id="' + imgElementId + '"]');
    var curImageSrc = $image.attr('src'); // Save current image src

    var fileTypesAllowed = ['jpg', 'jpeg', 'png', 'gif']; // File extention array

    $fileElement.off("change");
    $fileElement.on("change", function () {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            if (this.files && this.files.length > 0) {
                var file = this.files[0];
                $fileName.attr('value', file.name);
                var extension = file.name.split('.').pop().toLowerCase();
                if (fileTypesAllowed.indexOf(extension) > -1) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $image.attr("src", e.target.result);
                        };

                        reader.readAsDataURL(file);
                } else {
                    alert('選択されたファイルを表示できません。\n画像ファイルを選択してください。');
                    //:: Reset data
                    $fileElement.val('');
                    $fileName.val('');
                    $image.attr("src", curImageSrc);
                    return;
                }
            }
        }        
    });
}

/**
 * 
 * @returns {undefined}
 */
function onBannerImgClick() {
    $('#fileBannerImgPhoto').click();
}

/**
 * 
 * @returns {undefined}
 */
function onFrameOnImgClick() {
    $('#fileFrameOnImagePhoto').click();
}

/**
 * 
 * @returns {undefined}
 */
function onFrameUnderImgClick() {
    $('#fileFrameUnderImagePhoto').click();
}

/**
 * 
 * @returns {undefined}
 */
function onFrameIconImgClick() {
    $('#fileFrameIconImagePhoto').click();
}

function onDeleteChannelClick(id) {
    $('#delete-yes').attr("chid", id);
    showPopup('deleteComfirmPopup');
}

function deleteChannel() {
    refreshLastPage('listId', '/omolink/channel/delete/' + $('#delete-yes').attr('chid'), {}, function(response) {
        $('.alert').hide();
        if (response.error != null) {
            $('#error-message').html(response.error);
            $('#error-message').parent().show();
        } else if (response.message != null) {
            $('#info-message').html(response.message);
            $('#info-message').parent().show();                
        }
        if (response.isUserChannel != null && response.isUserChannel > 0) {
            $('#channel-update-menu').html('<a href="/omolink/channel/insert">チャネル登録</a>');
        }
        refreshDelayMessage();
    });
}

function registerSubmit() {
    $('#channelRegisterForm').submit();
}

function onShowContentList(channelId) {
    $('#contentListPopup').attr('lp', lastPage);
    $('#contentListPopup').attr('chid', channelId);
    lastPage = 1;
    refreshLastPage('contentList', '/omolink/channel/contents/' + channelId, {}, function(response) {
        showPopup('contentListPopup');
    });    
}

function addParamContentsCallback(params) {
    params.chid = $('#contentListPopup').attr('chid');
}

function pagingContentsDone(response) {
    showPopup('contentListPopup');
}