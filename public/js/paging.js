$(document).ready(function () {
    
});

var lastPage = 1;
var lastOrderBy = '';
var lastOrder = 'ASC';

function goToPage(tableId, url, page, order_by, order, addParams, doneCallback) {
    var data = {'page': page, 'order_by': order_by, 'order': order};
    
    var params = {};
    if (addParams != null) {
        addParams(params);
        if (params != null) {
            $.each(params, function(k, v) {
                data[k] = v;
            });
        }
    }
    
    var tab = '';
    if (data.tab != null) {
        tab = data.tab;
    }
    
    $.ajax({
        type: 'GET',
        dataType: "json",
        async: true,
        url: url,
        data: data,
        beforeSend : function() {
            showLoading();
        },
        success: function (response) {
            hideLoading();
            if (doneCallback != null) {
                doneCallback(response);
            }
            if (response.error != null) {

                //alertMsg(response.error);
                return;
            }
            if (response.data != null) {
                lastPage = page;
                lastOrderBy = order_by;
                lastOrder = order;
                
                //clear all rows
                $('#' + tableId + ' > tbody').html('');
                //insert new rows
                for (var i = 0; i < response.data.length; i++) {
                    $('#' + tableId + ' > tbody').append(response.data[i]);
                }
                //change paging
                $('#' + tableId + '_paginator').html(response.paginator);
                
                //save to history
                if (typeof(Storage) !== "undefined") {
                    var currentURL = sessionStorage.getItem("currentURL");
                    var data = 'goToPage|' + tableId + '|' + url + '|' + page + '|' + 
                            order_by + '|' + order + '|' + (addParams != null ? addParams.name : '') + '|' + 
                            (doneCallback != null ? doneCallback.name : '') + '|' + tab + '|' + currentURL;
                    
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
            }
        }
    });
}

function refreshLastPage(tableId, url, params, doneCallback) {
    var data = {'page': lastPage, 'order_by': lastOrderBy, 'order': lastOrder};
    if (params != null) {
        $.each(params, function(k, v) {
            data[k] = v;
        });
    }
    
    $.ajax({
        type: 'GET',
        dataType: "json",
        async: true,
        url: url,
        data: data,
        beforeSend : function() {
            showLoading();
        },
        success: function (response) {
            hideLoading();
            doneCallback(response);
            if (response.error != null) {
                return;
            }
            if (response.data != null) {              
                
                //clear all rows
                $('#' + tableId + ' > tbody').html('');
                //insert new rows
                for (var i = 0; i < response.data.length; i++) {
                    $('#' + tableId + ' > tbody').append(response.data[i]);
                }
                //change paging
                $('#' + tableId + '_paginator').html(response.paginator);
            }
        }
    });
}