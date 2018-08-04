$(window).load(function() {
	setActiveFilters();
});

$( document ).ready(function() {
	$('#algo').selectize({
		create: true
	});
	// search list items
	var options = {
		valueNames: [ 
			'coinName_cryptopia', 
			'coinMarket_cryptopia', 
			'coinAlgo_cryptopia', 
			'coinPrice_cryptopia', 
			'coinVolume_cryptopia', 
			'coinCMCVolume_cryptopia', 
			'coinChange_cryptopia', 
			'coinCap_cryptopia',
			'coinSupply_cryptopia'
		]
	};
	
	// define list
	var userList = new List('coinResults', options);
	userList.sort("coinVolume_cryptopia", {
    	order: "desc"
	})

	$.each(userList.items, function(k, item){
    	var val = item._values; 

		//var coinChange_cryptopia = item._values.coinCMCVolume_cryptopia.replace(/[^\d\%]/g,"");
		//item._values.coinChange_cryptopia = parseFloat(coinChange_cryptopia); 
		
		var coinCMCVolume_cryptopia = item._values.coinCMCVolume_cryptopia.replace(/[^\d\.]/g,"");
		if(item._values.coinCMCVolume_cryptopia == 'N/A'){
		    item._values.coinCMCVolume_cryptopia = parseFloat('0'); 
    	} else {
		    item._values.coinCMCVolume_cryptopia = parseFloat(coinCMCVolume_cryptopia); 
    	}
   
		var coinCap_cryptopia = item._values.coinCap_cryptopia.replace(/[^\d\.]/g,"");
		if(item._values.coinCap_cryptopia == 'N/A'){
		    item._values.coinCap_cryptopia = parseFloat('0'); 
    	} else {
		    item._values.coinCap_cryptopia = parseFloat(coinCap_cryptopia); 
    	}
    	
    	var coinSupply_cryptopia = item._values.coinSupply_cryptopia.replace(/[^\d\.]/g,"");
		if(item._values.coinSupply_cryptopia == 'N/A'){
		    item._values.coinSupply_cryptopia = parseFloat('0'); 
    	} else {
		    item._values.coinSupply_cryptopia = parseFloat(coinSupply_cryptopia); 
    	}
	});
	
	$('#low_sat, #high_sat, #low_lite, #high_lite, #low_doge, #high_doge').priceFormat({
    	prefix: '',
    	suffix: '',
    	centsLimit: 8
    });
    
    $('#low_usd, #high_usd').priceFormat({
    	prefix: '',
    	suffix: '',
    	centsLimit: 2
    });

	// add commas to numbers while typing
	$('input.numbers').keyup(function(event) {
		if(event.which >= 37 && event.which <= 40) return;
			// format number
			$(this).val(function(index, value) {
				return value
				.replace(/\D/g, "")
				.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  			});
	});
	
	$("#allFilters").submit(function( event ) {
		event.preventDefault();
		
		var allParameters = 'cryptopia.php?';
		
		var btc_market;
		var low_sat_init;
		var high_sat_init;
		var low_sat;
		var high_sat;
		
		var ltc_market;
		var low_lite_init;
		var high_lite_init;
		var low_lite;
		var high_lite;
		
		var usd_market;
		var low_usd_init;
		var high_usd_init;
		var low_usd;
		var high_usd;
		
		var doge_market;
		var low_doge_init;
		var high_doge_init;
		var low_doge;
		var high_doge;
		
		var algo;
		var algo_init;
		var min_cryptopia_volume_init
		var min_cryptopia_volume;
		
		var gain_filter;
		var green_24hr_init;
		var red_24hr_init;
		
		var volume_init;
		var volumeToMarketcap;
		
		var volume_min_init;
		var volumeMiniumum;
		
		var max_supply_init;
		var max_supply;
		
		var showOnlyCMC;
		var hideOnlyCMC;
		
		// btc market values
		if ($('#btc_market').is(":checked") ){ 
			btc_market = 'on'; 
			allParameters += 'btc_market='+btc_market+'&';
		}
		if ($('#low_sat_init').is(":checked") && $('#btc_market').is(":checked")){ 
			low_sat_init = 'on'; 
			allParameters += 'low_sat_init='+low_sat_init+'&';
		}
		if ($('#low_sat').val() != '' && $('#low_sat_init').is(":checked") && $('#btc_market').is(":checked")){
			low_sat = $('INPUT[name="low_sat"]').val();
			allParameters += 'low_sat='+low_sat+'&';
		}
		if( $('#high_sat_init').is(":checked") && $('#btc_market').is(":checked")){ 
			high_sat_init = 'on'; 
			allParameters += 'high_sat_init='+high_sat_init+'&';
		}
		if ($('#high_sat').val() != '' && $('#high_sat_init').is(":checked") && $('#btc_market').is(":checked")){
			high_sat = $('INPUT[name="high_sat"]').val();
			allParameters += 'high_sat='+high_sat+'&';
		}
		
		// usd market values
		if ($('#usd_market').is(":checked") ){ 
			usd_market = 'on'; 
			allParameters += 'usd_market='+usd_market+'&';
		}
		if ($('#low_usd_init').is(":checked") ){ 
			low_usd_init = 'on'; 
			allParameters += 'low_usd_init='+low_usd_init+'&';
		}
		if ($('#low_usd').val() != '' && $('#low_usd_init').is(":checked") && $('#usd_market').is(":checked")){
			low_usd = $('INPUT[name="low_usd"]').val();
			allParameters += 'low_usd='+low_usd+'&';
		}
		if( $('#high_usd_init').is(":checked") ){ 
			high_usd_init = 'on'; 
			allParameters += 'high_usd_init='+high_usd_init+'&';
		}
		if ($('#high_usd').val() != '' && $('#high_usd_init').is(":checked") && $('#usd_market').is(":checked")){
			high_usd = $('INPUT[name="high_usd"]').val();
			allParameters += 'high_usd='+high_usd+'&';
		}
		
		// ltc market values
		if ($('#ltc_market').is(":checked") ){ 
			ltc_market = 'on'; 
			allParameters += 'ltc_market='+ltc_market+'&';
		}
		if ($('#low_lite_init').is(":checked") ){ 
			low_lite_init = 'on'; 
			allParameters += 'low_lite_init='+low_lite_init+'&';
		}
		if ($('#low_lite').val() != '' && $('#low_lite_init').is(":checked") && $('#ltc_market').is(":checked")){
			low_lite = $('INPUT[name="low_lite"]').val();
			allParameters += 'low_lite='+low_lite+'&';
		}
		if( $('#high_lite_init').is(":checked") ){ 
			high_lite_init = 'on'; 
			allParameters += 'high_lite_init='+high_lite_init+'&';
		}
		if ($('#high_lite').val() != '' && $('#high_lite_init').is(":checked") && $('#ltc_market').is(":checked")){
			high_lite = $('INPUT[name="high_lite"]').val();
			allParameters += 'high_lite='+high_lite+'&';
		}
		
		// doge market values
		if ($('#doge_market').is(":checked") ){ 
			doge_market = 'on'; 
			allParameters += 'doge_market='+doge_market+'&';
		}
		if ($('#low_doge_init').is(":checked") ){ 
			low_doge_init = 'on'; 
			allParameters += 'low_doge_init='+low_doge_init+'&';
		}
		if ($('#low_doge').val() != '' && $('#low_doge_init').is(":checked") && $('#doge_market').is(":checked")){
			low_doge = $('INPUT[name="low_doge"]').val();
			allParameters += 'low_doge='+low_doge+'&';
		}
		if( $('#high_doge_init').is(":checked") ){ 
			high_doge_init = 'on'; 
			allParameters += 'high_doge_init='+high_doge_init+'&';
		}
		if ($('#high_doge').val() != '' && $('#high_doge_init').is(":checked") && $('#doge_market').is(":checked")){
			high_doge = $('INPUT[name="high_doge"]').val();
			allParameters += 'high_doge='+high_doge+'&';
		}
		
		if( $('#algo_init').is(":checked") ){ 
			algo_init = 'on'; 
			allParameters += 'algo_init='+algo_init+'&';
		} 
		if ($('#algo').val() != '' && $('#algo_init').is(":checked")){
			algo = $('SELECT[name="algo"]').val();
			allParameters += 'algo='+algo+'&';
		}
		
		if( $('#gain_filter').is(":checked") ){ 
			gain_filter = 'on'; 
			allParameters += 'gain_filter='+gain_filter+'&';
		} 
		
		if( $('#min_cryptopia_volume_init').is(":checked") ){ 
			min_cryptopia_volume_init = 'on'; 
			allParameters += 'min_cryptopia_volume_init='+min_cryptopia_volume_init+'&';
		} 
		if ($('#min_cryptopia_volume').val() != '' && $('#min_cryptopia_volume_init').is(":checked")){
			min_cryptopia_volume = $('INPUT[name="min_cryptopia_volume"]').val();
			allParameters += 'min_cryptopia_volume='+min_cryptopia_volume+'&';
		}
		
		if( $('#marketcap_low_init').is(":checked") ){ 
			marketcap_low_init = 'on'; 
			allParameters += 'marketcap_low_init='+marketcap_low_init+'&';
		}
		if ($('#marketcap_low').val() != '' && $('#marketcap_low_init').is(":checked")){
			marketcap_low = $('INPUT[name="marketcap_low"]').val();
			allParameters += 'marketcap_low='+marketcap_low+'&';
		}
		if( $('#marketcap_high_init').is(":checked") ){ 
			marketcap_high_init = 'on'; 
			marketcap_high = $('INPUT[name="marketcap_high"]').val();
			allParameters += 'marketcap_high_init='+marketcap_high_init+'&';
		}
		if ($('#marketcap_high').val() != '' && $('#marketcap_high_init').is(":checked")){
			marketcap_high = $('INPUT[name="marketcap_high"]').val();
			allParameters += 'marketcap_high='+marketcap_high+'&';
		}
		
		if( $('#volume_init').is(":checked") ){ 
			volume_init = 'on'; 
			allParameters += 'volume_init='+volume_init+'&';
		} 
		if ($('#volumeToMarketcap').val() != '' && $('#volume_init').is(":checked")){
			volumeToMarketcap = $('INPUT[name="volumeToMarketcap"]').val();
			allParameters += 'volumeToMarketcap='+volumeToMarketcap+'&';
		}
		
		if( $('#volume_min_init').is(":checked") ){ 
			volume_min_init = 'on'; 
			allParameters += 'volume_min_init='+volume_min_init+'&';
		} 
		if ($('#volumeMinimum').val() != '' && $('#volume_min_init').is(":checked")){
			volumeMinimum = $('INPUT[name="volumeMinimum"]').val();
			allParameters += 'volumeMinimum='+volumeMinimum+'&';
		}
		
		if( $('#max_supply_init').is(":checked") ){ 
			max_supply_init = 'on'; 
			allParameters += 'max_supply_init='+max_supply_init+'&';
		}
		if ($('#max_supply').val() != '' && $('#max_supply_init').is(":checked")){
			max_supply = $('INPUT[name="max_supply"]').val();
			allParameters += 'max_supply='+max_supply+'&';
		}
		
		if( $('#showOnlyCMC_init').is(":checked") ){ 
			showOnlyCMC = 'on'; 
			allParameters += 'showOnlyCMC='+showOnlyCMC+'&';
		}
		if( $('#hideOnlyCMC_init').is(":checked") ){ 
			hideOnlyCMC = 'on'; 
			allParameters += 'hideOnlyCMC='+hideOnlyCMC+'&';
		}
		
		allParameters = allParameters.slice(0, -1);
		//alert(allParameters);
		window.location.replace(allParameters);
	});

});

