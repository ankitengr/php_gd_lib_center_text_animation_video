<?php 

class GD_AI_WFRND {
	
	private $output_content;
	public $image_width;
	public $image_height;
	private $border_thickness = 0;
	
	public function load($path){
		$this->im = imagecreatefromstring(file_get_contents($path));
		$this->image_width = imagesx($this->im);
		$this->image_height = imagesy($this->im);
		
		return $this;
		//echo $this->image_width;
	}
	
	public function overlay($src_image, $dst_x=0, $dst_y=0, $src_x=0, $src_y=0, $dst_width=0, $dst_height=0, $src_width=0,$src_height=0) {
		$img = imagecreatefrompng($src_image);
		imagecopyresized($this->im, $img, $dst_x, $dst_y, $src_x, $src_y, $dst_width, $dst_height, $src_width, $src_height);
	}
	
	public function draw_box($x=0, $y=0, $width=100, $height=100, $bg_color=-1) {
		$width = $width + $x;
		$height = $height + $y;
		
		if($bg_color == -1) {
			// Transparent Background
			//echo 'hi'; die;
			imagealphablending($this->im, false);
			$transparency = imagecolorallocatealpha($this->im, 0, 0, 0, 127);
			imagefill($this->im, $x, $y, $transparency);
			imagesavealpha($this->im, true);
		}
		else {
			$theRGB = $this->hexcolor2rgb($bg_color);
			$background = imagecolorallocate($this->im, $theRGB[0], $theRGB[1], $theRGB[2]);
			imagefilledrectangle($this->im, $x, $y, $width, $height, $background);
		}
		
		return $this;
	}
	
	public function create_box($width=100, $height=100, $bg_color=-1){
		$this->im = imagecreatetruecolor($width, $height);
		$this->image_width = $width;
		$this->image_height = $height;

		if($bg_color == -1) {
			// Transparent Background
			imagealphablending($this->im, false);
			$transparency = imagecolorallocatealpha($this->im, 0, 0, 0, 127);
			imagefill($this->im, 0, 0, $transparency);
			imagesavealpha($this->im, true);
		}

		// Drawing over
		if($bg_color != -1) {
			$theRGB = $this->hexcolor2rgb($bg_color);
			$background = imagecolorallocate($this->im, $theRGB[0], $theRGB[1], $theRGB[2]);
			imagefilledrectangle($this->im, 0, 0, $width, $height, $background);
		}
		
		return $this;	
	}
	
	public function add_border($thickness=1, $border_color='FFFFFF', $x1 = 0, $y1 = 0, $x2=0, $y2=0){
		if($thickness==0){
			return $this;
		}
		//Draw Border 
		$x2 = empty($x2) ? ImageSX($this->im) - 1 : $x2; 
		$y2 = empty($y2) ? ImageSY($this->im) - 1 : $y2;

		$theRGB = $this->hexcolor2rgb($border_color);
		$border_color = imagecolorallocate($this->im, $theRGB[0], $theRGB[1], $theRGB[2]);

		for($i = 0; $i < $thickness; $i++) { 
			ImageRectangle($this->im, $x1++, $y1++, $x2--, $y2--, $border_color); 
		}
		$this->border_thickness = $thickness*2;
		return $this;
	}
	
	public function add_text($text="Hello World!",$x=0, $y=0, $bound_w=0, $bound_h=0,  $font_size=10, $color='#000',$font='arial.ttf',$wordwrap=false, $centeralign=FALSE, $middlealign= FALSE, $bold=FALSE) {
		
		if(empty($text)) {
			return $this;
		}
		
		if(empty($bound_w)) {
			$bound_w = $this->image_width;
			$bound_h = $this->image_height;
		}
		/*else {
			$bound_w = $x + $bound_w ;
			$bound_h = $y + $bound_h ;
		}*/
		//echo $bound_w; die;
		$theRGB = $this->hexcolor2rgb($color);
		$color = imagecolorallocate($this->im, $theRGB[0], $theRGB[1], $theRGB[2]);
		
		$final_text = '';
		
		if($wordwrap){
			$final_text_arr = $this->wrap_text($font_size, 0, $font, $text, $xpos=0, $bound_w, $bound_h);
		}
		else {
			$final_text_arr[] = $text;
		}
		
		
		$x = $x + $this->border_thickness; 
		$y = $y + $this->border_thickness; 
		$line_height = 15;
		
		if($middlealign) {
			$last_text_y = $y + count($final_text_arr)*$font_size + ($line_height * (count($final_text_arr)-1)) ;
			$y = ($this->image_height - $last_text_y) / 2 ;
		}
		
		if(isset($final_text_arr) && is_array($final_text_arr) && !empty($final_text_arr)) {
			foreach($final_text_arr as $final_text) {
				$y += $font_size;
				
				if($centeralign) {
					$box = @imagettfbbox($font_size, $angle, $font, trim($final_text));
					$width = abs($box[4] - $box[0]);
					
					if($width < $bound_w) {
						//$x = $bound_w / 2 + $x;
					}
					
					$x = ($this->image_width - $width) / 2;
					//echo $width; die;
					
					//$height = abs($box[3] - $box[5]);
					//echo $bound_w - $width; die;
					//echo $x . 'x-' . $bound_w . 'x2--';
					
					//echo $x; die;
				}
				
				imagettftext($this->im, $font_size, $angle=0, $x, $y, $color, $font, $final_text);
				
				if($bold) {
					imagettftext($this->im, $font_size, 0, $x, $y+1, $color, $font, $final_text);
					imagettftext($this->im, $font_size, 0, $x+1, $y, $color, $font, $final_text);
					$y += 1;
				}
				
				$y += $line_height;
			}
			
		}
		
		return $this;
		
	}
	
