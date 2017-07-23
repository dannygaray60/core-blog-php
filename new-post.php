<?php 
include 'php/options.php'; include 'php/general-functions.php';
$date = current_date_to_user($arrayD,$arrayM);
$time = date('h:i:s A'); 
?>
<!DOCTYPE html>
<html>
<head>
	<title>Blog - Crear</title>
	<?php include 'includes/head.php';?>
  <!-- summernote -->
  <link href="plugins/summernote/summernote.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<!-- dentro de este div va el contenido de la pag -->
<div class="container content">

  <div class="well" id="div_new_post">
  	  <div class="form-group">
  	    <label for="post_name">Título</label>
  	    <input type="text" class="form-control" placeholder="Título..." id="title">
  	  </div>
  	  <div class="form-group">
  	    <label for="userblogpost">Contenido</label>
  	    <div id="userblogpost"></div>
  	  </div>
  	  <div class="form-group">
  	   <button onclick="send_post()" class="btn btn-primary btn-lg">Publicar</button>
  	  </div>
  </div>

  <!-- Seccion de previsualizacion de entrada solo para fines didacticos -->
  <div class="row hidden container" id="div_preview">
    <div class="alert alert-info" role="alert">Previsualización de la entrada</div>
    <hr>
    <h1 id="h1title"></h1>
    <p class="lead">por <a href="#"><?php echo NAME_USER; ?></a></p>
    <hr>
    <p><span class="glyphicon glyphicon-time"></span> <?php echo $date.' a las '.$time; ?></p>
    <hr>
    <div id="form-response"></div>
    <br>
    <div class="well">
      <h4>Comentar:</h4>
      <form role="form">
        <div class="form-group">
          <textarea class="form-control" rows="3"></textarea>
        </div>
        <button type="" class="btn btn-primary">Enviar</button>
      </form>
    </div>
    <a href="new-post.php" class="btn btn-success btn-lg btn-block">Nueva entrada</a>
    <hr>
  </div>


<!-- Modal - Este es necesario para summernote -->
<div class="modal fade" id="modalSelectImage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="myModalLabel">Seleccione una imagen</h4>
      </div>
      <div class="modal-body">
        <div id="div_upload" class="">
          <br>
          <form id="form_upload_image" action="php/forms-submits/image-blog.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="opt" value="0">
            <input name="file[]" type="file" multiple required>
            <br>
            <button type="submit" id="btn_upload_files" class="btn btn-success"><i class="fa fa-upload"></i> Subir archivos</button>
            <br>
            <small><b>Tipos permitidos:</b> jpg, jpeg, gif, png</small>
            <small><b>Resolución máxima:</b> 1920x1200</small>
            <small><b>Tamaño máximo:</b> 1 MB</small>
          </form> 
        </div>
        <hr>
        <div class="img-form-response"></div>
      </div>
      <div class="modal-footer">
        <button type="button" id="load_more_images" class="btn btn-lg btn-primary btn-block" onclick="load_images_for_summernote_blog(0)"><i class="fa fa-file-image-o"></i> Cargar más imágenes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</div>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/foot.php'; ?>
<!-- summernote -->
<script src="plugins/summernote/custom1-summernote.js"></script>
<script src="plugins/summernote/lang/summernote-es-ES.js"></script>
<script type="text/javascript">
  //contador para cargar imagenes, en este caso mostramos 8 por defecto
  var cont = 8;

  $(document).ready(function(){
    $("#title").focus();
    load_images_for_summernote_blog(1);//cargamos las primeras imagenes para insertar
    $('#userblogpost').summernote({
      lang: 'es-ES',
      disableDragAndDrop: true,
      placeholder: 'Escribir publicación...',
      minHeight: 400,
    });
  });

  function send_post() {
    //estas dos variables puedes enviarlas mediante ajax ;)
    var post_title = document.getElementById("title").value;
    var post_cont = $('#userblogpost').summernote('code');
    if (post_title.length<3) {
      alert("Ingrese un titulo");
      $("#title").focus();
    }
    else if (post_cont==="<p><br></p>"){
      alert("Ingrese contenido");
    }
    //esto es solo para fines didacticos
    else{
      $("#div_new_post").addClass('hidden');
      $("#div_preview").removeClass('hidden');
      $("#h1title").html(post_title);
      $("#form-response").html(post_cont);
    }
  }  


  function insert_url_image_summernote(url) {
    $('#modalSelectImage').modal('hide');
    $("#img_url").val(url);
  }

  function load_images_for_summernote_blog(keep_count) {    
    if (keep_count!=1) {cont=cont+8;} //con esto mantenemos el contador intacto
    $.ajax({
        type: "POST",
        url: "php/forms-submits/image-blog.php",
        data:{count:cont,opt:3},
        success: function(response){
          $(".img-form-response").html(response);
        },
    });  
  }
  
  $("#form_upload_image").bind("submit",function(){
    var formData = new FormData($("#form_upload_image")[0]);
    $.ajax({
      type: $(this).attr("method"),
      url: $(this).attr("action"),
      data:formData,
      contentType: false,
      processData: false,
      beforeSend: function(){
        $("#btn_upload_files").addClass('disabled');
        $("#btn_upload_files").html('<i class="fa fa-cog fa-spin"></i> Subiendo');
      },
      success: function(data){
        $("#btn_upload_files").removeClass('disabled');
        $("#btn_upload_files").html('<i class="fa fa-upload"></i> Subir archivos');
        if (data==false){
          alert("Seleccione una imagen");
        }
        else if (data==true){
          load_images_for_summernote_blog(1);
          $('#form_upload_image')[0].reset();              
        }
        else{
          load_images_for_summernote_blog(1);
          alert(data);
        }
      },
      error: function(){
        $("#btn_upload_files").removeClass('disabled');
        $("#btn_upload_files").html('<i class="fa fa-upload"></i> Subir archivos');
        alert("Problemas al tratar de enviar los datos");
      }
    });         
    return false;
  });
    
</script>
</body>
</html>