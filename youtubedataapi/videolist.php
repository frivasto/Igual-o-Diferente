<?php
    // function to parse a video <entry>
    function parseVideoEntry($entry) {      
      $obj= new stdClass;
      
      // get nodes in media: namespace for media information
      $media = $entry->children('http://search.yahoo.com/mrss/');
      $obj->title = $media->group->title;
      $obj->description = $media->group->description;
      
      // get video player URL
      $attrs = $media->group->player->attributes();
      $obj->watchURL = $attrs['url']; 
      
      // get video thumbnail
      $attrs = $media->group->thumbnail[0]->attributes();
      $obj->thumbnailURL = $attrs['url']; 
            
      // get <yt:duration> node for video length
      $yt = $media->children('http://gdata.youtube.com/schemas/2007');
      $attrs = $yt->duration->attributes();
      $obj->length = $attrs['seconds']; 
      
      // get <yt:stats> node for viewer statistics
      $yt = $entry->children('http://gdata.youtube.com/schemas/2007');
      $attrs = $yt->statistics->attributes();
      $obj->viewCount = $attrs['viewCount']; 
      
      // get <gd:rating> node for video ratings
      $gd = $entry->children('http://schemas.google.com/g/2005'); 
      if ($gd->rating) { 
        $attrs = $gd->rating->attributes();
        $obj->rating = $attrs['average']; 
      } else {
        $obj->rating = 0;         
      }
	  
      // return object to caller  
      return $obj;      
    }
    function getVideoDescriptions($vid){
      // set video data feed URL
      $feedURL = 'http://gdata.youtube.com/feeds/api/videos/' . $vid;
      // read feed into SimpleXML object
      $entry = simplexml_load_file($feedURL);
      // parse video entry
      $video = parseVideoEntry($entry);
      return $video;
	}

class VideoItem{
  public $url;
  public $categoria;
  public $video_id;
  public $duracion;

  // Require $url and $categoria when INSTANTIATING
  public function __construct($url, $categoria)    
  {
    $this->url = $url; 
    $this->categoria = $categoria;
  }

  public function getUrl() {
        return $this->url;
  }
  
  public function getCategoria() {
        return $this->categoria;
  }
  
  public function getDuracion() {
        return $this->duracion;
  }
  
  //Setear el id de video
  public function setVideoId($video_id) {
        $this->video_id = $video_id;
        return $this;
  }
  
  //Setear el Duracion
  public function setDuracion($duracion) {
        $this->duracion = $duracion;
        return $this;
  }
  
  public function __toString()
  {
    return "VideoItem [url='$this->url', categoria='$this->categoria']";
  }
}
 
