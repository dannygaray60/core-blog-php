<?php 
include 'options.php';include 'general-functions.php';
/**
* author: dannygaray60
* email: dannygaray60@gmail.com
*/

//retornará  la ruta de la carpeta de subidas de imagenes
function getPath(){
	return "uploads/images/";
}

//si la ruta no existe, se creara el directorio y otro adentro llamado thumbnails
function checkDirs($path){
	if (!file_exists('../../'.$path)) {
		mkdir('../../'.$path);
	}
	if (!file_exists('../../'.$path.'thumbnails/')) {
		mkdir('../../'.$path.'thumbnails/');
	}
}

function reArrayFiles($file_post){
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    for ($i=0; $i < $file_count; $i++) { 
        foreach ($file_keys as $key) {
            $file_ary[$i][$key]=$file_post[$key][$i];
        }
    }
    return $file_ary;
}

/* function:  generates thumbnail */
function make_thumb($src,$dest,$desired_height,$ext) {
    /* read the source image */
    if ($ext=='jpg' or $ext=='jpeg') {
        $source_image = imagecreatefromjpeg($src);
    }
    elseif ($ext=='png') {
        $source_image = imagecreatefrompng($src);
    }
    elseif ($ext=='gif') {
        $source_image = imagecreatefromgif($src);
    }
    $width = imagesx($source_image);
    $height = imagesy($source_image);
    /* find the "desired height" of this thumbnail, relative to the desired width  */
    $desired_width = floor($width*($desired_height/$height));
    /* create a new, "virtual" image */
    $virtual_image = imagecreatetruecolor($desired_width,$desired_height);
    /* copy source image at a resized size */
    imagecopyresized($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
    /* create the physical thumbnail image to its destination */
    if ($ext=='jpg' or $ext=='jpeg') {
        imagejpeg($virtual_image,$dest);
    }
    elseif ($ext=='png') {
        imagepng($virtual_image,$dest);
    }
    elseif ($ext=='gif') {
        imagegif($virtual_image,$dest);
    }
    
}

function mtimecmp($a, $b) {
    $mt_a = filemtime($a);
    $mt_b = filemtime($b);
    if ($mt_a == $mt_b)
        return 0;
    //cambiar signo a '>' para recientes primero o lo contrario
    else if ($mt_a > $mt_b)
        return -1;
    else
        return 1;
}

class image_blog{
	
	public function load_images($maxfiles,$img_type_container){
		/* para mostrar $img_type_container:
			3: summernote: imagenes para insertar imagen en post
			5: biblioteca eliminar varios: imagenes para seleccionar varias y poder eliminarlas al mismo tiempo
			4: biblioteca: imagenes que al hacer click muestran detalles
		*/
		$images = glob('../../'.getPath()."*.*");
		$totalFiles = (count(scandir('../../'.getPath()))-3);
		//nos aseguramos que existan archivos en la carpeta
		if ($totalFiles==0) {
			$show_imgs_title = '<div class="alert alert-warning text-center" role="alert">Sin imágenes en la biblioteca.</div>';
			$show_imgs_body='';
			$script = '<script type="text/javascript">$("#load_more_images").addClass("hidden");</script>';
		}
		else{
		    usort($images, "mtimecmp");
		    array_reverse($images);
		    $cont=1;
		    //recorremos todas las imagenes
		    foreach ($images as $image) {
		    	if ($cont<=$maxfiles) {
		    		$img_name = explode('/', $image);
		            if ($img_type_container==3) {
		                @$show_imgs_body .= '
		                    <a href="#">
		                        <img src="'.URL_BASE.getPath().'thumbnails/'.end($img_name).'" onclick="insert_url_image_summernote(\''.URL_BASE.getPath().end($img_name).'\')" class="img-thumbnail imgs-blogs">
		                    </a>
		                ';            
		            }
		            elseif ($img_type_container==5){
		                @$show_imgs_body .= '
		                    <option data-img-class="maxheight" data-img-src="'.URL_BASE.getPath().'thumbnails/'.end($img_name).'" value="'.URL_BASE.getPath().'/thumbnails/'.end($img_name).'"></option>
		                '; 
		            }
		            elseif ($img_type_container==4) {
		                @$show_imgs_body .= '
		                    <a href="#">
		                        <img src="'.URL_BASE.getPath().'thumbnails/'.end($img_name).'" onclick="show_image_info(\''.URL_BASE.getPath().end($img_name).'\')" class="img-thumbnail imgs-blogs">
		                    </a>
		                ';            
		            }
					$cont++;
		    	}   
		    }
		    $show_imgs_title = '<div class="alert alert-info text-center" role="alert">Mostrando '.($cont-1).' de '.$totalFiles.' imágenes</div>';	
		    //si se han cargado todas las imagenes disponibles entonces ocultamos boton de cargar más
		    if ($totalFiles==($cont-1)) {
		    	$script = '<script type="text/javascript">$("#load_more_images").addClass("hidden");</script>';
		    }
		    else {
		    	$script = '<script type="text/javascript">$("#load_more_images").removeClass("hidden");</script>';
		    }
		}//finalizamos el recorrido de imagenes

		//si se necesita mostrar las imagenes con eliminado multiple, mostraremos las imagenes en forma de select/option e inicializamos el plugin imagepicker
		if ($img_type_container==5) {
			$msg = $script.$show_imgs_title.'<script type="text/javascript">$("select").imagepicker();</script>'.'<select onchange="$(\'#del_btn2\').removeClass(\'hidden\');" name="urls[]" multiple="multiple" id="select_imgs" class="image-picker show-html">'.$show_imgs_body.'</select>'; 
		}
		//si es para summernote o la pestaña principal de la biblioteca mostramos esto...
		else{
			$msg = $script.$show_imgs_title.$show_imgs_body;
		}
		echo $msg;//y aqui esta todo el codigo html necesario para mostrar
	}//fin de clase

	public function upload_images($files){
		if (isset($files)) {    
		    $file_ary = reArrayFiles($files);
		    $path = getPath();
		    foreach ($file_ary as $f) {
		        $file_type = $f["type"];
		        $size = $f["size"]; //retorna tamaño de archivo en bytes
		        $width_height = getimagesize($f["tmp_name"]);
		        $width = $width_height[0];
		        $height = $width_height[1];
		        if ($file_type != 'image/jpg' && $file_type != 'image/jpeg' && $file_type != 'image/png' && $file_type != 'image/gif'){
		        	$msg = "Error, el archivo no es una imagen";
		        }
		        else if ($size > 1024*1024){
		        	$msg = "Error, el tamaño máximo permitido es un 1MB";
		        }
		        else if ($width > 1920 || $height > 1200){
		            $msg = "Error, resolución máxima: 1920x1200";
		        }
		        else if($width < 50 || $height < 50){
		            $msg = "Error, la anchura y la altura mínima permitida es 50px";
		        }
		        else{
		            $extension = explode('.', $f["name"]);    
		            $name_file = generate_text(30);//por motivos de seguridad generamos un nombre aleatorio
		            $temp_name = $f["tmp_name"];//aqui es donde se almacena el archivo antes de subirlo al sitio
		            $src = '../../'.$path.$name_file.'.'.end($extension);
		            move_uploaded_file($temp_name, $src);//subimos a sitio web
			        $thumbnail_image = '../../'.$path.'thumbnails/'.$name_file.'.'.end($extension);
			        $thumbs_height = 160;
			        //si no tiene imagen de previsualizacion entonces crearemos una
			        if(!file_exists($thumbnail_image)) {
		                make_thumb($src,$thumbnail_image,$thumbs_height,end($extension));
			        }
		            $msg = true;
		        }
		    }
		}
		else{
		    $msg = false;
		}
		return $msg;
	}

	public function delete_image($urls)	{
		//si urls no viene nulo
		if ($urls!=null) {
			foreach ($urls as $url) {
				$filename = explode('/', $url);
				$path = '../../'.getPath();
				unlink($path.'thumbnails/'.end($filename));
				unlink($path.end($filename));
			}
			return true;
		}
		else{
			return 'Seleccione imagen';
		}
	}

	public function return_data_image($url){
		$filename = explode('/', $url);
		$path = '../../'.getPath().end($filename);
		$file_size = round(filesize($path)/1024,2).' KB';
		$file_upload_time = strftime('%A %d de %B del %Y a las %r',filemtime($path)); //la fecha de subida solo se muestra en sistemas UNIX/Linux
		$width_height = getimagesize($path);
        $width = $width_height[0];
        $height = $width_height[1];
		$msg = '<b>Tamaño:</b> '.$file_size."<br>";
		$msg .= '<b>Resolución:</b> '.$width.' x '.$height.' (px)<br>';
		$msg .= '<b>Subido el:</b> '.$file_upload_time."<br>";
		$msg .= '<b>URL:</b> <a href="'.$url.'" target="_blank">'.$url.'</a>';
		return $msg;
	}

}

?>