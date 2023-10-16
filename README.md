# php_gd_lib_center_text_animation_video
When the requirement to create multiple image for animation video or centralize text on image. This is under GPU License so feel free to override to add functionlities. Request on ankitengr@gmail.com for any query or contribute as developer.


Need to include following Library and line in all below examples.
```
	require_once('../config.php');
	require_once(DIR_ROOT . '/libs/gd/gd.class.php');

	$result_folder = DIR_ROOT . '/results/';

	$font = 'calibri/calibrib.ttf';

	$im = new GD_AI_WFRND();
```

Notes: 
1. change font as per your requirement. but font should be available in font folder or add new font in font folder first.
2. ../config.php .. may be required to change as per your wording directory

-----------------------------------------------------------------------------------------------------------------
Create a Transparent Box with black border
```
	$im->create_box($width=800, $height=150, $bg_color=-1)
		->add_border($thickness=1,$border_color='#000')
		->download($result_folder . 'transparent.png')
		->prepare()
		->display();
```	
Notes: 
1. To fill color in box pass any hex color code to $bg_color like $bg_color=#FFFFFF
2. remove download line if not required to save on system result folder.

-----------------------------------------------------------------------------------------------------------------
Create a Box with light brown fill color and add a white border with thickness=5.  Alos add a text in middle.	
```
	$im->create_box($width=800, $height=150, $bg_color='#893015')
		->add_border($thickness=5,$border_color='#FFFFFF')
		->add_text($text='Lorem Ipsum is simply dummy text of the printing and typesetting industry.',$x=0, $y=0, $bound_x=0, $bound_y=0, $font_size=35, $font_color='#FFFFFF', DIR_ROOT . '/fonts/' . $font, $wordwrap=1, $centeralign=1, $middlealign=1, $extrabold=0)
		->download($result_folder . 'box.png')
		->prepare()
		->display();
```		
-----------------------------------------------------------------------------------------------------------------	

Load other image as background and overlay other image on required position
```
	$im->load(DIR_ROOT . '/sample_bg.png');
	$center_pos_x = $im->image_width/2;
	$center_pos_y = $im->image_height/2;

	$overlay_image = DIR_ROOT . '/sample_overlay.png';

	$im->overlay($overlay_image, $dst_x=($center_pos_x-200), $dst_y=($center_pos_y-50), $src_x=0, $src_y=0, $dst_width=400, $dst_height=100, $src_width=800,$src_height=150);

	$im->prepare();
	$im->display();
```
-----------------------------------------------------------------------------------------------------------------
For any help feel free to email on ankitengr@gmail.com