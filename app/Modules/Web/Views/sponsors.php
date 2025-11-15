<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 21:41
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<style>
body {
    background: url('<?php echo asset_url('images/brain-Events6-Portal-2025.png'); ?>') no-repeat center center fixed;
    background-size: cover;
    overflow-x: hidden;
    font-family: 'Poppins', sans-serif;
    color: #fff;
}

/* Container */
.sponsors-container {
    padding: 120px 10%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

/* Page title */
.sponsors-container h2 {
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    margin-bottom: 70px;
    letter-spacing: 1px;
}

/* Grid layout */
.sponsor-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 60px;
    justify-items: center;
    align-items: center;
    width: 90%;
    max-width: 1000px;
}

/* Circular sponsor bubble */
.sponsor-card {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 25px rgba(255, 255, 255, 0.2);
    transition: all 0.4s ease;
    overflow: hidden;
    position: relative;
}

.sponsor-card:hover {
    transform: scale(1.07);
    box-shadow: 0 0 35px rgba(239, 177, 30, 0.4);
}

/* Logo inside the circle */
.sponsor-card img {
    width: 70%;
    height: auto;
    object-fit: contain;
    transition: transform 0.4s ease;
}

.sponsor-card:hover img {
    transform: scale(1.1);
}

/* Subtle glow overlay on hover */
.sponsor-card::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: radial-gradient(circle at center, rgba(239,177,30,0.2), transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
}
.sponsor-card:hover::after {
    opacity: 1;
}

.sponsor-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 50%;
    padding: 4px;
    background: radial-gradient(circle at 50% 50%, #bf159f, #790584);
    -webkit-mask:
            linear-gradient(#fff 0 0) content-box,
            linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
}
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}
.sponsor-card {
    animation: float 6s ease-in-out infinite;
}

.sponsor-card.platinum { box-shadow: 0 0 25px rgba(255, 255, 255, 0.6); }
.sponsor-card.gold { box-shadow: 0 0 25px rgba(239,177,30,0.5); }
.sponsor-card.silver { box-shadow: 0 0 25px rgba(200,200,200,0.4); }

</style>

<div class="sponsors-container">
    <h2>SPONSORS</h2>

    <div class="sponsor-grid">
        <?php if (!empty($sponsors)): ?>
            <?php foreach ($sponsors as $sponsor): ?>
                <div class="sponsor-card <?php echo strtolower($sponsor['tier']); ?>" title="<?php echo esc($sponsor['name']); ?>">
                    <?php if (!empty($sponsor['website'])): ?>
                        <a href="<?php echo esc($sponsor['website']); ?>" target="_blank">
                            <img src="<?php echo base_url('uploads/sponsors/' . $sponsor['logo']); ?>"
                                 alt="<?php echo esc($sponsor['name']); ?>"
                                 onerror="this.src='<?php echo asset_url('images/sponsors/default-logo.png'); ?>';">
                        </a>
                    <?php else: ?>
                        <img loading="lazy" src="<?php echo base_url('uploads/sponsors/' . $sponsor['logo']); ?>"
                             alt="<?php echo esc($sponsor['name']); ?>"
                             onerror="this.src='<?php echo asset_url('images/sponsors/default-logo.png'); ?>';">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-light">No sponsors found.</p>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>

</body>
</html>