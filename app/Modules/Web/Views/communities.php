<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$tabs = [
    ['ATTENDEES', base_url('attendees/attendees'), false],
    ['SPEAKERS', base_url('attendees/speakers'), false],
    ['COMMUNITIES', base_url('attendees/communities'), true],
];
$communities = [
    [
        'name' => 'Founders Circle', 'icon' => 'fa-rocket', 'members' => 412, 'active' => 38, 'joined' => true, 'private' => false,
        'desc' => 'For founders building companies that run without them in every meeting.',
        'posts' => [
            ['id' => 'fc1', 'name' => 'Zainab Musa', 'role' => 'Founder · Kora Foods', 'ago' => '12m', 'text' => 'After this morning I’m moving our weekly review from Monday to Friday morning. Has anyone tried that? Curious whether it changed how your week started.', 'replies' => 8, 'likes' => 24],
            ['id' => 'fc2', 'name' => 'Ifeoma Obi', 'role' => 'Founder & CEO · Kindred Health', 'ago' => '34m', 'text' => 'Sharing the one-page operating calendar we use at Kindred Health. Happy to walk anyone through it during the 12:30 break.', 'replies' => 5, 'likes' => 31],
            ['id' => 'fc3', 'name' => 'Esther Danjuma', 'role' => 'Co-founder · Word Books & Apps', 'ago' => '1h', 'text' => 'What was the first system you put in place when you hired your first manager?', 'replies' => 14, 'likes' => 19],
        ],
    ],
    [
        'name' => 'Ops Leaders Guild', 'icon' => 'fa-cogs', 'members' => 286, 'active' => 21, 'joined' => false, 'private' => false,
        'desc' => 'Heads of operations swapping playbooks, templates and hard lessons.',
        'posts' => [
            ['id' => 'og1', 'name' => 'Grace Otieno', 'role' => 'Chief Operating Officer · Swiftroute', 'ago' => '8m', 'text' => 'Execution at Scale panel starts at 11:15. Drop your questions here and I’ll raise the ones the moderator doesn’t get to.', 'replies' => 6, 'likes' => 17],
            ['id' => 'og2', 'name' => 'Chidi Nwosu', 'role' => 'Operations Manager · Ledgerline', 'ago' => '40m', 'text' => 'We cut our weekly ops meeting from 90 to 35 minutes by sending the dashboard 24 hours ahead. Template in the replies.', 'replies' => 11, 'likes' => 42],
        ],
    ],
    [
        'name' => 'Women Who Execute', 'icon' => 'fa-bolt', 'members' => 358, 'active' => 44, 'joined' => false, 'private' => false,
        'desc' => 'A community for women leading teams, companies and change.',
        'posts' => [
            ['id' => 'ww1', 'name' => 'Bisi Lawal', 'role' => 'Head of People · Savewell', 'ago' => '20m', 'text' => 'Meetup at the Emergence Booth right after Day 1 close. Everyone is welcome.', 'replies' => 9, 'likes' => 36],
            ['id' => 'ww2', 'name' => 'Halima Bello', 'role' => 'Finance Lead · Tallypay', 'ago' => '1h', 'text' => 'Looking for an accountability partner for the 30-day systems challenge Kemi mentioned this morning.', 'replies' => 7, 'likes' => 22],
        ],
    ],
    [
        'name' => 'Finance & Growth Desk', 'icon' => 'fa-line-chart', 'members' => 194, 'active' => 12, 'joined' => false, 'private' => false,
        'desc' => 'Cash flow, fundraising and the numbers behind sustainable growth.',
        'posts' => [
            ['id' => 'fg1', 'name' => 'Femi Adebayo', 'role' => 'Head of Strategy · Crestfield Bank', 'ago' => '25m', 'text' => 'How are you forecasting cash with FX this volatile? Monthly re-forecast or a rolling 13-week view?', 'replies' => 10, 'likes' => 15],
        ],
    ],
    [
        'name' => 'Tech Builders', 'icon' => 'fa-code', 'members' => 231, 'active' => 17, 'joined' => false, 'private' => false,
        'desc' => 'Product and engineering leaders building the tools teams run on.',
        'posts' => [
            ['id' => 'tb1', 'name' => 'Kwame Asante', 'role' => 'Product Lead · Paylink', 'ago' => '15m', 'text' => 'What are you using to keep decision logs? We tried a wiki and lost track within a month.', 'replies' => 12, 'likes' => 20],
            ['id' => 'tb2', 'name' => 'Samuel Eze', 'role' => 'Engineering Manager · Codeforge', 'ago' => '50m', 'text' => 'Hiring two senior engineers in Accra. Message me if you know someone who likes boring, reliable systems.', 'replies' => 3, 'likes' => 28],
        ],
    ],
    [
        'name' => 'Emergence Cohort 2026', 'icon' => 'fa-star', 'members' => 48, 'active' => 9, 'joined' => false, 'private' => true,
        'desc' => 'Private space for the 2026 Emergence founders and their mentors.', 'posts' => [],
    ],
];
$active = $communities[0];
$initials = static function (string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    return strtoupper(substr($parts[0] ?? 'A', 0, 1) . substr($parts[1] ?? '', 0, 1));
};
?>