// IF DIV HAS ACTIVE CLASS, ENABLE CHECKBOX
function setActiveFilters(){
	if($("#btc_market_selector").hasClass('active')){
		$('#btc_market').prop('checked', true);
	} else {
		$('#btc_market').prop('checked', false);
	}
	if($("#ltc_market_selector").hasClass('active')){
		$('#ltc_market').prop('checked', true);
	} else {
		$('#ltc_market').prop('checked', false);
	}
	if($("#usd_market_selector").hasClass('active')){
		$('#usd_market').prop('checked', true);
	} else {
		$('#usd_market').prop('checked', false);
	} 
	if($("#doge_market_selector").hasClass('active')){
		$('#doge_market').prop('checked', true);
	} else {
		$('#doge_market').prop('checked', false);
	} 
	if($("#low_sat_selector").hasClass('active')){
		$('#low_sat_init').prop('checked', true);
	} else {
		$('#low_sat_init').prop('checked', false);
	} 
	if($("#high_sat_selector").hasClass('active')){
		$('#high_sat_init').prop('checked', true);
	} else {
		$('#high_sat_init').prop('checked', false);
	} 
	if($("#low_usd_selector").hasClass('active')){
		$('#low_usd_init').prop('checked', true);
	} else {
		$('#low_usd_init').prop('checked', false);
	} 
	if($("#high_usd_selector").hasClass('active')){
		$('#high_usd_init').prop('checked', true);
	} else {
		$('#high_usd_init').prop('checked', false);
	} 
	if($("#low_lite_selector").hasClass('active')){
		$('#low_lite_init').prop('checked', true);
	} else {
		$('#low_lite_init').prop('checked', false);
	} 
	if($("#high_lite_selector").hasClass('active')){
		$('#high_lite_init').prop('checked', true);
	} else {
		$('#high_lite_init').prop('checked', false);
	} 
	if($("#low_doge_selector").hasClass('active')){
		$('#low_doge_init').prop('checked', true);
	} else {
		$('#low_doge_init').prop('checked', false);
	} 
	if($("#high_doge_selector").hasClass('active')){
		$('#high_doge_init').prop('checked', true);
	} else {
		$('#high_doge_init').prop('checked', false);
	} 
	if($("#algo_selector").hasClass('active')){
		$('#algo_init').prop('checked', true);
	} else {
		$('#algo_init').prop('checked', false);
	} 
	if($("#marketcap_low_selector").hasClass('active')){
		$('#marketcap_low_init').prop('checked', true);
	} else {
		$('#marketcap_low_init').prop('checked', false);
	} 
	if($("#marketcap_high_selector").hasClass('active')){
		$('#marketcap_high_init').prop('checked', true);
	} else {
		$('#marketcap_high_init').prop('checked', false);
	} 
	if($("#volumeToMarketcap_selector").hasClass('active')){
		$('#volume_init').prop('checked', true);
		$('#volume_min_init').prop('checked', false);
	} else {
		$('#volume_init').prop('checked', false);
		
	} 
	if($("#volumeMinimum_selector").hasClass('active')){
		$('#volume_min_init').prop('checked', true);
		$('#volume_init').prop('checked', false);
		
	} else {
		$('#volume_min_init').prop('checked', false);
	} 
	if($("#min_cryptopia_volume_selector").hasClass('active')){
		$('#min_cryptopia_volume_init').prop('checked', true);
	} else {
		$('#min_cryptopia_volume_init').prop('checked', false);
	} 
	if($("#gain_filter_selector").hasClass('active')){
		$('#gain_filter').prop('checked', true);
	} else {
		$('#gain_filter').prop('checked', false);
	} 
	if($("#red_24hr_selector").hasClass('active')){
		$('#red_24hr_init').prop('checked', true);
	} else {
		$('#red_24hr_init').prop('checked', false);
	} 
	if($("#green_24hr_selector").hasClass('active')){
		$('#green_24hr_init').prop('checked', true);
	} else {
		$('#green_24hr_init').prop('checked', false);
	} 
	if($("#max_supply_selector").hasClass('active')){
		$('#max_supply_init').prop('checked', true);
	} else {
		$('#max_supply_init').prop('checked', false);
	} 
	if($("#showOnlyCMC").hasClass('active')){
		$('#showOnlyCMC_init').prop('checked', true);
	} else {
		$('#showOnlyCMC_init').prop('checked', false);
	} 
	if($("#hideOnlyCMC").hasClass('active')){
		$('#hideOnlyCMC_init').prop('checked', true);
	} else {
		$('#hideOnlyCMC_init').prop('checked', false);
	} 	
}

