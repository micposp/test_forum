<?php
  // Помещение всех включаемых файлов в одном месте означает,
  // что они будут отнимать время на загрузку при просмотре каждой страницы,
  // однако, поступая таким образом, мы гарантируем, что не забудем
  // включить какой-нибудь из них.

  include_once('db_fns.php');
  include_once('data_valid_fns.php');
  include_once('output_fns.php');
  include_once('discussion_fns.php');
  include_once('treenode_class.php');

?>
