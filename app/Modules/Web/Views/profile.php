<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$fullName = trim(($profile['firstname'] ?? '') . ' ' . ($profile['lastname'] ?? ''));
$fullName = $fullName !== '' ? $fullName : 'Attendee';
$role = trim(($profile['position'] ?? '') . (!empty($profile['company']) ? ', ' . $profile['company'] : ''));
$location = trim(($profile['city'] ?? '') . (!empty($profile['country']) ? ', ' . $profile['country'] : ''));
$email = trim((string) ($profile['email'] ?? ''));
$photo = !empty($profile['profile_picture'])
    ? base_url('uploads/attendee_pictures/' . $profile['profile_picture'])
    : asset_url('images/user.png');

$fields = [
    ['First name', $profile['firstname'] ?? ''],
    ['Last name', $profile['lastname'] ?? ''],
    ['Telephone', $profile['telephone'] ?? ''],
    ['Country', $profile['country'] ?? ''],
    ['City', $profile['city'] ?? ''],
    ['State', $profile['state'] ?? ''],
    ['Company', $profile['company'] ?? ''],
    ['Position', $profile['position'] ?? ''],
];

$socials = [
    'facebook'  => ['icon' => 'facebook.svg',  'base' => 'https://facebook.com/'],
    'twitter'   => ['icon' => 'x.svg',         'base' => 'https://twitter.com/'],
    'linkedin'  => ['icon' => 'linkedin.svg',  'base' => 'https://linkedin.com/in/'],
    'instagram' => ['icon' => 'instagram.svg', 'base' => 'https://instagram.com/'],
];
?>

<main class="rc-page rc-page-center" data-screen-label="Profile">
    <div class="rc-container" style="max-width:1080px">
        <section class="rc-panel" style="border-radius:20px;padding:32px;display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:32px">
            <div style="position:relative;width:160px;height:160px">
                <img src="<?php echo $photo; ?>"
                     alt="Profile photo"
                     onerror="this.src='<?php echo asset_url('images/user.png'); ?>';"
                     style="width:100%;height:100%;border-radius:14px;object-fit:cover;border:4px solid #fff;box-sizing:border-box">
                <label style="position:absolute;bottom:10px;right:-14px;width:46px;height:46px;border-radius:50%;background:#b31bb7;display:flex;align-items:center;justify-content:center;font-size:18px;cursor:pointer;box-shadow:0 6px 16px rgba(0,0,0,.35)">
                    <i class="fa fa-camera" aria-hidden="true"></i>
                    <input type="file" id="uploadPhoto" accept="image/*" style="position:absolute;opacity:0;inset:0;cursor:pointer">
                </label>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px;min-width:0">
                <span class="rc-kicker" style="font-size:11px;letter-spacing:.2em">Attendee &middot; Accelerate 2026</span>
                <span style="font-size:36px;font-weight:700;letter-spacing:-.03em;line-height:1.05"><?php echo esc($fullName); ?></span>
                <span style="font-size:15px;color:rgba(255,255,255,.86)"><?php echo esc($role ?: 'Conference attendee'); ?></span>
                <span style="font-size:13px;color:rgba(255,255,255,.65);display:flex;gap:8px;align-items:center">
                    <i class="fa fa-map-marker" aria-hidden="true"></i><?php echo esc($location ?: 'Conference portal'); ?>
                </span>
            </div>

            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:18px">
                <div style="display:flex;gap:12px">
                    <?php foreach ($socials as $field => $meta): ?>
                        <?php $value = trim($profile[$field] ?? ''); ?>
                        <?php if (empty($value)) continue; ?>
                        <?php $href = preg_match('/^https?:\/\//', $value) ? $value : $meta['base'] . ltrim($value, '@'); ?>
                        <a href="<?php echo esc($href); ?>" target="_blank" rel="noopener" style="width:42px;height:42px;border-radius:50%;background:#9d0f82;display:flex;align-items:center;justify-content:center">
                            <img src="<?php echo asset_url('images/social/' . $meta['icon']); ?>" alt="<?php echo esc($field); ?>" width="20">
                        </a>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="openEditProfile" style="padding:12px 36px;border:0;border-radius:8px;background:#a1289b;color:#fff;font-size:15px;font-weight:600;cursor:pointer;white-space:nowrap">Edit Profile</button>
            </div>
        </section>

        <section class="rc-panel" style="border-radius:20px;padding:28px 32px;display:flex;flex-direction:column;gap:22px">
            <span style="font-size:18px;font-weight:600">Personal details</span>
            <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px 28px">
                <?php foreach ($fields as [$label, $value]): ?>
                    <div style="display:flex;flex-direction:column;gap:6px;min-width:0">
                        <span class="rc-mono" style="font-size:10px;letter-spacing:.18em;color:rgba(255,255,255,.55);text-transform:uppercase"><?php echo esc($label); ?></span>
                        <span style="font-size:15px;font-weight:500;overflow:hidden;text-overflow:ellipsis"><?php echo esc($value ?: '-'); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

<div id="editProfileModal" class="rc-modal-overlay" aria-hidden="true" style="padding:24px;box-sizing:border-box">
    <div style="position:relative;width:100%;max-width:650px;max-height:100%;overflow:auto;box-sizing:border-box;background:#260029;border:1px solid rgba(255,255,255,.12);border-radius:18px;padding:30px;box-shadow:0 20px 60px rgba(0,0,0,.5)">
        <button type="button" id="closeEditProfile" class="rc-modal-close" data-rc-close-overlay style="position:absolute;top:14px;right:18px" aria-label="Close">&times;</button>
        <div style="font-size:22px;font-weight:700;color:#efb11e;text-align:center;margin-bottom:20px">Edit Profile</div>

        <form id="ProfileUpdateForm" action="<?php echo base_url('attendees/profile/update'); ?>" method="post" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:16px 22px">
            <input type="hidden" name="id" value="<?php echo esc($profile['id'] ?? ''); ?>">
            <?php foreach (['firstname', 'lastname', 'telephone', 'country', 'city', 'state', 'company', 'position', 'facebook', 'twitter', 'instagram', 'linkedin'] as $field): ?>
                <label style="display:flex;flex-direction:column;gap:6px;font-size:13px;font-weight:600;color:rgba(255,255,255,.8);text-transform:capitalize">
                    <?php echo esc(str_replace('_', ' ', $field)); ?>
                    <input class="rc-input" name="<?php echo $field; ?>" value="<?php echo esc($profile[$field] ?? ''); ?>">
                </label>
            <?php endforeach; ?>
            <button class="rc-profile-save">Save Changes</button>
        </form>
    </div>
</div>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("editProfileModal");
    document.getElementById("openEditProfile")?.addEventListener("click", () => {
        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");
    });
    document.getElementById("closeEditProfile")?.addEventListener("click", () => {
        modal.classList.remove("is-open");
        modal.setAttribute("aria-hidden", "true");
    });
    modal?.addEventListener("click", event => {
        if (event.target === modal) {
            modal.classList.remove("is-open");
            modal.setAttribute("aria-hidden", "true");
        }
    });

    document.getElementById("uploadPhoto")?.addEventListener("change", async event => {
        const file = event.target.files?.[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("profile_picture", file);

        const response = await fetch("<?php echo base_url('attendees/profile/upload-photo'); ?>", {
            method: "POST",
            body: formData
        });
        const result = await response.json();
        if (result.status === "success" && result.url) {
            window.location.reload();
        } else {
            alert(result.message || "Unable to update photo.");
        }
    });
});
</script>
</body>
</html>
