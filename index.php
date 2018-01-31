<?php
  include ('include_fns.php');
  session_start();

  // Проверить, создана ли переменная сеанса
  if (!isset($_SESSION['expanded'])) {
    $_SESSION['expanded'] = array();
  }

  // Проверить, была ли нажата кнопка 'Развернуть'.
  // Значением параметра expand может быть 'all', 
  // идентификатор postid или же значение может быть не установлено
  if (isset($_GET['expand'])) {
    if ($_GET['expand'] == 'all') {
      expand_all($_SESSION['expanded']);
    } else {
      $_SESSION['expanded'][$_GET['expand']] = true;
    }
  }

  // Проверить, была ли нажата кнопка 'Свернуть'.
  // Значением параметра collapse может быть 'all', 
  // идентификатор postid или же значение может быть не установлено
  if (isset($_GET['collapse'])) {
    if ($_GET['collapse'] == 'all') {
      $_SESSION['expanded'] = array();
    } else {
      unset($_SESSION['expanded'][$_GET['collapse']]);
    }
  } 

  do_html_header('Тестовый форум');

  display_index_toolbar();

  // Вывести древовидное представление бесед
  display_tree($_SESSION['expanded']);  

  do_html_footer();
?> 
