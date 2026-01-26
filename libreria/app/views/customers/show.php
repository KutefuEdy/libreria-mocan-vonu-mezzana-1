<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($customer['last_name'].' '.$customer['first_name']); ?></h5>
            <a href="<?php echo \App\Core\Helpers::url('/customers/'.$customer['id'].'/edit'); ?>" class="btn btn-warning">Modifica</a>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                <p><strong>Note:</strong> <?php echo htmlspecialchars($customer['notes']); ?></p>
            </div>
        </div>

        <!-- sezione PC rimossa -->

        <h5 class="card-title fw-semibold mb-3">Pagamenti bonifico</h5>
        <div class="table-responsive">
          <table class="table table-striped table-bordered text-nowrap">
            <thead class="table-light"><tr><th>Data</th><th>Importo</th><th>Stato</th><th>Contabile</th><th>Riferimento</th></tr></thead>
            <tbody>
            <?php foreach ($payments as $p) { ?>
              <tr>
                <td><?php echo htmlspecialchars($p['paid_at']); ?></td>
                <td>€<?php echo number_format($p['amount'],2,',','.'); ?></td>
                <?php $__pay_labels = ['pending'=>'In attesa','verified'=>'Verificato','rejected'=>'Rifiutato']; $st = $p['status']; ?>
                <td><span class="badge bg-<?php echo $st=='verified'?'success':($st=='rejected'?'danger':'warning'); ?>"><?php echo htmlspecialchars($__pay_labels[$st] ?? $st); ?></span></td>
                <td><?php echo $p['receipt_path'] ? '<a class="btn btn-sm btn-outline-info" href="'.\App\Core\Helpers::url($p['receipt_path']).'" target="_blank">Apri</a>' : ''; ?></td>
                <td><?php echo htmlspecialchars($p['reference']??''); ?></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
