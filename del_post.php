<?php
  // Включить библиотеки функций
  include ('include_fns.php');
  $postid = $_GET['postid'];

  del_post($postid);

  include ('index.php');
?>
