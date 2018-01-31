<?php
// В этом файле определены функции для загрузки, создания
// и отображения дерева

class treenode { 
  // Каждый узел дерева имеет атрибуты, которые содержат все данные,
  // необходимые для отправки всего, кроме тела сообщения
  public $m_postid;
  public $m_title;
  public $m_poster;
  public $m_posted;
  public $m_children;
  public $m_childlist;
  public $m_depth;

  public function __construct($postid, $title, $poster, $posted, $children, 
                              $expand, $depth, $expanded, $sublist)
  { 
    // Конструктор устанавливает значения атрибутов, но, что еще 
    // важнее, он рекурсивно создает нижние части дерева
    $this->m_postid = $postid;
    $this->m_title = $title;
    $this->m_poster = $poster;
    $this->m_posted = $posted;
    $this->m_children =$children;
    $this->m_childlist = array();
    $this->m_depth = $depth;

    // Списки, расположенные ниже этого узла, представляют интерес, только 
    // если узел имеет дочерние списки, которые должны быть всегда развернуты
    if (($sublist || $expand) && $children) {
      $conn = db_connect();

      $query = "select * from header where
                parent = '".$postid."' order by posted";
      $result = $conn->query($query);

      for ($count = 0; $row = @$result->fetch_assoc(); $count++) {
        if ($sublist || $expanded[$row['postid']] == true) {
          $expand = true;
        } else {
          $expand = false;
        }
        $this->m_childlist[$count]= new treenode($row['postid'],
                $row['title'], $row['poster'],$row['posted'],
                $row['children'], $expand, $depth+1, $expanded,
                $sublist);
      }
    }
  }  

  function display($row, $sublist = false) {
    // Поскольку это объект, он сам отвечает за свое отображение.

    // $row указывает, с какой строкой при отображении мы имеем дело.
    // Таким образом, нам известно, каким цветом эта строка должна выводиться

    // $sublist указывает, на какой странице мы находимся - 
    // на главной или на странице сообщения. Для страниц сообщений 
    // переменная $sublist равна true.
    // В подсписках все сообщения развернуты и не содержат
    // символов "+" и "-".

    // Если данный узел - пустой корневой узел, пропустить его вывод 
    if ($this->m_depth > -1) {
      // Чередовать цвет вывода строк
      echo "<tr><td bgcolor=\"";
      if ($row%2) {
        echo "#cccccc\">";
      } else {
        echo "#ffffff\">";
      }

      // Вывести отступ в соответствие с глубиной вложения
      for ($i = 0; $i < $this->m_depth; $i++) {
        echo "<img src=\"images/spacer.gif\" height=\"22\"
                   width=\"22\" alt=\"\" valign=\"bottom\" />";
      }

      // Вывести символ '+' или '-' или 'пробел' 
      if ((!$sublist) && ($this->m_children) && (sizeof($this->m_childlist))) {
      // Мы находимся на главной странице, имеем несколько дочерних узлов, 
      // и они развернуты 

        // Развернутое состояние - необходима кнопка сворачивания
        echo "<a href=\"index.php?collapse=".
              $this->m_postid."#".$this->m_postid."\"><img
              src=\"images/minus.gif\" valign=\"bottom\"
              height=\"22\" width=\"22\" alt=\"Свернуть цепочку\"
              border=\"0\" /></a>\n";
      } else if (!$sublist && $this->m_children) {
        // Свернутое состояние - необходима кнопка разворачивания
        echo "<a href=\"index.php?expand=".
              $this->m_postid."#".$this->m_postid."\"><img
              src=\"images/plus.gif\" valign=\"bottom\"
              height=\"22\" width=\"22\" alt=\"Развернуть цепочку\"
              border=\"0\" /></a>\n";
      } else {
        // Дочерних элементов нет или же мы находимся в подсписке - 
        // не нужно никаких кнопок
        echo "<img src=\"images/spacer.gif\" height=\"22\"
              width=\"22\" alt=\"\" valign=\"bottom\"/>\n";
      }

      echo "<a name=\"".$this->m_postid."\"><a href=
            \"view_post.php?postid=".$this->m_postid."\">".
            $this->m_title." - ".$this->m_poster." - ".
            reformat_date($this->m_posted)."</a></td></tr>";

      // Увеличить значение счетчика строк для чередования цветов вывода
      $row++;
    }
    // Вызвать метод display для каждого дочернего элемента этого узла.
    // Обратите внимание, что узел будет иметь дочерние узлы в своем списке, 
    // только если он развернут
    $num_children = sizeof($this->m_childlist);
    for ($i = 0; $i < $num_children; $i++) {
      $row = $this->m_childlist[$i]->display($row, $sublist);
    }
    return $row;
  }
};

?> 
