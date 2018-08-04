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
	    
		$cryptopiaCurrenciesArray = array();
		$cryptopiaMarketsArray = array();	
		
		foreach($cryptopiaCurrenciesData as $key => $val){
			
			$coinID = $cryptopiaCurrenciesData[$key]['Id'];
			$coinName = $cryptopiaCurrenciesData[$key]['Name'];
			$coinSymbol = $cryptopiaCurrenciesData[$key]['Symbol'];
			
			$cryptopiaCurrenciesArray[$key] = array();
			$cryptopiaCurrenciesArray[$key][objPos] 		= $key;
			$cryptopiaCurrenciesArray[$key][coinID] 		= $coinID;
			$cryptopiaCurrenciesArray[$key][coinName] 		= $coinName;
			$cryptopiaCurrenciesArray[$key][coinSymbol] 	= $coinSymbol;	
			
			$cryptopiaCurrenciesArray[$key][markets] 		= array();
			
			$matchingKey = $key;
			
			foreach($cryptopiaMarketsData as $key => $val){
				
				$marketLabel = $cryptopiaMarketsData[$key]['Label'];
				$selectedSymbol;
				$typeOfMarket;
				
				if(strpos($marketLabel, '/BTC') !== false){
					$selectedSymbol = str_replace('/BTC', '', $marketLabel);
				} else if(strpos($marketLabel, '/LTC') !== false){
					$selectedSymbol = str_replace('/LTC', '', $marketLabel);
				} else if(strpos($marketLabel, '/USDT') !== false){
					$selectedSymbol = str_replace('/USDT', '', $marketLabel);
				} else if(strpos($marketLabel, '/DOGE') !== false){
					$selectedSymbol = str_replace('/DOGE', '', $marketLabel);
				}
				
								
				if($selectedSymbol == $coinSymbol){
					if(strpos($marketLabel, $selectedSymbol) !== false){
						
						$typeOfMarket = str_replace($selectedSymbol.'/', '', $marketLabel);
						$coinTradePairID = $cryptopiaMarketsData[$key]['TradePairId'];
						$coinLabel = $cryptopiaMarketsData[$key]['Label'];
						$coinAskPrice = $cryptopiaMarketsData[$key]['AskPrice'];
						$coinBidPrice = $cryptopiaMarketsData[$key]['BidPrice'];
						$coinVolume = $cryptopiaMarketsData[$key]['Volume'];
						
						$cryptopiaCurrenciesArray[$matchingKey][markets][$typeOfMarket] = array();
						$cryptopiaCurrenciesArray[$matchingKey][markets][$typeOfMarket][typeOfMarket] 	= $typeOfMarket;
						$cryptopiaCurrenciesArray[$matchingKey][markets][$typeOfMarket][tradePairID] 	= $coinTradePairID;
						$cryptopiaCurrenciesArray[$matchingKey][markets][$typeOfMarket][label] 			= $coinLabel;
						$cryptopiaCurrenciesArray[$matchingKey][markets][$typeOfMarket][askPrice] 		= $coinAskPrice;
						$cryptopiaCurrenciesArray[$matchingKey][markets][$typeOfMarket][bidPrice] 		= $coinBidPrice;
						$cryptopiaCurrenciesArray[$matchingKey][markets][$typeOfMarket][volume] 		= $coinVolume;
						
					} 
				}
			}
			
			
			
		}
	?>
	
	<?php
		
		echo '<div class="list">';

		foreach($cryptopiaCurrenciesArray as $key => $val){
			
			$FVal_Pos = $cryptopiaCurrenciesArray[$key][objPos];
			$FVal_ID = $cryptopiaCurrenciesArray[$key][coinID];
			$FVal_Name = $cryptopiaCurrenciesArray[$key][coinName];
			$FVal_Symbol = $cryptopiaCurrenciesArray[$key][coinSymbol];
			
			$displayInformation .= '<div class="coinInfo">';
			$displayInformation .= '<div class="Columns_5 symbol-lrg bold">';
			$displayInformation .= 'coinSymbol : '.$FVal_Symbol.'<BR><BR>';
			$displayInformation .= '</div>';
			
				foreach($cryptopiaCurrenciesArray[$key][markets] as $marketkey => $marketval){
					$FVal_typeOfMarket = $cryptopiaCurrenciesArray[$key][markets][$marketkey][typeOfMarket];
					$FVal_tradePairID = $cryptopiaCurrenciesArray[$key][markets][$marketkey][tradePairID];
					$FVal_label = $cryptopiaCurrenciesArray[$key][markets][$marketkey][label];
					$askPrice = $cryptopiaCurrenciesArray[$key][markets][$marketkey][askPrice];
					$FVal_askPrice = $askPrice;
					$FVal_bidPrice = $cryptopiaCurrenciesArray[$key][markets][$marketkey][bidPrice];
					$FVal_volume = $cryptopiaCurrenciesArray[$key][markets][$marketkey][volume];
					
					$displayInformation .= '<ul class="Columns_5">';
					$displayInformation .= '<li>';
					$displayInformation .= 'market type : '.$FVal_typeOfMarket.'<BR>';
					$displayInformation .= 'trade paid id : '.$FVal_tradePairID.'<BR>';
					$displayInformation .= 'label : '.$FVal_label.'<BR>';
					$displayInformation .= 'ask price: '.$FVal_askPrice.'<BR>';
					$displayInformation .= 'bid price : '.$FVal_bidPrice.'<BR>'.'<BR>';
					$displayInformation .= 'volume : '.$FVal_volume.'<BR>'.'<BR>';
					$displayInformation .= '</li>';
					$displayInformation .= '</ul>';
					
				}
			
			$displayInformation .= '</div>';
			echo $displayInformation;

		}

		echo '</div>';
		
		print_r($cryptopiaCurrenciesArray[0]);
		
	?>
	

</body>
</html>
