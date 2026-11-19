<?php
$paid         = (bool) ($paid ?? false);
$reference    = trim((string) ($reference ?? ''));
$conferenceId = (int) ($conference_id ?? 0);
$continueUrl  = $paid
    ? ($conferenceId > 0 ? site_url('mobile/agenda/' . $conferenceId) : site_url('mobile/lobby'))
    : site_url('mobile/upgrade');

echo module_view('MobileApp', 'includes/header', ['shell' => 'bare', 'back' => false, 'activeTab' => '', 'title' => $paid ? 'Payment confirmed' : 'Payment cancelled', 'screenLabel' => 'Checkout Result']);
?>

<div class="m-checkout">
    <div class="m-glass">
        <?php if ($paid): ?>
            <span class="m-checkout__icon"><i class="fa fa-check" aria-hidden="true"></i></span>
            <h1 class="m-checkout__title">Payment confirmed</h1>
            <p>Your Premium Access is active. All paid sessions, workbooks and replays are now unlocked.</p>
            <?php if ($reference !== ''): ?><span class="m-checkout__receipt">REF · <?= esc($reference) ?></span><?php endif; ?>
            <a class="m-btn" href="<?= $continueUrl ?>">Continue</a>
        <?php else: ?>
            <span class="m-checkout__icon is-warn"><i class="fa fa-times" aria-hidden="true"></i></span>
            <h1 class="m-checkout__title">Payment cancelled</h1>
            <p>Your transaction was not completed. No charges were applied to your account.</p>
            <a class="m-btn" href="<?= $continueUrl ?>">Try again</a>
            <a class="m-btn-ghost m-btn-ghost--44" href="<?= site_url('mobile/lobby') ?>">Back to lobby</a>
        <?php endif; ?>
    </div>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
