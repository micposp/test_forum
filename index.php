<?php
  include ('include_fns.php');
  session_start();

  // ���������, ������� �� ���������� ������
  if (!isset($_SESSION['expanded'])) {
    $_SESSION['expanded'] = array();
  }

  // ���������, ���� �� ������ ������ '����������'.
  // ��������� ��������� expand ����� ���� 'all', 
  // ������������� postid ��� �� �������� ����� ���� �� �����������
  if (isset($_GET['expand'])) {
    if ($_GET['expand'] == 'all') {
      expand_all($_SESSION['expanded']);
    } else {
      $_SESSION['expanded'][$_GET['expand']] = true;
    }
  }

  // ���������, ���� �� ������ ������ '��������'.
  // ��������� ��������� collapse ����� ���� 'all', 
  // ������������� postid ��� �� �������� ����� ���� �� �����������
  if (isset($_GET['collapse'])) {
    if ($_GET['collapse'] == 'all') {
      $_SESSION['expanded'] = array();
    } else {
      unset($_SESSION['expanded'][$_GET['collapse']]);
    }
  } 

  do_html_header('�������� �����');

  display_index_toolbar();

  // ������� ����������� ������������� �����
  display_tree($_SESSION['expanded']);  

  do_html_footer();
?> 
