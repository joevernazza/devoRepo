<html>
<head>
	<link href="css/main.css" rel="stylesheet">
</head>

<body>


	<?php
	    $json = file_get_contents("https://api.coinmarketcap.com/v1/ticker/?limit=0");
	    $cmc = json_decode($json);

			$cmcFilteredArray = array();

			$portfolioArray = array('IFT', 'MSP', 'BIS', 'OPC', 'HOT', 'TOMO', 'SS', 'SEN', 'TFD', 'ZCO', 'CS', 'BDG', 'ORI', 'ELEC', 'PARETO', 'LINX', 'RBX');

			//print_r($portfolioArray);

	    foreach($cmc as $key => $val){
				$cmcFilteredArray[$key]->symbol = $cmc[$key]->symbol;
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
			}

			processResults($cmcFilteredArray, $portfolioArray);

			function processResults($cmcFilteredArray, $portfolioArray){
				foreach($cmcFilteredArray as $key => $val){
					//echo $cmcFilteredArray[$key]->market_cap_usd.' '.$lowMarketcap.' '.$highMarketcap;
					if (in_array($cmcFilteredArray[$key]->symbol, $portfolioArray)) {
						$displayInformation = '<div class="coinInfo">';
						$displayInformation .= '<div class="coinSymbol">';
						$displayInformation .= $cmcFilteredArray[$key]->symbol.' : #'.$cmcFilteredArray[$key]->rank;
						$displayInformation .= '</div>';
						$displayInformation .= '<div class="coinPrice">';
						$displayInformation .= '<strong>Price :</strong> '.$cmcFilteredArray[$key]->price_btc.' BTC';
						$displayInformation .= '</div>';
						$displayInformation .= '<div class="coinMarketCap">';
						$displayInformation .= '<strong>Market Cap :</strong> $'.number_format($cmcFilteredArray[$key]->market_cap_usd);
						$displayInformation .= '</div>';
						$displayInformation .= '<div class="coinVolume">';
						$displayInformation .= '<strong>Volume :</strong> $'.number_format($cmcFilteredArray[$key]->{'24h_volume_usd'});
						$displayInformation .= '</div>';
						$displayInformation .= '<div class="clearFloats"></div>';
						$displayInformation .= '<div class="coinSupplyAvailable">';
						$displayInformation .= '<strong>Available Supply :</strong> '.number_format($cmcFilteredArray[$key]->available_supply);
						$displayInformation .= '</div>';
						$displayInformation .= '<div class="coinSupplyTotal">';
						$displayInformation .= '<strong>Total Supply :</strong> '.number_format($cmcFilteredArray[$key]->total_supply);
						$displayInformation .= '</div>';
						$displayInformation .= '<div class="coinSupplyMax">';
						$displayInformation .= '<strong>Max Supply :</strong> '.number_format($cmcFilteredArray[$key]->max_supply);
						$displayInformation .= '</div>';
						$displayInformation .= '<div class="clearFloats"></div>';
						$displayInformation .= '<div class="coinCMClink">';
						$cmcLink = 'https://coinmarketcap.com/currencies/'.$cmcFilteredArray[$key]->symbol;
						$displayInformation .= '<a href="'.$cmcLink.'">'.$cmcLink.'</a>';
						$displayInformation .= '</div>';
						$displayInformation .= '</div>';
						echo $displayInformation;
					}

				}
			}




	?>

</body>
</html>
