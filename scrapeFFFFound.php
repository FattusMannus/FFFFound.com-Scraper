<?
 
set_time_limit(0);
mkdir("images");
 
 
for ($i=0;$i<3000;$i+=25){
 
$contents = file_get_contents("http://www.ffffound.com?offset=$i");
 
//find all images and not thumbnails
preg_match_all("/http:\/\/img\.(.*?)\" /smi",$contents,$match);
 
    foreach ($match[0] as $img){
 
	$img = substr($img,0,-2);
 
	$size = getimagesize($img);
 
	//if image isnt too square
	if ($size[0]-($size[0]*0.2)>$size[1]){
		$x++;
 
		//resize to a ration where the image will fit in the frame
		if ($size[0]/$size[1]>1.33){
			//resize to width
			$ratio = 720/$size[0];			
		}else{
			//resize to height
			$ratio = 480/$size[1];	
		}
 
 
		//resizing code
		$thumb = imagecreatetruecolor($size[0]*$ratio, $size[1]*$ratio);
		if (strcasecmp(substr($img,strrpos($img,".")),".jpg")==0){
 
			$source = imagecreatefromjpeg($img);
		}
		if (strcasecmp(substr($img,strrpos($img,".")),".gif")==0){
			if (is_ani($img)){
				continue;
			}
			$source = imagecreatefromgif($img);
		}
		if (strcasecmp(substr($img,strrpos($img,".")),".png")==0){
 
			$source = imagecreatefrompng($img);
		}
		imagecopyresized($thumb, $source, 0, 0, 0, 0, $size[0]*$ratio, $size[1]*$ratio, $size[0], $size[1]);
 
		// Output
		imagejpeg($thumb,"images/$x.jpg");
 
 
	}
 
 
    }
 
}
 
 
 
function is_ani($filename) {
    if(!($fh = @fopen($filename, 'rb')))
        return false;
    $count = 0;
    //an animated gif contains multiple "frames", with each frame having a 
    //header made up of:
    // * a static 4-byte sequence (\x00\x21\xF9\x04)
    // * 4 variable bytes
    // * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)
 
    // We read through the file til we reach the end of the file, or we've found 
    // at least 2 frame headers
    while(!feof($fh) && $count < 2) {
        $chunk = fread($fh, 1024 * 100); //read 100kb at a time
        $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
   }
 
    fclose($fh);
    return $count > 1;
}
 
 
?>