<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Helpers;
use App\Models\Libro;
use App\Services\QrCodeService;
class LibroController {
  public function index() {
    Auth::require();
    $books = (new Libro())->all();
    $available = 0; $borrowed = 0; $reserved = 0;
    foreach ($books as $b) {
      $st = $b['status'] ?? '';
      if ($st === 'available') { $available++; }
      elseif ($st === 'borrowed') { $borrowed++; }
      elseif ($st === 'reserved') { $reserved++; }
    }
    \App\Services\ViewCardService::refreshLibri();
    $summary = [
      'total'=>count($books),
      'available'=>$available,
      'borrowed'=>$borrowed,
      'reserved'=>$reserved
    ];
    $students = (new \App\Models\Student())->all([]);
    $customers = (new \App\Models\Customer())->all();
    Helpers::view('libri/index', ['title'=>'Libri','books'=>$books,'summary'=>$summary,'students'=>$students,'customers'=>$customers]);
  }
  public function createForm() {
    Auth::require();
    if (!Auth::isAdmin()) { \App\Core\Helpers::redirect('/libri'); return; }
    $students = (new \App\Models\Student())->all([]);
    $customers = (new \App\Models\Customer())->all();
    Helpers::view('libri/form', ['title'=>'Nuovo Libro','book'=>null,'students'=>$students,'customers'=>$customers]);
  }
  public function store() {
    Auth::require();
    if (!Auth::isAdmin()) { \App\Core\Helpers::redirect('/libri'); return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = [
      'code'=>trim($_POST['code'] ?? ''),
      'title'=>trim($_POST['title'] ?? ''),
      'author'=>trim($_POST['author'] ?? ''),
      'location'=>trim($_POST['location'] ?? ''),
      'genre'=>trim($_POST['genre'] ?? ''),
      'year'=>trim($_POST['year'] ?? ''),
      'status'=>$_POST['status'] ?? 'available',
      'notes'=>trim($_POST['notes'] ?? ''),
      'holder_student_id'=>null,
      'holder_customer_id'=>null,
      'borrowed_at'=>null,
      'due_at'=>null,
      'reserved_by_student_id'=>null,
      'reserved_by_customer_id'=>null,
      'reserved_at'=>null,
    ];
    if ($data['status'] === 'borrowed') {
      $data['holder_student_id'] = $_POST['holder_student_id'] ?? null;
      $data['holder_customer_id'] = $_POST['holder_customer_id'] ?? null;
      $data['borrowed_at'] = $_POST['borrowed_at'] ?? null;
      $data['due_at'] = $_POST['due_at'] ?? null;
      $data['reserved_by_student_id'] = null;
      $data['reserved_by_customer_id'] = null;
      $data['reserved_at'] = null;
    } elseif ($data['status'] === 'reserved') {
      $data['reserved_by_student_id'] = $_POST['reserved_by_student_id'] ?? null;
      $data['reserved_by_customer_id'] = $_POST['reserved_by_customer_id'] ?? null;
      $data['reserved_at'] = $_POST['reserved_at'] ?? null;
      $data['holder_student_id'] = null;
      $data['holder_customer_id'] = null;
      $data['borrowed_at'] = null;
      $data['due_at'] = null;
    }
    $id = (new Libro())->create($data);
    (new \App\Models\Log())->addAction('create_book', \App\Core\Auth::user()['id'] ?? null, ['note'=>$data['title']]);
    \App\Services\ViewCardService::refreshLibri();
    Helpers::redirect('/libri');
  }
  public function editForm($id) {
    Auth::require();
    if (!Auth::isAdmin()) { \App\Core\Helpers::redirect('/libri'); return; }
    $book = (new Libro())->find($id);
    $students = (new \App\Models\Student())->all([]);
    $customers = (new \App\Models\Customer())->all();
    Helpers::view('libri/form', ['title'=>'Modifica Libro','book'=>$book,'students'=>$students,'customers'=>$customers]);
  }
  public function update($id) {
    Auth::require();
    if (!Auth::isAdmin()) { \App\Core\Helpers::redirect('/libri'); return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    $data = [
      'code'=>trim($_POST['code'] ?? ''),
      'title'=>trim($_POST['title'] ?? ''),
      'author'=>trim($_POST['author'] ?? ''),
      'location'=>trim($_POST['location'] ?? ''),
      'genre'=>trim($_POST['genre'] ?? ''),
      'year'=>trim($_POST['year'] ?? ''),
      'status'=>$_POST['status'] ?? 'available',
      'notes'=>trim($_POST['notes'] ?? ''),
      'holder_student_id'=>null,
      'holder_customer_id'=>null,
      'borrowed_at'=>null,
      'due_at'=>null,
      'reserved_by_student_id'=>null,
      'reserved_by_customer_id'=>null,
      'reserved_at'=>null,
    ];
    if ($data['status'] === 'borrowed') {
      $data['holder_student_id'] = $_POST['holder_student_id'] ?? null;
      $data['holder_customer_id'] = $_POST['holder_customer_id'] ?? null;
      $data['borrowed_at'] = $_POST['borrowed_at'] ?? null;
      $data['due_at'] = $_POST['due_at'] ?? null;
    } elseif ($data['status'] === 'reserved') {
      $data['reserved_by_student_id'] = $_POST['reserved_by_student_id'] ?? null;
      $data['reserved_by_customer_id'] = $_POST['reserved_by_customer_id'] ?? null;
      $data['reserved_at'] = $_POST['reserved_at'] ?? null;
    }
    (new Libro())->update($id, $data);
    (new \App\Models\Log())->addAction('update_book', \App\Core\Auth::user()['id'] ?? null, ['note'=>$data['title']]);
    \App\Services\ViewCardService::refreshLibri();
    Helpers::redirect('/libri');
  }
  public function delete($id) {
    Auth::require();
    if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
    if (!CSRF::validate($_POST['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; return; }
    (new Libro())->delete($id);
    (new \App\Models\Log())->addAction('delete_book', \App\Core\Auth::user()['id'] ?? null, ['note'=>strval($id)]);
    \App\Services\ViewCardService::refreshLibri();
    Helpers::redirect('/libri');
  }
  public function showPublicFromQrCode($code) {
    $model = new Libro();
    $book = $model->findByCode($code);
    if (!$book) {
      http_response_code(404);
      echo 'Libro non trovato';
      return;
    }
    $qrRelative = 'public/qrcodes/libro_' . preg_replace('/[^A-Za-z0-9_\\-]/', '_', $code) . '.png';
    $qrAbsolute = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $qrRelative);
    if (!is_file($qrAbsolute)) {
      $qrRelative = QrCodeService::generateBookQrByCode($code);
      $qrAbsolute = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $qrRelative);
    }
    $status = $book['status'] ?? 'available';
    $isBorrowed = $status === 'borrowed';
    $dueDate = $isBorrowed ? ($book['due_at'] ?? null) : null;
    $location = $book['location'] ?? null;
    Helpers::view('libri/qr_public', [
      'title' => 'Dettaglio libro',
      'book' => $book,
      'isBorrowed' => $isBorrowed,
      'dueDate' => $dueDate,
      'location' => $location,
      'qrPath' => $qrRelative,
    ]);
  }
}