<main class="rc-page rc-page-center" data-screen-label="Communities">
    <div class="rc-container" style="gap:24px">
        <header class="rc-net-sub-header">
            <div class="rc-net-heading">
                <a href="<?php echo base_url('attendees/networking_center'); ?>" class="rc-back-link"><i class="fa fa-arrow-left" aria-hidden="true"></i>NETWORKING CENTER</a>
                <span class="rc-page-title">Communities</span>
                <span class="rc-page-sub"><?php echo count($communities); ?> communities · join to post and reply</span>
            </div>
            <div class="rc-net-tabs">
                <?php foreach ($tabs as [$label, $url, $activeTab]): ?><a href="<?php echo $url; ?>" class="rc-net-tab <?php echo $activeTab ? 'is-active' : ''; ?>"><?php echo esc($label); ?></a><?php endforeach; ?>
            </div>
        </header>

        <section class="rc-community-layout">
            <div class="rc-community-list">
                <?php foreach ($communities as $index => $community): ?>
                    <button class="rc-community-option<?php echo $index === 0 ? ' is-active' : ''; ?>" type="button" data-community-index="<?php echo $index; ?>">
                        <span class="rc-community-option-icon"><i class="fa <?php echo $community['icon']; ?>" aria-hidden="true"></i></span>
                        <span class="rc-community-option-copy"><strong><?php echo esc($community['name']); ?></strong><small><?php echo $community['members']; ?> members · <?php echo $community['active']; ?> active now</small></span>
                        <?php if ($community['joined']): ?><span class="rc-community-joined">JOINED</span><?php elseif ($community['private']): ?><i class="fa fa-lock rc-community-lock" aria-hidden="true"></i><?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="rc-community-feed">
                <article class="rc-community-hero">
                    <div class="rc-community-hero-head">
                        <span class="rc-community-hero-icon"><i id="communityActiveIcon" class="fa <?php echo $active['icon']; ?>" aria-hidden="true"></i></span>
                        <div class="rc-community-hero-copy"><h2 id="communityActiveName"><?php echo esc($active['name']); ?></h2><p id="communityActiveDesc"><?php echo esc($active['desc']); ?></p></div>
                        <button id="communityAction" class="rc-community-action is-joined" type="button" data-state="joined"><i class="fa fa-check" aria-hidden="true"></i><span>Joined</span></button>
                    </div>
                    <div class="rc-community-meta"><span class="rc-community-stack"><span>ZM</span><span>IO</span><span>ED</span><span>GO</span><span>BL</span></span><span id="communityActiveMeta"><?php echo $active['members']; ?> members · <?php echo $active['active']; ?> active now</span></div>
                </article>

                <div id="communityComposer" class="rc-community-composer">
                    <img src="<?php echo asset_url('images/user.png'); ?>" alt="">
                    <input id="communityPostInput" type="text" placeholder="Share something with <?php echo esc($active['name']); ?>...">
                    <button id="communityPostButton" type="button">Post</button>
                </div>
                <div id="communityJoinHint" class="rc-community-join-hint" hidden>Join this community to post and reply.</div>
                <div id="communityLockedState" class="rc-community-locked" hidden><i class="fa fa-lock" aria-hidden="true"></i><strong>Private community</strong><span>Posts are visible to Emergence 2026 founders and their mentors. Request access and the cohort lead will review it.</span></div>

                <div id="communityPosts" class="rc-community-posts">
                    <?php foreach ($active['posts'] as $post): ?>
                        <article class="rc-community-post" data-post-id="<?php echo esc($post['id']); ?>">
                            <header><span class="rc-community-post-avatar"><?php echo $initials($post['name']); ?></span><span><strong><?php echo esc($post['name']); ?></strong><small><?php echo esc($post['role']); ?></small></span><time><?php echo esc($post['ago']); ?></time></header>
                            <p><?php echo esc($post['text']); ?></p>
                            <footer><button type="button" data-community-like><i class="fa fa-heart-o" aria-hidden="true"></i><span><?php echo $post['likes']; ?></span></button><span><i class="fa fa-comment-o" aria-hidden="true"></i><?php echo $post['replies']; ?> replies</span></footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
