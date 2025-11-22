<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 16:31
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

/**
 * NEW Universal Premium Access Variables
 * --------------------------------------
 * $premium is passed from controller as $user['premium_access']
 */
$userPremium = $premium ?? 0;
$timezone    = $timezone ?? 'Africa/Lagos';

$conferenceId = $conference_id ?? null;

?>

<style>
    body { font-family: 'Poppins', sans-serif; background-color: #fafafa; }
    h4.text-epr { font-weight: 700; letter-spacing: 0.5px; }
    .nav-pills { display: flex; flex-wrap: wrap; gap: 10px; margin-left: 3px; position: relative; top: 5px; border: none; }
    .nav-pills .nav-link {
        border: 2px solid #9e3383; color: #9D0F82; border-top-left-radius: 10px; border-top-right-radius: 10px;
        padding: 8px 20px; font-weight: 500; transition: all 0.25s ease;
    }
    .nav-pills .nav-link.active {
        background: url("<?php echo asset_url('images/button-bg-short.png'); ?>") repeat-y;
        color: #fff; box-shadow: 0 3px 10px rgba(157, 15, 130, 0.3);
    }
    #sessionsContainer { background-color: #fff; border: 6px solid #9D0F82; border-radius: 12px; padding: 25px; min-height: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .session-item { padding: 15px 10px; border-bottom: 1px solid #cacaca; transition: 0.2s ease; }
    .session-item:hover { background-color: #faf3f9; }
    .session-item.locked { opacity: 0.6; cursor: not-allowed; }
    .btn-epr-purple { background-color: #9D0F82; color: white; border: none; border-radius: 6px; font-size: 13px; padding: 6px 12px; }
    .btn-epr-purple:hover { background-color: #7a0e69; }
    .btn-light { color: #aaa; border-radius: 6px; font-size: 13px; }
</style>

<div class="container py-5"F>
    <h4 class="text-epr mb-4 text-left" style="color: #9D0F82;">AGENDA</h4>

    <?php if (empty($sessionsByDate)): ?>
        <p class="text-center text-muted mt-4">No sessions found for this conference.</p>
    <?php else: ?>

        <!-- Day Tabs -->
        <ul class="nav nav-pills" id="dayTabs">
            <?php $first = true; foreach ($sessionsByDate as $date => $sessions): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $first ? 'active' : ''; ?>" data-toggle="tab"
                       href="#day-<?php echo str_replace('-', '', $date); ?>">
                        <?php echo date('F j, Y', strtotime($date)); ?>
                    </a>
                </li>
                <?php $first = false; endforeach; ?>
        </ul>

        <!-- Sessions Container -->
        <div class="tab-content" id="sessionsContainer">
            <?php $first = true; foreach ($sessionsByDate as $date => $sessions): ?>
                <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>" id="day-<?php echo str_replace('-', '', $date); ?>">

                    <?php foreach ($sessions as $s): ?>
                        <?php
                        // Time formatting
                        $start = (new DateTime($s['event_date'].' '.$s['start_time']))->format('h:i A');
                        $end   = (new DateTime($s['event_date'].' '.$s['end_time']))->format('h:i A');

                        // NEW Universal Premium Access Logic
                        $isPaidSession = ($s['access_level'] == 2);
                        $canAccess     = (!$isPaidSession) || ($userPremium == 1);
                        ?>

                        <div class="session-item <?php echo $canAccess ? '' : 'locked'; ?>">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">

                                <div>
                                    <h6><strong><?php echo esc($s['sessions_name']); ?></strong>
                                        <?php if ($isPaidSession && !$canAccess): ?>
                                            <img src="<?php echo asset_url('images/paid-session.svg'); ?>" class="lock-icon" width="20" alt="Locked">
                                        <?php endif; ?>
                                    </h6>
                                    <span class="font-12"><?php echo $start; ?> - <?php echo $end; ?> (<?php echo esc($timezone); ?>)</span><br>

                                    <?php if (!empty($s['speakers'])): ?>
                                        <span class="font-12">
                                            Speakers:
                                            <?php
                                            $speakerList = array_map(function ($sp) {
                                                return esc($sp['name']) .
                                                    (!empty($sp['company']) ? ' (' . esc($sp['company']) . ')' : '');
                                            }, $s['speakers']);
                                            echo implode(', ', $speakerList);
                                            ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="font-12 text-muted">Speakers: TBA</span>
                                    <?php endif; ?>
                                </div>

                                <!-- SESSION BUTTONS -->
                                <div class="mt-2 mt-md-0">
                                    <?php if ($canAccess): ?>
                                        <a href="<?php echo base_url('attendees/sessions/' . $s['sessions_id']); ?>"
                                           class="btn btn-epr-purple btn-sm">View Session</a>
                                    <?php else: ?>
                                        <a href="<?php echo base_url('attendees/upgrade/' . $conference_id); ?>"
                                           class="btn btn-warning btn-sm"
                                           style="background:#FFC107;border:none;color:#000;font-weight:600;">
                                            Upgrade to Premium
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
                <?php $first = false; endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
