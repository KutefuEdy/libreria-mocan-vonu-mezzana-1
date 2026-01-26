<?php
declare(strict_types=1);
$vendorAutoload = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (is_file($vendorAutoload)) {
  require $vendorAutoload;
}
session_start();
spl_autoload_register(function($class){
  $prefix = 'App\\';
  if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
  $path = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
  if (file_exists($path)) require $path;
});
use App\Core\Router;
use App\Core\Auth;
use App\Core\Helpers;
use App\Core\DB;
use App\Controllers\AuthController;
use App\Controllers\LibroController;
use App\Controllers\CustomerController;
use App\Controllers\StudentController;
use App\Controllers\WorkGroupController;
use App\Controllers\PaymentController;
use App\Controllers\SoftwareController;
use App\Controllers\LogsController;
use App\Services\ViewCardService;
$router = new Router();
$pdoBootstrap = DB::conn();
// Ensure superuser admin exists
$adminExists = (int)$pdoBootstrap->query("SELECT COUNT(*) c FROM students WHERE email='admin'")->fetch()['c'];
if ($adminExists === 0) {
  $stmt = $pdoBootstrap->prepare('INSERT INTO students (first_name,last_name,email,password_hash,role,active) VALUES (?,?,?,?,?,?)');
  $stmt->execute(['Super','Admin','admin',password_hash('admin', PASSWORD_DEFAULT),'admin',1]);
}
// Default seed if empty (optional, but keeping logical flow)
$exists = (int)$pdoBootstrap->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
if ($exists === 0) {
  $stmt = $pdoBootstrap->prepare('INSERT INTO students (first_name,last_name,email,password_hash,role,active) VALUES (?,?,?,?,?,?)');
  $stmt->execute(['Admin','User','admin@example.com',password_hash('admin123', PASSWORD_DEFAULT),'admin',1]);
}
$router->get('/login', [AuthController::class,'loginForm']);
$router->post('/login', [AuthController::class,'login']);
$router->get('/logout', [AuthController::class,'logout']);
$router->get('/', function(){
  if (!Auth::check()) { Helpers::redirect('/login'); }
  $pdo = DB::conn();
  $stmt = $pdo->prepare('SELECT metric, value FROM view_cards WHERE scope=?');
  $stmt->execute(['dashboard']);
  $rows = $stmt->fetchAll();
  if (!$rows) {
    ViewCardService::refreshDashboard();
    $stmt->execute(['dashboard']);
    $rows = $stmt->fetchAll();
  }
  $counts = [];
  foreach ($rows as $r) { $counts[$r['metric']] = (int)$r['value']; }
  Helpers::view('dashboard', ['title'=>'Dashboard','counts'=>$counts]);
});
$router->get('/admin/migrate', function(){
  if (!Auth::isAdmin()) { http_response_code(403); echo '403'; return; }
  $pdo = DB::conn();
  $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
  $files = [];
  foreach (glob($dir . DIRECTORY_SEPARATOR . '*.sql') as $f) { $files[] = $f; }
  sort($files);
  foreach ($files as $file) {
    $sql = file_get_contents($file);
    $chunks = array_filter(array_map('trim', preg_split('/;\\s*/', $sql)));
    foreach ($chunks as $chunk) {
      if ($chunk === '') continue;
      try { $pdo->exec($chunk); } catch (\Throwable $e) {}
    }
  }
  \App\Core\Helpers::addFlash('success', 'Migrazioni eseguite');
  ViewCardService::refreshAll();
  \App\Core\Helpers::redirect('/');
});
$router->get('/customers', [CustomerController::class,'index']);
$router->get('/customers/export', [CustomerController::class,'export']);
$router->post('/customers/import', [CustomerController::class,'import']);
$router->get('/customers/create', [CustomerController::class,'createForm']);
$router->post('/customers', [CustomerController::class,'store']);
$router->get('/customers/{id}', [CustomerController::class,'show']);
$router->get('/customers/{id}/edit', [CustomerController::class,'editForm']);
$router->post('/customers/{id}/update', [CustomerController::class,'update']);
$router->post('/customers/{id}/delete', [CustomerController::class,'delete']);
$router->get('/students', [StudentController::class,'index']);
$router->get('/students/export', [StudentController::class,'export']);
$router->post('/students/import', [StudentController::class,'import']);
$router->get('/students/create', [StudentController::class,'createForm']);
$router->post('/students', [StudentController::class,'store']);
$router->get('/students/{id}/edit', [StudentController::class,'editForm']);
$router->post('/students/{id}/update', [StudentController::class,'update']);
$router->post('/students/{id}/delete', [StudentController::class,'delete']);
$router->get('/work-groups', [WorkGroupController::class,'index']);
$router->get('/work-groups/create', [WorkGroupController::class,'createForm']);
$router->post('/work-groups', [WorkGroupController::class,'store']);
$router->get('/work-groups/{id}', [WorkGroupController::class,'show']);
$router->get('/work-groups/{id}/edit', [WorkGroupController::class,'editForm']);
$router->post('/work-groups/{id}/update', [WorkGroupController::class,'update']);
$router->post('/work-groups/{id}/add-member', [WorkGroupController::class,'addMember']);
$router->post('/work-groups/{id}/remove-member', [WorkGroupController::class,'removeMember']);
$router->get('/work-groups/export', [WorkGroupController::class,'export']);
$router->post('/work-groups/import', [WorkGroupController::class,'import']);
$router->post('/work-groups/{id}/delete', [WorkGroupController::class,'delete']);
$router->get('/logs', [LogsController::class,'index']);
$router->post('/logs/clear-access', [LogsController::class,'clearAccess']);
$router->post('/logs/clear-actions', [LogsController::class,'clearActions']);
$router->get('/libri', [LibroController::class,'index']);
$router->get('/libri/create', [LibroController::class,'createForm']);
$router->post('/libri', [LibroController::class,'store']);
$router->get('/libri/{id}/edit', [LibroController::class,'editForm']);
$router->post('/libri/{id}/update', [LibroController::class,'update']);
$router->post('/libri/{id}/delete', [LibroController::class,'delete']);
$router->get('/libri/qr/{code}', [LibroController::class,'showPublicFromQrCode']);
$router->get('/api/view-cards', function(){
  if (!Auth::check()) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); return; }
  header('Content-Type: application/json');
  $scope = $_GET['scope'] ?? '';
  $scope = is_string($scope) ? trim($scope) : '';
  if ($scope === '') { echo json_encode(['error'=>'scope_required']); return; }
  $pdo = DB::conn();
  $stmt = $pdo->prepare('SELECT metric, value FROM view_cards WHERE scope=?');
  $stmt->execute([$scope]);
  $rows = $stmt->fetchAll();
  if (!$rows) {
    try {
      switch ($scope) {
        case 'dashboard': ViewCardService::refreshDashboard(); break;
        case 'customers': ViewCardService::refreshCustomers(); break;
        case 'students': ViewCardService::refreshStudents(); break;
        case 'groups': ViewCardService::refreshGroups(); break;
        case 'libri': ViewCardService::refreshLibri(); break;
        case 'payments': ViewCardService::refreshPayments(); break;
        case 'software': ViewCardService::refreshSoftware(); break;
      }
      $stmt->execute([$scope]);
      $rows = $stmt->fetchAll();
    } catch (\Throwable $e) {}
  }
  $out = [];
  foreach ($rows as $r) { $out[$r['metric']] = (int)$r['value']; }
  echo json_encode(['scope'=>$scope,'metrics'=>$out]);
});
$router->dispatch();