$coleccion_videos=array();
$coleccion_videos[]=new VideoItem('3QMXJg04lsY', 'Educacion');
$coleccion_videos[]=new VideoItem('ANglxq0Qr6g', 'Educacion');
$coleccion_videos[]=new VideoItem('4S7GBaONq1g', 'Educacion');
$coleccion_videos[]=new VideoItem('7y57BBwhEEM', 'Educacion');
$coleccion_videos[]=new VideoItem('dCcCeig3mgE', 'Educacion');
$coleccion_videos[]=new VideoItem('v50qXXKV_c4', 'Educacion');
$coleccion_videos[]=new VideoItem('CiWTUVFKWkI', 'Educacion');
$coleccion_videos[]=new VideoItem('4dCO9VBlExE', 'Educacion');
$coleccion_videos[]=new VideoItem('9L8Ve7cbT4o', 'Educacion');
$coleccion_videos[]=new VideoItem('lgpy88oxJDs', 'Educacion');
$coleccion_videos[]=new VideoItem('kfMyfod5eW8', 'Educacion');
$coleccion_videos[]=new VideoItem('0ug96YW3HC0', 'Educacion');
$coleccion_videos[]=new VideoItem('DTQx3U31tmg', 'Educacion');
$coleccion_videos[]=new VideoItem('G_9SRysJM2c', 'Educacion');
$coleccion_videos[]=new VideoItem('Ud1WRI5Cy6Y', 'Educacion');
$coleccion_videos[]=new VideoItem('VwTfXGLFqOU', 'Educacion');
$coleccion_videos[]=new VideoItem('inRW-G8JJ2U', 'Educacion');
$coleccion_videos[]=new VideoItem('sQEIk0tBpVo', 'Educacion');
$coleccion_videos[]=new VideoItem('OAtSI0LkkGM', 'Educacion');
$coleccion_videos[]=new VideoItem('mY_3Nub1qhA', 'Educacion');
$coleccion_videos[]=new VideoItem('_dSl2IQ1c38', 'Educacion');
$coleccion_videos[]=new VideoItem('icEhwasTbZA', 'Educacion');
$coleccion_videos[]=new VideoItem('GH92JwDCnTg', 'Educacion');
$coleccion_videos[]=new VideoItem('bBgZ8WCZsBI', 'Educacion');
$coleccion_videos[]=new VideoItem('SXTNPpP3pQc', 'Educacion');
$coleccion_videos[]=new VideoItem('XHid-q5hQ68', 'Educacion');
$coleccion_videos[]=new VideoItem('JsQxXrIZpLQ', 'Educacion');
$coleccion_videos[]=new VideoItem('Yhq_NhsuyJM', 'Educacion');
$coleccion_videos[]=new VideoItem('EG0TaOgupaE', 'Educacion');
$coleccion_videos[]=new VideoItem('Kg1wow6pgps', 'Educacion');

$coleccion_videos[]=new VideoItem('SEBLt6Kd9EY', 'Blooper');
$coleccion_videos[]=new VideoItem('YnhP3kTdLeo', 'Blooper');
$coleccion_videos[]=new VideoItem('sd0g59X_IRI', 'Blooper');
$coleccion_videos[]=new VideoItem('ctJJrBw7e-c', 'Blooper');
$coleccion_videos[]=new VideoItem('wf_IIbT8HGk', 'Blooper');
$coleccion_videos[]=new VideoItem('Hb92wQpPG-s', 'Blooper');
$coleccion_videos[]=new VideoItem('89kKUeT2sxk', 'Blooper');
$coleccion_videos[]=new VideoItem('u23gEi47_j4', 'Blooper');
$coleccion_videos[]=new VideoItem('i479N2ei8Us', 'Blooper');
$coleccion_videos[]=new VideoItem('eTu6Fq8hC0U', 'Blooper');
$coleccion_videos[]=new VideoItem('lyzxrF8x5Ww', 'Blooper');
$coleccion_videos[]=new VideoItem('9d_i--693WE', 'Blooper');
$coleccion_videos[]=new VideoItem('cTbu4ZnzNko', 'Blooper');
$coleccion_videos[]=new VideoItem('81NeQJWGYJY', 'Blooper');
$coleccion_videos[]=new VideoItem('oDdI4jsnvyc', 'Blooper');
$coleccion_videos[]=new VideoItem('cXXm696UbKY', 'Blooper');
$coleccion_videos[]=new VideoItem('ZkWkirlQVxg', 'Blooper');
$coleccion_videos[]=new VideoItem('lL5Y0ng0uRU', 'Blooper');
$coleccion_videos[]=new VideoItem('CjqvLAs9t1Q', 'Blooper');
$coleccion_videos[]=new VideoItem('sIYzVORJFLI', 'Blooper');
$coleccion_videos[]=new VideoItem('kJY5BRCNAs4', 'Blooper');
$coleccion_videos[]=new VideoItem('JhUpejnZ-9Q', 'Blooper');
$coleccion_videos[]=new VideoItem('WahvT8hrnsQ', 'Blooper');
$coleccion_videos[]=new VideoItem('GJ6OG5fZ7M0', 'Blooper');
$coleccion_videos[]=new VideoItem('Q2xzt5_ITtY', 'Blooper');
$coleccion_videos[]=new VideoItem('7PokysnUOo4', 'Blooper');
$coleccion_videos[]=new VideoItem('HsHfV-Ns8Dg', 'Blooper');
$coleccion_videos[]=new VideoItem('HMPFf66oDFk', 'Blooper');
$coleccion_videos[]=new VideoItem('g8gPicK5Mug', 'Blooper');
$coleccion_videos[]=new VideoItem('qJzREZXiXIw', 'Blooper');

