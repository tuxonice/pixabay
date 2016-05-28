<?php
class apiCall
{
	
	protected $curl = NULL;
	protected $requestUrl = NULL;
	protected $validParams = NULL;
	protected $validCategories = NULL;
	protected $validLangs = NULL;
	
	const _API_END_POINT = 'https://pixabay.com/api/';
	
	
	
	public function __construct($apiKey)
	{
		
		$this->requestUrl = self::_API_END_POINT.'?key='.$apiKey;
		$this->curl = curl_init();
		
		$this->validLangs = array('cs','da','de','en','es','fr','id','it',
				'hu','nl','no','pl','pt','ro','sk','fi','sv','tr','vi','th',
				'bg','ru','el','ja','ko','zh');
		
		$this->validCategories = array(
				'fashion',
				'nature',
				'backgrounds',
				'science',
				'education',
				'people',
				'feelings',
				'religion',
				'health',
				'places',
				'animals',
				'industry',
				'food',
				'computer',
				'sports',
				'transportation',
				'travel',
				'buildings',
				'business',
				'music');
		
		$this->validParams = array();
		$this->validParams['q'] = NULL;
		$this->validParams['lang'] = 'en';
		$this->validParams['id'] = NULL;
		$this->validParams['response_group'] = 'image_details'; //Accepted values: "image_details", "high_resolution" (requires permission) 
		$this->validParams['image_type'] = 'all'; //"all", "photo", "illustration", "vector"
		$this->validParams['orientation'] = 'all'; //"all", "horizontal", "vertical"
		$this->validParams['category'] = NULL; //fashion, nature, backgrounds, science, education, people, feelings, religion, health, places, animals, industry, food, computer, sports, transportation, travel, buildings, business, music
		$this->validParams['min_width'] = 0;
		$this->validParams['min_height'] = 0;
		$this->validParams['editors_choice'] = 'false'; //"true", "false" 
		$this->validParams['safesearch'] = 'false'; //"true", "false"
		$this->validParams['order'] = 'popular'; //"popular", "latest" 
		$this->validParams['page'] = 1;
		$this->validParams['per_page'] = 20; //3 - 200
		$this->validParams['callback'] = NULL;
		$this->validParams['pretty'] = 'false'; //"true", "false"
		
	}
	
	
	
	
	
	
	public function call($params)
	{
		$urlParams = http_build_query($this->validate($params));
		
		curl_setopt_array($this->curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HEADER => 1,
			CURLOPT_VERBOSE => 1,
			CURLOPT_URL => $this->requestUrl.'&'.$urlParams
		));
		
		
		$response = curl_exec($this->curl);
		
		
		list($header, $body) = explode("\r\n\r\n", $response, 2);
		$header = explode("\r\n", $header);
		
		return array($header,$body);
	}
	
	
	public function close(){
		curl_close($this->curl);
	}
	
	
	protected function validate($params)
	{
		
		$acceptedParams = array_keys($this->validParams);
		$cleanParams = array();
		foreach($params as $key=>$value){
			
			if(!in_array($key,$acceptedParams)){
				throw new Exception('Invalid Param!');
			}
			
			if($key == 'lang' && !in_array($params[$key],$this->validLangs)){
				throw new Exception('Invalid Language!');
			}
			
			if($key == 'category' && !in_array($params[$key],$this->validCategories)){
				throw new Exception('Invalid Category!');
			}
			
			if(trim($value) == '' && !is_null($this->validParams[$key])){
				$cleanParams[$key] = $this->validParams[$key];
			}else{
				$cleanParams[$key] = $value;
			}
				
		}
		
		return $cleanParams;
		
	}
	
}