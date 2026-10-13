<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$team = [
    ['Tolu Adeyemi', 'Programme Lead', 'online'],
    ['Ifeoma Eze', 'Partnerships', 'online'],
    ['Seun Bakare', 'Community Manager', 'away'],
];

$programmes = [
    ['Emergence Fellowship', '12 WEEKS · COHORT', 'A guided cohort for founders installing the operating systems that let a business run without them.', 'fa-rocket'],
    ['Executive Roundtables', 'MONTHLY · INVITE ONLY', 'Closed-door sessions where COOs and CEOs pressure-test execution plans with peers.', 'fa-comments'],
    ['EPR Global Webinars', 'BI-WEEKLY · FREE', 'Short, practical sessions on cadence, metrics and accountability, with live Q&A.', 'fa-video-camera'],
];

$resources = [
    ['Accelerate 2026 Workbook', 'PDF · 4.8 MB', 'fa-file-pdf-o'],
    ['Weekly Operating Review template', 'XLSX · 120 KB', 'fa-file-excel-o'],
    ['Systems Audit checklist', 'PDF · 640 KB', 'fa-file-pdf-o'],
    ['Emergence 2025 Impact Report', 'PDF · 3.1 MB', 'fa-file-pdf-o'],
];

$topics = ['Programmes', 'Partnerships', 'Speaking', 'Resources'];
?>

