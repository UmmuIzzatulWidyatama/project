<?php 
namespace App\Controllers;
use CodeIgniter\Controller;
use Config\Database;

class Health extends Controller {
  public function db() {
    try {
      $db  = Database::connect();
      $row = $db->query('SELECT 1 AS ok, NOW() AS server_time')->getRowArray();
      return $this->response->setJSON(['status'=>'ok','data'=>$row]);
    } catch (\Throwable $e) {
      return $this->response->setJSON([
        'status'=>'error',
        'message'=>$e->getMessage()
      ])->setStatusCode(500);
    }
  }
  
  public function session()
  {
    return $this->response->setJSON([
      'id'         => session_id(),
      'isLoggedIn' => session()->get('isLoggedIn'),
      'user'       => session()->get('user'),
    ]);
  }

}
