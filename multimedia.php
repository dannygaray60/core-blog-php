<?php include 'php/options.php';  ?>
<!DOCTYPE html>
<html>
<head>
  <title>Blog - Biblioteca</title>
  <?php include 'includes/head.php';?>
  <!-- Image Picker -->
  <link rel="stylesheet" href="plugins/image-picker/image-picker.css">
</head>

<body>
<?php include 'includes/header.php'; ?>
<!-- dentro de este div va el contenido de la pag -->
<div class="container content">

  <div class="row container">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#tab_images" data-toggle="tab"><i class="fa fa-file-image-o"></i> Imágenes</a></li>
      <li id="button_tab_delete"><a href="#tab_delete" data-toggle="tab"><i class="fa fa-times"></i> Eliminar varios</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="tab_images">
        <br>
        <form id="form_upload_image" action="php/forms-submits/image-blog.php" method="POST" enctype="multipart/form-data">
          <input name="file[]" type="file" multiple required>
          <input type="hidden" name="opt" value="0">
          <br>
          <button type="submit" id="btn_upload_files" class="btn btn-success"><i class="fa fa-upload"></i> Subir archivos</button>
          <br>
          <small><b>Tipos permitidos:</b> jpg, jpeg, gif, png</small>
          <small><b>Resolución máxima:</b> 1920x1200</small>
          <small><b>Tamaño máximo:</b> 1 MB</small>
        </form> 
        <hr>
        <div class="img-form-response1"></div>
      </div>
      <!-- /.tab-pane -->
      <div class="tab-pane" id="tab_delete">
        <br>
        <form method="POST" id="form_del_images" action="php/forms-submits/image-blog.php">
          <input type="hidden" name="opt" value="2">
          <button type="button" class="hidden btn btn-lg btn-danger btn-block" id="del_btn2"><i class="fa fa-times"></i> Eliminar seleccionados</button>
          <small>Seleccione imágenes</small>
          <hr>
          <div class="img-form-response2"></div>
        </form>
      </div>
      <!-- /.tab-pane -->
      <hr>
      <button type="button" id="load_more_images" class="btn btn-lg btn-primary btn-block" onclick="load_images_for_summernote_blog(0)"><i class="fa fa-file-image-o"></i> Cargar más imágenes</button>
    </div>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="modalInfoImage" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title" id="myModalLabel">Detalles de imagen</h4>
        </div>
        <div class="modal-header">
          <div id="div_img_details"></div>
        </div>
        <div class="modal-body">
          <div class="row" align="center">
              <div id="div_image_full"></div>
          </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="urls" id="del_url">
            <button type="button" class="btn btn-danger btn-lg btn-block" id="del_btn"><i class="fa fa-times"></i> Eliminar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</div>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/foot.php'; ?>
<script src="plugins/image-picker/image-picker.js"></script>
<script type="text/javascript">
  //contador para cargar imagenes, en este caso mostramos 15 por defecto
  var cont = 15;

  $(document).ready(function(){
    load_images_for_summernote_blog(1);//cargamos las primeras imagenes para mostrar
  });

  function load_images_for_summernote_blog(keep_count) {    
    if (keep_count!=1) {cont=cont+8;} //con esto mantenemos el contador intacto
    $("#del_btn2").addClass("hidden");

    //esta peticion es para mostrar las imagenes en pestaña principal
    $.ajax({
      type: "POST",
      url: "php/forms-submits/image-blog.php",
      data:{count:cont,opt:4},
      success: function(response){
        $(".img-form-response1").html(response);
      }
    });  

    //y esta es para mostrar imagenes con la opcion de eliminacion multiple
    $.ajax({
      type: "POST",
      url: "php/forms-submits/image-blog.php",
      data:{count:cont,opt:5},
      success: function(response){
        $(".img-form-response2").html(response);
      }
    });

  }

  function show_image_info(img_url) {
    $.ajax({
      type: "POST",
      url: "php/forms-submits/image-blog.php",
      data:{opt:1,urls:img_url},
      success: function(data){
        $("#div_image_full").html('<img src="'+img_url+'" class="img-thumbnail img-responsive" style="max-width:95%">');
        $("#div_img_details").html(data);
        $("#del_url").val(img_url);
        $('#modalInfoImage').modal('show');
      }
    });     
  }  

  $("#del_btn").click(function(e){
    e.preventDefault();
    p = confirm("¿Está seguro de eliminar imagen?");
    if(p){
      var img_url = document.getElementById('del_url').value;
      $.ajax({
        type: "POST",
        url: "php/forms-submits/image-blog.php",
        data:{opt:2,urls:img_url},
        success: function(data){
          if (data!=true) {
            alert(data);
          }
          else{
            $("#div_image_full").html('');
            $("#div_img_details").html('');
            $("#del_url").val('');
            $('#modalInfoImage').modal('hide');
            load_images_for_summernote_blog(1); 
            $("#del_btn2").addClass("hidden"); 
          }
        },
      }); 
    }
  });

  $("#del_btn2").click(function(e){
    e.preventDefault();
    p = confirm("¿Está seguro de eliminar imágenes?");
    if(p){
      $.ajax({
          type: $("#form_del_images").attr("method"),
          url: $("#form_del_images").attr("action"),
          data:$("#form_del_images").serialize(),
          success: function(response){
            if (response==true) {
              load_images_for_summernote_blog(1);
              $("#del_btn2").addClass("hidden");
            }
            else{
              load_images_for_summernote_blog(1);
              $("#del_btn2").addClass("hidden");
              alert(response);
            }
          },
      });
    }
  });  

  $("#form_upload_image").bind("submit",function(){
    var formData = new FormData($("#form_upload_image")[0]); //obtenemos datos de formulario form_upload_image
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