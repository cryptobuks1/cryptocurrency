var currencyExchangeRates = [];
var hardcoded = {'USD' : {}, 'EUR': {}, 'GBP': {}, 'CAD': {}, 'AUD': {}, 'BTC': {}, 'ETH': {}, 'XRP': {}, 'BCH': {}, 'LTC': {}};
var crossRates = {'USD': {}, 'EUR': {}, 'GBP': {}, 'CAD': {}, 'AUD': {}, 'CHF': {}, 'INR': {}, 'CNY': {}, 'JPY': {}};

function getExchangeRates() {
    $.ajax({
        url: $("#ExchangeRatesLink").val(),
        dataType: "json",
        type: 'GET',
        success: function (data) {
            for (let i = 0; i < data['data'].length; i++) {
                var name = data['data'][i].name_quotes.substring(3);

                var flag = false;
                for (var item in hardcoded) {
                    if(item == name) {
                        var obj = {};
                        obj.name = name;
                        obj.price_usd = data['data'][i].value_quotes;
                        obj.fullName = "";
                        hardcoded[item] = obj;
                        flag = true;
                    }
                }
                if(!flag) {
                    var obj = {};
                    obj.name = name;
                    obj.price_usd = data['data'][i].value_quotes;
                    obj.fullName = "";
                    currencyExchangeRates.push(obj);
                }
                for (var item in crossRates) {
                    if(item == name) {
                        var obj = {};
                        obj.name = name;
                        obj.price_usd = data['data'][i].value_quotes;
                        obj.fullName = "";
                        crossRates[name] = obj;
                    }
                }
            }
            putValuesToTable();
        }
    });
}

function getGlobaldata() {
    $.ajax({
        url: $("#GlobalDataNames").val(),
        dataType: "json",
        type: 'GET',
        success: function (data) {
            for (let i = 0; i < data.length; i++) {

                var flag = false;

                for (var item in hardcoded) {
                    if (item == data[i].symbol) {
                        var obj = {};
                        obj.name = data[i].symbol;
                        obj.price_usd = data[i].price_usd;
                        obj.fullName = "";
                        hardcoded[item] = obj;
                        flag = true;
                    }
                }
                if(!flag) {
                    var obj = {};
                    obj.name = data[i].symbol;
                    obj.price_usd = data[i].price_usd;
                    obj.fullName = "";
                    currencyExchangeRates.push(obj);
                }
            }
        }
    });
}

$(document).ready(function () {
    $("#navigation li").removeClass('active');
    $("#calculatorTab").addClass("active");
    getExchangeRates();
    getGlobaldata();

    $("#to, #from").on('keyup', function (event) {
        var currentItem = $(event.currentTarget);
        var ulSelected = $("#" + currentItem.attr('id') + "Auto");
        $("#fromAuto li,#toAuto li").remove();
        var key = event.currentTarget.value.toUpperCase();

        ulSelected.append(getReadyList(hardcoded, key));
        ulSelected.append(getReadyList(currencyExchangeRates, key));

        ulSelected.find('li').on('click', function (event) {
            appendSelectedItem(event);
        });
    });

    //show all drop down list
    $("#to, #from").on('focusin', function (event) {
        var currentItem = $(event.currentTarget);
        $("#fromAuto li,#toAuto li").remove();
        var ulSelected = $("#" + currentItem.attr('id') + "Auto");

        ulSelected.append(getFullList(hardcoded));
        ulSelected.append(getFullList(currencyExchangeRates));
        ulSelected.find('li').on('click', function (event) {
            appendSelectedItem(event);
        });
    });
});
function checkIsConvert() {
    var counter = 0;
    $.each($("#converterTable input"), function (key, element) {
        if(element.value != '') {
            counter++;
        }
        if(counter == 3) {
            convert();
        }
    });
}
function convert() {
    var amount = parseInt(document.getElementById("amount").value);
    var from = parseFloat(document.getElementById("from").getAttribute('price_usd'));
    var to = parseFloat(document.getElementById("to").getAttribute('price_usd'));

    var result = 0;
    if(from < to) {
        result = amount * to;
    } else {
        result = amount / from;
    }
    document.getElementById("result").innerHTML = result;
}
function appendSelectedItem(selectedItem) {
    var selectedItem = $(event.currentTarget);
    var id = selectedItem.parent().attr('id');
    var price_usd = selectedItem.attr('price_usd');
    var inputSel = id.substring(0, id.indexOf('Auto'));

    inputSel = $("#" + inputSel);
    inputSel.val(selectedItem.text());
    inputSel.attr('price_usd', price_usd);
    $("#fromAuto li,#toAuto li").remove();
    checkIsConvert();
};


function getFullList(array) {
    var readyList = [];

    for (var item in array) {
        readyList.push("<li price_usd='" + array[item].price_usd + "'>" + array[item].name + "</li>");
    }

    return readyList;
}

function getReadyList(array, key) {
    var readyList = [];

    for (var item in array) {
        if(array[item].name.indexOf(key) != -1 ) {
            readyList.push("<li price_usd='" + array[item].price_usd +"'>" + array[item].name + "</li>");
        }
    }
    return readyList;
}

function putValuesToTable() {

    $.each($("#crossRatesTable thead th"), function (key, value) {

        if (key > 0) {
            putFirstRow(key, value);
        }
    });
}

function putFirstRow(key, value) {

    var body = $("#crossRatesTable tbody tr");
    var currence = $(value).find('p').html();
    var itemInVhichPutValue = $(body[0]).find('td')[key];
    $(itemInVhichPutValue).text(crossRates[currence]);
}