<?php
$pageTitle = 'SafeParcel Courier Service';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <p class="text-uppercase fw-bold text-warning mb-2">Courier Service Parcel Tracking</p>
        <h1 class="display-4 fw-bold mb-3">SafeParcel</h1>
        <p class="lead mb-4">Book parcels, manage delivery records, calculate bills, and let senders or receivers track shipment progress instantly.</p>
        <div class="d-flex flex-wrap gap-2">
          <a href="login.php" class="btn btn-warning btn-lg fw-semibold">Admin / User Login</a>
          <a href="track.php" class="btn btn-outline-light btn-lg">Track Parcel</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-pad">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="content-card bg-white p-4 h-100">
          <h5 class="fw-bold">Parcel Booking</h5>
          <p class="text-muted mb-0">Capture sender, receiver, product, weight, destination, remarks, and automatic bill amount.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="content-card bg-white p-4 h-100">
          <h5 class="fw-bold">Admin & User Dashboard</h5>
          <p class="text-muted mb-0">Both roles can add, show, update, and delete parcel records while viewing recent activity.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="content-card bg-white p-4 h-100">
          <h5 class="fw-bold">Public Tracking</h5>
          <p class="text-muted mb-0">Senders or receivers can track using parcel serial number or receiver mobile number.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
