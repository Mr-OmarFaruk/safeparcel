<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$total = (int) $pdo->query('SELECT COUNT(*) FROM parcels')->fetchColumn();
$delivered = (int) $pdo->query("SELECT COUNT(*) FROM parcels WHERE current_status = 'Delivered'")->fetchColumn();
$transit = (int) $pdo->query("SELECT COUNT(*) FROM parcels WHERE current_status IN ('In Transit', 'Out for Delivery')")->fetchColumn();
$revenue = (float) $pdo->query('SELECT COALESCE(SUM(bill_amount), 0) FROM parcels')->fetchColumn();

$recentParcels = $pdo->query('SELECT * FROM parcels ORDER BY updated_at DESC LIMIT 8')->fetchAll();
$activities = $pdo->query(
    'SELECT parcel_activity.*, parcels.parcel_serial, users.name AS user_name
     FROM parcel_activity
     JOIN parcels ON parcels.id = parcel_activity.parcel_id
     LEFT JOIN users ON users.id = parcel_activity.user_id
     ORDER BY parcel_activity.created_at DESC
     LIMIT 10'
)->fetchAll();

$pageTitle = 'Dashboard - SafeParcel';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section-pad">
  <div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center mb-4">
      <div>
        <h1 class="fw-bold mb-1">Dashboard</h1>
        <p class="text-muted mb-0">All parcel booking and tracking activity at a glance.</p>
      </div>
      <a class="btn btn-primary" href="parcel_form.php">Add New Parcel</a>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-md-3"><div class="metric-card bg-white p-4"><p class="text-muted mb-1">Total Parcels</p><h2 class="fw-bold mb-0"><?= $total ?></h2></div></div>
      <div class="col-md-3"><div class="metric-card bg-white p-4"><p class="text-muted mb-1">In Movement</p><h2 class="fw-bold mb-0"><?= $transit ?></h2></div></div>
      <div class="col-md-3"><div class="metric-card bg-white p-4"><p class="text-muted mb-1">Delivered</p><h2 class="fw-bold mb-0"><?= $delivered ?></h2></div></div>
      <div class="col-md-3"><div class="metric-card bg-white p-4"><p class="text-muted mb-1">Total Bill</p><h2 class="fw-bold mb-0">$<?= number_format($revenue, 2) ?></h2></div></div>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <div class="content-card bg-white p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Recent Parcels</h5>
            <a href="parcels.php" class="btn btn-outline-primary btn-sm">Show All</a>
          </div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead><tr><th>Serial</th><th>Receiver</th><th>Mobile</th><th>Bill</th><th>Status</th></tr></thead>
              <tbody>
              <?php foreach ($recentParcels as $parcel): ?>
                <tr>
                  <td class="fw-semibold"><?= e($parcel['parcel_serial']) ?></td>
                  <td><?= e($parcel['receiver_name']) ?></td>
                  <td><?= e($parcel['receiver_mobile']) ?></td>
                  <td>$<?= number_format((float) $parcel['bill_amount'], 2) ?></td>
                  <td><span class="status-pill status-<?= str_replace(' ', '-', e($parcel['current_status'])) ?>"><?= e($parcel['current_status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="content-card bg-white p-4">
          <h5 class="fw-bold mb-3">Activity</h5>
          <?php foreach ($activities as $activity): ?>
            <div class="border-bottom pb-3 mb-3">
              <div class="fw-semibold"><?= e($activity['parcel_serial']) ?></div>
              <div class="small text-muted"><?= e($activity['activity']) ?></div>
              <div class="small text-muted">By <?= e($activity['user_name'] ?? 'System') ?>, <?= e($activity['created_at']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
