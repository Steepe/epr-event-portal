<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 19:07
 */
?>

<!-- MobileApp Topbar -->
<style>
/* Base layout */
.topbar {
    position: fixed;
    top: 0;
    width: 100%;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.topbar img {
    height: 35px;
}

/* Hamburger */
.menu-toggle {
    background: transparent;
    border: none;
    cursor: pointer;
    outline: none;
}
.menu-toggle span {
    display: block;
    width: 26px;
    height: 3px;
    margin: 5px;
    background: #fff;
    border-radius: 3px;
    transition: 0.4s;
}

/* Overlay menu */
.menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 0;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(12px);
    overflow: hidden;
    transition: height 0.4s ease;
    z-index: 999;
}
.menu-overlay.open {
    height: 100vh;
}

/* Menu content */
.menu-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    opacity: 0;
    transition: opacity 0.4s ease;
}
.menu-overlay.open .menu-content {
    opacity: 1;
}

.menu-content a {
    color: #fff;
    text-decoration: none;
    font-size: 18px;
    margin: 12px 0;
    transition: all 0.3s ease;
    font-weight: 500;
    letter-spacing: 0.5px;
}
.menu-content a:hover {
    color: #EFB11E;
    transform: scale(1.05);
}

/* Animated toggle transformation */
.menu-toggle.active span:nth-child(1) {
transform: rotate(45deg) translate(5px, 6px);
}
.menu-toggle.active span:nth-child(2) {
opacity: 0;
}
.menu-toggle.active span:nth-child(3) {
transform: rotate(-45deg) translate(5px, -6px);
}

/* Logo text for fallback */
.logo-text {
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    letter-spacing: 0.5px;
}
</style>

<div class="topbar">
    <img src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="EPR Global Logo">
    <button class="menu-toggle" id="menuToggle">
        <span></span><span></span><span></span>
    </button>
</div>

<div class="menu-overlay" id="menuOverlay">
    <div class="menu-content">
        <a href="<?php echo base_url('mobile/lobby'); ?>">🏠 Lobby</a>
        <a href="<?php echo base_url('mobile/exhibitors'); ?>">🎪 Exhibitors</a>
        <a href="<?php echo base_url('mobile/sponsors'); ?>">💎 Sponsors</a>
        <a href="<?php echo base_url('mobile/agenda'); ?>">🗓️ Agenda</a>
        <a href="<?php echo base_url('mobile/earn-points'); ?>">⭐ Earn Points</a>
        <a href="<?php echo base_url('mobile/emergence-booth'); ?>">🎤 Emergence Booth</a>
        <a href="<?php echo base_url('mobile/profile'); ?>">👤 Profile</a>
        <a href="<?php echo base_url('mobile/logout'); ?>">🚪 Logout</a>
    </div>
</div>

<script>
const toggle = document.getElementById("menuToggle");
const overlay = document.getElementById("menuOverlay");

toggle.addEventListener("click", () => {
    toggle.classList.toggle("active");
    overlay.classList.toggle("open");
});

// Close on outside click
overlay.addEventListener("click", (e) => {
    if (e.target === overlay) {
        overlay.classList.remove("open");
        toggle.classList.remove("active");
    }
});
</script>
