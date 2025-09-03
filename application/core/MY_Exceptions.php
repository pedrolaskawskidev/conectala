<?php
defined('BASEPATH') or exit('No direct script access allowed');


class MY_Exceptions extends CI_Exceptions
{

  public function show_error_api($message, $status = 500)
  {
    header("Content-Type: application/json");
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit;
  }
}
