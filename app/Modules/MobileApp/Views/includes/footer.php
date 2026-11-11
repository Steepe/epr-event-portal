<?php
$mobilePath = service('uri')->getPath();
$mobileAgenda = session()->get('live-conference-id');
$mobileTab = static function (string $fragment) use ($mobilePath): string {
    return str_contains($mobilePath, $fragment) ? 'is-active' : '';
};
?>
<nav class="epr-bottom-tabs" aria-label="Primary navigation">
    <a class="<?= $mobileTab('lobby') ?>" href="<?= base_url('mobile/lobby') ?>"><i class="fa fa-th-large"></i><span>Lobby</span></a>
    <a class="<?= $mobileTab('agenda') ?>" href="<?= base_url('mobile/agenda/' . $mobileAgenda) ?>"><i class="fa fa-calendar"></i><span>Agenda</span></a>
    <a class="<?= $mobileTab('networking') ?>" href="<?= base_url('mobile/networking-center') ?>"><i class="fa fa-users"></i><span>Network</span></a>
    <a class="<?= $mobileTab('exhibitor') ?>" href="<?= base_url('mobile/exhibitors') ?>"><i class="fa fa-building"></i><span>Booths</span></a>
    <a class="<?= $mobileTab('profile') ?>" href="<?= base_url('mobile/profile') ?>"><i class="fa fa-user"></i><span>Me</span></a>
</nav>
</body>
</html>