$coleccion_videos[]=new VideoItem('wbqFMgdSeGs', 'Popfail');
$coleccion_videos[]=new VideoItem('lwnoVnaFwxM', 'Popfail');
$coleccion_videos[]=new VideoItem('F-sSooqYisM', 'Popfail');
$coleccion_videos[]=new VideoItem('DNJ6a9Lf_K8', 'Popfail');
$coleccion_videos[]=new VideoItem('T-NviMJMQGQ', 'Popfail');
$coleccion_videos[]=new VideoItem('g-RMityCgyo', 'Popfail');
$coleccion_videos[]=new VideoItem('E_emNxk1CKQ', 'Popfail');
$coleccion_videos[]=new VideoItem('-vgoa0IAOls', 'Popfail');
$coleccion_videos[]=new VideoItem('wpqzukQJ1bU', 'Popfail');
$coleccion_videos[]=new VideoItem('20lgHlQcLs0', 'Popfail');
$coleccion_videos[]=new VideoItem('QBt9E3dZlNw', 'Popfail');
$coleccion_videos[]=new VideoItem('y4xLM9wIM8A', 'Popfail');
$coleccion_videos[]=new VideoItem('Q31YDyxbS5k', 'Popfail');
$coleccion_videos[]=new VideoItem('Ct-FfReSonw', 'Popfail');
$coleccion_videos[]=new VideoItem('5HyMJpG4hvg', 'Popfail');
$coleccion_videos[]=new VideoItem('QT-rQZ6LmKE', 'Popfail');
$coleccion_videos[]=new VideoItem('jOkH55V6HvY', 'Popfail');
$coleccion_videos[]=new VideoItem('Tby-LnyiZUE', 'Popfail');
$coleccion_videos[]=new VideoItem('3QMXJg04lsY', 'Popfail');
$coleccion_videos[]=new VideoItem('kfVsfOSbJY0', 'Popfail');
$coleccion_videos[]=new VideoItem('cSfgUQKbl0w', 'Popfail');
$coleccion_videos[]=new VideoItem('thhOrlhst0E', 'Popfail');
$coleccion_videos[]=new VideoItem('8-QToIzw7Fk', 'Popfail');
$coleccion_videos[]=new VideoItem('0AbQpzn6t4c', 'Popfail');
$coleccion_videos[]=new VideoItem('OtZyj_UHAcw', 'Popfail');
$coleccion_videos[]=new VideoItem('9Sw7z2PWZqU', 'Popfail');
$coleccion_videos[]=new VideoItem('8_lTdVNMbIM', 'Popfail');
$coleccion_videos[]=new VideoItem('OlR_CQ3NPvI', 'Popfail');
$coleccion_videos[]=new VideoItem('SSA3ydf06yg', 'Popfail');
$coleccion_videos[]=new VideoItem('or2GH3CHXqY', 'Popfail');

for ($i = 0; $i <90; $i++):
  $videodes=getVideoDescriptions($coleccion_videos[$i]->getUrl()); 
  $duracion=$videodes->length."";
  $coleccion_videos[$i]->setDuracion($duracion);
  //if(($duracion/10+$duracion/2+30)<$duracion):
  //echo "<br />aaaqui";
  //endif;
endfor;

//print_r($coleccion_videos);

for ($i = 0; $i <90; $i++):
  echo "<br />\$coleccion_videos[]=new VideoItem('".($coleccion_videos[$i]->getUrl())."', '".($coleccion_videos[$i]->getCategoria())."','".($coleccion_videos[$i]->getDuracion())."');";
endfor;
?>