<main class="rc-page rc-page-center" data-screen-label="Emergence Booth">
    <div class="rc-container" style="max-width:1120px;gap:28px">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap">
            <div class="rc-title-stack">
                <span class="rc-kicker">EPR Global &middot; Host Booth</span>
                <span class="rc-page-title">Emergence Booth</span>
                <span class="rc-page-sub" style="max-width:680px;line-height:1.65">Meet the team behind Accelerate. Explore our programmes, take the toolkit home, or book time with us.</span>
            </div>
            <span style="display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:999px;background:rgba(34,16,38,.72);border:1px solid rgba(255,255,255,.14);font-size:12px">
                <span style="width:8px;height:8px;border-radius:50%;background:#4ade80;box-shadow:0 0 10px #4ade80"></span>
                2 team members online
            </span>
        </div>

        <section class="rc-em-top-grid" style="display:grid;grid-template-columns:minmax(0,1.45fr) minmax(280px,1fr);gap:24px">
            <div class="rc-emergence-video">
                <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="" style="width:46%;height:auto;opacity:.22">
                <button type="button" class="rc-em-play" aria-label="Play welcome film" style="position:absolute;width:76px;height:76px;border:0;border-radius:50%;background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585);color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;cursor:pointer;box-shadow:0 0 0 10px rgba(216,25,142,.16),0 0 40px rgba(216,25,142,.55);transition:transform .25s ease">
                    <i class="fa fa-play" aria-hidden="true"></i>
                </button>
                <span style="position:absolute;top:16px;left:16px;display:flex;align-items:center;gap:8px;font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:.16em;padding:6px 10px;border-radius:999px;background:rgba(18,5,15,.78)">
                    <i class="fa fa-film" aria-hidden="true"></i>WELCOME FILM
                </span>
                <div style="position:absolute;left:0;right:0;bottom:0;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;background:linear-gradient(0deg, rgba(10,2,12,.85), rgba(10,2,12,0))">
                    <span style="font-size:14px;font-weight:600">Why we build systems, not hype</span>
                    <span class="rc-mono" style="font-size:11px;color:rgba(255,255,255,.7)">2:14</span>
                </div>
            </div>

            <aside class="rc-panel" style="padding:22px;display:flex;flex-direction:column;gap:16px">
                <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)">AT THE BOOTH</span>
                <?php foreach ($team as [$name, $role, $status]): ?>
                    <?php $initials = implode('', array_map(static fn($part) => strtoupper(substr($part, 0, 1)), explode(' ', $name))); ?>
                    <div style="display:flex;align-items:center;gap:12px">
                        <span style="position:relative;flex:none;width:46px;height:46px;border-radius:50%;background:#9d0f82;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:15px">
                            <?php echo esc($initials); ?>
                            <span style="position:absolute;right:0;bottom:0;width:11px;height:11px;border-radius:50%;background:<?php echo $status === 'online' ? '#4ade80' : '#efb11e'; ?>;box-shadow:0 0 0 2px #1e0a22"></span>
                        </span>
                        <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:2px">
                            <span style="font-size:14px;font-weight:600"><?php echo esc($name); ?></span>
                            <span style="font-size:12px;color:rgba(255,255,255,.7)"><?php echo esc($role); ?></span>
                        </div>
                        <button class="rc-card-action rc-em-book" type="button" style="flex:none;width:auto;padding:0 14px;font-size:12px">
                            <i class="fa fa-calendar" aria-hidden="true"></i><span>Book 15 min</span>
                        </button>
                    </div>
                <?php endforeach; ?>
                <span style="margin-top:auto;padding-top:14px;border-top:1px solid rgba(255,255,255,.08);font-size:12px;color:rgba(255,255,255,.65);line-height:1.5">Booth hours: 9:00 AM - 5:00 PM WAT, all conference days.</span>
            </aside>
        </section>

        <section style="display:flex;flex-direction:column;gap:14px">
            <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)">PROGRAMMES</span>
            <div class="rc-em-programmes" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px">
                <?php foreach ($programmes as [$name, $tag, $desc, $icon]): ?>
                    <article class="rc-emergence-card">
                        <span style="width:44px;height:44px;border-radius:12px;background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585);display:flex;align-items:center;justify-content:center;font-size:18px">
                            <i class="fa <?php echo $icon; ?>" aria-hidden="true"></i>
                        </span>
                        <span class="rc-mono" style="font-size:10px;letter-spacing:.16em;color:#efb11e"><?php echo esc($tag); ?></span>
                        <h3 style="margin:0;font-size:18px;font-weight:600;letter-spacing:-.01em"><?php echo esc($name); ?></h3>
                        <p style="margin:0;color:rgba(255,255,255,.72);font-size:13px;line-height:1.55"><?php echo esc($desc); ?></p>
                        <button type="button" class="rc-card-action rc-em-interest" style="margin-top:auto;align-self:flex-start;flex:none;width:auto;padding:0 16px;font-size:13px">
                            <i class="fa fa-plus" aria-hidden="true"></i><span>Register interest</span>
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="rc-em-two-col" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px">
            <div class="rc-panel" style="padding:22px;display:flex;flex-direction:column;gap:4px">
                <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6);margin-bottom:10px">TAKE-HOME TOOLKIT</span>
                <?php foreach ($resources as [$name, $meta, $icon]): ?>
                    <div class="rc-resource-row">
                        <span style="flex:none;width:40px;height:40px;border-radius:10px;background:rgba(239,177,30,.14);color:#efb11e;display:flex;align-items:center;justify-content:center;font-size:17px">
                            <i class="fa <?php echo $icon; ?>" aria-hidden="true"></i>
                        </span>
                        <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:2px">
                            <span style="font-size:14px;font-weight:500"><?php echo esc($name); ?></span>
                            <span class="rc-mono" style="font-size:10px;letter-spacing:.1em;color:rgba(255,255,255,.55)"><?php echo esc($meta); ?></span>
                        </div>
                        <button class="rc-icon-action rc-resource-action" type="button" title="Download" aria-label="Download">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="rc-panel" style="padding:22px;display:flex;flex-direction:column;gap:14px">
                <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)">LEAVE YOUR CARD</span>
                <div class="rc-em-form">
                    <span style="font-size:13px;color:rgba(255,255,255,.75);line-height:1.5">Tell us what you'd like to talk about and the team will reach out within one working day.</span>
                    <div style="display:flex;flex-wrap:wrap;gap:8px">
                        <?php foreach ($topics as $index => $topic): ?>
                            <button type="button" class="rc-outline-pill rc-em-topic <?php echo $index === 0 ? 'is-active' : ''; ?>" style="<?php echo $index === 0 ? 'background:rgba(157,15,130,.55);border-color:#d8198e' : ''; ?>"><?php echo esc($topic); ?></button>
                        <?php endforeach; ?>
                    </div>
                    <textarea class="rc-input" rows="4" placeholder="Add a short note (optional)" style="height:116px;padding:12px 14px!important;resize:none"></textarea>
                    <button type="button" class="rc-btn rc-em-send" style="height:46px;text-transform:none;letter-spacing:0;display:flex;align-items:center;justify-content:center;gap:10px">
                        <i class="fa fa-phone" aria-hidden="true"></i>Request a callback
                    </button>
                </div>
                <div class="rc-em-sent" style="display:none;flex:1;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:12px;padding:20px 0">
                    <span style="width:56px;height:56px;border-radius:50%;background:rgba(74,222,128,.14);color:#4ade80;display:flex;align-items:center;justify-content:center;font-size:22px"><i class="fa fa-check" aria-hidden="true"></i></span>
                    <span style="font-size:17px;font-weight:600">Card received</span>
                    <span style="font-size:13px;color:rgba(255,255,255,.72);max-width:300px;line-height:1.5">Thanks. The Emergence team will follow up with you shortly.</span>
                    <button type="button" class="rc-outline-pill rc-em-reset">Send another</button>
                </div>
            </div>
        </section>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("click", function(event) {
    const play = event.target.closest(".rc-em-play");
    if (play) {
        const icon = play.querySelector("i");
        const isPlaying = icon.classList.contains("fa-pause");
        icon.className = "fa " + (isPlaying ? "fa-play" : "fa-pause");
        return;
    }

    const book = event.target.closest(".rc-em-book");
    if (book) {
        book.classList.toggle("is-booked");
        const booked = book.classList.contains("is-booked");
        book.querySelector("i").className = "fa " + (booked ? "fa-check" : "fa-calendar");
        book.querySelector("span").textContent = booked ? "Booked" : "Book 15 min";
        return;
    }

    const interest = event.target.closest(".rc-em-interest");
    if (interest) {
        const card = interest.closest(".rc-emergence-card");
        interest.classList.toggle("is-registered");
        card?.classList.toggle("is-registered");
        const registered = interest.classList.contains("is-registered");
        interest.querySelector("i").className = "fa " + (registered ? "fa-check" : "fa-plus");
        interest.querySelector("span").textContent = registered ? "Interest registered" : "Register interest";
        return;
    }

    const resource = event.target.closest(".rc-resource-action");
    if (resource) {
        resource.classList.toggle("is-saved");
        const saved = resource.classList.contains("is-saved");
        resource.querySelector("i").className = "fa " + (saved ? "fa-check" : "fa-download");
        resource.setAttribute("title", saved ? "Saved" : "Download");
        resource.setAttribute("aria-label", saved ? "Saved" : "Download");
        return;
    }

    const topic = event.target.closest(".rc-em-topic");
    if (topic) {
        document.querySelectorAll(".rc-em-topic").forEach(function(item) {
            item.classList.remove("is-active");
            item.style.background = "";
            item.style.borderColor = "";
        });
        topic.classList.add("is-active");
        topic.style.background = "rgba(157,15,130,.55)";
        topic.style.borderColor = "#d8198e";
        return;
    }

    if (event.target.closest(".rc-em-send")) {
        document.querySelector(".rc-em-form").style.display = "none";
        document.querySelector(".rc-em-sent").style.display = "flex";
        return;
    }

    if (event.target.closest(".rc-em-reset")) {
        document.querySelector(".rc-em-form").style.display = "flex";
        document.querySelector(".rc-em-sent").style.display = "none";
    }
});
</script>
</body>
</html>
