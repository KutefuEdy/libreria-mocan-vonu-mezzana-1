<h3 class="mb-3">Libri</h3>
<div class="row mb-3">
  <div class="col-md-3">
    <div class="card bg-primary-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-primary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:book-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Numero libri</h5>
        <h2 class="card-text text-primary text-center metric-value" data-metric="total"><?php echo (int)($summary['total'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-success-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-success flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:check-circle-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Libri disponibili</h5>
        <h2 class="card-text text-success text-center metric-value" data-metric="available"><?php echo (int)($summary['available'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-warning-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-warning flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:clock-circle-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Libri prestati</h5>
        <h2 class="card-text text-warning text-center metric-value" data-metric="borrowed"><?php echo (int)($summary['borrowed'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-info-subtle">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-info flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:bookmark-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Libri riservati</h5>
        <h2 class="card-text text-info text-center metric-value" data-metric="reserved"><?php echo (int)($summary['reserved'] ?? 0); ?></h2>
      </div>
    </div>
  </div>
</div>
<?php if (\App\Core\Auth::isAdmin()) { ?>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?php echo \App\Core\Helpers::url('/libri/create'); ?>">Nuovo Libro</a></div>
<?php } ?>
<div class="table-responsive">
  <table id="libriTable" class="table table-striped table-bordered text-nowrap">
    <thead class="table-light"><tr><th>Codice</th><th>Titolo</th><th>Autore</th><th>Genere</th><th>Anno</th><th>Stato</th><th>Destinatario</th><th>Data prestito</th><th>Restituzione</th><th>Note</th><?php if (\App\Core\Auth::isAdmin()) { ?><th>Azioni</th><?php } ?></tr></thead>
    <tbody>
    <?php foreach ($books as $b) { ?>
      <tr>
        <td><?php echo htmlspecialchars($b['code']); ?></td>
        <td><?php echo htmlspecialchars($b['title']); ?></td>
        <td><?php echo htmlspecialchars($b['author']); ?></td>
        <td><?php echo htmlspecialchars($b['genre']??''); ?></td>
        <td><?php echo htmlspecialchars($b['year']??''); ?></td>
        <td><span class="badge bg-<?php echo $b['status']==='available'?'success':($b['status']==='borrowed'?'warning':($b['status']==='reserved'?'info':'secondary')); ?>">
          <?php echo htmlspecialchars(($b['status']==='available')?'Disponibile':(($b['status']==='borrowed')?'Prestato':(($b['status']==='reserved')?'Riservato':$b['status']))); ?>
        </span></td>
        <td>
          <?php
            $dest = '';
            if (($b['status'] ?? '') === 'borrowed') {
              if (!empty($b['holder_customer_id'])) {
                $cust = null;
                foreach (($customers ?? []) as $c) { if ((int)$c['id']===(int)$b['holder_customer_id']) { $cust=$c; break; } }
                $dest = $cust ? trim(($cust['last_name']??'').' '.($cust['first_name']??'')) : '';
              } elseif (!empty($b['holder_student_id'])) {
                $stud = null;
                foreach (($students ?? []) as $s) { if ((int)$s['id']===(int)$b['holder_student_id']) { $stud=$s; break; } }
                $dest = $stud ? trim(($stud['last_name']??'').' '.($stud['first_name']??'')) : '';
              }
            } elseif (($b['status'] ?? '') === 'reserved') {
              if (!empty($b['reserved_by_customer_id'])) {
                $cust = null;
                foreach (($customers ?? []) as $c) { if ((int)$c['id']===(int)$b['reserved_by_customer_id']) { $cust=$c; break; } }
                $dest = $cust ? ('Riservato da '.$cust['last_name'].' '.$cust['first_name']) : '';
              } elseif (!empty($b['reserved_by_student_id'])) {
                $stud = null;
                foreach (($students ?? []) as $s) { if ((int)$s['id']===(int)$b['reserved_by_student_id']) { $stud=$s; break; } }
                $dest = $stud ? ('Riservato da '.$stud['last_name'].' '.$stud['first_name']) : '';
              }
            }
            echo htmlspecialchars($dest);
          ?>
        </td>
        <td><?php echo htmlspecialchars(isset($b['borrowed_at']) ? $b['borrowed_at'] : ''); ?></td>
        <td><?php echo htmlspecialchars(isset($b['due_at']) ? $b['due_at'] : ''); ?></td>
        <td><?php echo htmlspecialchars($b['notes']??''); ?></td>
        <?php if (\App\Core\Auth::isAdmin()) { ?>
        <td>
          <a class="btn btn-sm btn-outline-secondary ms-1" href="<?php echo \App\Core\Helpers::url('/libri/'.$b['id'].'/edit'); ?>">Modifica</a>
          <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteBookModal" data-id="<?php echo $b['id']; ?>" data-title="<?php echo htmlspecialchars($b['title']); ?>">Elimina</button>
        </td>
        <?php } ?>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    if (!window.jQuery) return;
    var $ = window.jQuery;
    $('#libriTable').DataTable({
      responsive: true,
      deferRender: true,
      autoWidth: false,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "Tutti"]],
      order: [],
      columnDefs: [
        { targets: -1, orderable: false, searchable: false }
      ],
      dom: 'B<"d-flex justify-content-end align-items-center"f>rt<"d-flex justify-content-between align-items-center mt-2"l i p>',
      buttons: [
        { extend: 'copy', text: 'Copia', className: 'btn btn-outline-primary' },
        { extend: 'csv', text: 'CSV', className: 'btn btn-outline-primary' },
        { extend: 'excel', text: 'Excel', className: 'btn btn-outline-primary' },
        { extend: 'pdf', text: 'PDF', className: 'btn btn-outline-primary' },
        { extend: 'print', text: 'Stampa', className: 'btn btn-outline-primary' },
        { extend: 'colvis', text: 'Colonne', className: 'btn btn-outline-primary' }
      ],
      language: {
        search: 'Cerca:',
        lengthMenu: 'Mostra _MENU_ righe',
        info: 'Mostra da _START_ a _END_ di _TOTAL_',
        infoEmpty: 'Nessun record',
        zeroRecords: 'Nessun risultato trovato',
        loadingRecords: 'Caricamento...',
        processing: 'Elaborazione...',
        paginate: { first: 'Prima', last: 'Ultima', next: 'Successiva', previous: 'Precedente' }
      }
    });
    var t = document.getElementById('libriTable');
    var wid = t.id + '_search';
    var wrap = $(t).closest('.dataTables_wrapper');
    var lbl = wrap.find('.dataTables_filter label');
    var inp = lbl.find('input');
    inp.attr({ id: wid, name: wid, 'aria-label': 'Cerca libri' });
    lbl.attr('for', wid);
    var lsel = wrap.find('.dataTables_length select');
    var lid = t.id + '_length';
    lsel.attr({ id: lid, name: lid, 'aria-label': 'Numero righe' });
    fetch('<?php echo \App\Core\Helpers::url('/api/view-cards'); ?>?scope=libri').then(function(r){ return r.json(); }).then(function(data){
      if (data && data.metrics) {
        var ms = document.querySelectorAll('.metric-value');
        ms.forEach(function(el){
          var m = el.getAttribute('data-metric');
          if (m && Object.prototype.hasOwnProperty.call(data.metrics, m)) { el.textContent = parseInt(data.metrics[m], 10) || 0; }
        });
      }
    }).catch(function(){});
  });
</script>
<?php if (\App\Core\Auth::isAdmin()) { ?>
<div class="modal fade" id="deleteBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteBookForm" action="" method="post">
        <input type="hidden" name="csrf" value="<?php echo \App\Core\CSRF::token(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Conferma eliminazione</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Eliminare il libro <span id="delBookTitle"></span>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-danger">Elimina</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  document.getElementById('deleteBookModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-title');
    document.getElementById('deleteBookForm').setAttribute('action', '<?php echo \App\Core\Helpers::url('/libri/'); ?>' + id + '/delete');
    document.getElementById('delBookTitle').textContent = name || '';
  });
</script>
<?php } ?>
