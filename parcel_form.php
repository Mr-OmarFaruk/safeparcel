<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $id > 0;
$error = '';

$parcel = [
    'parcel_serial' => 'SP-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6)),
    'sender_name' => '',
    'sender_mobile' => '',
    'sender_location' => '',
    'product_description' => '',
    'destination_address' => '',
    'weight_kg' => '',
    'sender_remarks' => '',
    'receiver_name' => '',
    'receiver_mobile' => '',
    'receiver_address' => '',
    'bill_per_kg' => '100.00',
    'bill_amount' => '0.00',
    'receiver_remarks' => '',
    'current_status' => 'Booked',
];

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM parcels WHERE id = ?');
    $stmt->execute([$id]);
    $parcel = $stmt->fetch();
    if (!$parcel) {
        header('Location: parcels.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight = (float) ($_POST['weight_kg'] ?? 0);
    $billPerKg = 100.00;
    $billAmount = $weight * $billPerKg;
    $data = [
        'parcel_serial' => trim($_POST['parcel_serial'] ?? ''),
        'sender_name' => trim($_POST['sender_name'] ?? ''),
        'sender_mobile' => trim($_POST['sender_mobile'] ?? ''),
        'sender_location' => trim($_POST['sender_location'] ?? ''),
        'product_description' => trim($_POST['product_description'] ?? ''),
        'destination_address' => trim($_POST['destination_address'] ?? ''),
        'weight_kg' => $weight,
        'sender_remarks' => trim($_POST['sender_remarks'] ?? ''),
        'receiver_name' => trim($_POST['receiver_name'] ?? ''),
        'receiver_mobile' => trim($_POST['receiver_mobile'] ?? ''),
        'receiver_address' => trim($_POST['receiver_address'] ?? ''),
        'bill_per_kg' => $billPerKg,
        'bill_amount' => $billAmount,
        'receiver_remarks' => trim($_POST['receiver_remarks'] ?? ''),
        'current_status' => $_POST['current_status'] ?? 'Booked',
    ];

    try {
        if ($isEdit) {
            $stmt = $pdo->prepare(
                'UPDATE parcels SET parcel_serial=?, sender_name=?, sender_mobile=?, sender_location=?, product_description=?,
                 destination_address=?, weight_kg=?, sender_remarks=?, receiver_name=?, receiver_mobile=?, receiver_address=?,
                 bill_per_kg=?, bill_amount=?, receiver_remarks=?, current_status=?, updated_by=? WHERE id=?'
            );
            $stmt->execute([
                $data['parcel_serial'], $data['sender_name'], $data['sender_mobile'], $data['sender_location'],
                $data['product_description'], $data['destination_address'], $data['weight_kg'], $data['sender_remarks'],
                $data['receiver_name'], $data['receiver_mobile'], $data['receiver_address'], $data['bill_per_kg'],
                $data['bill_amount'], $data['receiver_remarks'], $data['current_status'], $user['id'], $id,
            ]);
            $activity = $pdo->prepare('INSERT INTO parcel_activity (parcel_id, user_id, activity) VALUES (?, ?, ?)');
            $activity->execute([$id, $user['id'], 'Parcel updated. Status: ' . $data['current_status']]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO parcels (parcel_serial, sender_name, sender_mobile, sender_location, product_description,
                 destination_address, weight_kg, sender_remarks, receiver_name, receiver_mobile, receiver_address,
                 bill_per_kg, bill_amount, receiver_remarks, current_status, created_by, updated_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $data['parcel_serial'], $data['sender_name'], $data['sender_mobile'], $data['sender_location'],
                $data['product_description'], $data['destination_address'], $data['weight_kg'], $data['sender_remarks'],
                $data['receiver_name'], $data['receiver_mobile'], $data['receiver_address'], $data['bill_per_kg'],
                $data['bill_amount'], $data['receiver_remarks'], $data['current_status'], $user['id'], $user['id'],
            ]);
            $newId = (int) $pdo->lastInsertId();
            $activity = $pdo->prepare('INSERT INTO parcel_activity (parcel_id, user_id, activity) VALUES (?, ?, ?)');
            $activity->execute([$newId, $user['id'], 'Parcel booked. Status: ' . $data['current_status']]);
        }
        header('Location: parcels.php');
        exit;
    } catch (PDOException $e) {
        $error = 'Unable to save parcel. Parcel serial number must be unique.';
        $parcel = array_merge($parcel, $data);
    }
}

$pageTitle = ($isEdit ? 'Update Parcel' : 'Add Parcel') . ' - SafeParcel';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section-pad">
  <div class="container">
    <div class="content-card bg-white p-4 p-md-5">
      <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
          <h1 class="fw-bold mb-1"><?= $isEdit ? 'Update Parcel' : 'Book New Parcel' ?></h1>
          <p class="text-muted mb-0">Bill is automatically calculated as weight in kg x $100.</p>
        </div>
        <a class="btn btn-outline-secondary align-self-start" href="parcels.php">Back</a>
      </div>
      <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
      <form method="post" class="row g-4">
        <div class="col-12"><h5 class="fw-bold border-bottom pb-2">Parcel & Sender Information</h5></div>
        <div class="col-md-4">
          <label class="form-label">Parcel Serial Number</label>
          <input class="form-control" name="parcel_serial" value="<?= e($parcel['parcel_serial']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Sender Name</label>
          <input class="form-control" name="sender_name" value="<?= e($parcel['sender_name']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Sender Mobile Number</label>
          <input class="form-control" name="sender_mobile" value="<?= e($parcel['sender_mobile']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Sender Location</label>
          <input class="form-control" name="sender_location" value="<?= e($parcel['sender_location']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Weight-in Kg</label>
          <input class="form-control" id="weight" type="number" step="0.01" min="0" name="weight_kg" value="<?= e((string) $parcel['weight_kg']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Product Description</label>
          <textarea class="form-control" name="product_description" rows="3" required><?= e($parcel['product_description']) ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Destination Address</label>
          <textarea class="form-control" name="destination_address" rows="3" required><?= e($parcel['destination_address']) ?></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Sender Remarks</label>
          <textarea class="form-control" name="sender_remarks" rows="2"><?= e($parcel['sender_remarks']) ?></textarea>
        </div>

        <div class="col-12"><h5 class="fw-bold border-bottom pb-2 mt-2">Receiver & Billing Information</h5></div>
        <div class="col-md-4">
          <label class="form-label">Receiver Name</label>
          <input class="form-control" name="receiver_name" value="<?= e($parcel['receiver_name']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Receiver Mobile Number</label>
          <input class="form-control" name="receiver_mobile" value="<?= e($parcel['receiver_mobile']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Current Status</label>
          <select class="form-select" name="current_status">
            <?php foreach (['Booked', 'In Transit', 'Out for Delivery', 'Delivered', 'Cancelled'] as $status): ?>
              <option value="<?= e($status) ?>" <?= $parcel['current_status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Receiver Address</label>
          <textarea class="form-control" name="receiver_address" rows="3" required><?= e($parcel['receiver_address']) ?></textarea>
        </div>
        <div class="col-md-3">
          <label class="form-label">Bill per kg</label>
          <input class="form-control" value="$100.00" readonly>
        </div>
        <div class="col-md-3">
          <label class="form-label">Total Bill</label>
          <input class="form-control" id="billPreview" value="$<?= number_format((float) $parcel['bill_amount'], 2) ?>" readonly>
        </div>
        <div class="col-12">
          <label class="form-label">Receiver Remarks</label>
          <textarea class="form-control" name="receiver_remarks" rows="2"><?= e($parcel['receiver_remarks']) ?></textarea>
        </div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary px-4" type="submit"><?= $isEdit ? 'Update Parcel' : 'Add Parcel' ?></button>
          <a class="btn btn-outline-secondary" href="parcels.php">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</section>
<script>
const weightInput = document.getElementById('weight');
const billPreview = document.getElementById('billPreview');
function updateBill() {
  const weight = Number.parseFloat(weightInput.value || '0');
  billPreview.value = '$' + (weight * 100).toFixed(2);
}
weightInput.addEventListener('input', updateBill);
updateBill();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
