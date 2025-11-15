<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:41
 */

echo module_view('MobileApp', 'includes/header');

$plan = $plan ?? 1;
$timezone = $timezone ?? 'Africa/Lagos';
?>

<style>

    body {
        background-image: url('<?php echo asset_url('images/mobile-brain-light.png'); ?>');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed; /* 🔥 Keeps background fixed during scroll */
        font-family: 'Inter', 'Poppins', sans-serif;
        min-height: 100vh;
        color: #A70B91;
        overflow-x: hidden;
        overflow-y: auto; /* 🔥 Ensure only content scrolls */
    }

    /* 🔥 Add a transparent overlay for legibility */
    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(6px);
        z-index: -1;
    }

    /* Ensure content sits above fixed background */
    .sessions-wrapper {
        position: relative;
        z-index: 2;
        padding: 0 1rem 3rem;
        overflow-y: auto;
    }


    /* 🎥 Background Video */
    #bgVideo {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: blur(6px) brightness(0.6);
        z-index: -2;
    }

    /* 🌈 Overlay Gradient */
    #videoOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    h4.text-epr {
        color: #A70B91;
        text-align: center;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-top: 100px;
        margin-bottom: 1.5rem;
    }

    /* 📅 Day Pills */
    .day-pills {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-bottom: 1.5rem;
        z-index: 1;
        position: relative;
    }

    .day-pill {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #fff;
        padding: 7px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .day-pill.active {
        border: none;
        color: #A70B91;
        font-weight: 600;
    }

    /* 🧾 Session Cards */
    .sessions-wrapper {
        padding: 0 1rem 3rem;
        position: relative;
        z-index: 2;
    }

    .session-card {
        background: rgba(255, 255, 255, 1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 18px;
        padding: 1rem 1rem 0.8rem;
        margin-bottom: 1rem;
        transition: transform 0.25s ease;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.25);
    }

    .session-card.locked {
        opacity: 0.6;
        pointer-events: none;
    }

    .session-card:hover {
        transform: scale(1.03);
    }

    .session-title {
        font-weight: 600;
        font-size: 1rem;
        color: #000000;
        margin-bottom: .4rem;
    }

    .session-time {
        font-size: 0.85rem;
        color: rgba(80, 78, 78, 0.85);
        margin-bottom: 0.4rem;
    }

    .session-speakers {
        font-size: 0.85rem;
        color: #A70B91;
        margin-bottom: 0.8rem;
    }

    .session-actions {
        text-align: right;
    }

    .btn-epr {
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 200;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-epr:hover {
        transform: scale(1.05);
    }

    .btn-light {
        background: rgba(255, 255, 255, 0.15);
        color: #bbb;
        border: none;
        border-radius: 25px;
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .no-sessions {
        text-align: center;
        color: #ccc;
        margin-top: 50px;
        font-size: 0.9rem;
    }

    @media (max-width: 480px) {
        .session-card {
            padding: 1rem;
        }
        h4.text-epr {
            margin-top: 80px;
            font-size: 1.2rem;
        }
    }
</style>


<h4 class="text-epr">AGENDA</h4>

<div class="sessions-wrapper">
<?php if (empty($sessions)): ?>
    <p class="no-sessions">No sessions found for this conference.</p>
<?php else: ?>
    <?php
    // Group sessions by event_date
    $sessionsByDate = [];
    foreach ($sessions as $s) {
        $sessionsByDate[$s['event_date']][] = $s;
    }
    ?>

    <!-- 📅 Date Tabs -->
    <div class="day-pills" id="dayPills">
        <?php $first = true; foreach ($sessionsByDate as $date => $list): ?>
            <div class="day-pill <?php echo $first ? 'active' : ''; ?>"
                 data-target="day-<?php echo str_replace('-', '', $date); ?>">
                <?php echo date('M j, Y', strtotime($date)); ?>
            </div>
        <?php $first = false; endforeach; ?>
    </div>

    <!-- 🧾 Session Lists -->
    <div id="sessionsContainer">
        <?php $first = true; foreach ($sessionsByDate as $date => $list): ?>
            <div class="session-day <?php echo $first ? '' : 'hidden'; ?>"
                 id="day-<?php echo str_replace('-', '', $date); ?>">
                <?php foreach ($list as $s): ?>
                    <?php
                        $start = (new DateTime($s['event_date'].' '.$s['start_time']))->format('h:i A');
                        $end   = (new DateTime($s['event_date'].' '.$s['end_time']))->format('h:i A');
                        $isPaid = $s['access_level'] == 2;
                        $canAccess = ($plan ?? 1) >= $s['access_level'];
                    ?>
                    <div class="session-card <?php echo $canAccess ? '' : 'locked'; ?>">
                        <div class="session-title"><?php echo esc($s['sessions_name']); ?></div>
                        <div class="session-time"><?php echo $start; ?> - <?php echo $end; ?> (GMT)</div>
                        <div class="session-speakers">
                            <?php if (!empty($s['speakers'])): ?>
                                Speakers:
                                <?php echo implode(', ', array_column($s['speakers'], 'speaker_name')); ?>
                            <?php else: ?>
                                Speakers: TBA
                            <?php endif; ?>
                        </div>
                        <div class="session-actions">
                            <?php if ($canAccess): ?>
                                <a href="<?php echo site_url('mobile/session/'.$s['sessions_id']); ?>" class="btn-epr epr-btn-one">View Session</a>
                            <?php else: ?>
                                <button class="btn-light" disabled>Locked</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php $first = false; endforeach; ?>
    </div>
<?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const pills = document.querySelectorAll('.day-pill');
    const sections = document.querySelectorAll('.session-day');

    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            pills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');

            sections.forEach(sec => sec.classList.add('hidden'));

            const target = pill.getAttribute('data-target');
            document.getElementById(target).classList.remove('hidden');
        });
    });
});
</script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
