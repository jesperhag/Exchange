<?php

class Exchange{
	
	private $amount = 1;
	
	private $from_currency;
	
	private $to_currency;
	
	private $to_amount;
	
	private $accepted_currency = array(
"AUD", "BHD", "BRL", "BGN", "CLP", "DKK", "DOP", "EGP", "EUR", "FJD", "PHP", "AED", "HKD", "INR", "ISK", "ILS", "JPY", "JOD", "CAD", "KES", "CNY", "HRK", "KWD", "LVL", "LTL", "MYR", "MUR", "MXN", "NOK", "NZD", "PLN", "RON", "RUB", "SAR", "CHF", "SGD", "GBP", "ZAR", "TWD", "THB", "CZK", "TTD", "TRY", "HUF", "USD", "SEK"
	);
	
	private $pattern = '/<b>([\d\.\d\s]+)([\w\s\.]+)+=\s+([\d\.\d\s]+)+([\w\s\.]+)+<\/b>/';
	
	function get_contents($url){

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
 
		$contents = curl_exec($ch); 
		
		$contents = str_replace("&nbsp;", " ", $contents);
		
		curl_close($ch);
		
		return $contents;
	
	}
	
	function change_from($from_currency){
		
		$from_currency = strtoupper($from_currency);
		
		if( ! in_array( $from_currency, $this->accepted_currency ) ){
			
			echo "Invalid from exchange...";
			
			return $this;
			
		}else{
			
			$this->from_currency = $from_currency;
		
			return $this;
		}
	}
	
	function amount($amount){
		
		$this->amount = $amount;
		
		return $this;
		
	}
	
	function change_to($to_currency){
		
		$to_currency = strtoupper($to_currency);
		
		if( ! in_array( $to_currency, $this->accepted_currency ) ){
			
			echo "Invalid to exchange...";
			
			return $this;
			
		}else{
		
			$this->to_currency = $to_currency;
		
			return $this;
			
		}
	}
	
	function exchange($return = NULL){
				
		$url = "http://www.google.com/search?q=".$this->amount."%20".$this->from_currency."%20in%20".$this->to_currency."";
		
		$subject = $this->get_contents($url);

		if( preg_match($this->pattern, $subject, $matches) ){
			$matches = array(
				"amount" => $matches[1],
				"from_currency" => $matches[2],
				"rate" => $matches[3],
				"to_currency" => $matches[4]
			);
			if($return != NULL){
				
				if( ! empty($matches[$return]) && array_key_exists($return, $matches) ){
					
					echo $matches[strtolower($return)];
					
				}
				else{
					
					echo "Invalid return type given...";
					
				}
				
			}else{
				
			return ($matches);
			
			}
			
		}
		
	else{
		
			return FALSE;
		}
		
	}
	
	function exchange_json(){
		
		$url = "http://www.google.com/search?q=".$this->amount."%20".$this->from_currency."%20in%20".$this->to_currency."";
		
		$subject = $this->get_contents($url);

		if( preg_match($this->pattern, $subject, $matches) ){
			
			$matches[3] = preg_replace("/([^\d\.])+/", "", $matches[3]);
			
			$matches = array(
				"amount" => $matches[1],
				"from_currency" => $matches[2],
				"rate" => $matches[3],
				"to_currency" => $matches[4]
			);
			
			
			return json_encode($matches);
		}
		
		else{
		
			return FALSE;
		}
	}
	
	function render_options($data = NULL){ // $data could be = 'id = "someid" class="someclass"' , etc... For styling and javascript purpose mainly
		
		echo "<select $data>";
		
		foreach($this->accepted_currency as $cur){
			
			echo '<option value="'.$cur.'">'.$cur.'</option>';
			
		}
		
		echo "</select>";
		
	}
	
}
/* End of file exchange_api.php */