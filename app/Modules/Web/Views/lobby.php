<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/lobby_topbar');

$conferenceId = session()->get('live-conference-id');
$agendaUrl = $conferenceId ? base_url('attendees/agenda/' . $conferenceId) : base_url('attendees/start');
$accessRequiresPayment = (bool) ($access_requires_payment ?? false);
$checkoutEnabled = (bool) ($checkout_enabled ?? false);
$paymentNotice = isset($payment_notice) ? (bool) $payment_notice : true;
$paidUser = (bool) (session()->get('portal_paid') ?? session()->get('paid') ?? false);
$showPaymentNotice = $paymentNotice && !$paidUser;
?>

<?php if ($showPaymentNotice): ?>
    <div class="rc-payment-notice" data-payment-notice>
        <i class="fa fa-lock" style="color:#efb11e;font-size:16px" aria-hidden="true"></i>
        <div class="rc-payment-copy">
            <strong style="font-weight:600">Access restricted.</strong>
            This is a paid event. Please complete your registration payment to unlock all sessions.
            <span class="rc-payment-ticket">Ticket: NGN 25,000</span>
        </div>
        <a href="<?php echo site_url('attendees/checkout'); ?>" class="rc-payment-action">Pay now</a>
        <button type="button" class="rc-payment-close" aria-label="Close payment notice" data-close-payment-notice>&times;</button>
    </div>
<?php endif; ?>

<main data-screen-label="Lobby" style="position:absolute;inset:0">
    <a data-node="agenda" data-to="networking exhibitors" class="rc-lobby-node"
       href="<?php echo $agendaUrl; ?>"
       style="left:15%;top:46%;transform:translate(-50%,-50%);background:radial-gradient(circle at top left,#f1a91e,#d68500)">
        AGENDA
    </a>

    <a data-node="networking" data-to="sponsors exhibitors" class="rc-lobby-node"
       href="<?php echo base_url('attendees/networking_center'); ?>"
       style="left:34%;top:max(30%,250px);transform:translate(-50%,-50%);background:radial-gradient(circle at top left,#8b4ac0,#693b9c)">
        NETWORKING<br>CENTER
    </a>

    <a data-node="exhibitors" data-to="sponsors emergence" class="rc-lobby-node"
       href="<?php echo base_url('attendees/exhibitors'); ?>"
       style="left:50%;top:64%;transform:translate(-50%,-50%);background:radial-gradient(circle at top left,#d61be3,#9b0eaa)">
        EXHIBITORS
    </a>

    <a data-node="sponsors" data-to="emergence" class="rc-lobby-node"
       href="<?php echo base_url('attendees/sponsors'); ?>"
       style="left:66%;top:max(32%,262px);transform:translate(-50%,-50%);background:radial-gradient(circle at top left,#a82ad2,#7200a4)">
        SPONSORS
    </a>

    <a data-node="emergence" class="rc-lobby-node"
       href="<?php echo base_url('attendees/emergence_booth'); ?>"
       style="left:84%;top:56%;transform:translate(-50%,-50%);background:radial-gradient(circle at top left,#e988a2,#c95c7c)">
        EMERGENCE<br>BOOTH
    </a>

    <span class="rc-lobby-index" style="left:15%;top:calc(46% + 92px)">01</span>
    <span class="rc-lobby-index" style="left:34%;top:calc(max(30%,250px) + 92px)">02</span>
    <span class="rc-lobby-index" style="left:50%;top:calc(64% + 92px)">03</span>
    <span class="rc-lobby-index" style="left:66%;top:calc(max(32%,262px) + 92px)">04</span>
    <span class="rc-lobby-index" style="left:84%;top:calc(56% + 92px)">05</span>

    <div class="rc-lobby-brand">
        <span class="rc-kicker">Lobby &middot; Emergence 2026</span>
        <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="Accelerate: Building sustainable systems for execution">
    </div>

    <div class="rc-footer-note">Crafted with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://about.me/steepe" target="_blank" rel="noopener">Crèyatif</a> &bull; &copy; 2026 Powered By EPR Global.</div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("click", function(event) {
    const close = event.target.closest("[data-close-payment-notice]");
    if (!close) {
        return;
    }
    const notice = close.closest("[data-payment-notice]");
    if (notice) {
        notice.classList.add("is-hidden");
    }
});
</script>
</body>
</html>