(() => {
    const communities = <?php echo json_encode($communities, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    const userAvatar = <?php echo json_encode(asset_url('images/user.png')); ?>;
    let activeIndex = 0;
    let newPosts = {};

    const initials = name => name.split(/\s+/).slice(0, 2).map(part => part.charAt(0)).join("").toUpperCase();
    const escapeHtml = value => String(value).replace(/[&<>"']/g, char => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"})[char]);
    const postMarkup = post => `<article class="rc-community-post" data-post-id="${escapeHtml(post.id)}"><header>${post.me ? `<img class="rc-community-post-image" src="${userAvatar}" alt="">` : `<span class="rc-community-post-avatar">${initials(post.name)}</span>`}<span><strong>${escapeHtml(post.name)}</strong><small>${escapeHtml(post.role || "Attendee")}</small></span><time>${escapeHtml(post.ago)}</time></header><p>${escapeHtml(post.text)}</p><footer><button type="button" data-community-like><i class="fa fa-heart-o" aria-hidden="true"></i><span>${post.likes}</span></button><span><i class="fa fa-comment-o" aria-hidden="true"></i>${post.replies} replies</span></footer></article>`;

    function render(index) {
        activeIndex = index;
        const community = communities[index];
        document.querySelectorAll(".rc-community-option").forEach((button, i) => button.classList.toggle("is-active", i === index));
        document.getElementById("communityActiveIcon").className = "fa " + community.icon;
        document.getElementById("communityActiveName").textContent = community.name;
        document.getElementById("communityActiveDesc").textContent = community.desc;
        document.getElementById("communityActiveMeta").textContent = `${community.members} members · ${community.active} active now`;
        document.getElementById("communityPostInput").placeholder = `Share something with ${community.name}...`;

        const action = document.getElementById("communityAction");
        const state = community.joined ? "joined" : community.private ? "private" : "open";
        action.dataset.state = state;
        action.dataset.requested = "0";
        action.className = "rc-community-action" + (community.joined ? " is-joined" : "");
        action.querySelector("i").className = "fa " + (community.joined ? "fa-check" : community.private ? "fa-lock" : "fa-plus");
        action.querySelector("span").textContent = community.joined ? "Joined" : community.private ? "Request access" : "Join community";

        const locked = community.private && !community.joined;
        document.getElementById("communityLockedState").hidden = !locked;
        document.getElementById("communityPosts").hidden = locked;
        document.getElementById("communityComposer").hidden = !community.joined;
        document.getElementById("communityJoinHint").hidden = community.joined || locked;
        document.getElementById("communityPosts").innerHTML = [...(newPosts[community.name] || []), ...community.posts].map(postMarkup).join("");
    }

    document.addEventListener("click", event => {
        const option = event.target.closest("[data-community-index]");
        if (option) { render(Number(option.dataset.communityIndex)); return; }
        const action = event.target.closest("#communityAction");
        if (action && action.dataset.state !== "joined") {
            const requested = action.dataset.requested !== "1";
            action.dataset.requested = requested ? "1" : "0";
            action.classList.toggle("is-requested", requested);
            action.querySelector("i").className = "fa " + (requested ? "fa-clock-o" : action.dataset.state === "private" ? "fa-lock" : "fa-plus");
            action.querySelector("span").textContent = requested ? "Requested" : action.dataset.state === "private" ? "Request access" : "Join community";
            return;
        }
        const like = event.target.closest("[data-community-like]");
        if (like) {
            const active = like.classList.toggle("is-liked");
            const count = like.querySelector("span");
            count.textContent = String(Number(count.textContent) + (active ? 1 : -1));
            like.querySelector("i").className = "fa " + (active ? "fa-heart" : "fa-heart-o");
        }
    });

    const submitPost = () => {
        const input = document.getElementById("communityPostInput");
        const text = input.value.trim();
        if (!text) return;
        const community = communities[activeIndex];
        newPosts[community.name] = [{ id: "new-" + Date.now(), name: "You", role: "Attendee", ago: "Just now", text, replies: 0, likes: 0, me: true }, ...(newPosts[community.name] || [])];
        input.value = "";
        render(activeIndex);
    };
    document.getElementById("communityPostButton")?.addEventListener("click", submitPost);
    document.getElementById("communityPostInput")?.addEventListener("keydown", event => { if (event.key === "Enter") submitPost(); });
})();
</script>
</body>
</html>
