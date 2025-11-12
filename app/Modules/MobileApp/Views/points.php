<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 11:47
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

    .points-container {
        min-height: 100vh;
        padding: 90px 1.5rem 120px;
        text-align: center;
    }

    .points-title {
        font-weight: 700;
        color: #9D0F82;
        margin-bottom: 0.5rem;
        text-shadow: 0 0 10px rgba(255, 216, 77, 0.5);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .points-subtitle {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 1.8rem;
    }

    /* Responsive Grid */
    .points-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 25px;
        justify-items: center;
    }

    /* Card Styling */
    .points-card {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 24px 10px 55px; /* Add bottom padding to balance button space */
        width: 100%;
        max-width: 200px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(5px);
        position: relative;
        overflow: hidden;
    }

    .points-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(255, 216, 77, 0.4);
    }

    .points-card img {
        width: 65px;
        height: 65px;
        object-fit: contain;
        margin-bottom: 15px;
    }

    .points-card h5 {
        color: #fff;
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 14px;
        line-height: 1.4;
        min-height: 42px; /* keeps text area uniform */
    }

    /* Fixed Button Position */
    .points-card button {
        border: none;
        color: #fff;
        font-weight: 600;
        border-radius: 25px;
        padding: 8px 13px;
        font-size: 0.8rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
    }

    .points-card button:hover {
        transform: translateX(-50%) scale(1.05);
        box-shadow: 0 0 15px rgba(239, 177, 30, 0.5);
    }

    /* Responsive */
    @media (max-width: 480px) {
        .points-card {
            max-width: 160px;
            padding: 18px 8px 60px;
        }
        .points-card img {
            width: 55px;
            height: 55px;
        }
        .points-card h5 {
            font-size: 0.85rem;
            min-height: 40px;
        }
    }
</style>

<div class="points-container">
    <h3 class="points-title">Earn Points</h3>
    <p class="points-subtitle">Complete activities and earn reward points to boost your experience.</p>

    <div class="points-grid">
        <?php if (!empty($point_guides)): ?>
            <?php foreach ($point_guides as $guide): ?>
                <?php $image_file = asset_url('images/' . $guide['image']); ?>
                <div class="points-card">
                    <img src="<?php echo $image_file; ?>" alt="<?php echo esc($guide['activity']); ?>">
                    <h5><?php echo esc($guide['activity']); ?></h5>
                    <button class="epr-btn-two">Earn <?php echo esc($guide['points']); ?> Points</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-light mt-5">No activities available yet. Check back soon!</p>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
