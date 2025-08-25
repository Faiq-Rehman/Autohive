<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

// Fetch unique cities
$cities = [];
$city_query = $conn->query("SELECT DISTINCT city FROM ev_stations ORDER BY city ASC");
while ($city_row = $city_query->fetch_assoc()) {
    $cities[] = $city_row['city'];
}

// Get selected city & stations
$selected_city = isset($_GET['city']) ? trim($_GET['city']) : '';
if ($selected_city !== '') {
    $stmt = $conn->prepare("SELECT * FROM ev_stations WHERE city = ? ORDER BY name ASC");
    $stmt->bind_param("s", $selected_city);
    $stmt->execute();
    $stations = $stmt->get_result();

    // If city has stations, get average rate
    $rate_stmt = $conn->prepare("SELECT AVG(electricity_rate) AS avg_rate FROM ev_stations WHERE city = ? AND electricity_rate IS NOT NULL");
    $rate_stmt->bind_param("s", $selected_city);
    $rate_stmt->execute();
    $rate_res = $rate_stmt->get_result();
    if ($rate_row = $rate_res->fetch_assoc()) {
        if (!empty($rate_row['avg_rate'])) {
            $rate = round($rate_row['avg_rate'], 2);
        }
    }
} else {
    $stations = $conn->query("SELECT * FROM ev_stations ORDER BY city ASC, name ASC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EV Tools - AutoHive</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />


<!-- ✅ AOS Animation CSS -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

<style>
    body { background-color: white; }
    .tool-card { 
        background: #fff; 
        border-radius: 10px; 
        padding: 20px; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        margin-bottom: 30px; 
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .tool-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    h4 { color: #d90429; }
    .badge-fast { background-color: #28a745; }
    .badge-normal { background-color: #6c757d; }
    .containers { padding-top: 80px; padding-bottom: 40px; }
    table th, table td { vertical-align: middle; white-space: nowrap; }
    .table-responsive { overflow-x: auto; }

    /* hero-section */
    .hero {
      background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('extraimages/evtoolsbanner.jpg') center/cover no-repeat;
      padding: 100px 20px;
      color: white;
      text-align: center;
    }

     @media (max-width: 768px) {
      .hero {
        margin-top: 90px;
      }}

    .lead {
      font-size: 1.5rem;
    }

    .section-title {
      font-weight: 700;
      margin-bottom: 40px;
      text-transform: uppercase;
    }
</style>
<link rel="stylesheet" href="css/footer.css">
<link rel="stylesheet" href="css/navbar.css">
</head>
<body>

    <!-- Hero Section -->
    <section class="hero" data-aos="fade-down" data-aos-duration="1200">
        <div class="container">
            <h1 class="display-4 fw-bold text-uppercase">EV Tools | AUTOHIVE</h1>
            <p class="lead">Driving the future forward with powerful, reliable, and innovative EV tools for every electric journey.</p>
        </div>
    </section>

<div class="container my-5">

    <!-- Range & Cost Calculator -->
    <div class="tool-card" data-aos="fade-up" data-aos-delay="100">
        <h4>Range & Cost Calculator</h4>
        <p>Calculate how far your EV can go and how much it costs to fully charge it.</p>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Battery Capacity (kWh)</label>
                <input type="number" id="batteryCapacity" class="form-control" placeholder="e.g. 40">
            </div>
            <div class="col-md-4">
                <label class="form-label">Electricity Rate (PKR/kWh)</label>
                <input type="number" id="electricityRate" class="form-control" value="<?php echo isset($rate) ? htmlspecialchars($rate) : ''; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Efficiency (km/kWh)</label>
                <input type="number" id="efficiency" class="form-control" placeholder="e.g. 6">
            </div>
        </div>
        <button class="btn btn-danger mt-3" onclick="calculateEV()">Calculate</button>
        <div class="mt-3">
            <strong>Full-Charge Cost:</strong> <span id="costResult">-</span><br>
            <strong>Estimated Range:</strong> <span id="rangeResult">-</span>
        </div>
    </div>

    <!-- EV Tips Section -->
    <div class="tool-card" data-aos="fade-up" data-aos-delay="200">
        <h4>EV Maintenance & Charging Tips</h4>
        <ul>
            <li>Charge your EV between 20% and 80% to prolong battery life.</li>
            <li>Use fast chargers sparingly to avoid battery degradation.</li>
            <li>Check tire pressure regularly for better efficiency.</li>
            <li>Plan your trips to include charging stations when needed.</li>
            <li>Keep your EV software updated for better performance.</li>
        </ul>
    </div>

    <!-- Charging Stations List -->
    <div class="tool-card" data-aos="fade-up" data-aos-delay="300">
        <h4>EV Charging Stations in Pakistan</h4>
        <form method="GET" class="mb-3">
            <label class="form-label">Filter by City:</label>
            <div class="input-group">
                <select name="city" class="form-select" onchange="this.form.submit()">
                    <option value="">All Cities</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo htmlspecialchars($city); ?>" <?php if ($selected_city === $city) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($city); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($selected_city): ?>
                    <a href="ev_tools.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>

<?php if ($stations->num_rows > 0): ?>
    <div class="table-responsive" data-aos="fade-in" data-aos-delay="400">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>City</th>
                    <th>Location</th>
                    <th>Ports</th>
                    <th>Fast Charging</th>
                    <th>Rate (PKR/kWh)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($station = $stations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($station['name']); ?></td>
                        <td><?php echo htmlspecialchars($station['city']); ?></td>
                        <td><?php echo htmlspecialchars($station['location']); ?></td>
                        <td><?php echo (int)$station['ports']; ?></td>
                        <td><?php echo $station['fast_charging'] ? '<span class="badge badge-fast">Yes</span>' : '<span class="badge badge-normal">No</span>'; ?></td>
                        <td>
                            <?php echo $station['electricity_rate'] ? 'PKR ' . number_format($station['electricity_rate'], 2) : '-'; ?>
                        </td>
                        <td>
                            <?php if (!empty($station['location'])): ?>
                                <a href="https://www.google.com/maps/search/<?php echo urlencode($station['location']); ?>" target="_blank" class="btn btn-sm btn-primary">Map</a>
                            <?php endif; ?>
                            <?php if ($station['electricity_rate']): ?>
                                <button type="button" class="btn btn-sm btn-success" onclick="setRate(<?php echo $station['electricity_rate']; ?>)">Use Rate</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>No charging stations found for this city.</p>
<?php endif; ?>
    </div>
</div>

<!-- ✅ Footer Animated -->
<div data-aos="fade-up" data-aos-duration="1200">
    <?php include "footer.php"; ?>
</div>

<!-- ✅ Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init();

function calculateEV() {
    let capacity = parseFloat(document.getElementById("batteryCapacity").value);
    let rate = parseFloat(document.getElementById("electricityRate").value);
    let efficiency = parseFloat(document.getElementById("efficiency").value);

    if (isNaN(capacity) || isNaN(rate) || isNaN(efficiency) || capacity <= 0 || rate <= 0 || efficiency <= 0) {
        alert("Please enter valid positive numbers for all fields.");
        return;
    }

    let cost = capacity * rate;
    let range = capacity * efficiency;

    document.getElementById("costResult").innerText = "PKR " + Number(cost.toFixed(2)).toLocaleString();
    document.getElementById("rangeResult").innerText = range.toFixed(2) + " km";
}

function setRate(val) {
    document.getElementById("electricityRate").value = val;
}
</script>
</body>
</html>