<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../libs/gd/gd.class.php');
require_once('../config.php');

$result_folder = '../results/';

$font = 'calibri/calibrib.ttf';

$boxes[] = ['text' => 'What is Lorem Ipsum?'] ;
$boxes[] = ['text' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.'] ;
$boxes[] = ['text' => 'Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s'] ;

//echo DIR_ROOT . '/fonts/' . $font; die;
foreach($boxes as $bi => $bv) {
	$text = $bv['text'];
	//echo $text; die;

	$im = new GD_AI_WFRND();
	$im->create_box($width=800, $height=150, $bg_color='#893015')
			->add_border($thickness=5,$border_color='#ffffff')
			->add_text($text,$x=0, $y=0, $bound_x=0, $bound_y=0, $font_size=35, $font_color='#FFFFFF', DIR_ROOT . '/fonts/' . $font, $wordwrap=1, $centeralign=1, $middlealign=1, $extrabold=0)
			->download($result_folder . 'box_' . ($bi + 1). '.png')
			->prepare()
			->display()
		;

}
