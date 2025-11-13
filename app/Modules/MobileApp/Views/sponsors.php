<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 11:17
 */

echo module_view('MobileApp', 'includes/header'); ?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', 'Poppins', sans-serif;
        color: #fff;
        background: url('<?php echo asset_url('images/mobile-bg.png'); ?>') no-repeat center center fixed;
        background-size: cover;
        overflow-x: hidden;
    }

    .sponsors-container {
        min-height: 100vh;
        padding: 100px 1.2rem 120px;
        text-align: center;
    }

    h3.sponsors-title {
        font-weight: 700;
        color: #f3bb1a;
        margin-bottom: 1.5rem;
        text-shadow: 0 0 10px rgba(255, 216, 77, 0.4);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Grid layout */
    .sponsor-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        justify-items: center;
    }

    /* Sponsor bubble */
    .sponsor-card {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    .sponsor-card img {
        width: 70%;
        height: 70%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .sponsor-card:hover {
        transform: scale(1.05);
    }

    .sponsor-card:hover img {
        transform: scale(1.1);
    }

    /* Tier effects */
    .sponsor-card.Diamond { box-shadow: 0 0 25px rgba(0, 255, 255, 0.6); }
    .sponsor-card.Platinum { box-shadow: 0 0 25px rgba(239, 177, 30, 0.6); }
    .sponsor-card.Distruptor { box-shadow: 0 0 25px rgba(157, 15, 130, 0.6); }

    /* 🩵 Bottom Drawer Modal */
    .sponsor-modal {
        position: fixed;
        left: 0;
        bottom: -100%;
        width: 100%;
        background: rgba(15, 0, 25, 0.95);
        border-radius: 25px 25px 0 0;
        box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.4);
        color: #fff;
        padding: 25px 1.5rem 60px;
        transition: bottom 0.5s ease;
        z-index: 999;
    }

    .sponsor-modal.active {
        bottom: 0;
    }

    .sponsor-modal-header {
        text-align: center;
        margin-bottom: 15px;
    }

    .sponsor-modal-header img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: contain;
        background: #fff;
        padding: 8px;
    }

    .sponsor-modal h4 {
        color: #f3bb1a;
        font-weight: 700;
        margin-top: 10px;
        font-size: 1.2rem;
    }

    .sponsor-modal p {
        color: #eee;
        font-size: 0.9rem;
        line-height: 1.6;
        text-align: center;
        margin-top: 10px;
    }

    .visit-btn {
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 30px;
        padding: 10px 24px;
        font-size: 0.9rem;
        margin-top: 20px;
        transition: transform 0.3s ease;
    }

    .visit-btn:hover {
        transform: scale(1.05);
    }

    .close-modal {
        position: absolute;
        top: 10px;
        right: 18px;
        color: #fff;
        font-size: 26px;
        cursor: pointer;
        opacity: 0.8;
    }

    .close-modal:hover {
        opacity: 1;
    }
</style>

<div class="sponsors-container">
    <h3 class="sponsors-title">Sponsors</h3>

    <div class="sponsor-grid">
        <?php if (!empty($sponsors)): ?>
            <?php foreach ($sponsors as $sponsor): ?>
                <?php
                    $logo = !empty($sponsor['logo'])
                        ? base_url('uploads/sponsors/' . $sponsor['logo'])
                        : asset_url('images/sponsors/default-logo.png');
                ?>
                <div class="sponsor-card <?php echo esc($sponsor['tier']); ?>"
                     data-name="<?php echo esc($sponsor['name']); ?>"
                     data-logo="<?php echo $logo; ?>"
                     data-desc="<?php echo esc($sponsor['description']); ?>"
                     data-website="<?php echo esc($sponsor['website']); ?>"
                     onclick="openSponsorModal(this)">
                    <img src="<?php echo $logo; ?>" alt="<?php echo esc($sponsor['name']); ?>">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-light">No sponsors found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- 🩵 Bottom Drawer Modal -->
<div id="sponsorModal" class="sponsor-modal">
    <span class="close-modal" onclick="closeSponsorModal()">&times;</span>
    <div class="sponsor-modal-header">
        <img id="modalLogo" src="" alt="Sponsor Logo">
        <h4 id="modalName"></h4>
    </div>
    <p id="modalDescription"></p>

    <div class="text-center">
        <button id="visitButton" class="visit-btn epr-btn-two">Visit Website</button>
    </div>
</div>

<?php echo module_view('MobileApp', 'includes/footer'); ?>

<script>
function openSponsorModal(el) {
    const modal = document.getElementById('sponsorModal');
    document.getElementById('modalLogo').src = el.dataset.logo;
    document.getElementById('modalName').textContent = el.dataset.name;
    document.getElementById('modalDescription').textContent = el.dataset.desc || 'No description available.';

    const websiteBtn = document.getElementById('visitButton');
    if (el.dataset.website) {
        websiteBtn.onclick = () => window.open(el.dataset.website, '_blank');
        websiteBtn.style.display = 'inline-block';
    } else {
        websiteBtn.style.display = 'none';
    }

    modal.classList.add('active');
}

function closeSponsorModal() {
    document.getElementById('sponsorModal').classList.remove('active');
}
</script>
