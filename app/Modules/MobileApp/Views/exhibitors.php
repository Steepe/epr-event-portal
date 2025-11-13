<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 10:39
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

/* ===== Layout ===== */
.exhibitors-container {
    padding: 100px 1.2rem 120px;
    text-align: center;
}

.exhibitors-container h3 {
    font-weight: 700;
    color: #f3bb1a;
    text-transform: uppercase;
    margin-bottom: 2rem;
    text-shadow: 0 0 10px rgba(255, 216, 77, 0.4);
}

/* ===== Grid Layout (2 Columns) ===== */
.exhibitors-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    justify-items: center;
}

@media (max-width: 480px) {
    .exhibitors-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

/* ===== Exhibitor Card ===== */
.exhibitor-card {
    background: rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 18px 12px 60px;
    width: 100%;
    max-width: 180px;
    text-align: center;
    position: relative;
    transition: transform 0.25s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}

.exhibitor-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 18px rgba(239, 177, 30, 0.3);
}

.exhibitor-logo {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: contain;
    background: #fff;
    margin-bottom: 10px;
}

.exhibitor-name {
    font-weight: 600;
    color: #fff;
    font-size: 0.9rem;
    margin-bottom: 4px;
}

.exhibitor-meta {
    font-size: 0.75rem;
    color: #ddd;
    opacity: 0.85;
    line-height: 1.2;
    min-height: 30px;
}

/* ===== Enter Booth Button ===== */
.enter-booth-btn {
    position: absolute;
    bottom: 12px;
    left: 50%;
    transform: translateX(-50%);
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 25px;
    padding: 6px 18px;
    font-size: 0.8rem;
    transition: all 0.3s ease;
    width: 80%;
}

.enter-booth-btn:hover {
    transform: translateX(-50%) scale(1.05);
}

/* ===== Bottom Drawer (Booth Popup) ===== */
.booth-sheet {
    position: fixed;
    left: 0;
    bottom: -100%;
    width: 100%;
    height: 92%;
    background: rgba(21, 0, 32, 0.97);
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    box-shadow: 0 -5px 25px rgba(0,0,0,0.5);
    z-index: 9999;
    transition: bottom 0.4s ease;
    overflow-y: auto;
    padding: 20px;
    color: #fff;
}

.booth-sheet.active {
    bottom: 0;
}

.booth-header {
    text-align: center;
    margin-bottom: 10px;
}

.booth-header h5 {
    color: #f3bb1a;
    margin: 0;
    font-weight: 700;
}

.close-sheet {
    position: absolute;
    top: 12px;
    right: 20px;
    background: none;
    border: none;
    font-size: 26px;
    color: #f3bb1a;
}

/* ===== Booth Video ===== */
.booth-video {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    border-radius: 16px;
    overflow: hidden;
    margin: 15px 0;
}

.booth-video iframe {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    border: none;
}

/* ===== Booth Details ===== */
.booth-summary {
    font-size: 0.9rem;
    line-height: 1.6;
    color: #eee;
    margin-bottom: 15px;
    text-align: left;
}

.booth-promo {
    background: rgba(255, 216, 77, 0.1);
    border: 1px solid rgba(255, 216, 77, 0.4);
    border-radius: 10px;
    padding: 10px;
    margin-top: 10px;
    text-align: left;
}

.contact-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 12px;
    text-align: left;
    margin-top: 15px;
}

.contact-card strong {
    color: #f3bb1a;
    display: block;
}

/* ===== Message Form ===== */
.message-form {
    margin-top: 20px;
    text-align: left;
}

.message-form textarea {
    width: 100%;
    border-radius: 10px;
    border: 1px solid #9D0F82;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    padding: 10px;
    font-size: 0.9rem;
    resize: none;
    outline: none;
    min-height: 100px;
}

.message-form textarea::placeholder {
    color: rgba(255,255,255,0.7);
}

