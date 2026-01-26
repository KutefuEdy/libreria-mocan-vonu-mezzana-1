<h2 class="mb-4">Dashboard</h2>

<!-- Sezione PC rimossa -->

<div class="row mt-2">
  <div class="col-lg-4">
    <div class="card bg-secondary-subtle" style="border: none; margin-bottom: 20px;">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-secondary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:users-group-rounded-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Docenti totali</h5>
        <h2 class="card-text text-secondary text-center metric-value" data-metric="customers_total"><?php echo $counts['customers_total']; ?></h2>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card bg-primary-subtle" style="border: none; margin-bottom: 20px;">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-primary flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:user-circle-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Studenti totali</h5>
        <h2 class="card-text text-primary text-center metric-value" data-metric="students_total"><?php echo $counts['students_total']; ?></h2>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card" style="background-color: #cff4fc; border: none; margin-bottom: 20px;">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center round-48 rounded text-bg-info flex-shrink-0 mb-3 mx-auto">
          <iconify-icon icon="solar:users-group-two-rounded-line-duotone" class="icon-24 text-white"></iconify-icon>
        </div>
        <h5 class="card-title fw-semibold text-center mb-1">Gruppi totali</h5>
        <h2 class="card-text text-info text-center metric-value" data-metric="groups_total"><?php echo $counts['groups_total']; ?></h2>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    fetch('<?php echo \App\Core\Helpers::url('/api/view-cards'); ?>?scope=dashboard')
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (data && data.metrics) {
          var ms = document.querySelectorAll('.metric-value');
          ms.forEach(function(el){
            var m = el.getAttribute('data-metric');
            if (m && Object.prototype.hasOwnProperty.call(data.metrics, m)) { 
              el.textContent = parseInt(data.metrics[m], 10) || 0; 
            }
          });
        }
      }).catch(function(e){ console.error("Errore API:", e); });
  });
</script>
