<?php
namespace App\Services;
use App\Core\DB;
class ViewCardService {
  private static function upsert(string $scope, string $metric, int $value): void {
    $pdo = DB::conn();
    $stmt = $pdo->prepare("INSERT INTO view_cards (scope, metric, value, updated_at) VALUES (?,?,?,NOW()) ON DUPLICATE KEY UPDATE value=VALUES(value), updated_at=NOW()");
    $stmt->execute([$scope, $metric, $value]);
  }
  public static function refreshDashboard(): void {
    $pdo = DB::conn();
    $customers = (int)$pdo->query('SELECT COUNT(*) c FROM customers')->fetch()['c'];
    $students = (int)$pdo->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
    $groups = (int)$pdo->query('SELECT COUNT(*) c FROM work_groups')->fetch()['c'];
    self::upsert('dashboard', 'customers_total', $customers);
    self::upsert('dashboard', 'students_total', $students);
    self::upsert('dashboard', 'groups_total', $groups);
  }
  public static function refreshCustomers(): void {
    $pdo = DB::conn();
    $docenti = (int)$pdo->query('SELECT COUNT(*) c FROM customers')->fetch()['c'];
    self::upsert('customers', 'docenti', $docenti);
  }
  public static function refreshStudents(): void {
    $pdo = DB::conn();
    $students_total = (int)$pdo->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
    $leaders_total = (int)$pdo->query("SELECT COUNT(DISTINCT student_id) c FROM group_members WHERE role='leader'")->fetch()['c'];
    $installers_total = (int)$pdo->query("SELECT COUNT(DISTINCT student_id) c FROM group_members WHERE role='installer'")->fetch()['c'];
    self::upsert('students', 'students', $students_total);
    self::upsert('students', 'leaders', $leaders_total);
    self::upsert('students', 'installers', $installers_total);
  }
  public static function refreshGroups(): void {
    $pdo = DB::conn();
    $groups_total = (int)$pdo->query('SELECT COUNT(*) c FROM work_groups')->fetch()['c'];
    $members_total = (int)$pdo->query('SELECT COUNT(*) c FROM group_members')->fetch()['c'];
    self::upsert('groups', 'groups', $groups_total);
    self::upsert('groups', 'students', $members_total);
  }
  public static function refreshLibri(): void {
    $pdo = DB::conn();
    $total = (int)$pdo->query("SELECT COUNT(*) c FROM books")->fetch()['c'];
    $available = (int)$pdo->query("SELECT COUNT(*) c FROM books WHERE status='available'")->fetch()['c'];
    $borrowed = (int)$pdo->query("SELECT COUNT(*) c FROM books WHERE status='borrowed'")->fetch()['c'];
    $reserved = (int)$pdo->query("SELECT COUNT(*) c FROM books WHERE status='reserved'")->fetch()['c'];
    self::upsert('libri', 'total', $total);
    self::upsert('libri', 'available', $available);
    self::upsert('libri', 'borrowed', $borrowed);
    self::upsert('libri', 'reserved', $reserved);
  }
  public static function refreshPayments(): void {
    $pdo = DB::conn();
    $payments_total = (int)$pdo->query("SELECT COUNT(*) AS t FROM payment_transfers")->fetch()['t'];
    $customers_cnt = (int)$pdo->query("SELECT COUNT(DISTINCT customer_id) AS c FROM payment_transfers")->fetch()['c'];
    self::upsert('payments', 'payments', $payments_total);
    self::upsert('payments', 'customers', $customers_cnt);
  }
  public static function refreshSoftware(): void {
    $pdo = DB::conn();
    $total = (int)$pdo->query('SELECT COUNT(*) c FROM software')->fetch()['c'];
    $free = (int)$pdo->query("SELECT COUNT(*) c FROM software WHERE cost IS NULL OR cost=0 OR LOWER(COALESCE(license,'')) LIKE 'free%'")->fetch()['c'];
    $paid = (int)$pdo->query("SELECT COUNT(*) c FROM software WHERE cost IS NOT NULL AND cost > 0")->fetch()['c'];
    self::upsert('software', 'total', $total);
    self::upsert('software', 'free', $free);
    self::upsert('software', 'paid', $paid);
  }
  public static function refreshAll(): void {
    self::refreshDashboard();
    self::refreshCustomers();
    self::refreshStudents();
    self::refreshGroups();
    self::refreshLibri();
    self::refreshPayments();
    self::refreshSoftware();
  }
}
