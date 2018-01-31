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
        $title = '����� ������';
      }
    } else {
      // �������� �������� ������
      $title = get_post_title($parent);

      // �������� Re:
      if (strstr($title, 'Re: ') == false) {
        $title = 'Re: '.$title;
      }

      // ���������, ���������� �� ��������� � ���� ������
      $title = substr($title, 0, 20);

      // �������� ������, �� ������� ������ �����, � ����� ����������� ���������
      $message = add_quoting(get_post_message($parent));
    }
  }
  do_html_header($title);

  display_new_post_form($parent, $area, $title, $message, $poster);

  if ($error) {
     echo "<p>���� ��������� �� ���������.<br/>
           ���������, ��������� �� ��� ����,<br/>
           �� ��������� �� ������������ ������� ����������� ��������� (";
     echo $GLOBALS['max_level'];
     echo "),<br/> � ��������� �������.</p>";
  }

  do_html_footer();
?>
