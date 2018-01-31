<?php
  // Включить библиотеки функций
  include ('include_fns.php');
  $postid = $_GET['postid'];

  // Получить детальную информацию о статье
  $post = get_post($postid);

  do_html_header($post['title']);

  // Отобразить статью
  display_post($post);

  // Если со статьей связаны ответы, вывести их древовидное представление
  if ($post['children']) {
    echo "<br /><br />";
    display_replies_line();
    display_tree($_SESSION['expanded'], 0, $postid);
  }

  do_html_footer();
?>
