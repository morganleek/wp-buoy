<?php 
  $data = file_get_contents('php://input');
  file_put_contents('spotter-' . time() . '.json', $data);

  exit();