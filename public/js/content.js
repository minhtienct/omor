var mapObject = null;
var mapZoom = 18;
var updateMapTimeout = null;
var mapLocation = null;
var mapMarker = null, tmpMarker = null;
var subGenreNameFirstChange = true;
$(document).ready(function () {
    $('.datepicker').not('[readonly]').datepicker({
            language: 'ja',
            format: "yyyy/mm/dd",
            autoclose: true
        });

    onImageSelected('imageContentFile1', 'contentImage1', 'txtFileName1');
    onImageSelected('imageContentFile2', 'contentImage2', 'txtFileName2');
    onImageSelected('imageContentFile3', 'contentImage3', 'txtFileName3');
    onImageSelected('imageContentFile4', 'contentImage4', 'txtFileName4');
    onImageSelected('imageContentFile5', 'contentImage5', 'txtFileName5');

    $('#btnUpdate').click(function(){
        showPopup('registerComfirmPopup');
    });

    $('#delete-yes').click(function(){
        var tableId = 'listId';
        refreshLastPage(tableId, '/omolink/content/content-status', {'tab' : 1, 'mode' : 'delete', 'ctid' : $(this).attr('ctid')}, function (response) {
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
    $('#approval-cancel-yes').click(function(){
        var tableId = 'listId';
        refreshLastPage(tableId, '/omolink/content/content-status', {'tab' : 2, 'mode' : 'approvalCancel', 'ctid' : $(this).attr('ctid'), 'chid' : $(this).attr('chid')}, function (response) {
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
    $('#approval-yes').click(function(){
        var tableId = 'listId';
        refreshLastPage(tableId, '/omolink/content/content-status', {'tab' : 3, 'mode' : 'approval', 'ctid' : $(this).attr('ctid'), 'chid' : $(this).attr('chid')}, function (response) {
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
    $('#denial-yes').click(function(){
        var tableId = 'listId';
        refreshLastPage(tableId, '/omolink/content/content-status', {'tab' : 3, 'mode' : 'denial', 'ctid' : $(this).attr('ctid'), 'chid' : $(this).attr('chid')}, function (response) {
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

    $('#genreID').change(function () {
        var data = {'id': $('#genreID option:selected').val()};
        $.ajax({
            type: 'GET',
            dataType: "json",
            async: true,
            url: '/omolink/content/genre-changed',
            data: data,
            beforeSend : function() {
                showLoading();
            },
            success: function (response) {
                hideLoading();

                $('#subGenreName').html('');
                if (response.error != null) {
                    //alertMsg(response.error);
                    return;
                }
                if (response.data != null) {
                    $.each(response.data, function(i, el) {
                        $('#subGenreName').append(new Option(el, el));
                    });
                    if (subGenreNameFirstChange && subGenreNameValue != '') {
                        $('#subGenreName').val(subGenreNameValue);
                        subGenreNameFirstChange = false;
                    }
                }
            }
        });
    });

    $('#genreID').change();
});

/**
 * Set on change event for file input element
 * @param {type} fileElementId
 * @param {type} imgElementId
 * @returns {undefined}
 */
function onImageSelected(fileElementId, imgElementId, fileNameId)
{
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

function onChangeDisplayOrder(obj)
{
    var keys = [1, 2, 3, 4, 5];
    var missingKey = 0;
    for (var i = 0; i < keys.length; i++) {
        var f = false;
        for (var j = 0; j < keys.length; j++) {
            if ($('.order-input:eq(' + j + ')').val() == keys[i]) {
                f = true;
                break;
            }
        }
        if (!f) {
            missingKey = keys[i];
            break;
        }
    }
    if (missingKey == 0) {
        return;
    }
    var id = $(obj).attr('id');
    var newVal = $(obj).val();
    $('select[name*="displayOrder"]').each(function ()
    {
        var fileElementId = $(this).attr('id');
        if(fileElementId == id)
            return;

        if(newVal == $(this).val())
        {
            $(this).val(missingKey) ;
            return false;
        }
    });
}

/**
 *
 * @returns {undefined}
 */
function onImageContentFile1Click() {
    $('#imageContentFile1').click();
}

/**
 *
 * @returns {undefined}
 */
function onImageContentFile2Click() {
    $('#imageContentFile2').click();
}

/**
 *
 * @returns {undefined}
 */
function onImageContentFile3Click() {
    $('#imageContentFile3').click();
}

/**
 *
 * @returns {undefined}
 */
function onImageContentFile4Click() {
    $('#imageContentFile4').click();
}

/**
 *
 * @returns {undefined}
 */
function onImageContentFile5Click() {
    $('#imageContentFile5').click();
}

function onFileClick() {
    $('#imageContentFile1').click();
}

function onShowMapClick() {
    $('#overlay').show();
    $('#mapPopup').show();

    var lat = $('#registerLocationLatitude').val();
    var lng = $('#registerLocationLongitude').val();
    mapZoom = 18;

    if (lat == '') {
        lat = 39;
        mapZoom = 5;
        mapMarker = tmpMarker = null;
    }
    if (lng == '') {
        lng = 139;
        mapZoom = 5;
        mapMarker = tmpMarker = null;
    }
    mapLocation = new google.maps.LatLng(lat, lng);
    mapObject = new google.maps.Map(document.getElementById('mapContainer'), {
        center: mapLocation,
        zoom: mapZoom
    });
    if (mapMarker != null) {
        tmpMarker = mapMarker;
        mapMarker.setMap(mapObject);
    }

    google.maps.event.addListener(mapObject, 'click', function(event) {
        mapZoom = mapObject.getZoom();
        mapLocation = event.latLng;
        updateMapTimeout = setTimeout(placeMarker, 200);
    });

    google.maps.event.addListener(mapObject, 'dblclick', function(event) {
        clearTimeout(updateMapTimeout);
    });
}

function clearAllMarker() {
    google.maps.Map.prototype.clearMarkers = function() {
        for(var i=0; i < this.markers.length; i++){
            this.markers[i].setMap(null);
        }
        this.markers = new Array();
    };
}

function placeMarker() {
    if (tmpMarker != null) {
        tmpMarker.setMap(null);
    }
    if (mapZoom == mapObject.getZoom()) {
        tmpMarker = new google.maps.Marker({position: mapLocation, map: mapObject});
    }
}

function onCancelMapClick() {
    $('#overlay').hide();
    $('#mapPopup').hide();
}

function onOKMapClick() {
    if (mapObject == null) {
        return;
    }
    if (tmpMarker != null) {
        $('#registerLocationLatitude').val(tmpMarker.getPosition().lat());
        $('#registerLocationLongitude').val(tmpMarker.getPosition().lng());
        mapZoom = mapObject.getZoom();
        mapMarker = tmpMarker;

        $('#overlay').hide();
        $('#mapPopup').hide();
    } else {
        alert('位置を選択してください。');
    }


}

function onDeleteContentClick(obj) {
    $('#delete-yes').attr('ctid', $(obj).attr("ctid"));
    showPopup('deleteComfirmPopup');
}

function onApprovalContentClick(obj) {
    $('#approval-yes').attr('ctid', $(obj).attr("ctid"));
    $('#approval-yes').attr('chid', $(obj).attr("chid"));
    showPopup('approvalComfirmPopup');
}

function onDenialContentClick(obj) {
    $('#denial-yes').attr('ctid', $(obj).attr("ctid"));
    $('#denial-yes').attr('chid', $(obj).attr("chid"));
    showPopup('denialComfirmPopup');
}

function onApprovalCancelContentClick(obj) {
    $('#approval-cancel-yes').attr('ctid', $(obj).attr("ctid"));
    $('#approval-cancel-yes').attr('chid', $(obj).attr("chid"));
    showPopup('approvalCancelComfirmPopup');
}

function userFilterTab(id, page) {
    $('#UserFilterTabBar > li').removeClass('active');
    $('#UserFilterTabBar > li[key=' + id + ']').addClass('active');
    var tableId = 'listId';

    lastPage = page != null ? page : 1;
    refreshLastPage(tableId, '/omolink/content/paging', {'tab' : id, 'page' : lastPage}, function () {
        //change header
        $('#' + tableId + ' > thead > tr').html('');
        if (id === 1) {
            $('#title-content').html('自分で登録したコンテンツ一覧');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">コンテンツ名</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">有効期限</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">チャネルへの登録申請状態</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">削除</th>');
        }
        else if (id === 2) {
            $('#title-content').html('チャネルに登録済みのコンテンツ一覧');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">コンテンツ名</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">有効期限</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">コンテンツオーナーID</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">コンテンツ毎のレポート</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">承認取消</th>');
        }
        else if (id === 3) {
            $('#title-content').html('チャネルに配信申請中のコンテンツ一覧');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">コンテンツ名</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">有効期限</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">コンテンツオーナーID</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">承認</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center">拒否</th>');
        } else if (id === 4) {
            $('#title-content').html('全コンテンツ一覧');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center" width="50%">コンテンツ名</th>');
            $('#' + tableId + ' > thead > tr').append('<th class="text-center" width="50%">有効期限</th>');
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
    $('#contentForm').submit();
}
