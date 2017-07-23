<?php 
require_once '../class-image-blog.php';
checkDirs(getPath());//comprobamos que existan las carpetas que se necesitan, sino las creará
$opt = $_POST['opt']*1;
@$urls = $_POST['urls'];
@$count = $_POST['count'];
$class_img = new image_blog();

if ($opt==0) {
	echo $class_img->upload_images($_FILES["file"]);
}
elseif ($opt==1) {
	echo $class_img->return_data_image($urls);
}
elseif ($opt==2) {
	if (!$urls) {
		$urls==null;
	}
	else{
		if (!is_array($urls)) {
			$urls = array(0 => $urls);
		}
	}
	echo $class_img->delete_image($urls);
}
//3 para summernote, 4 para biblioteca, 5 para biblioteca eliminar varios
elseif ($opt==3 or $opt==4 or $opt==5) {
	echo $class_img->load_images($count,$opt);
}
?>