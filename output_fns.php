<?php
$table_width = '680';

function reformat_date($datetime) {
  // Преобразует дату в формат, принятый в США, отбрасывая секунды
  list($year, $month, $day, $hour, $min, $sec) = split( '[: -]', $datetime );
  return "$hour:$min $month/$day/$year";
}

function display_tree($expanded, $row = 0, $start = 0) {
  // Выводит древовидное представление бесед

  global $table_width;
  echo "<table width=\"".$table_width."\">";

  // Проверить, отображается полный список или подсписок
  if ($start > 0) {
    $sublist = true;
  } else {
    $sublist = false;
  }

  // Создать древовидную структуру, представляющую беседу целиком
  $tree = new treenode($start, '', '', '', 1, true, -1, $expanded, $sublist);

  // Указать дереву на необходимость отобразить себя
  $tree->display($row, $sublist);

  echo "</table>";
}

function do_html_header($title = '') {
  // Выводит HTML-заголовок

  global $table_width;
?>
  <html>
  <head>
    <title><?php echo $title?></title>
    <style>
      h1 { font-family: 'Times New Roman', Times,  serif; font-size: 32; 
           font-weight: normal; color:  white; margin-bottom: 0}
      b { font-family: 'Times New Roman', Times,  serif; font-size: 18; 
          font-weight: normal; color: black }
      body, li, td { font-family: Arial, Helvetica, sans-serif; 
                     font-size: 15px; margin = 5px }
      a { color: #000000 }
    </style>
  </head>
  <body>
    <table width=<?php echo $table_width?> cellspacing=0 cellpadding=6>
      <tr>
        <td bgcolor="#3333cc" width=110>
        </td>
        <td bgcolor="#3333cc">
          <h1><?php echo $title?></h1>
        </td>
      </tr>
    </table>
<?php
}

function do_html_footer() {
  // Выводит завершающие HTML-дескрипторы

  global $table_width;
?>
  <table width=<?php echo $table_width?> cellspacing=0 cellpadding=6>
    <tr>
      <td bgcolor="#3333cc" align="right">
      </td>
    </tr>
  </table>
  </body>
  </html>
<?php
}

function display_replies_line() {
  global $table_width;
?>
  <table width=<?php echo $table_width?>
         cellpadding=4 cellspacing=0 bgcolor="#cccccc">
    <tr><td><strong>Ответы на это сообщение</strong></td></tr>
  </table>
<?php
}

function display_index_toolbar() {
  global $table_width;
?>
  <table width=<?php echo $table_width?> cellpadding=4 cellspacing=0>
    <tr>
      <td bgcolor="#cccccc" align="right">
        <a href="new_post.php?parent=0">
          <img src="images/new-post.gif" border=0 width=99 height=39>
        </a>
        <a href="index.php?expand=all">
          <img src="images/expand.gif" border=0 width=99 height=39 
               alt="Развернуть все цепочки">
        </a>
        <a href="index.php?collapse=all">
          <img src="images/collapse.gif" border=0 width=99 height=39
               alt="Свернуть все цепочки">
        </a>
      </td>
    </tr>
  </table>
<?php
}

function display_post($post) {
  global $table_width;
 
  if (!$post)
    return;
?>
  <table width=<?php echo $table_width?> cellpadding=4 cellspacing=0>
    <tr>
      <td bgcolor="#cccccc">
        <b>From: <?php echo $post['poster'];?></b><br />
        <b>Posted: <?php echo $post['posted'];?></b>
      </td>
      <td bgcolor="#cccccc" align="right">
        <a href='new_post.php?parent=0'>
          <img src='images/new-post.gif' border=0 width=99 height=39 />
        </a>
        <a href='del_post.php?postid=<?php echo $post['postid'];?>'>
          <img src='images/delete.png' border=0 width=99 height=39 />
        </a>
        <a href='new_post.php?parent=<?php echo $post['postid'];?>'>
          <img src='images/reply.gif' border=0 width=99 height=39 />
        </a>
        <a href='index.php?expanded=<?php echo $post['postid'];?>'>
          <img src="images/index.gif" border=0 width=99 height=39 />
        </a>
      </td>
    </tr>
    <tr>
      <td colspan = 2>
        <?php echo nl2br($post['message']);?>
      </td>
    </tr>
  </table>
<?php
}

function display_new_post_form($parent=0, $area=1, $title='', $message='', $poster='') {
  global $table_width;
?>
  <table cellpadding=0 cellspacing=0 border=0 width=<?php echo $table_width?>>
  <form action="store_new_post.php?expand=<?php echo $parent;?>#<?php echo $parent;?>" 
        method = "post">
    <tr>
      <td bgcolor="#cccccc"> Ваше имя: </td>
      <td bgcolor="#cccccc"> 
        <input type="text" name="poster" value="<?php echo $poster?>" 
               size=20 maxlength=20>
      </td>
    </tr>
    <tr>
      <td bgcolor="#cccccc"> Заголовок: </td>
      <td bgcolor="#cccccc">
        <input type="text" name="title" value="<?php echo $title?>" 
               size=20 maxlength=20>
      </td>
    </tr>
    <tr>
      <td colspan=2>
        <textarea name="message" rows=10 cols=55>
          <?php echo stripslashes($message);?>
        </textarea>
      </td>
    </tr>
    <tr>
      <td colspan=2 align="center" bgcolor="#cccccc">
        <input type="image" name="post" src="images/post.gif" 
               alt="Отправить сообщение" width=99 height=39>
      </td>
      <input type="hidden" name="parent" value=<?php echo $parent;?>>
      <input type="hidden" name="area" value=<?php echo $area;?>>
    </tr>
  </form>
  </table>
<?php
}

?>
