<!DOCTYPE html>
<html>
<head>
	<link href="css/main.css" rel="stylesheet">
	<link href="css/mediaQueries.css" rel="stylesheet">
</head>

<body>

	<?php
		if(isset( $_GET['low_price']) and ($_GET['low_price'] != '') ){
			$lowPriceRaw = $_GET['low_price'];
			$lowPrice = str_replace( ',', '', $lowPriceRaw );
		} else {
			$lowPrice = '';
		}
		
		if(isset( $_GET['low_price_filter']) and ($_GET['low_price_filter'] != '') ){
			$low_price_filter = $_GET['low_price_filter'];
		} else {
			$low_price_filter = 'off';
		}
		
		if(isset($_GET['high_price']) and ($_GET['high_price'] != '') ){
			$highPriceRaw = $_GET['high_price'];
			$highPrice = str_replace( ',', '', $highPriceRaw );
		} else {
			$highPrice = '';
		}
		
		if(isset( $_GET['high_price_filter']) and ($_GET['high_price_filter'] != '') ){
			$high_price_filter = $_GET['high_price_filter'];
		} else {
			$high_price_filter = 'off';
		}
		
		if(isset($_GET['marketcap_low']) and ($_GET['marketcap_low'] != '') ){
			$lowMarketcapRaw = $_GET['marketcap_low'];
			$lowMarketcap = str_replace( ',', '', $lowMarketcapRaw );
		} else {
			$lowMarketcap = '';
		}
		
		if(isset( $_GET['marketcap_low_filter']) and ($_GET['marketcap_low_filter'] != '') ){
			$marketcap_low_filter = $_GET['marketcap_low_filter'];
		} else {
			$marketcap_low_filter = 'off';
		}
		
		if(isset($_GET['marketcap_high']) and ($_GET['marketcap_high'] != '') ){
			$highMarketcapRaw = $_GET['marketcap_high'];
			$highMarketcap = str_replace( ',', '', $highMarketcapRaw );
		} else {
			$highMarketcap = '';
		}
		
		if(isset( $_GET['marketcap_high_filter']) and ($_GET['marketcap_high_filter'] != '') ){
			$marketcap_high_filter = $_GET['marketcap_high_filter'];
		} else {
			$marketcap_high_filter = 'off';
		}
		
		if(isset($_GET['maxCoins']) and ($_GET['maxCoins'] != '') ){
			$volueToMarketcap = $_GET['volumeToMarketcap'];
		} else {
			$volueToMarketcap = '';
		}
		
		if(isset( $_GET['volume_filter']) and ($_GET['volume_filter'] != '') ){
			$volume_filter = $_GET['volume_filter'];
		} else {
			$volume_filter = 'off';
		}
			
		if(isset($_GET['maxCoins']) and ($_GET['maxCoins'] != '') ){
			$maxCoinsRaw = $_GET['maxCoins'];
			$maxCoins = str_replace( ',', '', $maxCoinsRaw );
		} else {
			$maxCoins = '';
		}
		
		if(isset( $_GET['maxTotalCoins_filter']) and ($_GET['maxTotalCoins_filter'] != '') ){
			$maxTotalCoins_filter = $_GET['maxTotalCoins_filter'];
		} else {
			$maxTotalCoins_filter = 'off';
		}
		
		
		// GET JSON AND FILTER AND CREATE NEW ARRAY	
	    $json = file_get_contents("https://api.coinmarketcap.com/v1/ticker/?limit=0");
	    $cmc = json_decode($json);
		$cmcFilteredArray = array();	
		
		foreach($cmc as $key => $val){
			// remove coins with no prices (dead)
			if($cmc[$key]->price_btc != ''){
			
			// remove coins with no available supply (dead)
			if($cmc[$key]->available_supply != 0){
		
			// filter low price
			if($low_price_filter != 'on' || $cmc[$key]->price_btc >= ($lowPrice / 100000000)){
			// filter high price
			if($high_price_filter != 'on' || $cmc[$key]->price_btc <= ($highPrice / 100000000)){
			// filter low markercap
			if($marketcap_low_filter != 'on' || $cmc[$key]->market_cap_usd >= $lowMarketcap){
			// filter high markercap
			if($marketcap_high_filter != 'on' || $cmc[$key]->market_cap_usd <= $highMarketcap){
			// filter volume
			if($maxTotalCoins_filter != 'on' || $cmc[$key]->available_supply <= $maxCoins){
			// filter supply
			$vol2Cap = $cmc[$key]->market_cap_usd * ($volueToMarketcap / 100);
			if($volume_filter != 'on' || $cmc[$key]->{'24h_volume_usd'} >= $vol2Cap){
			
				$cmcFilteredArray[$key] = new stdClass();
				$cmcFilteredArray[$key]->symbol = $cmc[$key]->symbol;
				$cmcFilteredArray[$key]->id = $cmc[$key]->id;
				$cmcFilteredArray[$key]->rank = $cmc[$key]->rank;
				$cmcFilteredArray[$key]->price_btc = $cmc[$key]->price_btc;
				$cmcFilteredArray[$key]->{'24h_volume_usd'} = $cmc[$key]->{'24h_volume_usd'};
				$cmcFilteredArray[$key]->market_cap_usd = $cmc[$key]->market_cap_usd;
				$cmcFilteredArray[$key]->available_supply = $cmc[$key]->available_supply;
				$cmcFilteredArray[$key]->total_supply = $cmc[$key]->total_supply;
				$cmcFilteredArray[$key]->max_supply = $cmc[$key]->max_supply;
				$cmcFilteredArray[$key]->percent_change_1h = $cmc[$key]->percent_change_1h;
				$cmcFilteredArray[$key]->percent_change_24h = $cmc[$key]->percent_change_24h;
				$cmcFilteredArray[$key]->percent_change_7d = $cmc[$key]->percent_change_7d;
			
			}}}}}}}}
		}
	?>
	<header>
		<h2>COIN-TOOLS.COM</h2>
	</header>
	
	<div id="searchInformation">
	 	<img src="images/logos/coinmarketcap.svg" width="100%" /><BR><BR>
	 	Use these filters to search CoinMarketCap.com using refined parameters. <BR><BR><BR>
	 	
		<form>
			<div class="inputField <?php if($low_price_filter == 'off'){ echo 'inactive'; } else { echo 'active'; } ?>" id="low_price">
				<label>lowest satoshi price</label>
				<input name="low_price" type="text" value="<?php echo $lowPrice ?>" class="satoshis" />
				<label class="switch">
					<input type="checkbox" id="low_price_filter" name="low_price_filter" onchange="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField <?php if($high_price_filter == 'off'){ echo 'inactive'; } else { echo 'active'; } ?>" id="high_price">
				<label>highest satoshi price</label>
				<input name="high_price" type="text" value="<?php echo $highPrice ?>" class="satoshis" />
				<label class="switch">
					<input type="checkbox" id="high_price_filter" name="high_price_filter" onchange="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField <?php if($marketcap_low_filter == 'off'){ echo 'inactive'; } else { echo 'active'; } ?>" id="marketcap_low">
				<label>lowest marketcap</label>
				<input name="marketcap_low" type="text" value="<?php echo $lowMarketcap ?>" class="numbers" />
				<label class="switch">
					<input type="checkbox" id="marketcap_low_filter" name="marketcap_low_filter" onchange="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField <?php if($marketcap_high_filter == 'off'){ echo 'inactive'; } else { echo 'active'; } ?>" id="marketcap_high">
				<label>highest marketcap</label>
				<input name="marketcap_high" type="text" value="<?php echo $highMarketcap ?>" class="numbers" />
				<label class="switch">
					<input type="checkbox" id="marketcap_high_filter" name="marketcap_high_filter" onchange="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField <?php if($volume_filter == 'off'){ echo 'inactive'; } else { echo 'active'; } ?>" id="volumeToMarketcap">
				<label>volume / marketcap % (2% default)</label>
				<input name="volumeToMarketcap" type="text" value="<?php echo $volueToMarketcap ?>" class="numbers" />
				<label class="switch">
					<input type="checkbox" id="volume_filter" name="volume_filter" onchange="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField <?php if($maxTotalCoins_filter == 'off'){ echo 'inactive'; } else { echo 'active'; } ?>" id="maxTotalCoins">
				<label>max available supply</label>
				<input name="maxCoins" type="text" value="<?php echo $maxCoins ?>" class="numbers" />
				<label class="switch">
					<input type="checkbox" id="maxTotalCoins_filter" name="maxTotalCoins_filter" onchange="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField" style="display:none;">
				<div class="selectGreenOrRed"><span class="redGreenSelectText">See only daily red coins</span><span class="checkboxHolder"><input class="radioInput" type="radio" name="allCoins" value="redCoins"></span></div>
				<div class="selectGreenOrRed"><span class="redGreenSelectText">See only daily green coins</span><span class="checkboxHolder"><input class="radioInput" type="radio" name="allCoins" value="greenCoins"></span></div>
				<div class="selectGreenOrRed"><span class="redGreenSelectText">See all coins</span><span class="checkboxHolder"><input class="radioInput" type="radio" name="allCoins" value="allCoins" checked></div>
			</div>
			<div class="clearFloats"></div>
			<div id="resultsInformation">
				<?php
					if($low_price_filter != 'on' && $high_price_filter != 'on'){
						echo '<strong>Showing unfiltered results of '.count($cmc).' coins on CMC</strong>';
					} else {
					
						echo '<strong>Filter showing '.count($cmcFilteredArray).' of '.count($cmc).' coins on CMC</strong><BR><BR>';
						echo '<strong>Filtered parameters</strong><BR>';
						if($low_price_filter == 'on'){ 
							$showLowBTCPrice = $lowPrice / 100000000;
							echo 'Lowest Price : '.number_format($showLowBTCPrice, 8, '.', '').' btc<BR>';
						}
						if($high_price_filter == 'on'){ 
							$showHighBTCPrice = $highPrice / 100000000;
							echo 'Highest Price : '.number_format($showHighBTCPrice, 8, '.', '').' btc<BR>';
						}
						if($marketcap_low_filter == 'on'){ 
							echo 'Lowest MktCap : $'.number_format($lowMarketcap).'<BR>';
						}
						if($marketcap_high_filter == 'on'){ 
							echo 'Highest MktCap : $'.number_format($highMarketcap).'<BR>';
						}
						if($volume_filter == 'on'){ 
							echo 'Volume/Marketcap Ratio : '.$volueToMarketcap.'%+<BR>';
						}
						if($maxTotalCoins_filter == 'on'){ 
							echo 'Max Available Coins : $'.number_format($maxCoins).'<BR>';
						}
					
					}
					 
					
				?>
			</div>
			<button id="runQuery">Filter CMC results</button>
		</form>
		
		<div id="totalCoinsScanned"></div>
		<div id="totalCoinsSelected"></div>

