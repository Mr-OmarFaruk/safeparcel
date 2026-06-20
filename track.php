<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$query = trim($_GET['q'] ?? '');
$results = [];
if ($query !== '') {
    $stmt = $pdo->prepare(
        'SELECT * FROM parcels
         WHERE parcel_serial = ? OR receiver_mobile = ?
         ORDER BY updated_at DESC'
    );
    $stmt->execute([$query, $query]);
    $results = $stmt->fetchAll();
}

$pageTitle = 'Track Parcel - SafeParcel';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero" style="min-height: 360px;">
  <div class="container">
    <div class="col-lg-7">
      <h1 class="display-5 fw-bold">Track Your Parcel</h1>
      <p class="lead mb-0">Use a parcel serial number or receiver mobile number to see current delivery details.</p>
    </div>
  </div>
</section>

<section class="section-pad pt-0">
  <div class="container">
    <div class="content-card bg-white p-4 p-md-5 track-panel">
      <form class="row g-2" method="get">
        <div class="col-md-10">
          <input class="form-control form-control-lg" name="q" value="<?= e($query) ?>" placeholder="Enter parcel serial number or receiver mobile number" required>
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-primary btn-lg" type="submit">Track</button>
        </div>
      </form>
    </div>

    <?php if ($query !== ''): ?>
      <div class="mt-4">
      <?php if (!$results): ?>
        <div class="alert alert-warning">No parcel found for "<?= e($query) ?>".</div>
      <?php endif; ?>
      <?php foreach ($results as $parcel): ?>
        <div class="parcel-card bg-white p-4 mb-3">
          <div class="d-flex flex-column flex-md-row justify-content-between gap-3 border-bottom pb-3 mb-3">
            <div>
              <h4 class="fw-bold mb-1"><?= e($parcel['parcel_serial']) ?></h4>
              <p class="text-muted mb-0">Booked on <?= e($parcel['created_at']) ?></p>
            </div>
            <span class="status-pill status-<?= str_replace(' ', '-', e($parcel['current_status'])) ?> align-self-start"><?= e($parcel['current_status']) ?></span>
          </div>
          <div class="row g-4">
            <div class="col-md-4">
              <h6 class="fw-bold">Sender</h6>
              <p class="mb-1"><?= e($parcel['sender_name']) ?></p>
              <p class="mb-1"><?= e($parcel['sender_mobile']) ?></p>
              <p class="text-muted mb-0"><?= e($parcel['sender_location']) ?></p>
            </div>
            <div class="col-md-4">
              <h6 class="fw-bold">Receiver</h6>
              <p class="mb-1"><?= e($parcel['receiver_name']) ?></p>
              <p class="mb-1"><?= e($parcel['receiver_mobile']) ?></p>
              <p class="text-muted mb-0"><?= e($parcel['receiver_address']) ?></p>
            </div>
            <div class="col-md-4">
              <h6 class="fw-bold">Parcel</h6>
              <p class="mb-1"><?= e($parcel['product_description']) ?></p>
              <p class="mb-1">Weight: <?= number_format((float) $parcel['weight_kg'], 2) ?> kg</p>
              <p class="mb-0">Bill: $<?= number_format((float) $parcel['bill_amount'], 2) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
