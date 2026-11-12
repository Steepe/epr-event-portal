<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => false, 'activeTab' => 'me', 'title' => 'Profile']) ?>
<?php
$profile  = $profile ?? [];
$name     = trim(($profile['firstname'] ?? '') . ' ' . ($profile['lastname'] ?? '')) ?: 'Attendee';
$photo    = !empty($profile['profile_picture']) ? '/uploads/attendee_pictures/' . rawurlencode($profile['profile_picture']) : mobile_asset_url('images/user.png');
$role     = implode(', ', array_filter([trim((string) ($profile['position'] ?? '')), trim((string) ($profile['company'] ?? ''))])) ?: 'Conference attendee';
$location = implode(', ', array_filter([trim((string) ($profile['city'] ?? '')), trim((string) ($profile['country'] ?? ''))])) ?: 'Conference portal';
$isPremium = (int) ($premium ?? 0) === 1;

$socialBase = ['linkedin' => 'https://www.linkedin.com/in/', 'twitter' => 'https://x.com/', 'facebook' => 'https://www.facebook.com/', 'instagram' => 'https://www.instagram.com/'];
$socialIcon = ['linkedin' => 'linkedin.svg', 'twitter' => 'x.svg', 'facebook' => 'facebook.svg', 'instagram' => 'instagram.svg'];
$socials    = [];
foreach ($socialBase as $key => $base) {
    $value = trim((string) ($profile[$key] ?? ''));
    if ($value === '') {
        continue;
    }
    $socials[$key] = preg_match('#^https?://#i', $value) ? $value : $base . rawurlencode(ltrim($value, '@'));
}

$fields = [
    ['First name', 'firstname'], ['Last name', 'lastname'], ['Telephone', 'telephone'], ['Country', 'country'],
    ['City', 'city'], ['State', 'state'], ['Company', 'company'], ['Position', 'position'],
];
$editFields = ['firstname', 'lastname', 'telephone', 'country', 'city', 'state', 'company', 'position', 'facebook', 'twitter', 'instagram', 'linkedin'];
$explore    = [
    ['fa-bell', 'Notifications', 'mobile/notifications'],
    ['fa-star', 'Earn Points', 'mobile/points'],
    ['fa-eye', 'Envision', 'mobile/envision'],
    ['fa-history', 'Past Conferences', 'mobile/past-conferences'],
    ['fa-video-camera', 'Webinars', 'mobile/webinars'],
    ['fa-diamond', 'Sponsors', 'mobile/sponsors'],
    ['fa-microphone', 'Emergence Booth', 'mobile/emergence-booth'],
    ['fa-unlock-alt', $isPremium ? 'Premium Access · Active' : 'Upgrade to Premium', 'mobile/upgrade'],
];
?>

<div class="m-me">
    <section class="m-card m-card--20 m-me__card">
        <div class="m-me__photo">
            <img id="profileImage" src="<?= esc($photo, 'attr') ?>" alt="Profile photo" onerror="this.onerror=null;this.src='<?= mobile_asset_url('images/user.png') ?>'">
            <label class="m-me__camera" aria-label="Change photo"><i class="fa fa-camera" aria-hidden="true"></i><input id="uploadPhoto" type="file" accept="image/*"></label>
        </div>
        <div class="m-me__id">
            <span class="m-me__kicker">Attendee · Accelerate 2026</span>
            <h1 class="m-me__name"><?= esc($name) ?></h1>
            <span class="m-me__role"><?= esc($role) ?></span>
            <span class="m-me__place"><i class="fa fa-map-marker" aria-hidden="true"></i><?= esc($location) ?></span>
        </div>
        <div class="m-me__socials"><?php foreach ($socials as $key => $url): ?><a title="<?= $key ?>" href="<?= esc($url, 'attr') ?>" target="_blank" rel="noopener"><img src="<?= mobile_asset_url('images/social/' . $socialIcon[$key]) ?>" alt="<?= $key ?>"></a><?php endforeach; ?></div>
        <button class="m-me__edit" type="button" data-open-sheet="edit">Edit Profile</button>
    </section>

    <section class="m-card m-card--20 m-me__details">
        <h2>Personal details</h2>
        <div class="m-me__fields">
            <?php foreach ($fields as [$label, $key]): ?>
                <div class="m-me__field"><small><?= $label ?></small><span><?= esc(trim((string) ($profile[$key] ?? '')) ?: '-') ?></span></div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="m-me__explore">
        <span class="m-eyebrow">Explore</span>
        <div class="m-card m-card--clip">
            <?php foreach ($explore as [$icon, $label, $route]): ?>
                <a class="m-me__link" href="<?= site_url($route) ?>"><i class="fa <?= $icon ?>" aria-hidden="true"></i><span><?= esc($label) ?></span><i class="fa fa-angle-right" aria-hidden="true"></i></a>
            <?php endforeach; ?>
        </div>
    </div>

    <a class="m-me__logout" href="<?= site_url('mobile/logout') ?>"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
</div>

<aside class="m-sheet" data-sheet="edit" role="dialog" aria-modal="true" aria-label="Edit profile" hidden>
    <div class="m-sheet__grip"></div>
    <form class="m-edit" action="<?= site_url('mobile/profile/update') ?>" method="post">
        <?= csrf_field() ?>
        <div class="m-edit__title">Edit Profile<button class="m-x" type="button" data-close-sheet aria-label="Close">×</button></div>
        <div class="m-edit__grid">
            <?php foreach ($editFields as $key): ?>
                <label><?= $key ?><input class="m-input m-input--42" name="<?= $key ?>" value="<?= esc((string) ($profile[$key] ?? ''), 'attr') ?>"<?= in_array($key, ['firstname', 'lastname'], true) ? ' required' : '' ?>></label>
            <?php endforeach; ?>
        </div>
        <button class="m-btn m-btn--plain" type="submit">Save Changes</button>
    </form>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('uploadPhoto').addEventListener('change', async function (event) {
        var file = event.target.files[0];
        if (!file) return;
        var form = new FormData();
        form.append('profile_picture', file);
        try {
            var response = await fetch(<?= json_encode(site_url('mobile/profile/upload-photo')) ?>, { method: 'POST', body: form, credentials: 'same-origin' });
            var result = await response.json();
            if (result.status !== 'success') throw new Error(result.message || '');
            document.getElementById('profileImage').src = result.url;
            var avatar = document.querySelector('.m-topbar__avatar'); if (avatar) avatar.src = result.url;
            EPR.toast('Profile photo updated.');
        } catch (error) {
            EPR.toast(error.message || 'Unable to update your photo.', true);
        }
        event.target.value = '';
    });
});
</script>

<?= module_view('MobileApp', 'includes/footer') ?>
