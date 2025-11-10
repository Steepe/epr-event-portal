<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 15:00
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<div class="container py-5">
    <h4 class="text-epr mb-4" style="color:#9D0F82;">
        <?php echo esc($conference['title']); ?> — Agenda
    </h4>

    <div class="tab-content border rounded p-4 bg-white shadow-sm">
        <?php if (empty($sessions)): ?>
            <p class="text-center text-muted">No sessions found for this conference.</p>
        <?php else: ?>
            <?php foreach ($sessions as $s): ?>
                <div class="session-item mb-3 pb-3 border-bottom">
                    <h6><strong><?php echo esc($s['sessions_name']); ?></strong></h6>
                    <p class="text-muted small mb-1">
                        <?php echo date('F j, Y', strtotime($s['event_date'])); ?> —
                        <?php echo substr($s['start_time'], 0, 5); ?> - <?php echo substr($s['end_time'], 0, 5); ?>
                    </p>
                    <p class="text-muted small mb-2">Speakers: <?php echo esc($s['speakers'] ?: 'TBA'); ?></p>
                    <a href="<?php echo base_url('attendees/sessions/' . $s['sessions_id']); ?>"
                       class="btn btn-epr-purple btn-sm">View Session</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