</div>

<div id="coinResults">

	<ul class="coinHeadline" id="cmcHeader">
		<input class="search" placeholder="Search" style="display:none;" />
		<li class="sorter">SYMBOL</li>
		<li class="sorter"><button class="sort" data-sort="coinPrice">PRICE</button></li>
		<li class="sorter"><button class="sort" data-sort="coinMarketCap">MARKET CAP</button></li>
		<li class="sorter"><button class="sort" data-sort="coinVolume">VOLUME</button></li>
		<li class="sorter"><button class="sort" data-sort="coinSupplyAvailable">AVAILABLE SUPPLY</button></li>
		<li class="sorter"><button class="sort" data-sort="coinSupplyTotal">TOTAL SUPPLY</button></li>
		<li class="sorter"><button class="sort" data-sort="coinSupplyMax">MAX SUPPLY</button></li>
		<li class="clearFloats"></li>
	</ul>

	<?php

		echo '<ul class="list">';

		foreach($cmcFilteredArray as $key => $val){
			
				$displayInformation = '<li class="coinInfo">';
				$displayInformation .= '<div class="coinSymbol">';
				$displayInformation .= $cmcFilteredArray[$key]->symbol.' - #'.$cmcFilteredArray[$key]->rank;
				$displayInformation .= '<div class="movement">'.$cmcFilteredArray[$key]->percent_change_24h.'% : 24hr | ';
				$displayInformation .= $cmcFilteredArray[$key]->percent_change_7d.'% : 7d</div>';
				$displayInformation .= '</div>';
				$displayInformation .= '<div class="coinPrice">';
				$displayInformation .= $cmcFilteredArray[$key]->price_btc.' BTC';
				$displayInformation .= '</div>';
				$displayInformation .= '<div class="coinMarketCap">';
				$displayInformation .= number_format($cmcFilteredArray[$key]->market_cap_usd);
				$displayInformation .= '</div>';
				$displayInformation .= '<div class="coinVolume">';
				$displayInformation .= number_format($cmcFilteredArray[$key]->{'24h_volume_usd'});
				$displayInformation .= '</div>';
				$displayInformation .= '<div class="coinSupplyAvailable">';
				$displayInformation .= number_format($cmcFilteredArray[$key]->available_supply);
				$displayInformation .= '</div>';
				$displayInformation .= '<div class="coinSupplyTotal">';
				$displayInformation .= number_format($cmcFilteredArray[$key]->total_supply);
				$displayInformation .= '</div>';
				$displayInformation .= '<div class="coinSupplyMax">';
				$maxSupply = number_format($cmcFilteredArray[$key]->max_supply);
				if($maxSupply == 0){
					$maxSupply = 'N/A';
				}
				$displayInformation .= $maxSupply;
				$displayInformation .= '</div>';
				$displayInformation .= '<div class="clearFloats"></div>';
				$displayInformation .= '<div class="coinCMClink">';
				$cmcLink = 'https://coinmarketcap.com/currencies/'.$cmcFilteredArray[$key]->id;
				$displayInformation .= '<a href="'.$cmcLink.'" target="_blank">'.$cmcLink.'</a>';
				$displayInformation .= '</div>';

				$displayInformation .= '</li>';
				echo $displayInformation;
			

		}

		echo '</ul>';

	?>
