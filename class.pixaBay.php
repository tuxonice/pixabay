<?php

class pixaBay {

	protected $apiCallInstance = NULL;
	protected $databaseInstance = NULL;
	protected $mediaFolder = NULL;
	protected $isCLIMode = false;
	const _TIME_BETWEEN_REQ = 2;
	
	protected $categories = array(
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


	public function __construct($dbConfig, $apiKey){

		$this->databaseInstance = Gzz_Database::getInstance($dbConfig);
		$this->apiCallInstance = new apiCall($apiKey);
		$this->mediaFolder = './media';
		
		$this->isCLIMode = (php_sapi_name() === 'cli');
		
	}


	public function getImagesByCategory(){
		
		$per_page = 100;
		foreach($this->categories as $category){
			for($page=1;$page<=5;$page++){
				$this->processCategory($category,$per_page, $page);
				$this->log('Category '.$category.' - page '.$page);
			}
		}
		
	}
	

	protected function processCategory($category, $per_page = 100, $page){
		
		if(!file_exists($this->mediaFolder._DS.$category)){
			mkdir($this->mediaFolder._DS.$category);
		}
		

		$params = array('category'=>$category,'per_page'=>$per_page,'page'=>$page);
		list($header, $body) = $this->apiCallInstance->call($params);

		$bodyObj = json_decode($body);
			
		$this->processHits($bodyObj, $category);
				
		
	}


	protected function processHits($bodyObj, $category){

		foreach($bodyObj->hits as $hit){

			if($this->imageExists($hit->id)){
				$this->log('Image ID '.$hit->id.' already exists');
				continue;
			}
			
			$normalURL = str_replace('_640.jpg','_960.jpg', $hit->webformatURL);
			$newFileName = $hit->id.'-'.uniqid().'.jpg';
			
			$this->log($hit->id.' - '.$hit->tags.' - '.$this->mediaFolder._DS.$category._DS.$newFileName);

			list($normalWidth, $normalHeight) = $this->downloadFile($normalURL, $this->mediaFolder._DS.$category._DS.$newFileName);

			$ratio = round($normalWidth/$normalHeight,4);
			
			$sql = "INSERT INTO images (
				pixabay_id,
				page_url,
				type,
				tags,
				category,
				preview_url,
				preview_width,
				preview_height,
				normal_url,
 				normal_width,
				normal_height,
				ratio,
				original_image_width,
				original_image_height
				)
				VALUES
				(
				:pixabay_id,
				:page_url,
				:type,
				:tags,
				:category,
				:preview_url,
				:preview_width,
				:preview_height,
				:normal_url,
 				:normal_width,
				:normal_height,
				:ratio,
				:original_image_width,
				:original_image_height
				)";
			$stmt = $this->databaseInstance->dbh->prepare($sql);
			$stmt->bindValue(':pixabay_id',$hit->id,PDO::PARAM_STR);
			$stmt->bindValue(':page_url',$hit->pageURL,PDO::PARAM_STR);
			$stmt->bindValue(':type',$hit->type,PDO::PARAM_STR);
			$stmt->bindValue(':tags',$hit->tags,PDO::PARAM_STR);
			$stmt->bindValue(':category',$category,PDO::PARAM_STR);
			$stmt->bindValue(':preview_url',$hit->previewURL,PDO::PARAM_STR);
			$stmt->bindValue(':preview_width',$hit->previewWidth,PDO::PARAM_INT);
			$stmt->bindValue(':preview_height',$hit->previewHeight,PDO::PARAM_INT);
			$stmt->bindValue(':normal_url',$newFileName,PDO::PARAM_STR);
			$stmt->bindValue(':normal_width',$normalWidth,PDO::PARAM_INT);
			$stmt->bindValue(':normal_height',$normalHeight,PDO::PARAM_INT);
			$stmt->bindValue(':ratio',$ratio,PDO::PARAM_STR);
			$stmt->bindValue(':original_image_width',$hit->imageWidth,PDO::PARAM_INT);
			$stmt->bindValue(':original_image_height',$hit->imageHeight,PDO::PARAM_INT);
			$stmt->execute();
			
			
			sleep(self::_TIME_BETWEEN_REQ);
		}
	}
	
	
	protected function imageExists($pixabayId){
		
		$sql = "SELECT COUNT(id) FROM images WHERE pixabay_id = :pixabay_id";
		$stmt = $this->databaseInstance->dbh->prepare($sql);
		$stmt->bindValue(':pixabay_id',$pixabayId,PDO::PARAM_STR);
		$stmt->execute();
		$ncount = $stmt->fetchColumn();
		
		return $count ? true:false;
		
	}
	
	
	protected function getIndividual($pixabayId, $line){
		
		if($this->imageExists($pixabayId)){
			return;
		}
			
		$params = array('id'=>$pixabayId);
		list($header, $body) = $this->apiCallInstance->call($params);
			
		$bodyObj = json_decode($body);
			
		foreach($bodyObj->hits as $hit){
			
			$normalURL = str_replace('_640.jpg','_960.jpg', $hit->webformatURL);
			$newFileName = $hit->id.'-'.uniqid().'.jpg';

			$folder = date('YmdH');
			if(!file_exists($this->mediaFolder._DS.$folder)){
				mkdir($this->mediaFolder._DS.$folder);
			}
				
			$this->log($hit->id.' - '.$hit->tags.' - '.$folder.'/'.$newFileName);
			
			list($normalWidth, $normalHeight) = $this->downloadFile($normalURL, $this->mediaFolder._DS.$folder._DS.$newFileName);
			
			$ratio = round($normalWidth/$normalHeight,4);
			
				$sql = "INSERT INTO images (
				pixabay_id,
				page_url,
				type,
				tags,
				preview_url,
				preview_width,
				preview_height,
				normal_url,
 				normal_width,
				normal_height,
				ratio,
				original_image_width,
				original_image_height,
				csv_id
				)
				VALUES
				(
				:pixabay_id,
				:page_url,
				:type,
				:tags,
				:preview_url,
				:preview_width,
				:preview_height,
				:normal_url,
 				:normal_width,
				:normal_height,
				:ratio,
				:original_image_width,
				:original_image_height,
				:csv_id
				)";
				$stmt = $this->databaseInstance->dbh->prepare($sql);
				$stmt->bindValue(':pixabay_id',$hit->id,PDO::PARAM_STR);
				$stmt->bindValue(':page_url',$hit->pageURL,PDO::PARAM_STR);
				$stmt->bindValue(':type',$hit->type,PDO::PARAM_STR);
				$stmt->bindValue(':tags',$hit->tags,PDO::PARAM_STR);
				$stmt->bindValue(':preview_url',$hit->previewURL,PDO::PARAM_STR);
				$stmt->bindValue(':preview_width',$hit->previewWidth,PDO::PARAM_INT);
				$stmt->bindValue(':preview_height',$hit->previewHeight,PDO::PARAM_INT);
				$stmt->bindValue(':normal_url',$newFileName,PDO::PARAM_STR);
				$stmt->bindValue(':normal_width',$normalWidth,PDO::PARAM_INT);
				$stmt->bindValue(':normal_height',$normalHeight,PDO::PARAM_INT);
				$stmt->bindValue(':ratio',$ratio,PDO::PARAM_STR);
				$stmt->bindValue(':original_image_width',$hit->imageWidth,PDO::PARAM_INT);
				$stmt->bindValue(':original_image_height',$hit->imageHeight,PDO::PARAM_INT);
				$stmt->bindValue(':csv_id',$line,PDO::PARAM_INT);
				$stmt->execute();
				
				$imageId = $this->databaseInstance->dbh->lastInsertId();
				$this->processTags($hit->tags, $imageId);
					
				sleep(self::_TIME_BETWEEN_REQ);
			}
		
		
	}
	
	
	protected function processTags($tags, $imageId){
		
		$tagsArray = explode(',',trim($tags));
		
		foreach($tagsArray as $tag){

			$tag = trim($tag);
		
			$stmt = $this->databaseInstance->dbh->prepare("SELECT id FROM tags WHERE tag = :tag");
			$stmt->bindValue(':tag', $tag, PDO::PARAM_STR);
			$stmt->execute();
			$tagId = $stmt->fetchColumn();
			if($tagId === false){
				//Insert
				$stmt = $this->databaseInstance->dbh->prepare("INSERT INTO tags (tag) VALUES (:tag)");
				$stmt->bindValue(':tag', $tag, PDO::PARAM_STR);
				$stmt->execute();
				$tagId = $this->databaseInstance->dbh->lastInsertId();
			}

			$stmt = $this->databaseInstance->dbh->prepare("INSERT INTO imgtags (image_id, tag_id) VALUES (:image_id, :tag_id)");
			$stmt->bindValue(':tag_id', $tagId, PDO::PARAM_INT);
			$stmt->bindValue(':image_id', $imageId, PDO::PARAM_INT);
			$stmt->execute();
			
		}
		
	}

	
	public function processCSVFile($filename){
		
		
		//GET LAST CSV LINE
		$sql = "SELECT MAX(csv_id) FROM images";
		$stmt = $this->databaseInstance->dbh->prepare($sql);
		$stmt->execute();
		$lastCsvId = $stmt->fetchColumn();
		
		if(is_null($lastCsvId)){
			$lastCsvId = 0;
		}
		
		$this->log('Last line: '.$lastCsvId);
		
		$row = 0;
		if (($handle = fopen($filename, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
				
				$line = intval(trim($data[0]));
				$id = trim($data[1],'/');
				$id = explode('-', $id);
				$id = intval($id[count($id)-1]);
				
				if($line <= $lastCsvId) continue;
		
				$row++;
				
				$this->getIndividual($id, $line);
					
			}
			fclose ($handle);
		}
		
		
		
	}
	
	
		
	protected function log($msg){
		
		if($this->isCLIMode)
			echo(date('Y-m-d H:i:s').' '.$msg.chr(10));
		else
			echo(date('Y-m-d H:i:s').' '.$msg.'<br/>');
	}
	

	protected function downloadFile($url, $destFile){

		$fp = fopen($destFile, "wb");
		if(!$fp)
			return false;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec ($ch);
		$error = curl_error($ch);
		curl_close ($ch);
		fclose($fp);

		return getimagesize($destFile);

	}

}