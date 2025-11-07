<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 03/11/2025
 * Time: 02:56
 */


echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<style>
body {
  background: url('<?php echo asset_url('images/brain-Events9-Portal-2025.png'); ?>') no-repeat center center fixed;
  background-size: cover;
  font-family: 'Poppins', sans-serif;
  color: #fff;
  overflow-x: hidden;
  text-align: center;
}

/* ===== Container ===== */
.emergence-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 120px 10%;
}

/* ===== Title ===== */
.emergence-container h2 {
  font-weight: 700;
  color: #fff;
  text-transform: uppercase;
  margin-bottom: 80px;
  letter-spacing: 1px;
}

/* ===== Grid layout ===== */
.emergence-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 100px;
  align-items: center;
  justify-items: center;
  width: 100%;
  max-width: 1200px;
}

/* ===== Icon items ===== */
.emergence-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
  color: #fff;
  transition: transform 0.3s ease;
}

.emergence-item:hover {
  transform: scale(1.07);
}

/* ===== Icon images ===== */
.emergence-item img {
  width: 220px;
  height: 220px;
  object-fit: contain;
  border-radius: 50%;
  transition: all 0.3s ease;
  box-shadow: 0 0 0 rgba(239, 177, 30, 0);
}

.emergence-item:hover img {
  box-shadow: 0 0 30px rgba(239, 177, 30, 0.5);
}

/* ===== Labels ===== */
.emergence-item span {
  margin-top: 20px;
  font-size: 16px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* ===== Mobile responsiveness ===== */
@media (max-width: 768px) {
  .emergence-item img {
    width: 160px;
    height: 160px;
  }

  .emergence-item span {
    font-size: 14px;
  }

  .emergence-container {
    padding: 100px 5%;
  }
}
</style>

<div class="emergence-container">
  <h2>EMERGENCE BOOTH</h2>

  <div class="emergence-grid">

    <!-- Need Assistance -->
    <a href="https://wa.me/2348033868618" target="_blank" class="emergence-item">
      <img src="<?php echo asset_url('images/assistance.png'); ?>" alt="Need Assistance">
      <span>NEED ASSISTANCE?</span>
    </a>

    <!-- Want Some Merch -->
    <a href="https://shop.eprglobal.com" target="_blank" class="emergence-item">
      <img src="<?php echo asset_url('images/merch.png'); ?>" alt="Want Some Merch">
      <span>WANT SOME MERCH?</span>
    </a>

    <!-- Join The Community -->
    <a href="https://community.eprglobal.com" target="_blank" class="emergence-item">
      <img src="<?php echo asset_url('images/community.png'); ?>" alt="Join the Community">
      <span>JOIN THE COMMUNITY</span>
    </a>

  </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
