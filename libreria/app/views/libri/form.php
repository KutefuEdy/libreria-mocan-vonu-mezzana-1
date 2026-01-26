<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4"><?php echo htmlspecialchars($title ?? 'Libro'); ?></h5>
    <form method="post" action="<?php echo \App\Core\Helpers::url(isset($book['id']) ? '/libri/'.$book['id'].'/update' : '/libri'); ?>">
      <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="code">Codice/ISBN</label>
          <input type="text" class="form-control" id="code" name="code" required value="<?php echo htmlspecialchars($book['code']??''); ?>" autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="title">Titolo</label>
          <input type="text" class="form-control" id="title" name="title" required value="<?php echo htmlspecialchars($book['title']??''); ?>" autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="author">Autore</label>
          <input type="text" class="form-control" id="author" name="author" required value="<?php echo htmlspecialchars($book['author']??''); ?>" autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="genre">Genere</label>
          <input type="text" class="form-control" id="genre" name="genre" value="<?php echo htmlspecialchars($book['genre']??''); ?>" autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="year">Anno</label>
          <input type="number" class="form-control" id="year" name="year" min="0" value="<?php echo htmlspecialchars($book['year']??''); ?>" autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="location">Posizione in libreria</label>
          <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($book['location']??''); ?>" autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="status">Stato</label>
          <select class="form-select" id="status" name="status">
            <?php foreach (['available'=>'Disponibile','borrowed'=>'Prestato','reserved'=>'Riservato'] as $val=>$label) { ?>
              <option value="<?php echo $val; ?>" <?php echo (($book['status']??'available')===$val)?'selected':''; ?>><?php echo $label; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="holder_student_id">Prestato a Studente</label>
          <select class="form-select" id="holder_student_id" name="holder_student_id">
            <option value="">Nessuno</option>
            <?php foreach (($students??[]) as $s) { ?>
              <option value="<?php echo $s['id']; ?>" <?php echo (($book['holder_student_id']??null)==$s['id'])?'selected':''; ?>>
                <?php echo htmlspecialchars(trim(($s['last_name']??'').' '.($s['first_name']??''))); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="holder_customer_id">Prestato a Docente</label>
          <select class="form-select" id="holder_customer_id" name="holder_customer_id">
            <option value="">Nessuno</option>
            <?php foreach (($customers??[]) as $c) { ?>
              <option value="<?php echo $c['id']; ?>" <?php echo (($book['holder_customer_id']??null)==$c['id'])?'selected':''; ?>>
                <?php echo htmlspecialchars(trim(($c['last_name']??'').' '.($c['first_name']??''))); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="borrowed_at">Data prestito</label>
          <input type="datetime-local" class="form-control" id="borrowed_at" name="borrowed_at" value="<?php echo htmlspecialchars(isset($book['borrowed_at']) && $book['borrowed_at'] ? date('Y-m-d\TH:i', strtotime($book['borrowed_at'])) : ''); ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="due_at">Data restituzione</label>
          <input type="datetime-local" class="form-control" id="due_at" name="due_at" value="<?php echo htmlspecialchars(isset($book['due_at']) && $book['due_at'] ? date('Y-m-d\TH:i', strtotime($book['due_at'])) : ''); ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="reserved_by_student_id">Riservato da Studente</label>
          <select class="form-select" id="reserved_by_student_id" name="reserved_by_student_id">
            <option value="">Nessuno</option>
            <?php foreach (($students??[]) as $s) { ?>
              <option value="<?php echo $s['id']; ?>" <?php echo (($book['reserved_by_student_id']??null)==$s['id'])?'selected':''; ?>>
                <?php echo htmlspecialchars(trim(($s['last_name']??'').' '.($s['first_name']??''))); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="reserved_by_customer_id">Riservato da Docente</label>
          <select class="form-select" id="reserved_by_customer_id" name="reserved_by_customer_id">
            <option value="">Nessuno</option>
            <?php foreach (($customers??[]) as $c) { ?>
              <option value="<?php echo $c['id']; ?>" <?php echo (($book['reserved_by_customer_id']??null)==$c['id'])?'selected':''; ?>>
                <?php echo htmlspecialchars(trim(($c['last_name']??'').' '.($c['first_name']??''))); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="reserved_at">Data prenotazione</label>
          <input type="datetime-local" class="form-control" id="reserved_at" name="reserved_at" value="<?php echo htmlspecialchars(isset($book['reserved_at']) && $book['reserved_at'] ? date('Y-m-d\TH:i', strtotime($book['reserved_at'])) : ''); ?>">
        </div>
        <div class="col-md-12 mb-3">
          <label class="form-label" for="notes">Note</label>
          <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($book['notes']??''); ?></textarea>
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><?php echo isset($book['id']) ? 'Aggiorna' : 'Salva'; ?></button>
      <a href="<?php echo \App\Core\Helpers::url('/libri'); ?>" class="btn btn-outline-danger">Annulla</a>
    </form>
  </div>
</div>
