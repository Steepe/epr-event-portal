<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$tabs = [
    ['ATTENDEES', base_url('attendees/attendees'), false],
    ['SPEAKERS', base_url('attendees/speakers'), true],
    ['COMMUNITIES', base_url('attendees/communities'), false],
];
?>

<main class="rc-page rc-page-center" data-screen-label="Speakers">
    <div class="rc-container" style="gap:24px">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap">
            <div style="display:flex;flex-direction:column;gap:8px">
                <a href="<?php echo base_url('attendees/networking_center'); ?>" class="rc-kicker" style="align-self:flex-start;display:flex;gap:8px;align-items:center">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>Networking Center
                </a>
                <span class="rc-page-title">Speakers</span>
                <span class="rc-page-sub"><?php echo !empty($speakers) ? count($speakers) : 0; ?> speakers</span>
            </div>

            <div class="rc-net-tabs">
                <?php foreach ($tabs as [$label, $url, $active]): ?>
                    <a href="<?php echo $url; ?>"
                       class="rc-net-tab <?php echo $active ? 'is-active' : ''; ?>">
                        <?php echo esc($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($speakers)): ?>
            <section class="rc-speaker-grid">
                <?php foreach ($speakers as $speaker): ?>
                    <?php
                    $speakerId = $speaker['speaker_id'];
                    $image = !empty($speaker['speaker_photo'])
                        ? base_url('uploads/speakers/' . $speaker['speaker_photo'])
                        : asset_url('images/user.png');
                    $name = $speaker['speaker_name'] ?? 'Speaker';
                    $title = trim(($speaker['speaker_title'] ?? '') . (!empty($speaker['speaker_company']) ? ' · ' . $speaker['speaker_company'] : ''));
                    $sessions = $speaker['sessions'] ?? [];
                    $nextSession = !empty($sessions) ? ($sessions[0]['sessions_name'] ?? 'Conference session') : 'Conference session';
                    $topicText = $speaker['topics'] ?? $speaker['speaker_topics'] ?? $speaker['expertise'] ?? '';
                    $topics = array_filter(array_map('trim', preg_split('/[,|]/', (string) $topicText)));
                    if (empty($topics)) {
                        $topics = ['Systems', 'Execution'];
                    }
                    ?>
                    <article class="rc-person-card" data-toggle="modal" data-target="#speakerModal<?php echo $speakerId; ?>" style="cursor:pointer;display:flex;flex-direction:column">
                        <div class="rc-speaker-media">
                            <img src="<?php echo $image; ?>" alt="<?php echo esc($name); ?>"
                                 onerror="this.src='<?php echo asset_url('images/user.png'); ?>';"
                                 style="width:128px;height:128px;border-radius:50%;object-fit:cover;box-shadow:0 0 60px rgba(216,25,142,.4)">
                            <span class="rc-speaker-badge">
                                <?php echo count($sessions) ?: 1; ?> SESSION<?php echo count($sessions) === 1 ? '' : 'S'; ?>
                            </span>
                        </div>
                        <div style="padding:18px 22px 20px;display:flex;flex-direction:column;gap:14px">
                            <div style="display:flex;flex-direction:column;gap:3px">
                                <span style="font-size:18px;font-weight:600"><?php echo esc($name); ?></span>
                                <span style="font-size:13px;color:rgba(255,255,255,.75)"><?php echo esc($title ?: 'Speaker'); ?></span>
                            </div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                <?php foreach (array_slice($topics, 0, 3) as $topic): ?>
                                    <span class="rc-card-tag"><?php echo esc($topic); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div style="display:flex;align-items:flex-start;gap:12px;padding-top:14px;border-top:1px solid rgba(255,255,255,.08)">
                                <i class="fa fa-calendar-o" style="color:#efb11e;margin-top:2px" aria-hidden="true"></i>
                                <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
                                    <span class="rc-mono" style="font-size:11px;letter-spacing:.12em;color:#efb11e">NEXT SESSION</span>
                                    <span style="font-size:14px;line-height:1.35"><?php echo esc($nextSession); ?></span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <div class="modal fade" id="speakerModal<?php echo $speakerId; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content" style="background:#260029!important;border-radius:18px!important">
                                <div class="modal-header">
                                    <h5 class="modal-title" style="color:#efb11e"><?php echo esc($name); ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div style="display:grid;grid-template-columns:220px minmax(0,1fr);gap:24px;align-items:start">
                                        <div style="display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center">
                                            <img src="<?php echo $image; ?>" alt="<?php echo esc($name); ?>"
                                                 onerror="this.src='<?php echo asset_url('images/user.png'); ?>';"
                                                 style="width:180px;height:180px;border-radius:50%;object-fit:cover;box-shadow:0 0 60px rgba(216,25,142,.4)">
                                            <span style="font-size:22px;font-weight:700"><?php echo esc($name); ?></span>
                                            <span style="font-size:13px;color:rgba(255,255,255,.78)"><?php echo esc($title ?: 'Speaker'); ?></span>
                                        </div>
                                        <div style="display:flex;flex-direction:column;gap:18px">
                                            <div>
                                                <span class="rc-kicker" style="font-size:11px">About</span>
                                                <p style="margin:8px 0 0;font-size:15px;line-height:1.6;color:rgba(255,255,255,.9)">
                                                    <?php echo nl2br(esc($speaker['bio'] ?? 'No biography available.')); ?>
                                                </p>
                                            </div>
                                            <?php if (!empty($sessions)): ?>
                                                <div>
                                                    <span class="rc-kicker" style="font-size:11px">Sessions</span>
                                                    <div style="display:flex;flex-direction:column;gap:10px;margin-top:10px">
                                                        <?php foreach ($sessions as $session): ?>
                                                            <div style="padding:14px 16px;border-radius:12px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                                                                <?php echo esc($session['sessions_name']); ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <form action="<?php echo base_url('attendees/speakers/sendMessage'); ?>" method="post" style="display:flex;flex-direction:column;gap:10px">
                                                <input type="hidden" name="speaker_id" value="<?php echo $speakerId; ?>">
                                                <input type="hidden" name="speaker_email" value="<?php echo esc($speaker['speaker_email'] ?? ''); ?>">
                                                <input class="rc-input" type="text" name="subject" placeholder="Subject" required>
                                                <textarea class="rc-input" name="message" rows="4" placeholder="Write your message to this speaker..." required style="height:120px;padding:14px!important"></textarea>
                                                <button class="rc-btn" style="text-transform:none;letter-spacing:0">Send Message</button>
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
            <div class="rc-empty">No speakers found.</div>
        <?php endif; ?>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