</div>

<script type="text/javascript" src="js/jquery-2.0.3.js"></script>
<script type="text/javascript" src="js/list.js"></script>
<script type="text/javascript">
$( document ).ready(function() {

var options = {
	valueNames: [ 'coinPrice', 'coinMarketCap', 'coinVolume', 'coinSupplyAvailable', 'coinSupplyTotal', 'coinSupplyMax' ]
};

var userList = new List('coinResults', options);

$.each(userList.items, function(k, item){
    var val = item._values; 
    
    var coinMarketCap = item._values.coinMarketCap.replace(/[^\d\.]/g,"");
    item._values.coinMarketCap = parseFloat(coinMarketCap); 
    
    var coinVolume = item._values.coinVolume.replace(/[^\d\.]/g,"");
    item._values.coinVolume = parseFloat(coinVolume);
    
    var coinSupplyAvailable = item._values.coinSupplyAvailable.replace(/[^\d\.]/g,"");
    item._values.coinSupplyAvailable = parseFloat(coinSupplyAvailable);
    
    var coinSupplyTotal = item._values.coinSupplyTotal.replace(/[^\d\.]/g,"");
    item._values.coinSupplyTotal = parseFloat(coinSupplyTotal);
    
    var coinSupplyMax = item._values.coinSupplyMax.replace(/[^\d\.]/g,"");
    item._values.coinSupplyMax = parseFloat(coinSupplyMax); 
});

$("input.numbers").each(function() {
    var x = $(this).val();
    $(this).val(x.toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ","));
});
$("input.satoshis").each(function() {
    var x = $(this).val();
    $(this).val(x.toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ","));
});

