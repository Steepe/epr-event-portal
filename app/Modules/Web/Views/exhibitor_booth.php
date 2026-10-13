<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$logo = !empty($exhibitor['logo']) ? base_url('uploads/exhibitors_logos/' . $exhibitor['logo']) : asset_url('images/eventslogo.png');
$website = $exhibitor['website'] ?? '';
?>

<main class="rc-page rc-page-center" data-screen-label="Exhibitor Booth">
    <div class="rc-container" style="max-width:1080px">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap">
            <div class="rc-title-stack">
                <span class="rc-kicker">Exhibitor Booth</span>
                <span class="rc-page-title"><?php echo esc($exhibitor['company_name'] ?? 'Exhibitor'); ?></span>
                <span class="rc-page-sub"><?php echo esc($exhibitor['tagline'] ?? 'Meet the exhibitor and start a conversation.'); ?></span>
            </div>
            <a href="<?php echo base_url('attendees/exhibitors'); ?>" class="rc-outline-pill" style="text-decoration:none">
                <i class="fa fa-long-arrow-left" aria-hidden="true"></i>Exhibitors
            </a>
        </div>

        <section class="rc-panel" style="display:grid;grid-template-columns:minmax(0,1.15fr) minmax(320px,.85fr);gap:28px;padding:24px">
            <div style="display:flex;flex-direction:column;gap:18px">
                <div style="position:relative;aspect-ratio:16/9;border-radius:16px;overflow:hidden;background:radial-gradient(circle at 50% 40%, #ffffff 0%, #d9d9d9 42%, #742c76 100%)">
                    <?php if (!empty($exhibitor['vimeo_id'])): ?>
                        <iframe src="https://player.vimeo.com/video/<?php echo esc($exhibitor['vimeo_id']); ?>?dnt=1"
                                allow="autoplay; fullscreen; picture-in-picture"
                                allowfullscreen
                                style="position:absolute;inset:0;width:100%;height:100%;border:0"></iframe>
                    <?php else: ?>
                        <img src="<?php echo $logo; ?>" alt="<?php echo esc($exhibitor['company_name'] ?? 'Exhibitor'); ?>" style="position:absolute;inset:0;margin:auto;max-width:52%;max-height:62%;object-fit:contain">
                    <?php endif; ?>
                </div>

                <?php if (!empty($website)): ?>
                    <a href="<?php echo esc($website); ?>" target="_blank" rel="noopener" class="rc-outline-pill" style="align-self:flex-start;text-decoration:none">
                        <i class="fa fa-external-link" aria-hidden="true"></i><?php echo esc($website); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div style="display:flex;flex-direction:column;gap:18px">
                <div style="display:flex;align-items:center;gap:14px">
                    <img src="<?php echo $logo; ?>" alt="" style="width:72px;height:72px;border-radius:22px;background:#fff;object-fit:contain;padding:8px">
                    <div style="display:flex;flex-direction:column;gap:3px">
                        <span style="font-size:18px;font-weight:700"><?php echo esc($exhibitor['company_name'] ?? 'Exhibitor'); ?></span>
                        <span style="font-size:13px;color:rgba(255,255,255,.7)"><?php echo esc($exhibitor['contact_person'] ?? 'Booth representative'); ?></span>
                    </div>
                </div>

                <form class="exhibitor-message-form" style="display:flex;flex-direction:column;gap:12px">
                    <input class="rc-input" type="text" name="subject" placeholder="Enter Subject" required>
                    <textarea class="rc-input" name="message" rows="6" placeholder="Type your message here..." required style="height:150px;padding:14px!important"></textarea>
                    <input type="hidden" name="exhibitor_id" value="<?php echo esc($exhibitor['id'] ?? ''); ?>">
                    <button type="submit" class="rc-btn" style="text-transform:none;letter-spacing:0">Send Message</button>
                </form>
            </div>
        </section>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
