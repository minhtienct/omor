$(document).ready(function () {
    refreshDelayMessage();

    $('#overlay').click(function(){
        if ($('.overlay-loading').is(":visible")) {
            return;
        }
        $('.overlay-content').each(function () {
            hidePopup($(this).attr('id'));
            if ($(this).attr('lp') != null) {
                lastPage = parseInt($(this).attr('lp'));
            }
        });
    });

    if (typeof(Storage) !== "undefined") {
        var links = sessionStorage.getItem("prevLinks");
        var url = window.location.href;
        var isBack = url.indexOf('?isBack=1') >= 0 || url.indexOf('&isBack=1') >= 0;

        url = url.replace('?isBack=1', '');
        url = url.replace('&isBack=1', '');
        sessionStorage.setItem("currentURL", url);

        if (isBack) {
            gotoPrevLink();
        } else {
            if (links == null || links == '') {
                links = url;
                $('.back-link').hide();
            } else {
                var arrLinks = links.split(';;');
                if (arrLinks[arrLinks.length - 1] !== url) {
                    links += ';;' + url;
                }
                if (links.split(';;').length <= 1) {
                    $('.back-link').hide();
                }
            }

            sessionStorage.setItem("prevLinks", links);
        }
    } else {
        $('.back-link').hide();
    }

});

function refreshDelayMessage() {
    $('.alert-info').delay(5000).fadeOut('slow');
}

function showPopup(popupID) {
    $('#overlay').show();
    $('#' + popupID).show();
}

function hidePopup(popupID) {
    $('#overlay').hide();
    $('#' + popupID).hide();
}

function showLoading() {
    $('#overlay').show();
    $('.overlay-content').hide();
    $('.overlay-loading').show();
}

function hideLoading() {
    $('#overlay').hide();
    $('.overlay-loading').hide();
}

function gotoPrevLink() {
    if (typeof(Storage) !== "undefined") {
        var links = sessionStorage.getItem("prevLinks");
        var arrLinks = links.split(';;');

        if (arrLinks.length > 1) {
            var goTo = arrLinks[arrLinks.length - 2];
            var arrGoTo = goTo.split('|');
            if (arrGoTo[0] === 'goToPage') {
                var currentURL = sessionStorage.getItem("currentURL");
                if (arrGoTo[9] === currentURL) {
                    links = links.substring(0, links.length - arrLinks[arrLinks.length - 1].length - arrLinks[arrLinks.length - 2].length - 4);
                    sessionStorage.setItem("prevLinks", links);
                    if (arrGoTo[8] !== '') {
                        userFilterTab(parseInt(arrGoTo[8]), parseInt(arrGoTo[3]));
                    } else {
                        goToPage(arrGoTo[1], arrGoTo[2], arrGoTo[3], arrGoTo[4] !== '' ? arrGoTo[4] : null,
                            arrGoTo[5] !== '' ? arrGoTo[5] : null, arrGoTo[6] !== '' ? window[arrGoTo[6]] : null,
                            arrGoTo[7] !== '' ? window[arrGoTo[7]] : null);
                    }
                } else {
                    var url = arrGoTo[9];
                    if (url.indexOf("?") >= 0) {
                        url += '&isBack=1';
                    } else {
                        url += '?isBack=1';
                    }

                    window.location.href = url;
                }

            } else if (arrGoTo[0] === 'userFilterTab') {
                var currentURL = sessionStorage.getItem("currentURL");
                if (arrGoTo[2] === currentURL) {
                    links = links.substring(0, links.length - arrLinks[arrLinks.length - 1].length - arrLinks[arrLinks.length - 2].length - 4);
                    sessionStorage.setItem("prevLinks", links);

                    userFilterTab(parseInt(arrGoTo[1]));
                } else {
                    var url = arrGoTo[2];
                    if (url.indexOf("?") >= 0) {
                        url += '&isBack=1';
                    } else {
                        url += '?isBack=1';
                    }

                    window.location.href = url;
                }
            } else {
                links = links.substring(0, links.length - arrLinks[arrLinks.length - 1].length - arrLinks[arrLinks.length - 2].length - 4);
                sessionStorage.setItem("prevLinks", links);

                var url = arrLinks[arrLinks.length - 2];
                window.location.href = url;
            }
        }
    }
}

function onlyInputNumber(elementName)
{
    $('input[name*="' + elementName + '"]').keydown(function (event) {
        // Allow only backspace, delete, tab, escape, enter
        if (event.keyCode == 46 //backspace
                || event.keyCode == 8 //delete
                || event.keyCode == 9 //tab
                || event.keyCode == 27 //escape
                || event.keyCode == 13 //enter
                ) {
            // let it happen, don't do anything
        }
        else {
            // Ensure that it is a number and stop the keypress
            if ((event.keyCode < 48 || event.keyCode > 57)
                    && (event.keyCode < 96 || event.keyCode > 105)) {
                event.preventDefault();
            }
        }
    });
}