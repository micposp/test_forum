<?php
// � ���� ����� ���������� ������� ��� ��������, ��������
// � ����������� ������

class treenode { 
  // ������ ���� ������ ����� ��������, ������� �������� ��� ������,
  // ����������� ��� �������� �����, ����� ���� ���������
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
    // ����������� ������������� �������� ���������, ��, ��� ��� 
    // ������, �� ���������� ������� ������ ����� ������
    $this->m_postid = $postid;
    $this->m_title = $title;
    $this->m_poster = $poster;
    $this->m_posted = $posted;
    $this->m_children =$children;
    $this->m_childlist = array();
    $this->m_depth = $depth;

    // ������, ������������� ���� ����� ����, ������������ �������, ������ 
    // ���� ���� ����� �������� ������, ������� ������ ���� ������ ����������
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
    // ��������� ��� ������, �� ��� �������� �� ���� �����������.

    // $row ���������, � ����� ������� ��� ����������� �� ����� ����.
    // ����� �������, ��� ��������, ����� ������ ��� ������ ������ ����������

    // $sublist ���������, �� ����� �������� �� ��������� - 
    // �� ������� ��� �� �������� ���������. ��� ������� ��������� 
    // ���������� $sublist ����� true.
    // � ���������� ��� ��������� ���������� � �� ��������
    // �������� "+" � "-".

    // ���� ������ ���� - ������ �������� ����, ���������� ��� ����� 
    if ($this->m_depth > -1) {
      // ���������� ���� ������ �����
      echo "<tr><td bgcolor=\"";
      if ($row%2) {
        echo "#cccccc\">";
      } else {
        echo "#ffffff\">";
      }

      // ������� ������ � ������������ � �������� ��������
      for ($i = 0; $i < $this->m_depth; $i++) {
        echo "<img src=\"images/spacer.gif\" height=\"22\"
                   width=\"22\" alt=\"\" valign=\"bottom\" />";
      }

      // ������� ������ '+' ��� '-' ��� '������' 
      if ((!$sublist) && ($this->m_children) && (sizeof($this->m_childlist))) {
      // �� ��������� �� ������� ��������, ����� ��������� �������� �����, 
      // � ��� ���������� 

        // ����������� ��������� - ���������� ������ ������������
        echo "<a href=\"index.php?collapse=".
              $this->m_postid."#".$this->m_postid."\"><img
              src=\"images/minus.gif\" valign=\"bottom\"
              height=\"22\" width=\"22\" alt=\"�������� �������\"
              border=\"0\" /></a>\n";
      } else if (!$sublist && $this->m_children) {
        // ��������� ��������� - ���������� ������ ��������������
        echo "<a href=\"index.php?expand=".
              $this->m_postid."#".$this->m_postid."\"><img
              src=\"images/plus.gif\" valign=\"bottom\"
              height=\"22\" width=\"22\" alt=\"���������� �������\"
              border=\"0\" /></a>\n";
      } else {
        // �������� ��������� ��� ��� �� �� ��������� � ��������� - 
        // �� ����� ������� ������
        echo "<img src=\"images/spacer.gif\" height=\"22\"
              width=\"22\" alt=\"\" valign=\"bottom\"/>\n";
      }

      echo "<a name=\"".$this->m_postid."\"><a href=
            \"view_post.php?postid=".$this->m_postid."\">".
            $this->m_title." - ".$this->m_poster." - ".
            reformat_date($this->m_posted)."</a></td></tr>";

      // ��������� �������� �������� ����� ��� ����������� ������ ������
      $row++;
    }
    // ������� ����� display ��� ������� ��������� �������� ����� ����.
    // �������� ��������, ��� ���� ����� ����� �������� ���� � ����� ������, 
    // ������ ���� �� ���������
    $num_children = sizeof($this->m_childlist);
    for ($i = 0; $i < $num_children; $i++) {
      $row = $this->m_childlist[$i]->display($row, $sublist);
    }
    return $row;
  }
};

?> 
