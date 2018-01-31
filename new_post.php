<?php
  include ('include_fns.php');

  $title = $_POST['title'];
  $poster = $_POST['poster'];
  $message = $_POST['message'];

  if (isset($_GET['parent'])) {
    $parent = $_GET['parent'];
  } else {
    $parent = $_POST['parent'];
  }

  if (!$area) {
    $area = 1;
  }

  if (!$error) {
    if (!$parent) {
      $parent = 0;
      if (!$title) {
        $title = 'Новая статья';
      }
    } else {
      // Получить название статьи
      $title = get_post_title($parent);

      // Добавить Re:
      if (strstr($title, 'Re: ') == false) {
        $title = 'Re: '.$title;
      }

      // Проверить, помещается ли заголовок в базу данных
      $title = substr($title, 0, 20);

      // Добавить статью, на которую дается ответ, в форме цитируемого сообщения
      $message = add_quoting(get_post_message($parent));
    }
  }
  do_html_header($title);

  display_new_post_form($parent, $area, $title, $message, $poster);

  if ($error) {
     echo "<p>Ваше сообщение не сохранено.<br/>
           Проверьте, заполнены ли все поля,<br/>
           не достигнут ли максимальный уровень вложенности сообщений (";
     echo $GLOBALS['max_level'];
     echo "),<br/> и повторите попытку.</p>";
  }

  do_html_footer();
?>
