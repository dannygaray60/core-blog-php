<?php 
$script = URL_BASE.$_SERVER['SCRIPT_NAME'];
$script = explode('/', $script);
$script = end($script);
switch ($script) {
  case 'index.php':
    @$act_index = 'active';
    break;
  case 'new-post.php':
    @$act_new_post = 'active';
    break;
  case 'multimedia.php':
    @$act_multimedia = 'active';
    break;
  case 'about.php':
    @$act_about = 'active';
    break;
}
 ?>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php"><i class="fa fa-leaf"></i> Blog de <?php echo NAME_USER; ?></a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li class="<?php echo @$act_index;?>"><a href="index.php"><i class="fa fa-home"></i> Inicio</a></li>
        <li class="<?php echo @$act_new_post;?>"><a href="new-post.php"><i class="fa fa-edit"></i> Crear entrada</a></li>
        <li class="<?php echo @$act_multimedia;?>"><a href="multimedia.php"><i class="fa fa-file-image-o"></i> Biblioteca</a></li>
        <li class="<?php echo @$act_about;?>"><a href="about.php"><i class="fa fa-user"></i> Acerca de</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</nav>