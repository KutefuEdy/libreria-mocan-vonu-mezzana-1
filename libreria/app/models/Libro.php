<?php
/*
  File: Libro.php
  Scopo: Modello per la gestione dei Libri della biblioteca.
  Spiegazione: Metodi CRUD basilari sulla tabella books.
*/
namespace App\Models;
class Libro extends Model {
  public function __construct() {
    parent::__construct();
    $this->ensureBooksTable();
  }
  public function all() {
    $st = $this->pdo->query('SELECT * FROM books ORDER BY title');
    return $st->fetchAll();
  }
  public function find($id) {
    $st = $this->pdo->prepare('SELECT * FROM books WHERE id=?');
    $st->execute([$id]);
    return $st->fetch();
  }
  public function findByCode($code) {
    $st = $this->pdo->prepare('SELECT * FROM books WHERE code=?');
    $st->execute([$code]);
    return $st->fetch();
  }
  public function create($data) {
    $st = $this->pdo->prepare('INSERT INTO books (code,title,author,location,genre,year,status,notes,holder_student_id,holder_customer_id,borrowed_at,due_at,reserved_by_student_id,reserved_by_customer_id,reserved_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $st->execute([
      $data['code'],
      $data['title'],
      $data['author'],
      $data['location'] !== '' ? $data['location'] : null,
      $data['genre'] !== '' ? $data['genre'] : null,
      $data['year'] !== '' ? (int)$data['year'] : null,
      $data['status'],
      $data['notes'] !== '' ? $data['notes'] : null,
      $data['holder_student_id'] ?? null,
      $data['holder_customer_id'] ?? null,
      $data['borrowed_at'] ?? null,
      $data['due_at'] ?? null,
      $data['reserved_by_student_id'] ?? null,
      $data['reserved_by_customer_id'] ?? null,
      $data['reserved_at'] ?? null
    ]);
    return $this->pdo->lastInsertId();
  }
  public function update($id, $data) {
    $st = $this->pdo->prepare('UPDATE books SET code=?, title=?, author=?, location=?, genre=?, year=?, status=?, notes=?, holder_student_id=?, holder_customer_id=?, borrowed_at=?, due_at=?, reserved_by_student_id=?, reserved_by_customer_id=?, reserved_at=? WHERE id=?');
    $st->execute([
      $data['code'],
      $data['title'],
      $data['author'],
      $data['location'] !== '' ? $data['location'] : null,
      $data['genre'] !== '' ? $data['genre'] : null,
      $data['year'] !== '' ? (int)$data['year'] : null,
      $data['status'],
      $data['notes'] !== '' ? $data['notes'] : null,
      $data['holder_student_id'] ?? null,
      $data['holder_customer_id'] ?? null,
      $data['borrowed_at'] ?? null,
      $data['due_at'] ?? null,
      $data['reserved_by_student_id'] ?? null,
      $data['reserved_by_customer_id'] ?? null,
      $data['reserved_at'] ?? null,
      $id
    ]);
  }
  public function delete($id) {
    $st = $this->pdo->prepare('DELETE FROM books WHERE id=?');
    $st->execute([$id]);
  }
  private function ensureBooksTable() {
    $sql = "CREATE TABLE IF NOT EXISTS books (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      code VARCHAR(64) NOT NULL,
      title VARCHAR(255) NOT NULL,
      author VARCHAR(255) NOT NULL,
      location VARCHAR(100) NULL,
      genre VARCHAR(100) NULL,
      year INT NULL,
      status VARCHAR(32) NOT NULL DEFAULT 'available',
      notes TEXT NULL,
      holder_student_id INT NULL,
      holder_customer_id INT NULL,
      borrowed_at DATETIME NULL,
      due_at DATETIME NULL,
      reserved_by_student_id INT NULL,
      reserved_by_customer_id INT NULL,
      reserved_at DATETIME NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      UNIQUE KEY uniq_code (code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $this->pdo->exec($sql);
    try { $this->pdo->exec("ALTER TABLE books ADD COLUMN location VARCHAR(100) NULL"); } catch (\Throwable $e) {}
    try { $this->pdo->exec("ALTER TABLE books ADD COLUMN holder_student_id INT NULL"); } catch (\Throwable $e) {}
    try { $this->pdo->exec("ALTER TABLE books ADD COLUMN holder_customer_id INT NULL"); } catch (\Throwable $e) {}
    try { $this->pdo->exec("ALTER TABLE books ADD COLUMN borrowed_at DATETIME NULL"); } catch (\Throwable $e) {}
    try { $this->pdo->exec("ALTER TABLE books ADD COLUMN due_at DATETIME NULL"); } catch (\Throwable $e) {}
    try { $this->pdo->exec("ALTER TABLE books ADD COLUMN reserved_by_student_id INT NULL"); } catch (\Throwable $e) {}
    try { $this->pdo->exec("ALTER TABLE books ADD COLUMN reserved_by_customer_id INT NULL"); } catch (\Throwable $e) {}
    try { $this->pdo->exec("ALTER TABLE books ADD COLUMN reserved_at DATETIME NULL"); } catch (\Throwable $e) {}
  }
}
