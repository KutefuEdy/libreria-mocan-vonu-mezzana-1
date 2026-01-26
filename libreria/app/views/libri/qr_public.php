<div class="card">
  <div class="card-body">
    <h2 class="mb-3">Dettaglio libro</h2>
    <div class="row">
      <div class="col-md-8">
        <h3 class="mb-1"><?php echo htmlspecialchars($book['title'] ?? ''); ?></h3>
        <p class="mb-2"><strong>Autore:</strong> <?php echo htmlspecialchars($book['author'] ?? ''); ?></p>
        <p class="mb-2"><strong>Codice libro:</strong> <?php echo htmlspecialchars($book['code'] ?? ''); ?></p>
        <p class="mb-2"><strong>Posizione in libreria:</strong> <?php echo !empty($location) ? htmlspecialchars($location) : 'Non specificata'; ?></p>
        <p class="mb-2">
          <strong>Stato prestito:</strong>
          <?php if (!empty($isBorrowed)) { ?>
            <span class="badge bg-danger">In prestito</span>
          <?php } else { ?>
            <span class="badge bg-success">Disponibile</span>
          <?php } ?>
        </p>
        <?php if (!empty($isBorrowed) && !empty($dueDate)) { ?>
          <p class="mb-2"><strong>Data rientro prevista:</strong>
            <?php
              $ts = strtotime($dueDate);
              echo $ts ? date('d/m/Y H:i', $ts) : htmlspecialchars($dueDate);
            ?>
          </p>
        <?php } ?>
      </div>
      <div class="col-md-4 text-center">
        <?php if (!empty($qrPath)) { ?>
          <img src="<?php echo \App\Core\Helpers::url($qrPath); ?>" alt="QR code libro" style="max-width: 260px;">
          <div class="form-text mt-2">Scansiona per aprire questa pagina</div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