.btn-epr-purple {
    background: linear-gradient(90deg, #9D0F82, #EFB11E);
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 25px;
    padding: 10px 28px;
    transition: all 0.3s ease;
    margin-top: 15px;
}

.btn-epr-purple:hover {
    transform: scale(1.05);
}
</style>

<div class="exhibitors-container">
    <h3>Exhibitors</h3>

    <div class="exhibitors-grid">
        <?php foreach ($exhibitors as $ex): ?>
            <?php $logo = !empty($ex['logo'])
                ? base_url('uploads/exhibitors/' . $ex['logo'])
                : asset_url('images/company-placeholder.png'); ?>

            <div class="exhibitor-card">
                <img src="<?php echo $logo; ?>" class="exhibitor-logo" alt="<?php echo esc($ex['company_name']); ?>">
                <div class="exhibitor-name"><?php echo esc($ex['company_name']); ?></div>
                <div class="exhibitor-meta"><?php echo esc($ex['tagline']); ?></div>

                <button class="enter-booth-btn epr-btn-two" data-id="<?php echo $ex['id']; ?>">Enter Booth</button>

                <!-- Hidden Booth Content -->
                <div class="booth-content" id="booth-<?php echo $ex['id']; ?>" style="display:none;">
                    <div class="booth-header">
                        <h5><?php echo esc($ex['company_name']); ?></h5>
                        <button class="close-sheet">&times;</button>
                    </div>

                    <?php if (!empty($ex['vimeo_id'])): ?>
                    <div class="booth-video">
                        <iframe src="https://player.vimeo.com/video/<?php echo esc($ex['vimeo_id']); ?>" allow="autoplay; fullscreen" allowfullscreen></iframe>
                    </div>
                    <?php endif; ?>

                    <div class="booth-summary"><?php echo nl2br(esc($ex['profile_summary'])); ?></div>

                    <?php if ($ex['has_promotion']): ?>
                        <div class="booth-promo">
                            <strong>Promo:</strong> <?php echo esc($ex['promotion_text']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="contact-card">
                        <strong><?php echo esc($ex['contact_person']); ?></strong>
                        <span><?php echo esc($ex['email']); ?></span><br>
                        <span><?php echo esc($ex['telephone']); ?></span><br>
                        <span><?php echo esc($ex['website']); ?></span>
                    </div>

                    <!-- 💬 Message Form -->
                    <form class="message-form" data-exhibitor="<?php echo $ex['id']; ?>">
                        <textarea name="message" placeholder="Type your message to this exhibitor..." required></textarea>
                        <button type="submit" class="btn-epr-purple">Send Message</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Bottom Sheet -->
<div id="boothSheet" class="booth-sheet"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sheet = document.getElementById('boothSheet');

    // 🟣 Open Booth Drawer
    document.querySelectorAll('.enter-booth-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            sheet.innerHTML = document.getElementById('booth-' + id).innerHTML;
            sheet.classList.add('active');
            sheet.querySelector('.close-sheet').addEventListener('click', () => sheet.classList.remove('active'));

            // Bind form submission dynamically inside the active booth
            const form = sheet.querySelector('.message-form');
            form.addEventListener('submit', async e => {
                e.preventDefault();
                const exhibitor_id = form.dataset.exhibitor;
                const attendee_id = "<?php echo session('user_id'); ?>";
                const message = form.querySelector('textarea').value.trim();
                if (!message) return alert('Please enter a message.');

                const res = await fetch('<?php echo base_url('mobile/exhibitors/send-message'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-API-KEY': '<?php echo env('api.securityKey'); ?>'
                    },
                    body: JSON.stringify({ exhibitor_id, attendee_id, message })
                });

                const data = await res.json();
                if (data.status === 'success') {
                    alert('✅ Message sent successfully!');
                    form.reset();
                    sheet.classList.remove('active');
                } else {
                    alert('❌ Failed to send message.');
                }
            });
        });
    });
});
</script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
