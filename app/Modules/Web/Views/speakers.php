<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$tabs = [
    ['ATTENDEES', base_url('attendees/attendees'), false],
    ['SPEAKERS', base_url('attendees/speakers'), true],
    ['COMMUNITIES', base_url('attendees/communities'), false],
];
$speakers = $speakers ?? [];
$initials = static function (string $name): string {
    $cleanName = preg_replace('/^Dr\.\s*/i', '', trim($name));
    $parts = preg_split('/\s+/', $cleanName);
    return strtoupper(substr($parts[0] ?? 'S', 0, 1) . substr($parts[1] ?? '', 0, 1));
};
?>

<main class="rc-page rc-page-center" data-screen-label="Speakers">
    <div class="rc-container" style="gap:24px">
        <header class="rc-net-sub-header">
            <div class="rc-net-heading">
                <a href="<?php echo base_url('attendees/networking_center'); ?>" class="rc-back-link"><i class="fa fa-arrow-left" aria-hidden="true"></i>NETWORKING CENTER</a>
                <span class="rc-page-title">Speakers</span>
                <span class="rc-page-sub"><?php echo count($speakers); ?> speakers</span>
            </div>
            <div class="rc-net-tabs">
                <?php foreach ($tabs as [$label, $url, $active]): ?><a href="<?php echo $url; ?>" class="rc-net-tab <?php echo $active ? 'is-active' : ''; ?>"><?php echo esc($label); ?></a><?php endforeach; ?>
            </div>
        </header>

        <?php foreach (['success' => '#4ade80', 'error' => '#f87171'] as $flash => $colour): ?>
            <?php if (session()->getFlashdata($flash)): ?>
                <div class="rc-panel" style="padding:14px 18px;border-color:<?php echo $colour; ?>;color:<?php echo $colour; ?>">
                    <?php echo esc(session()->getFlashdata($flash)); ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (!empty($speakers)): ?>
            <section class="rc-speaker-grid">
                <?php foreach ($speakers as $speakerIndex => $speaker): ?>
                    <?php
                    $speakerId = (int) ($speaker['speaker_id'] ?? ($speakerIndex + 1));
                    $name = trim((string) ($speaker['speaker_name'] ?? 'Speaker'));
                    $speakerTitle = trim((string) ($speaker['speaker_title'] ?? 'Speaker'));
                    $company = trim((string) ($speaker['speaker_company'] ?? ''));
                    $role = $speakerTitle . ($company !== '' ? ' · ' . $company : '');
                    $photo = !empty($speaker['speaker_photo']) ? base_url('uploads/speakers/' . $speaker['speaker_photo']) : '';
                    $sessions = $speaker['sessions'] ?? [];
                    $sessionTitles = array_values(array_filter(array_map(static fn (array $session): string => trim((string) ($session['sessions_name'] ?? '')), $sessions)));
                    $topicText = (string) ($speaker['topics'] ?? $speaker['speaker_topics'] ?? $speaker['expertise'] ?? 'Systems, Execution, Leadership');
                    $topics = array_values(array_filter(array_map('trim', preg_split('/[,|]/', $topicText))));
                    $bio = trim((string) ($speaker['bio'] ?? 'This speaker shares practical lessons on sustainable execution, leadership rhythms and the systems that help teams scale.'));
                    $linkedinUrl = trim((string) ($speaker['linkedin_url'] ?? ''));
                    if ($linkedinUrl !== '' && ! filter_var($linkedinUrl, FILTER_VALIDATE_URL)) {
                        $linkedinUrl = '';
                    }
                    ?>
                    <article class="rc-person-card rc-speaker-card" data-toggle="modal" data-target="#speakerModal<?php echo $speakerId; ?>" tabindex="0">
                        <div class="rc-speaker-media">
                            <?php if ($photo !== ''): ?>
                                <img src="<?php echo $photo; ?>" alt="<?php echo esc($name); ?>" onerror="this.hidden=true;this.nextElementSibling.hidden=false" class="rc-speaker-photo">
                                <span class="rc-speaker-initials" hidden><?php echo $initials($name); ?></span>
                            <?php else: ?>
                                <span class="rc-speaker-initials"><?php echo $initials($name); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="rc-speaker-card-copy">
                            <div><strong><?php echo esc($name); ?></strong><span><?php echo esc($role); ?></span></div>
                            <?php if ($sessionTitles): ?><div class="rc-speaker-next"><i class="fa fa-microphone" aria-hidden="true"></i><span><strong><?php echo esc(implode(' · ', $sessionTitles)); ?></strong></span></div><?php endif; ?>
                        </div>
                    </article>

                    <div class="modal fade rc-speaker-modal" id="speakerModal<?php echo $speakerId; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <button type="button" class="rc-speaker-close close" data-dismiss="modal" aria-label="Close">&times;</button>
                                <aside class="rc-speaker-modal-profile">
                                    <?php if ($photo !== ''): ?><img src="<?php echo $photo; ?>" alt="<?php echo esc($name); ?>" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><span class="rc-speaker-modal-initials" hidden><?php echo $initials($name); ?></span><?php else: ?><span class="rc-speaker-modal-initials"><?php echo $initials($name); ?></span><?php endif; ?>
                                    <div class="rc-speaker-modal-name"><strong><?php echo esc($name); ?></strong><span><?php echo esc($speakerTitle); ?></span><?php if ($company !== ''): ?><span><?php echo esc($company); ?></span><?php endif; ?></div>
                                    <div class="rc-speaker-topics"><?php foreach (array_slice($topics, 0, 3) as $topic): ?><span><?php echo esc($topic); ?></span><?php endforeach; ?></div>
                                    <?php if ($linkedinUrl !== ''): ?><div class="rc-speaker-socials"><a href="<?php echo esc($linkedinUrl, 'attr'); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc($name, 'attr'); ?> on LinkedIn"><i class="fa fa-linkedin" aria-hidden="true"></i></a></div><?php endif; ?>
                                </aside>
                                <section class="rc-speaker-modal-main">
                                    <div class="rc-speaker-about"><span>ABOUT</span><p><?php echo esc($bio); ?></p></div>
                                    <div class="rc-speaker-sessions"><span>SESSIONS</span>
                                        <?php if (!empty($sessions)): ?>
                                            <?php foreach ($sessions as $session): ?>
                                                <?php
                                                $sessionName = $session['sessions_name'] ?? 'Conference session';
                                                $sessionId = (int) ($session['sessions_id'] ?? $session['id'] ?? 0);
                                                ?>
                                                <div class="rc-speaker-session-row"><div><strong><?php echo esc($sessionName); ?></strong></div><?php if ($sessionId > 0): ?><a href="<?php echo base_url('attendees/sessions/' . $sessionId); ?>" class="rc-speaker-join">View session</a><?php endif; ?></div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="rc-speaker-session-row"><div><strong>Sessions will be announced soon.</strong></div></div>
                                        <?php endif; ?>
                                    </div>
                                    <form class="rc-speaker-booking" method="post" action="<?php echo site_url('attendees/speakers/sendMessage'); ?>">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="speaker_id" value="<?php echo $speakerId; ?>">
                                        <div><strong>Contact <?php echo esc($name); ?></strong><span>Your message will be sent to this speaker by email.</span></div>
                                        <input class="rc-input" type="text" name="subject" required maxlength="255" placeholder="Subject">
                                        <textarea class="rc-input" name="message" required maxlength="5000" rows="4" placeholder="Write your message" style="height:auto;padding:12px 14px!important;resize:vertical"></textarea>
                                        <button type="submit" class="rc-speaker-request">Send message</button>
                                    </form>
                                </section>
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
<script>
document.addEventListener("keydown", event => {
    const card = event.target.closest(".rc-speaker-card");
    if (card && (event.key === "Enter" || event.key === " ")) { event.preventDefault(); card.click(); }
});
</script>
</body>
</html>
