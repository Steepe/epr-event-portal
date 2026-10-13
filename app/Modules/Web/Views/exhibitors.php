<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<main class="rc-page rc-page-center" data-screen-label="Exhibitors">
    <div class="rc-container">
        <div class="rc-title-stack">
            <span class="rc-kicker">Accelerate 2026</span>
            <span class="rc-page-title">Exhibitors</span>
            <span class="rc-page-sub"><?php echo !empty($exhibitors) ? count($exhibitors) : 0; ?> booths open now</span>
        </div>

        <?php if (!empty($exhibitors)): ?>
            <section class="rc-content-grid">
                <?php foreach ($exhibitors as $ex): ?>
                    <?php
                    $logo = !empty($ex['logo']) ? base_url('uploads/exhibitors_logos/' . $ex['logo']) : asset_url('images/eventslogo.png');
                    $bg = 'radial-gradient(circle at 50% 40%, #ffffff 0%, #d9d9d9 42%, #742c76 100%)';
                    ?>
                    <article class="rc-booth-card">
                        <div style="position:relative;aspect-ratio:16/9;background:<?php echo $bg; ?>;display:flex;align-items:center;justify-content:center">
                            <?php if (!empty($ex['vimeo_id'])): ?>
                                <iframe src="https://player.vimeo.com/video/<?php echo esc($ex['vimeo_id']); ?>?dnt=1&background=1&autoplay=1&loop=1&muted=1"
                                        allow="autoplay; fullscreen"
                                        style="position:absolute;inset:0;width:100%;height:100%;border:0"></iframe>
                            <?php else: ?>
                                <img src="<?php echo $logo; ?>" alt="<?php echo esc($ex['company_name']); ?>" style="max-width:52%;max-height:62%;object-fit:contain">
                            <?php endif; ?>
                            <div style="position:absolute;top:0;left:0;right:0;padding:12px 18px;background:linear-gradient(90deg,rgba(0,0,0,.7),rgba(0,0,0,0))">
                                <span style="font-size:15px;font-weight:500;letter-spacing:.02em;color:#fff"><?php echo esc($ex['company_name']); ?></span>
                            </div>
                            <span style="position:absolute;left:14px;bottom:14px;display:flex;align-items:center;gap:8px;font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:.16em;padding:6px 10px;border-radius:999px;background:rgba(18,5,15,.78);color:#fff">
                                <i class="fa fa-play" aria-hidden="true"></i>Booth video
                            </span>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 22px">
                            <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
                                <span style="font-size:17px;font-weight:600"><?php echo esc($ex['company_name']); ?></span>
                                <span style="font-size:13px;color:rgba(255,255,255,.7)"><?php echo esc($ex['website'] ?? 'Exhibitor booth'); ?></span>
                            </div>
                            <button type="button" data-toggle="modal" data-target="#boothModal<?php echo $ex['id']; ?>"
                                    class="rc-pill rc-grad-pill" style="height:40px">
                                Enter Booth
                            </button>
                        </div>
                    </article>

                    <div class="modal fade" id="boothModal<?php echo $ex['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                            <div class="modal-content" style="background:#260029!important;border-radius:16px!important">
                                <div class="modal-body" style="padding:28px">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:12px;right:16px">&times;</button>
                                    <div style="display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,1fr);gap:28px;align-items:center">
                                        <div style="position:relative;aspect-ratio:16/9;border-radius:12px;overflow:hidden;background:<?php echo $bg; ?>;display:flex;align-items:center;justify-content:center">
                                            <?php if (!empty($ex['vimeo_id'])): ?>
                                                <iframe src="https://player.vimeo.com/video/<?php echo esc($ex['vimeo_id']); ?>"
                                                        width="100%" height="100%" frameborder="0"
                                                        allow="autoplay; fullscreen" allowfullscreen></iframe>
                                            <?php else: ?>
                                                <img src="<?php echo $logo; ?>" alt="" style="max-width:50%;max-height:56%;object-fit:contain;opacity:.9">
                                            <?php endif; ?>
                                        </div>
                                        <div style="display:flex;flex-direction:column;gap:16px">
                                            <span style="font-size:22px;font-weight:700;color:#efb11e"><?php echo esc($ex['company_name']); ?></span>
                                            <div style="display:flex;align-items:center;gap:14px">
                                                <img src="<?php echo asset_url('images/user.png'); ?>" alt="" style="width:60px;height:60px;border-radius:50%">
                                                <div style="display:flex;flex-direction:column;gap:2px;font-size:13px">
                                                    <strong style="font-size:15px"><?php echo esc($ex['contact_person']); ?></strong>
                                                    <span style="color:rgba(255,255,255,.7)"><?php echo esc($ex['email']); ?></span>
                                                    <a href="<?php echo esc($ex['website']); ?>" target="_blank" rel="noopener"><?php echo esc($ex['website']); ?></a>
                                                </div>
                                            </div>
                                            <form class="message-form" style="display:flex;flex-direction:column;gap:10px">
                                                <input class="rc-input" type="text" name="subject" placeholder="Enter Subject" required>
                                                <textarea class="rc-input" name="message" rows="4" placeholder="Enter message here..." required style="height:120px;padding:14px!important"></textarea>
                                                <input type="hidden" name="exhibitor_id" value="<?php echo $ex['id']; ?>">
                                                <input type="hidden" name="attendee_id_from" value="<?php echo session('attendee_id'); ?>">
                                                <button type="submit" class="rc-btn" style="text-transform:none;letter-spacing:0">Send</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <div class="rc-empty">No exhibitors found.</div>
        <?php endif; ?>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