$('input.numbers').keyup(function(event) {
  if(event.which >= 37 && event.which <= 40) return;

  // format number
  $(this).val(function(index, value) {
    return value
    .replace(/\D/g, "")
    .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    ;
  });
});


});

$(window).load(function() {
	setActiveFilters();
});

function setActiveFilters(){

	if($("#low_price").hasClass('active')){
		$('#low_price_filter').trigger('click');
	} 
	if($("#high_price").hasClass('active')){
		$('#high_price_filter').trigger('click');
	} 
	if($("#marketcap_low").hasClass('active')){
		$('#marketcap_low_filter').trigger('click');
	} 
	if($("#marketcap_high").hasClass('active')){
		$('#marketcap_high_filter').trigger('click');
	} 
	if($("#volumeToMarketcap").hasClass('active')){
		$('#volume_filter').trigger('click');
	} 
	if($("#maxTotalCoins").hasClass('active')){
		$('#maxTotalCoins_filter').trigger('click');
	} 
	
}

function filterState(checkboxElem) {
  if (checkboxElem.checked) {
    $(checkboxElem).parent().parent().removeClass('inactive').addClass('active');
  } else {
    $(checkboxElem).parent().parent().removeClass('active').addClass('inactive');
  }
}
</script>

</body>
</html>
