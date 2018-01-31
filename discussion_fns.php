<?php

$max_level = 5;

function expand_all(&$expanded) {
  // Помечает все цепочки с дочерними цепочками как отображаемые в развернутом виде
  $conn = db_connect();
  $query = "select postid from header where children = 1";
  $result = $conn->query($query);
  $num = $result->num_rows;

  for ($i = 0; $i < $num; $i++) {
    $this_row = $result->fetch_row();
    $expanded[$this_row[0]] = true;
  }
}

function get_post($postid) {
  // Извлекает из базы данных одну статью и возвращает ее в виде массива

  if (!$postid) {
    return false;
  }

  $conn = db_connect();

  // Получить всю информацию о заголовках из 'header'
  $query = "select * from header where postid = '".$postid."'";
  $result = $conn->query($query);
  if ($result->num_rows != 1) {
    return false;
  }
  $post = $result->fetch_assoc();

  // Получить сообщение из тела и добавить его к предыдущему результату
  $query = "select * from body where postid = '".$postid."'";
  $result2 = $conn->query($query);
  if ($result2->num_rows > 0) {
    $body = $result2->fetch_assoc();
    if ($body) {
      $post['message'] = $body['message'];
    }
  }
  return $post; 
}

function get_post_title($postid) {
  // Извлекает из базы данных название одной статьи

  if (!$postid) {
    return '';
  }

  $conn = db_connect();

  // Получить всю информацию о заголовке из 'header'
  $query = "select title from header where postid = '".$postid."'";
  $result = $conn->query($query);
  if ($result->num_rows != 1) {
    return '';
  }
  $this_row = $result->fetch_array();
  return  $this_row[0];
}

function get_post_message($postid) {
  // Извлекает из базы данных тело одной статьи

  if (!$postid) {
    return '';
  }

  $conn = db_connect();

  $query = "select message from body where postid = '".$postid."'";
  $result = $conn->query($query);
  if ($result->num_rows > 0) {
    $this_row = $result->fetch_array();
    return $this_row[0];
  }
}

function add_quoting($string, $pattern = '> ') {
  // Помечает текст как цитируемый с использованием шаблона '> '
  return $pattern.str_replace("\n", "\n$pattern", $string);
}

function store_new_post($post) {
  // Проверяет допустимость и затем сохраняет новую статью в базе данных

  $conn = db_connect();

  // Проверить, что ни одно поле не оставлено пустым
  if (!filled_out($post)) {
    return false;
  }
  $post = clean_all($post);

  // Проверить, существует ли родительская статья
  if ($post['parent'] != 0) {
    $query = "select postid from header where postid = '".$post['parent']."'";
    $result = $conn->query($query);
    if ($result->num_rows != 1) {
      return false;
    }
  }

  // Определить уровень вложенности
  $parent_id = $post['parent'];
  for ($level = 0; $level < $GLOBALS['max_level']; $level++) {
    $query = "select parent from header where postid = '".$parent_id."'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      $map = $result->fetch_assoc();
      $parent_id = $map['parent'];
    } else {
      break;
    }
  }

  // Проверить уровень вложенности
  if ($level == $GLOBALS['max_level']) {
    return false;
  }

  // Проверить, не появится ли дубликат
  $query = "select header.postid from header, body where
            header.postid = body.postid and
            header.parent = ".$post['parent']." and
            header.poster = '".$post['poster']."' and
            header.title = '".$post['title']."' and
            header.area = ".$post['area']." and
            body.message = '".$post['message']."'";

  $result = $conn->query($query);
  if (!$result) {
     return false;
  }

  if ($result->num_rows > 0) {
    $this_row = $result->fetch_array();
    $id = $this_row[0];
  }

  $query = "insert into header values
            ('".$post['parent']."',
            '".$post['poster']."',
            '".$post['title']."',
            0,
            '".$post['area']."',
            now(),
            NULL
            )";

  $result = $conn->query($query);
  if (!$result) {
     return false;
  }

  // Теперь родительская статья имеет дочернюю статью
  $query = "update header set children = 1 where postid = '".$post['parent']."'";
  $result = $conn->query($query);
  if (!$result) {
     return false;
  }

  // Выяснить идентификатор данной статьи. Обратите внимание, что вполне
  // может существовать несколько почти идентичных статей, которые
  // различаются только идентификаторами и, возможно, временем отправки.
  $query = "select header.postid from header left join body
            on header.postid = body.postid
            where parent = '".$post['parent']."'
              and poster = '".$post['poster']."'
              and title = '".$post['title']."'
              and body.postid is NULL";

  $result = $conn->query($query);
  if (!$result) {
     return false;
  }

  if ($result->num_rows > 0) {
    $this_row = $result->fetch_array();
    $id = $this_row[0];
  }

  if ($id) {
    $query = "insert into body values
             ($id, '".$post['message']."')";
    $result = $conn->query($query);
    if (!$result) {
      return false;
    }
    return $id;
  }
}

function del_post($postid) {
  // Удаляет из базы данных статью и ответы на нее

  if (!$postid) {
    return false;
  }

  $conn = db_connect();

  // Удаляем дочерние статьи
  $query = "select postid from header where parent = '".$postid."'";
  $result = $conn->query($query);
  for ($i = 0; $i < $result->num_rows; $i++) {
    $map = $result->fetch_assoc();
    $child_postid = $map['postid'];
    del_post($child_postid);
  }

  // Получить parent текущей статьи
  $query = "select parent from header where postid = '".$postid."'";
  $result = $conn->query($query);
  if ($result->num_rows != 1) {
    return false;
  }
  $map = $result->fetch_assoc();
  $parentid = $map['parent'];

  // Удалить заданную статью
  $query = "delete from header where postid = '".$postid."'";
  $result = $conn->query($query);
  $query = "delete from body where postid = '".$postid."'";
  $result = $conn->query($query);

  // Теперь у родительской статьи на одну дочернюю стало меньше
  if ($parentid != 0) {
    $query = "select postid from header where parent = '".$parentid."'";
    $result = $conn->query($query);
    if ($result->num_rows==0) {
      $query = "update header set children = 0 where postid = '".$parentid."'";
      $result = $conn->query($query);
      if (!$result) {
        return false;
      }
    }
  }
}

?>
