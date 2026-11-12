<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => false, 'activeTab' => 'lobby', 'title' => 'Lobby']) ?>
<?php
$agendaUrl      = !empty($conferenceId) ? site_url('mobile/agenda/' . $conferenceId) : site_url('mobile/home');
$conferenceYear = (string) ($conference['year'] ?? date('Y'));
$showNotice     = (bool) ($payment_notice ?? false);
$ticketPrice    = $ticket_price ?? null;
$ticketCurrency = $ticket_currency ?? 'USD';

$nodes = [
    ['NETWORKING', 'CENTER', '30%', '18%', '', 'radial-gradient(circle at top left,#8b4ac0,#693b9c)', '139,74,192', site_url('mobile/networking-center')],
    ['SPONSORS', '', '74%', '28%', '', 'radial-gradient(circle at top left,#a82ad2,#7200a4)', '168,42,210', site_url('mobile/sponsors')],
    ['EXHIBITORS', '', '22%', '50%', '', 'radial-gradient(circle at top left,#d61be3,#9b0eaa)', '214,27,227', site_url('mobile/exhibitors')],
    ['EMERGENCE', 'BOOTH', '78%', '60%', '', 'radial-gradient(circle at top left,#e988a2,#c95c7c)', '233,136,162', site_url('mobile/emergence-booth')],
    ['LIVE', 'CONFERENCE', '50%', '80%', 'm-lobby__node--lg', 'radial-gradient(circle at top left,#f1a91e,#d68500)', '241,169,30', $agendaUrl],
];
?>

<div class="m-lobby">
    <div class="m-lobby__head">
        <span class="m-kicker">Lobby · Emergence <?= esc($conferenceYear) ?></span>
        <img class="m-lobby__brand" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="<?= esc($conference['title'] ?? 'Accelerate', 'attr') ?>">
    </div>

    <?php if ($showNotice): ?>
        <div class="m-notice" data-payment-notice>
            <i class="fa fa-lock" aria-hidden="true"></i>
            <div class="m-notice__copy">
                <span><strong>Access restricted.</strong> Complete your registration payment to unlock all sessions.
                    <?php if ($ticketPrice !== null): ?><span class="m-notice__price">Ticket: <?= esc($ticketCurrency) ?> <?= esc(number_format((float) $ticketPrice)) ?></span><?php endif; ?>
                </span>
                <a class="m-notice__pay" href="<?= site_url('attendees/checkout') ?>">Pay now</a>
            </div>
            <button class="m-notice__close" type="button" aria-label="Close payment notice" onclick="this.closest('[data-payment-notice]').remove()">×</button>
        </div>
    <?php endif; ?>

    <div class="m-lobby__map" aria-label="Conference areas">
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
            <g fill="none" stroke="rgba(239,177,30,.32)" stroke-width="1" stroke-dasharray="2 5">
                <line x1="50" y1="80" x2="30" y2="18" vector-effect="non-scaling-stroke"></line>
                <line x1="50" y1="80" x2="22" y2="50" vector-effect="non-scaling-stroke"></line>
                <line x1="30" y1="18" x2="74" y2="28" vector-effect="non-scaling-stroke"></line>
                <line x1="30" y1="18" x2="22" y2="50" vector-effect="non-scaling-stroke"></line>
                <line x1="22" y1="50" x2="74" y2="28" vector-effect="non-scaling-stroke"></line>
                <line x1="22" y1="50" x2="78" y2="60" vector-effect="non-scaling-stroke"></line>
                <line x1="74" y1="28" x2="78" y2="60" vector-effect="non-scaling-stroke"></line>
            </g>
        </svg>
        <?php foreach ($nodes as [$line1, $line2, $x, $y, $modifier, $background, $rgb, $url]): ?>
            <a class="m-lobby__node <?= $modifier ?>" href="<?= $url ?>" style="left:<?= $x ?>;top:<?= $y ?>;background:<?= $background ?>;--halo:rgba(<?= $rgb ?>,.12);--glow:rgba(<?= $rgb ?>,.45)">
                <span><span><?= $line1 ?></span><?php if ($line2 !== ''): ?><span><?= $line2 ?></span><?php endif; ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="m-lobby__foot">Crafted with <i class="fa fa-heart" aria-hidden="true"></i> by <a>Crèyatif</a> • © <?= date('Y') ?> Powered By EPR Global.</div>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
