<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare(
        'SELECT * FROM parcels
         WHERE parcel_serial LIKE ? OR receiver_mobile LIKE ? OR sender_mobile LIKE ? OR receiver_name LIKE ?
         ORDER BY updated_at DESC'
    );
    $term = "%{$search}%";
    $stmt->execute([$term, $term, $term, $term]);
    $parcels = $stmt->fetchAll();
} else {
    $parcels = $pdo->query('SELECT * FROM parcels ORDER BY updated_at DESC')->fetchAll();
}

$pageTitle = 'Parcels - SafeParcel';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section-pad">
  <div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center mb-4">
      <div>
        <h1 class="fw-bold mb-1">Parcel Records</h1>
        <p class="text-muted mb-0">Add, show, update, and delete parcel booking items.</p>
      </div>
      <a class="btn btn-primary" href="parcel_form.php">Add Parcel</a>
    </div>

    <div class="content-card bg-white p-4">
      <form class="row g-2 mb-3" method="get">
        <div class="col-md-10">
          <input class="form-control" type="search" name="search" value="<?= e($search) ?>" placeholder="Search serial, sender mobile, receiver mobile, or receiver name">
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-outline-primary" type="submit">Search</button>
        </div>
      </form>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Serial</th><th>Sender</th><th>Receiver</th><th>Destination</th><th>Weight</th><th>Bill</th><th>Status</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($parcels as $parcel): ?>
            <tr>
              <td class="fw-semibold"><?= e($parcel['parcel_serial']) ?></td>
              <td><?= e($parcel['sender_name']) ?><br><span class="small text-muted"><?= e($parcel['sender_mobile']) ?></span></td>
              <td><?= e($parcel['receiver_name']) ?><br><span class="small text-muted"><?= e($parcel['receiver_mobile']) ?></span></td>
              <td><?= e($parcel['destination_address']) ?></td>
              <td><?= number_format((float) $parcel['weight_kg'], 2) ?> kg</td>
              <td>$<?= number_format((float) $parcel['bill_amount'], 2) ?></td>
              <td><span class="status-pill status-<?= str_replace(' ', '-', e($parcel['current_status'])) ?>"><?= e($parcel['current_status']) ?></span></td>
              <td class="text-nowrap">
                <a class="btn btn-sm btn-outline-primary" href="parcel_form.php?id=<?= (int) $parcel['id'] ?>">Update</a>
                <a class="btn btn-sm btn-outline-danger" href="parcel_delete.php?id=<?= (int) $parcel['id'] ?>" onclick="return confirm('Delete this parcel?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
