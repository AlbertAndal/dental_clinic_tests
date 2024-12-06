<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="row align-items-center min-vh-50 py-5">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">Modern Dental Care for Your Perfect Smile</h1>
            <p class="lead text-secondary mb-4">Schedule your dental appointment with ease. Professional care for all your dental needs.</p>
            <div class="d-grid gap-2 d-md-flex">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="book-appointment.php" class="btn btn-primary btn-lg px-4">Book Appointment</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary btn-lg px-4">Get Started</a>
                    <a href="login.php" class="btn btn-outline-primary btn-lg px-4">Login</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6 d-none d-lg-block">
            <img src="images/dental-hero.svg" alt="Dental Care" class="img-fluid">
        </div>
    </div>

    <section class="py-5">
        <h2 class="text-center mb-5">Our Services</h2>
        <div class="row g-4">
            <?php
            $stmt = $conn->query("SELECT * FROM services LIMIT 6");
            while($service = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
            <div class="col-md-4">
                <div class="card service-card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($service['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold">$<?php echo number_format($service['price'], 2); ?></span>
                            <span class="text-muted"><?php echo $service['duration']; ?> mins</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="py-5 bg-light rounded-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">Why Choose Us?</h2>
                    <div class="mb-4">
                        <h5><i class="fas fa-check-circle text-primary me-2"></i> Modern Equipment</h5>
                        <p class="text-muted">State-of-the-art dental technology for the best care</p>
                    </div>
                    <div class="mb-4">
                        <h5><i class="fas fa-check-circle text-primary me-2"></i> Experienced Team</h5>
                        <p class="text-muted">Highly qualified dentists and friendly staff</p>
                    </div>
                    <div class="mb-4">
                        <h5><i class="fas fa-check-circle text-primary me-2"></i> Comfortable Environment</h5>
                        <p class="text-muted">Relaxing atmosphere with patient comfort in mind</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="images/dental-care.svg" alt="Dental Care Features" class="img-fluid">
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