	public function download($file_path) {
		$this->prepare();
		
		if(preg_match('/%0(\d)d/', $file_path, $matches)){
			for($counter=1; $counter<1000; $counter++) {
				if(!file_exists(sprintf($file_path, $counter))) {
					$file_path = sprintf($file_path, $counter);
				}
			}
		}
		//echo $file_path; die;
		//print_r($matches); die;
		
		file_put_contents($file_path, $this->output_content);
		
		return $this;
	}
		
	public function prepare() {

        // Begin capturing the byte stream
        ob_start();

        // generate the byte stream
        //imagepng($this->im, NULL, 100);
		imagepng($this->im);
		//imagejpeg($this->im);

        // and finally retrieve the byte stream
        $this->output_content = ob_get_clean();

       //echo "<img src='data:image/png;base64," . base64_encode( $rawImageBytes ) . "' />";
	   
	   return $this;

    }
	
	public function get_content(){
		return $this->output_content;
	}
	
	public function display(){
		header('Content-type: image/png');
		echo $this->output_content;
	}
	
	function __destruct(){
		imagedestroy($this->im);
	}
	
	
	
	
	private function hexcolor2rgb($color){
	   if ($color[0] == '#')
		   $color = substr($color, 1);

	   if (strlen($color) == 6)
		   list($r, $g, $b) = array($color[0].$color[1],
								 $color[2].$color[3],
								 $color[4].$color[5]);
	   elseif (strlen($color) == 3)
		   list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
	   else
		   return false;

	   $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

	   return array($r, $g, $b);
	}
		
	function wrap_text($size, $angle, $font, $instr, $x_pos=0, $bound_width=0, $bound_height=0) {
		$box = @imagettfbbox($size, $angle, $font, $instr);
		$width = abs($box[4] - $box[0]);
		if(empty($bound_width)) {
			$bound_width = $this->image_width ;
		}
		//echo $bound_width; die;
		$overlap = (($x_pos + $width) - ($bound_width));
		//echo $overlap; die;
		if($overlap > 0) {
			$words = explode(" ", $instr);
			$word_arr = [];
			$count_word_wid = 0;
			
			$bo = imagettfbbox($size, 0, $font, " ");
			$space_wid = abs($bo[4] - $bo[0]);
			//$response = [];
			
			$final_string_arr = '';
			
			foreach($words as $wk=>$word){
				$bo = imagettfbbox($size, 0, $font, $word);
				$word_wid = abs($bo[4] - $bo[0]);
				$count_word_wid += $word_wid;
				if($wk > 0 && $wk < (count($words) - 1) ) {
					$count_word_wid += $space_wid ;
				}
				
				
				if($count_word_wid > ($bound_width - ((($this->border_thickness + 10)) + $x_pos))) {
					$count_word_wid =$word_wid;
					$final_string_arr .= PHP_EOL . $word . " ";
				}
				else {
					$final_string_arr .= $word . " ";
				}
				
				$word_arr[] = ['word'=>$word, 'word_wid'=> $word_wid, 'count_word_wid'=>$count_word_wid] ;
			}
			
			//print_r($word_arr); die;
			
			$final_string_arr = trim($final_string_arr);
			
			//echo $final_string_arr; die;
			
			return explode(PHP_EOL,$final_string_arr);
		}
	
		return [$instr];
	}
	
	
}