function filterState(checkboxElem) {
	var whichMarket = checkboxElem.id;
	var toggleMarketValues;
	
	switch(whichMarket){
		case 'btc_market':
		toggleMarketValues = '#low_sat_selector, #high_sat_selector';
		break;
		case 'ltc_market':
		toggleMarketValues = '#low_lite_selector, #high_lite_selector';
		break;
		case 'usd_market':
		toggleMarketValues = '#low_usd_selector, #high_usd_selector';
		break;
		case 'doge_market':
		toggleMarketValues = '#low_doge_selector, #high_doge_selector';
		break;
		case 'gain_filter':
		toggleMarketValues = '#green_24hr_selector, #red_24hr_selector';
		break;
	}
	
	if (checkboxElem.checked) {
    	$(checkboxElem).parent().parent().removeClass('inactive').addClass('active');
    	$(toggleMarketValues).removeClass('hide').addClass('show');
		
    	if(whichMarket == 'hideOnlyCMC_init'){    	
	    	$('#showOnlyCMC_init').prop('checked', false);
	    	
			$('#marketcap_low_init').prop('checked', false);
			$('#marketcap_high_init').prop('checked', false);
			$('#volume_init').prop('checked', false);
			$('#max_supply_init').prop('checked', false);
			
			$('#marketcap_low').removeClass('active').addClass('inactive');
			$('#marketcap_high').removeClass('active').addClass('inactive');
			$('#volumeToMarketcap').removeClass('active').addClass('inactive');
			$('#max_supply').removeClass('active').addClass('inactive');
    	}
    	if(whichMarket == 'showOnlyCMC_init'){    	
	    	$('#hideOnlyCMC_init').prop('checked', false);
	    	$('#hideOnlyCMC_init').removeClass('active').addClass('inactive');
	    }
	    if(whichMarket == 'green_24hr_init'){    	
	    	$('#red_24hr_init').prop('checked', false);
	    	$('#red_24hr_init').removeClass('active').addClass('inactive');
	    }
	    if(whichMarket == 'red_24hr_init'){    	
	    	$('#green_24hr_init').prop('checked', false);
	    	$('#green_24hr_init').removeClass('active').addClass('inactive');
	    }
	    if(whichMarket == 'volume_init'){    	
	    	$('#volume_min_init').prop('checked', false);
	    	$('#volumeMinimum_selector').removeClass('active').addClass('inactive');
	    }
	    if(whichMarket == 'volume_min_init'){    	
	    	$('#volume_init').prop('checked', false);
	    	$('#volumeToMarketcap_selector').removeClass('active').addClass('inactive');
	    }
  	} else {
    	$(checkboxElem).parent().parent().removeClass('active').addClass('inactive');
    	$(toggleMarketValues).removeClass('show').addClass('hide');
  	}
}