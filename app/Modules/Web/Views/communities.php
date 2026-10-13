<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$tabs = [
    ['ATTENDEES', base_url('attendees/attendees'), false],
    ['SPEAKERS', base_url('attendees/speakers'), false],
    ['COMMUNITIES', base_url('attendees/communities'), true],
];

$communities = [
    ['Founders Circle', 'Operating cadence, delegation, dashboards, and repeatable growth.', 'fa-rocket', '1,284 members · 86 active today', false, true],
    ['Operations Leaders', 'Weekly reviews, SOPs, hiring rhythms, and execution scorecards.', 'fa-cogs', '842 members · 49 active today', false, false],
    ['Finance & Controls', 'Cash discipline, controls, reporting, and investor-ready numbers.', 'fa-line-chart', '506 members · 22 active today', true, false],
    ['Growth Room', 'Funnels, sales systems, partnerships, and customer insight.', 'fa-bullhorn', '719 members · 38 active today', false, false],
];

$active = $communities[0];
?>

<main class="rc-page rc-page-center" data-screen-label="Communities">
    <div class="rc-container" style="gap:24px">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap">
            <div style="display:flex;flex-direction:column;gap:8px">
                <a href="<?php echo base_url('attendees/networking_center'); ?>" class="rc-kicker" style="align-self:flex-start;display:flex;gap:8px;align-items:center">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>Networking Center
                </a>
                <span class="rc-page-title">Communities</span>
                <span class="rc-page-sub"><?php echo count($communities); ?> communities · join to post and reply</span>
            </div>

            <div class="rc-net-tabs">
                <?php foreach ($tabs as [$label, $url, $activeTab]): ?>
                    <a href="<?php echo $url; ?>"
                       class="rc-net-tab <?php echo $activeTab ? 'is-active' : ''; ?>">
                        <?php echo esc($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <section class="rc-community-layout" style="display:grid;grid-template-columns:minmax(250px,330px) minmax(0,1fr);gap:24px;align-items:start">
            <div style="display:flex;flex-direction:column;gap:10px">
                <?php foreach ($communities as $index => [$name, $desc, $icon, $meta, $private, $joined]): ?>
                    <article class="rc-community-card rc-community-option <?php echo $index === 0 ? 'is-active' : ''; ?>"
                             role="button"
                             tabindex="0"
                             data-name="<?php echo esc($name, 'attr'); ?>"
                             data-desc="<?php echo esc($desc, 'attr'); ?>"
                             data-icon="<?php echo esc($icon, 'attr'); ?>"
                             data-meta="<?php echo esc($meta, 'attr'); ?>"
                             data-private="<?php echo $private ? '1' : '0'; ?>"
                             data-joined="<?php echo $joined ? '1' : '0'; ?>"
                             style="display:flex;align-items:center;gap:14px;padding:14px 16px;cursor:pointer;background:<?php echo $index === 0 ? 'rgba(157,15,130,.38)' : 'rgba(34,16,38,.72)'; ?>;border-color:<?php echo $index === 0 ? 'rgba(239,177,30,.55)' : 'rgba(255,255,255,.1)'; ?>">
                        <div style="flex:none;width:44px;height:44px;border-radius:12px;background:<?php echo $index === 0 ? 'linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585)' : 'rgba(255,255,255,.08)'; ?>;display:flex;align-items:center;justify-content:center;font-size:18px">
                            <i class="fa <?php echo $icon; ?>" aria-hidden="true"></i>
                        </div>
                        <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:3px">
                            <span style="font-size:15px;font-weight:600"><?php echo esc($name); ?></span>
                            <span style="font-size:12px;color:rgba(255,255,255,.7)"><?php echo esc($meta); ?></span>
                        </div>
                        <?php if ($joined): ?>
                            <span class="rc-mono" style="flex:none;font-size:10px;letter-spacing:.14em;color:#efb11e">JOINED</span>
                        <?php elseif ($private): ?>
                            <i class="fa fa-lock" style="flex:none;color:rgba(255,255,255,.6)" aria-hidden="true"></i>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <article class="rc-panel" style="padding:24px;display:flex;flex-direction:column;gap:22px">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap">
                    <div style="display:flex;gap:16px;align-items:center">
                        <span style="width:58px;height:58px;border-radius:16px;background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585);display:flex;align-items:center;justify-content:center;font-size:22px">
                            <i id="communityActiveIcon" class="fa <?php echo $active[2]; ?>" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h2 id="communityActiveName" style="margin:0;font-size:24px;font-weight:700"><?php echo esc($active[0]); ?></h2>
                            <p id="communityActiveMeta" style="margin:5px 0 0;color:rgba(255,255,255,.72);font-size:14px"><?php echo esc($active[3]); ?></p>
                        </div>
                    </div>
                    <button id="communityAction" class="rc-card-action is-muted" data-state="joined" style="flex:none;width:auto;height:40px;padding:0 18px" type="button">
                        <i class="fa fa-check" aria-hidden="true"></i><span>Joined</span>
                    </button>
                </div>

                <p id="communityActiveDesc" style="margin:0;color:rgba(255,255,255,.86);line-height:1.65"><?php echo esc($active[1]); ?></p>

                <div id="communityOpenState" style="border-top:1px solid rgba(255,255,255,.1);padding-top:18px;display:flex;flex-direction:column;gap:14px">
                    <div style="display:flex;gap:12px">
                        <img src="<?php echo asset_url('images/user.png'); ?>" alt="" style="width:42px;height:42px;border-radius:50%;object-fit:cover">
                        <input id="communityPostInput" class="rc-input" placeholder="Share something with <?php echo esc($active[0]); ?>..." style="background:#1d0a21!important;border-radius:12px!important">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:14px">
                        <div style="padding:16px;border-radius:14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                            <strong>Ada Okafor</strong>
                            <p style="margin:8px 0 0;color:rgba(255,255,255,.78)">How is everyone structuring Monday operating reviews after the conference?</p>
                        </div>
                        <div style="padding:16px;border-radius:14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                            <strong>Zainab Musa</strong>
                            <p style="margin:8px 0 0;color:rgba(255,255,255,.78)">We are trying a simple scorecard plus decision log. Happy to compare notes.</p>
                        </div>
                    </div>
                </div>

                <div id="communityLockedState" style="display:none;padding:48px 24px;border-radius:18px;background:rgba(34,16,38,.72);border:1px solid rgba(255,255,255,.1);flex-direction:column;align-items:center;gap:12px;text-align:center">
                    <i class="fa fa-lock" style="font-size:28px;color:#efb11e" aria-hidden="true"></i>
                    <span style="font-size:16px;font-weight:600">Private community</span>
                    <span style="font-size:14px;color:rgba(255,255,255,.75);max-width:360px;line-height:1.5">Posts are visible to Emergence 2026 founders and their mentors. Request access and the cohort lead will review it.</span>
                </div>
            </article>
        </section>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("click", function(event) {
    const option = event.target.closest(".rc-community-option");
    if (option) {
        document.querySelectorAll(".rc-community-option").forEach(function(item) {
            item.classList.remove("is-active");
            item.style.background = "rgba(34,16,38,.72)";
            item.style.borderColor = "rgba(255,255,255,.1)";
            const tile = item.querySelector("div");
            if (tile) {
                tile.style.background = "rgba(255,255,255,.08)";
            }
        });

        option.classList.add("is-active");
        option.style.background = "rgba(157,15,130,.38)";
        option.style.borderColor = "rgba(239,177,30,.55)";
        const activeTile = option.querySelector("div");
        if (activeTile) {
            activeTile.style.background = "linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585)";
        }

        const name = option.dataset.name;
        const desc = option.dataset.desc;
        const icon = option.dataset.icon;
        const meta = option.dataset.meta;
        const isPrivate = option.dataset.private === "1";
        const joined = option.dataset.joined === "1";
        const action = document.getElementById("communityAction");

        document.getElementById("communityActiveName").textContent = name;
        document.getElementById("communityActiveDesc").textContent = desc;
        document.getElementById("communityActiveMeta").textContent = meta;
        document.getElementById("communityActiveIcon").className = "fa " + icon;
        document.getElementById("communityPostInput").placeholder = "Share something with " + name + "...";

        document.getElementById("communityOpenState").style.display = isPrivate && !joined ? "none" : "flex";
        document.getElementById("communityLockedState").style.display = isPrivate && !joined ? "flex" : "none";

        action.classList.toggle("is-muted", joined);
        action.querySelector("i").className = "fa " + (joined ? "fa-check" : isPrivate ? "fa-lock" : "fa-plus");
        action.querySelector("span").textContent = joined ? "Joined" : isPrivate ? "Request access" : "Join community";
        action.dataset.state = joined ? "joined" : isPrivate ? "private" : "open";
        return;
    }

    const action = event.target.closest("#communityAction");
    if (action && action.dataset.state !== "joined") {
        const requested = action.dataset.requested === "1";
        action.dataset.requested = requested ? "0" : "1";
        action.classList.toggle("is-muted", !requested);
        action.querySelector("i").className = "fa " + (requested ? (action.dataset.state === "private" ? "fa-lock" : "fa-plus") : "fa-clock-o");
        action.querySelector("span").textContent = requested ? (action.dataset.state === "private" ? "Request access" : "Join community") : "Requested";
    }
});

document.addEventListener("keydown", function(event) {
    const option = event.target.closest(".rc-community-option");
    if (!option || (event.key !== "Enter" && event.key !== " ")) {
        return;
    }
    event.preventDefault();
    option.click();
});
</script>
</body>
</html>
