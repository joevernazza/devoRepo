<!DOCTYPE html>
<html>
<head>
	<link href="https://fonts.googleapis.com/css?family=Quattrocento" rel="stylesheet">  
	
	<link rel="stylesheet" href="css/simple-grid.css">
	<link rel="stylesheet" href="css/selectize.css">
	<link rel="stylesheet" href="css/main.css">
	
	<script type="text/javascript" src="js/jquery-2.0.3.js"></script>
	<script type="text/javascript" src="js/selectize.js"></script>
	<script type="text/javascript" src="js/list.js"></script>
	<script type="text/javascript" src="js/jquery.priceformat.js"></script>
	<script type="text/javascript" src="js/exchangeSearch_cryptopia.js"></script>
</head>

<body>

	<?php
		
		$variablesArray = [
			'btc_market', 'ltc_market', 'doge_market', 'usd_market',
			'low_sat_init', 'low_sat', 'high_sat_init', 'high_sat',
			'low_lite_init', 'low_lite', 'high_lite_init', 'high_lite',
			'low_doge_init', 'low_doge', 'high_doge_init', 'high_doge',
			'low_usd_init', 'low_usd', 'high_usd_init', 'high_usd',
			'algo_init', 'algo', 'gain_filter', 'red_24hr_selector', 'green_24hr_selector',
			'marketcap_low_init', 'marketcap_low', 'marketcap_high_init', 'marketcap_high',
			'volume_init', 'volumeToMarketcap', 'volume_min_init', 'volumeMinimum',
			'min_cryptopia_volume_init', 'min_cryptopia_volume', 'max_supply_init', 'max_supply',
			'showOnlyCMC', 'hideOnlyCMC'	
		];
		
		foreach($variablesArray as $key => $val){
			$variableToSetup = $val;
			if(isset( $_GET[$variableToSetup]) and $_GET[$variableToSetup] != ''){
				if($variableToSetup == 'low_usd' || $variableToSetup == 'high_usd' || $variableToSetup == 'marketcap_low' || $variableToSetup == 'marketcap_high' || $variableToSetup == 'current_supply' || $variableToSetup == 'volumeMinimum' || $variableToSetup == 'max_supply'){
					$$variableToSetup = str_replace( ',', '', $_GET[$variableToSetup] );
				} else {
					$$variableToSetup = $_GET[$variableToSetup];
				}
				
			} else {
				$$variableToSetup = '';
			}
		}
		
		
		
		
		
		
		// GET JSON AND FILTER AND CREATE NEW ARRAY	
	    $markets = file_get_contents("https://www.cryptopia.co.nz/api/GetMarkets");
	    $cryptopiaMarkets = json_decode($markets, true);
	    $cryptopiaMarketsData = $cryptopiaMarkets['Data'];
	    
	    $currencies = file_get_contents("https://www.cryptopia.co.nz/api/GetCurrencies");
	    $cryptopiaCurrencies = json_decode($currencies, true);
	    $cryptopiaCurrenciesData = $cryptopiaCurrencies['Data'];
	    
	    $cmc = file_get_contents("https://api.coinmarketcap.com/v1/ticker/?limit=0");
	    $cmcData = json_decode($cmc, true);
	    
		$cryptopiaFilteredMarketsArray = array();	
		
		foreach($cryptopiaMarketsData as $key => $val){
			
			$checkLabel = $cryptopiaMarketsData[$key]['Label'];
			$selectedSymbol = $cryptopiaMarketsData[$key]['Label'];
			
			if(strpos($checkLabel, '/BTC') !== false){
				$selectedSymbol = str_replace('/BTC', '', $selectedSymbol);
			} else if(strpos($checkLabel, '/LTC') !== false){
				$selectedSymbol = str_replace('/LTC', '', $selectedSymbol);
			} else if(strpos($checkLabel, '/USDT') !== false){
				$selectedSymbol = str_replace('/USDT', '', $selectedSymbol);
			} else if(strpos($checkLabel, '/DOGE') !== false){
				$selectedSymbol = str_replace('/DOGE', '', $selectedSymbol);
			}
			
			$cryptopiaFilteredMarketsArray[$key] = new stdClass();
			$cryptopiaFilteredMarketsArray[$key]->TradePairId 		= $cryptopiaMarketsData[$key]['TradePairId'];
			$cryptopiaFilteredMarketsArray[$key]->Symbol 	 		= $selectedSymbol;
			$cryptopiaFilteredMarketsArray[$key]->Label 			= $cryptopiaMarketsData[$key]['Label'];
			$cryptopiaFilteredMarketsArray[$key]->LastPrice 		= $cryptopiaMarketsData[$key]['LastPrice'];
			$cryptopiaFilteredMarketsArray[$key]->BaseVolume 		= $cryptopiaMarketsData[$key]['BaseVolume'];
			$cryptopiaFilteredMarketsArray[$key]->Change 			= $cryptopiaMarketsData[$key]['Change'];
			$cryptopiaFilteredMarketsArray[$key]->Name 				= ''; 
			$cryptopiaFilteredMarketsArray[$key]->Algo 	 			= '';
			$cryptopiaFilteredMarketsArray[$key]->MarketCap 		= ''; 
			$cryptopiaFilteredMarketsArray[$key]->CMCvolume24hr 	= ''; 
			$cryptopiaFilteredMarketsArray[$key]->availableSupply 	= ''; 
			
			$matchingKey = $key;
			foreach($cryptopiaCurrenciesData as $key => $val){
				if($cryptopiaCurrenciesData[$key]['Symbol'] == $selectedSymbol){
					$cryptopiaFilteredMarketsArray[$matchingKey]->Name = $cryptopiaCurrenciesData[$key]['Name']; 
					$cryptopiaFilteredMarketsArray[$matchingKey]->Algo = $cryptopiaCurrenciesData[$key]['Algorithm'];
				}
			}
			foreach($cmcData as $key => $val){
				if($cmcData[$key]['symbol'] == $selectedSymbol){
					if (array_key_exists('market_cap_usd', $cmcData[$key])) {
						$cryptopiaFilteredMarketsArray[$matchingKey]->CMCinfo 			= true;
						$cryptopiaFilteredMarketsArray[$matchingKey]->MarketCap 		= $cmcData[$key]['market_cap_usd']; 
						$cryptopiaFilteredMarketsArray[$matchingKey]->CMCvolume24hr 	= $cmcData[$key]['24h_volume_usd']; 
						$cryptopiaFilteredMarketsArray[$matchingKey]->availableSupply 	= $cmcData[$key]['available_supply']; 
					} else {
						$cryptopiaFilteredMarketsArray[$matchingKey]->CMCinfo 			= false;
						$cryptopiaFilteredMarketsArray[$matchingKey]->availableSupply	= '';
						
					}
				} 
			}
			
			if(isset($cryptopiaFilteredMarketsArray[$matchingKey]->Algo)){
				$algoEntry = $cryptopiaFilteredMarketsArray[$matchingKey]->Algo;
				$algoList[] = $algoEntry;
				$AlgosForSelect = array_unique($algoList);
			}
			
			// filter markets
			if($btc_market == 'on' && strpos($checkLabel, '/BTC') == false){
				unset($cryptopiaFilteredMarketsArray[$matchingKey]);
			}
			if($usd_market == 'on' && strpos($checkLabel, '/USDT') == false){
				unset($cryptopiaFilteredMarketsArray[$matchingKey]);
			}
			if($ltc_market == 'on' && strpos($checkLabel, '/LTC') == false){
				unset($cryptopiaFilteredMarketsArray[$matchingKey]);
			}
			if($doge_market == 'on' && strpos($checkLabel, '/DOGE') == false){
				unset($cryptopiaFilteredMarketsArray[$matchingKey]);
			}
			
			$lastPrice = $cryptopiaMarketsData[$matchingKey]['LastPrice'];
			
			// if coin is a btc market, and low sat filter is on >>
			if(strpos($checkLabel, '/BTC') !== false && $low_sat_init == 'on'){
				// filter low sat price
				if($lastPrice <= $low_sat){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			// if coin is a btc market, and high sat filter is on >>
			if(strpos($checkLabel, '/BTC') !== false && $high_sat_init == 'on'){
				// filter high sat price
				if($lastPrice >= $high_sat){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			
			// if coin is a USDT market, and low sat filter is on >>
			if(strpos($checkLabel, '/USDT') !== false && $low_usd_init == 'on' && isset($low_usd)){
				// filter low usd price
				if($lastPrice <= $low_usd){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			// if coin is a USDT market, and high sat filter is on >>
			if(strpos($checkLabel, '/USDT') !== false && $high_usd_init == 'on' && isset($low_usd)){
				// filter high usd price
				if($lastPrice >= $high_usd){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			
			// if coin is a LTC market, and low lite filter is on >>
			if(strpos($checkLabel, '/LTC') !== false && $low_lite_init == 'on' && isset($low_lite)){
				// filter low lite price
				if($lastPrice <= $low_lite){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			// if coin is a LTC market, and high lite filter is on >>
			if(strpos($checkLabel, '/LTC') !== false && $high_lite_init == 'on' && isset($high_lite)){
				// filter high lite price
				if($lastPrice >= $high_lite){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			
			// if coin is a DOGE market, and low doge filter is on >>
			if(strpos($checkLabel, '/DOGE') !== false && $low_doge_init == 'on' && isset($low_doge)){
				// filter low doge price
				if($lastPrice <= $low_doge){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			// if coin is a DOGE market, and high doge filter is on >>
			if(strpos($checkLabel, '/DOGE') !== false && $high_doge_init == 'on' && isset($high_doge)){
				// filter high doge price
				if($lastPrice >= $high_doge){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			
			if($algo_init == 'on' && isset($cryptopiaFilteredMarketsArray[$matchingKey]->Algo)){
				if($cryptopiaFilteredMarketsArray[$matchingKey]->Algo != $algo){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				} 
			}
			
			if($min_cryptopia_volume_init == 'on' && isset($cryptopiaFilteredMarketsArray[$matchingKey]->BaseVolume)){
				if($cryptopiaFilteredMarketsArray[$matchingKey]->BaseVolume <= $min_cryptopia_volume){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				} 
			}
			
			if($max_supply_init == 'on'){
				if($cryptopiaFilteredMarketsArray[$matchingKey]->availableSupply >= $max_supply || empty($cryptopiaFilteredMarketsArray[$matchingKey]->availableSupply) || $cryptopiaFilteredMarketsArray[$matchingKey]->availableSupply == ''){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			
			if(isset($cryptopiaFilteredMarketsArray[$matchingKey]->CMCinfo)) {
			
			if($cryptopiaFilteredMarketsArray[$matchingKey]->CMCinfo == true){
				// if marketcap filters are on, and if marketcap dosnt match filters, remove it from the array
				if($marketcap_low_init == 'on' && isset($marketcap_low)){
					if($cryptopiaFilteredMarketsArray[$matchingKey]->MarketCap <= $marketcap_low || empty($cryptopiaFilteredMarketsArray[$matchingKey]->MarketCap)){
						unset($cryptopiaFilteredMarketsArray[$matchingKey]);
					}
				}
				if($marketcap_high_init == 'on' && isset($marketcap_high)){
					if($cryptopiaFilteredMarketsArray[$matchingKey]->MarketCap >= $marketcap_high || empty($cryptopiaFilteredMarketsArray[$matchingKey]->MarketCap)){
						unset($cryptopiaFilteredMarketsArray[$matchingKey]);
					}
				}
					
				// if marketcap filters are on, and if marketcap dosn't match filters, remove it from the array
				if($volume_init == 'on'){
					$vol2Cap = $cryptopiaFilteredMarketsArray[$matchingKey]->MarketCap * ($volumeToMarketcap / 100);
					if($cryptopiaFilteredMarketsArray[$matchingKey]->CMCvolume24hr >= $vol2Cap){
						unset($cryptopiaFilteredMarketsArray[$matchingKey]);
					}
				}
				
				
				if($volume_min_init == 'on'){
					if($cryptopiaFilteredMarketsArray[$matchingKey]->CMCvolume24hr <= $volumeMinimum){
						unset($cryptopiaFilteredMarketsArray[$matchingKey]);
					} 
				}
				
				
				if($hideOnlyCMC == 'on'){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			} else {
				if($volume_min_init == 'on'){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]); 
				}
				if($showOnlyCMC == 'on'){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
				if($marketcap_low_init == 'on' || $marketcap_high_init == 'on'){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			
			} else {
				if($volume_min_init == 'on'){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]); 
				}
				if($showOnlyCMC == 'on'){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
				if($marketcap_low_init == 'on' || $marketcap_high_init == 'on'){
					unset($cryptopiaFilteredMarketsArray[$matchingKey]);
				}
			}
			
			
				
		}
			
	?>
	<header>
		<h2>COIN-TOOLS.COM</h2>
	</header>
	
	<div id="searchInformation">
		<div class="row">
		
		<div id="searchHeader" class="col-2">
			<img src="images/logos/cryptopia.png" id="logo-cryptopia" />
			<BR>
			Use these filters to search Cryptopia.co.nz using refined parameters, mixed with CoinMarketCap.com (CMC) for extra infomation when available.
			
			
			<div id="totalCoinsScanned"></div>
			<div id="totalCoinsSelected"></div>
	
		</div>
		
		<form id="allFilters" class="col-10">
		
		<div class="row">
		
		<div class="col-3" id="marketSelection">
			<div class="inputField <?php if($btc_market == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="btc_market_selector">
				<label>BTC Market</label>
				<label class="switch">
					<input type="checkbox" id="btc_market" name="btc_market" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField 
				<?php 
					if($low_sat_init == 'on' && $btc_market == 'on'){ echo 'show active'; } else { 
						if ($btc_market == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="low_sat_selector">
				<label>min btc price</label>
				<input name="low_sat" id="low_sat" class="satoshis" value="<?php echo $low_sat; ?>"  />
				<label class="switch">
					<input type="checkbox" id="low_sat_init" name="low_sat_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="endOfCluster inputField 
				<?php 
					if($high_sat_init == 'on' && $btc_market == 'on'){ echo 'show active'; } else { 
						if ($btc_market == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="high_sat_selector">
				<label>max btc price</label>
				<input name="high_sat" id="high_sat" class="satoshis" value="<?php echo $high_sat; ?>" />
				<label class="switch">
					<input type="checkbox" id="high_sat_init" name="high_sat_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
			<div class="inputField <?php if($usd_market == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="usd_market_selector">
				<label>USDT Market</label>
				<label class="switch">
					<input type="checkbox" id="usd_market" name="usd_market" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField 
				<?php 
					if($low_usd_init == 'on' && $usd_market == 'on'){ echo 'show active'; } else { 
						if ($usd_market == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="low_usd_selector">
				<label>Min usd price</label>
				<input name="low_usd" id="low_usd" type="text" value="<?php echo $low_usd; ?>" class="dollars" />
				<label class="switch">
					<input type="checkbox" id="low_usd_init" name="low_usd_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="endOfCluster inputField 
				<?php 
					if($high_usd_init == 'on' && $usd_market == 'on'){ echo 'show active'; } else { 
						if ($usd_market == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="high_usd_selector">
				<label>Max usd price</label>
				<input name="high_usd" id="high_usd" type="text" value="<?php echo $high_usd; ?>" class="dollars" />
				<label class="switch">
					<input type="checkbox" id="high_usd_init" name="high_usd_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
			<div class="inputField <?php if($ltc_market == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="ltc_market_selector">
				<label>LTC Market</label>
				<label class="switch">
					<input type="checkbox" id="ltc_market" name="ltc_market" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField <?php 
					if($low_lite_init == 'on' && $ltc_market == 'on'){ echo 'show active'; } else { 
						if ($ltc_market == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="low_lite_selector">
				<label>Min ltc price</label>
				<input name="low_lite" id="low_lite" type="text" value="<?php echo $low_lite; ?>" class="litoshis" />
				<label class="switch">
					<input type="checkbox" id="low_lite_init" name="low_lite_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="endOfCluster inputField <?php 
					if($high_lite_init == 'on' && $ltc_market == 'on'){ echo 'show active'; } else { 
						if ($ltc_market == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="high_lite_selector">
				<label>Max ltc price</label>
				<input name="high_lite" id="high_lite" type="text" value="<?php echo $high_lite; ?>" class="litoshis" />
				<label class="switch">
					<input type="checkbox" id="high_lite_init" name="high_lite_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
			<div class="inputField <?php if($doge_market == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="doge_market_selector">
				<label>DOGE Market</label>
				<label class="switch">
					<input type="checkbox" id="doge_market" name="doge_market" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField 
				<?php 
					if($high_doge_init == 'on' && $doge_market == 'on'){ echo 'show active'; } else { 
						if ($doge_market == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="low_doge_selector">
				<label>Min doge price</label>
				<input name="low_doge" id="low_doge" type="text" value="<?php echo $low_doge; ?>" class="dogeshi" />
				<label class="switch">
					<input type="checkbox" id="low_doge_init" name="low_doge_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="endOfCluster inputField 
				<?php 
					if($high_doge_init == 'on' && $doge_market == 'on'){ echo 'show active'; } else { 
						if ($doge_market == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="high_doge_selector">
				<label>Max doge price</label>
				<input name="high_doge" id="high_doge" type="text" value="<?php echo $high_doge; ?>" class="dogeshi" />
				<label class="switch">
					<input type="checkbox" id="high_doge_init" name="high_doge_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
		</div>
		<div class="col-3" id="cryptopiaFilters">
			
			
			<div class="inputField <?php if($algo_init == 'on'){ echo 'active'; }  else { echo 'inactive'; }  ?>" id="algo_selector">
				<label>Cryptopia - Filter by Algo</label>
				
	    			<?php
		    			sort($AlgosForSelect);
		    			echo '<select id="algo" name="algo" value="'.$algo.'">';
		    			foreach ($AlgosForSelect as $selectAlgoValue) {
							$selectAlgoValue = $selectAlgoValue;
							if($selectAlgoValue != ''){
								if($selectAlgoValue == $algo){
									echo '<option value="'.$selectAlgoValue.'" selected>'.$selectAlgoValue.'</option>';
								} else {
									echo '<option value="'.$selectAlgoValue.'">'.$selectAlgoValue.'</option>';	
								}
								
							}
							 
						}	
		    		?>
	    		</select>
				<label class="switch">
					<input type="checkbox" id="algo_init" name="algo_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
			<div class="inputField <?php if($min_cryptopia_volume_init == 'on'){ echo 'active'; }  else { echo 'inactive'; }  ?>" id="min_cryptopia_volume_selector">
				<label>Cryptopia - Min Volume
					<?php 
						if($btc_market == 'on'){
							echo ' in BTC';
						}
						
						if($ltc_market == 'on'){
							echo ' in LTC';
						}
						if($doge_market == 'on'){
							echo ' in DOGE';
						}
						if($usd_market == 'on'){
							echo ' in USD';
						}	
					?>
				</label>
				<input name="min_cryptopia_volume" id="min_cryptopia_volume" type="text" value="<?php echo $min_cryptopia_volume; ?>" class="numbers" />
				<label class="switch">
					<input type="checkbox" id="min_cryptopia_volume_init" name="min_cryptopia_volume_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
			<div class="inputField <?php if($gain_filter == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="gain_filter_selector">
				<label>Cryptopia - % Change</label>
				<label class="switch">
					<input type="checkbox" id="gain_filter" name="gain_filter" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
		</div>
		<div class="col-3" id="cmcFilter">
			
			
			<div class="inputField 
				<?php 
					if($green24hr == 'on' && $gain_filter == 'on'){ echo 'show active'; } else { 
						if ($gain_filter == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="green_24hr_selector">
				<label>Show only 24hr Gainers</label>
				<label class="switch">
					<input type="checkbox" id="green_24hr_init" name="green_24hr_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField 
				<?php 
					if($red24hr == 'on' && $gain_filter == 'on'){ echo 'show active'; } else { 
						if ($gain_filter == 'on'){ echo 'show inactive'; } else { echo 'hide inactive'; } 
					} 
				?>" id="red_24hr_selector">
				<label>Show only 24hr Losers</label>
				<label class="switch">
					<input type="checkbox" id="red_24hr_init" name="red_24hr_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
			
			
			
			
			<div class="inputField <?php if($marketcap_high_init == 'on'){ echo 'active'; }  else { echo 'inactive'; }  ?>" id="marketcap_high_selector">
				<label>CMC - Max marketcap</label>
				<input name="marketcap_high" id="marketcap_high" type="text" value="<?php echo $marketcap_high; ?>" class="numbers" />
				<label class="switch">
					<input type="checkbox" id="marketcap_high_init" name="marketcap_high_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="inputField <?php if($max_supply_init == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="max_supply_selector">
				<label>CMC - Max supply</label>
				<input name="max_supply" id="max_supply" type="text" value="<?php echo $max_supply; ?>" class="numbers" />
				<label class="switch">
					<input type="checkbox" id="max_supply_init" name="max_supply_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
			
			
			<div class="inputField <?php if($volume_init == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="volumeToMarketcap_selector">
				<label>CMC - volume / marketcap</label>
				<input name="volumeToMarketcap" id="volumeToMarketcap" type="text" value="<?php echo $volumeToMarketcap; ?>" class="numbers" />
				<label class="switch">
					<input type="checkbox" id="volume_init" name="volume_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="or">or</div>
			<div class="inputField <?php if($volume_min_init == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="volumeMinimum_selector">
				
				<label>CMC - Min volume in $</label>
				<input name="volumeMinimum" id="volumeMinimum" type="text" value="<?php echo $volumeMinimum; ?>" class="numbers" />
				<label class="switch">
					<input type="checkbox" id="volume_min_init" name="volume_min_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
		</div>
		<div class="col-3" id="cmcOrNoCMCFilter">
			
			
			
			<div class="inputField <?php if($showOnlyCMC == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="showOnlyCMC">
				<label>Show only coins on CMC</label>
				<label class="switch">
					<input type="checkbox" id="showOnlyCMC_init" name="showOnlyCMC_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			<div class="or">or</div>
			<div class="inputField <?php if($hideOnlyCMC == 'on'){ echo 'active'; } else { echo 'inactive'; } ?>" id="hideOnlyCMC">
				<label>Show only coins NOT on CMC</label>
				<label class="switch">
					<input type="checkbox" id="hideOnlyCMC_init" name="hideOnlyCMC_init" onchange="filterState(this)" onload="filterState(this)">
					<span class="slider round"></span>
				</label>
			</div>
			
		</div>

		<div class="clearFloats"></div>

		<div class="col-12" id="cmcOrNoCMCFilter">
			<button id="runQuery">Filter Results</button>
		</div>
		
		<div class="clearFloats"></div>
		
		</form>
		
		<div class="clearFloats"></div>
	</div>
	</div>
</div>
</div>
</div>
<div id="coinResults">

	<ul class="coinHeadline" id="resultsHeader">
		<input class="search" placeholder="Search" style="display:none;" />
		<li class="sorter"><button class="sort" data-sort="coinName_cryptopia">Name</button></li>
		<li class="sorter"><button class="sort" data-sort="coinMarket_cryptopia">Market</button></li>
		
		
		<li class="sorter"><button class="sort" data-sort="coinAlgo_cryptopia">Algo</button></li>
		<li class="sorter"><button class="sort" data-sort="coinPrice_cryptopia">Price</button></li>
		<li class="sorter"><button class="sort" data-sort="coinChange_cryptopia">24hr %</button></li>
		<li class="sorter"><button class="sort" data-sort="coinVolume_cryptopia">Exchange Volume</button></li>
		<li class="sorter"><button class="sort" data-sort="coinCMCVolume_cryptopia">Global Volume</button></li>
		
		<li class="sorter"><button class="sort" data-sort="coinCap_cryptopia">Market Cap</button></li>
		<li class="sorter"><button class="sort" data-sort="coinSupply_cryptopia">Supply</button></li>
		<li class="clearFloats"></li>
	</ul>

	<?php
		
		echo '<ul class="list">';

		foreach($cryptopiaFilteredMarketsArray as $key => $val){
			
				$displayInformation = '<li class="coinInfo">';
				
				$displayInformation .= '<div class="cell bold coinName_cryptopia">';
				$name = $cryptopiaFilteredMarketsArray[$key]->Name;
				if($name == ''){
					$name = '&nbsp;';
				}
				$nameSearchLink = 'http://www.google.com/search?q='.$name.'%20bitcointalk';
				$displayInformation .= '<a href=';
				$displayInformation .= $nameSearchLink;
				$displayInformation .= ' target="_blank">';
				$displayInformation .= $name;
				$displayInformation .= '</a></div>';
				
				$displayInformation .= '<div class="cell coinMarket_cryptopia">';
				$label = $cryptopiaFilteredMarketsArray[$key]->Label;
				$CMCLinkSymbol = str_replace( '/', '_', $label);
				$displayInformation .= '<a href="https://www.cryptopia.co.nz/Exchange?market='.$CMCLinkSymbol.'" target="_blank">';
				$displayInformation .= $label;
				$displayInformation .= '</a></div>';
				
				$displayInformation .= '<div class="cell coinAlgo_cryptopia">';
				$algo = $cryptopiaFilteredMarketsArray[$key]->Algo;
				if($algo == ''){
					$algo = 'N/A';
				}
				$displayInformation .= $algo;
				$displayInformation .= '</div>';
				
				$displayInformation .= '<div class="cell coinPrice_cryptopia">';
				
				$price = number_format($cryptopiaFilteredMarketsArray[$key]->LastPrice, 8, '.', '');
				if($price == ''){
					$price = '&nbsp;';
				}
				if(strpos($label, '/BTC') !== false){
					$marketType = ' btc';
					$displayInformation .= $price.$marketType;
				} else if(strpos($label, '/LTC') !== false){
					$marketType = ' ltc';
					$displayInformation .= $price.$marketType;
				} else if(strpos($label, '/USDT') !== false){
					$marketType = ' usdt';
					$displayInformation .= '$ '.number_format($price, 4);;
				} else if(strpos($label, '/DOGE') !== false){
					$marketType = ' doge';
					$displayInformation .= $price.$marketType;
				}
				
				$displayInformation .= '</div>';
				
				
				$movement = number_format($cryptopiaFilteredMarketsArray[$key]->Change);
				if($movement >= 0){
					$movementDirection = 'green';
				} else {
					$movementDirection = 'red';
				}
				$displayInformation .= '<div class="cell bold '.$movementDirection.' coinChange_cryptopia">';
				$percentChange = number_format($cryptopiaFilteredMarketsArray[$key]->Change);
				if($percentChange == ''){
					$percentChange = '&nbsp;';
				}
				$displayInformation .= $percentChange;
				$displayInformation .= '</div>';
				
				
				$displayInformation .= '<div class="cell coinVolume_cryptopia">';
				$coinVolume = (int)$cryptopiaFilteredMarketsArray[$key]->BaseVolume.$marketType;
				if($coinVolume == ''){
					$coinVolume = '&nbsp;';
				}
				$displayInformation .= number_format($coinVolume).$marketType;
				$displayInformation .= '</div>';
				
				$displayInformation .= '<div class="cell coinCMCVolume_cryptopia">';
				
				if($cryptopiaFilteredMarketsArray[$key]->CMCvolume24hr == ''){
					$coinCMCVolume = 'N/A';
				} else {
					$coinCMCVolume = '$'.number_format($cryptopiaFilteredMarketsArray[$key]->CMCvolume24hr);
				}
				$displayInformation .= $coinCMCVolume;
				
				$displayInformation .= '</div>';
				
				
				$displayInformation .= '<div class="cell coinCap_cryptopia">';
				if($cryptopiaFilteredMarketsArray[$key]->MarketCap == ''){
					$coinCap = 'N/A';
				} else {
					$coinCap = '$'.number_format($cryptopiaFilteredMarketsArray[$key]->MarketCap);
				}
				$displayInformation .= $coinCap;
				$displayInformation .= '</div>';
				
				
				$displayInformation .= '<div class="cell coinSupply_cryptopia">';
				if($cryptopiaFilteredMarketsArray[$key]->availableSupply == ''){
					$coinSupply = 'N/A';
				} else {
					$coinSupply = number_format($cryptopiaFilteredMarketsArray[$key]->availableSupply).' '.$cryptopiaFilteredMarketsArray[$key]->Symbol;
				}
				$displayInformation .= $coinSupply;
				$displayInformation .= '</div>';
				
				
				$displayInformation .= '<div class="clearFloats"></div>';
				$displayInformation .= '</li>';
				echo $displayInformation;
			

		}

		echo '</ul>';
		
	?>
</div>



</body>
</html>
