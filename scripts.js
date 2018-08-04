// JavaScript Document
$( document ).ready(function() {
    var totalCoinsScanned;
    var totalCoinsSelected;
    var lowestCap;
    var highestCap;
    var maxTotalCoins;
    var allCoins;
    var volumePercentOfCap = 2;
    var show1hrCoins = 'all';
    var show24hrCoins = 'all';
    var show7dCoins = 'all';
    var showAllCoins = true;

    var filteredArray = [];
    var finalArray = [];

    /* ON CLICK FUNCTION */
    $( "#runQuery" ).on( "click", function() {
      filteredArray.splice(0, filteredArray.length);
      lowestCap = $("#marketcap_low").val();
      lowestCap = lowestCap.replace(/\,/g,'');
      lowestCap = parseInt(lowestCap);
      highestCap = $("#marketcap_high").val();
      highestCap = highestCap.replace(/\,/g,'');
      highestCap = parseInt(highestCap);
      volumePercentOfCap = 100 / parseInt($("#volumeToMarketcap").val());
      maxTotalCoins = $("#maxTotalCoins").val();
      maxTotalCoins = maxTotalCoins.replace(/\,/g,'');
      maxTotalCoins = parseInt(maxTotalCoins);
      allCoins = $('input[name="allCoins"]:checked').val();
      
      runInitialQuery();
    });
    $('input.number').val(function(index, value) {
      return value
      .replace(/\D/g, "")
      .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    });
    $('input.number').keyup(function(event) {
      if(event.which >= 37 && event.which <= 40) return;
        // format number
        $(this).val(function(index, value) {
          return value
          .replace(/\D/g, "")
          .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        });
    });
    $('input[type=radio][name=allCoins1hr]').change(function () {
      if ($("input[name='allCoins1hr']:checked").val() == 'redCoins') {
        $("#input_allCoins_hidden").prop("checked", true).trigger("click");
      }
      if ($("input[name='allCoins1hr']:checked").val() == 'greenCoins') {
        $("#input_allCoins_hidden").prop("checked", true).trigger("click");
      }
    });
    $('input[type=radio][name=allCoins24hr]').change(function () {
      if ($("input[name='allCoins24hr']:checked").val() == 'redCoins') {
        $("#input_allCoins_hidden").prop("checked", true).trigger("click");
      }
      if ($("input[name='allCoins24hr']:checked").val() == 'greenCoins') {
        $("#input_allCoins_hidden").prop("checked", true).trigger("click");
      }
    });
    $('input[type=radio][name=allCoins7d]').change(function () {
      if ($("input[name='allCoins7d']:checked").val() == 'redCoins') {
        $("#input_allCoins_hidden").prop("checked", true).trigger("click");
      }
      if ($("input[name='allCoins7d']:checked").val() == 'greenCoins') {
        $("#input_allCoins_hidden").prop("checked", true).trigger("click");
      }
    });
    $('input[type=radio][name=allCoins]').change(function () {
      if ($("input[name='allCoins']:checked").val() == 'allCoins') {
        $("#input_allCoins1hr_none").prop("checked", true).trigger("click");
        $("#input_allCoins24hr_none").prop("checked", true).trigger("click");
        $("#input_allCoins7d_none").prop("checked", true).trigger("click");
      }
    });

    /* RUN QUERY FUNCTION */
    function runInitialQuery(){
      $('#display').html('').addClass('hidden');
      $.ajax({
        type: "GET",
        url: "https://api.coinmarketcap.com/v1/ticker/?limit=0",
        dataType: "json",
        success: processData,
        error: function(){ alert("failed"); }
      });
    }

    /* PROCESS DATA FUNCTION */
    function processData(coinData){
      totalCoinsScanned = coinData.length;
      for(var i= 0, l = coinData.length; i< l; i++){

        if(coinData[i].percent_change_1h >= 0){var redOrGreen1hr = 'green';}
        else {var redOrGreen1hr = 'red';}
        if(coinData[i].percent_change_24h >= 0){var redOrGreen24hr = 'green';}
        else {var redOrGreen24hr = 'red';}
        if(coinData[i].percent_change_7d >= 0){var redOrGreen7d = 'green';}
        else {var redOrGreen7d = 'red';}

        filteredArray.push(
          {
            id : coinData[i].id,
            symbol : coinData[i].symbol,
            rank : coinData[i].rank,
            price : coinData[i].price_btc,
            coinCap : Math.round(coinData[i].market_cap_usd),
            coinVolume : Math.round(coinData[i]['24h_volume_usd']),
            coinAvailSupply : Math.round(coinData[i].available_supply),
            coinTotalSupply : Math.round(coinData[i].total_supply),
            coinPercentChange1hr : coinData[i].percent_change_1h,
            coinPercentChange24hr : coinData[i].percent_change_24h,
            coinPercentChange7d : coinData[i].percent_change_7d,
            redOrGreen1hr : redOrGreen1hr,
            redOrGreen24hr : redOrGreen24hr,
            redOrGreen7d : redOrGreen7d,
            cmcLink : 'https://coinmarketcap.com/currencies/'+coinData[i].id
          }
        );

      }

      filterData(filteredArray);
    }

    /* FILTER DATA BASED ON SEARCH PARAMETERS */
    function filterData(filteredArray){

      show1hrCoins = $("input[name='allCoins1hr']:checked").val();
      show24hrCoins = $("input[name='allCoins24hr']:checked").val();
      show7dCoins = $("input[name='allCoins7d']:checked").val();
      showAllCoins = $("input[name='allCoins']:checked").val();

      console.log('1 hr '+show1hrCoins+' 24 hr '+show24hrCoins+' 7 d '+show7dCoins+' all coins'+showAllCoins);

      $.each( filteredArray, function( key, value ) {
        if(value.coinCap < lowestCap || value.coinCap > highestCap){

          filteredArray = jQuery.grep(filteredArray, function(value) {
            return value.coinCap > lowestCap;
          });
          filteredArray = jQuery.grep(filteredArray, function(value) {
            return value.coinCap < highestCap;
          });
          filteredArray = jQuery.grep(filteredArray, function(value) {
            return value.coinVolume < value.coinCap / 2;
          });
          filteredArray = jQuery.grep(filteredArray, function(value) {
            return value.coinTotalSupply < maxTotalCoins;
          });
          if(showAllCoins == 'filterCoins'){

          filteredArray = jQuery.grep(filteredArray, function(value) {
            if(showAllCoins == 'filterCoins' && show1hrCoins == 'greenCoins'){
              return value.coinPercentChange1hr >= 0;
            }
            if(showAllCoins == 'filterCoins' && show1hrCoins == 'redCoins'){
              return value.coinPercentChange1hr <= 0;
            }
          });
          filteredArray = jQuery.grep(filteredArray, function(value) {
            if(showAllCoins == 'filterCoins' && show24hrCoins == 'greenCoins'){
              return value.coinPercentChange24hr >= 0;
            }
            if(showAllCoins == 'filterCoins' && show24hrCoins == 'redCoins'){
              return value.coinPercentChange24hr <= 0;
            }
          });
          filteredArray = jQuery.grep(filteredArray, function(value) {
            if(showAllCoins == 'filterCoins' && show7dCoins == 'greenCoins'){
              return value.coinPercentChange7d >= 0;
            }
            if(showAllCoins == 'filterCoins' && show7dCoins == 'redCoins'){
              return value.coinPercentChange7d <= 0;
            }
          });

          }
        }
      });

      //console.log(filteredArray);

      compileNewData(filteredArray);
    }

    /* SHOW AMOUNT OF CRUNCHED DATA FUNCTION */
    function compileNewData(filteredArray){
      totalCoinsSelected = filteredArray.length;
      $('#totalCoinsScanned').html('Total coins scanned : '+totalCoinsScanned);
      $('#totalCoinsSelected').html('Total coins selected : '+totalCoinsSelected);

      displayData(filteredArray);
    }

    /* DISPLAY DATA ON SITE */
    function displayData(filteredArray){
        filteredArray.forEach(function(coin){
            var div = $('<div>', {
              "class" : "coinInfo"
            });
            var symbol = $('<div>', {
              "class" : "coinSymbol",
              "text" : coin.symbol+' : #'+coin.rank
            });

            var price = $('<div>', {
              "class" : "coinPrice",
              "text" : "Price : "+coin.price+'BTC'
            });
            var movement = $('<div>', {
              "class" : "movement"
            });
            var movement1hourText = $('<div>', {
              "text" : 'Price Movement 1hr : ',
              "class" : "priceMovement1hr"
            });
            var movement1hour = $('<span>', {
              "class" : coin.redOrGreen1hr,
              "text" : coin.coinPercentChange1hr+'%'
            });
            var movement24hourText = $('<div>', {
              "text" : 'Price Movement 24hr : ',
              "class" : "priceMovement24hr"
            });
            var movement24hour = $('<span>', {
              "class" : coin.redOrGreen24hr,
              "text" : coin.coinPercentChange24hr+'%'
            });
            var movement7dayText = $('<div>', {
              "text" : 'Price Movement 7day : ',
              "class" : "priceMovement7d"
            });
            var movement7day = $('<span>', {
              "class" : coin.redOrGreen7d,
              "text" : coin.coinPercentChange7d+'%'
            });
            var marketCap = $('<div>', {
              "class" : "coinCap",
              "text" : "MarketCap : $"+coin.coinCap
            });
            var coinVolume = $('<div>', {
              "class" : "coinVolume",
              "text" : "Volume : $"+coin.coinVolume
            });
            var coinAvailSupply = $('<div>', {
              "class" : "coinSupply",
              "text" : "Available/Total Supply : "+coin.coinAvailSupply+" / "+coin.coinTotalSupply
            });
            var cmcLink = $('<a>', {
              "class" : "cmcLink",
              "href" : coin.cmcLink,
              "text" : coin.cmcLink,
              "target" : "_blank"
            });

            div.append(symbol).append(price).append(marketCap).append(coinVolume).append(coinAvailSupply).append(movement).append(cmcLink).appendTo("#display");
            movement.append(movement1hourText).append(movement24hourText).append(movement7dayText);
            movement1hourText.append(movement1hour);
            movement24hourText.append(movement24hour);
            movement7dayText.append(movement7day);
            $(".coinCap, .coinVolume, coinSupply").digits();
        });
        $('#display').removeClass('hidden').append('<div class="clearFloats"></div>');
        console.log(filteredArray);
    }

});

$.fn.digits = function(){
    return this.each(function(){
        $(this).text( $(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") );
    })
}
