<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 00:09
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<style>
body {
    background: url('<?php echo asset_url('images/brain-Events-Portal4-2025.png'); ?>') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
    font-family: 'Poppins', sans-serif;
}

.attendees-container {
    padding: 100px 10%;
    min-height: 100vh;
}

.attendees-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.attendees-header h2 {
    color: #fff;
    font-weight: 700;
    text-transform: uppercase;
}

.search-box input {
    border-radius: 30px;
    border: solid 1px #9D0F82;
    padding: 10px 20px;
    width: 280px;
    outline: none;
    background: rgba(255,255,255,0.1);
    color: #fff;
}
.search-box input::placeholder {
    color: rgba(255,255,255,0.7);
}

.alphabet-filter {
    text-align: right;
    margin-bottom: 40px;
}
.alphabet-filter span,
.alphabet-filter a {
    color: #5a0a5e;
    font-size: 13px;
    margin: 0 3px;
    text-decoration: none;
    transition: color .3s ease;
}
.alphabet-filter a:hover,
.alphabet-filter a.active {
    color: #866387;
    text-decoration: underline;
}

/* === Grid layout === */
.attendees-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    justify-items: center;
}

.attendee-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    width: 100%;
    max-width: 320px;
    transition: background .3s ease, transform .3s ease;
}
.attendee-card:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-4px);
}

.attendee-card img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid transparent;
    margin-bottom: 15px;
    transition: transform .4s ease, box-shadow .4s ease;
}
.attendee-card:hover img {
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(255,255,255,0.3);
}

.attendee-name {
    font-size: 16px;
    font-weight: 600;
    color: #5a0a5e;
    margin-bottom: 10px;
    text-transform: capitalize;
}

.say-hello-btn {
    background-image: url("<?php echo asset_url('images/button-bg-short.png')?>");
    background-repeat: repeat-y;
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 30px;
    padding: 8px 25px;
    transition: all .3s ease;
    white-space: nowrap;
}
.say-hello-btn:hover {
    background-image: url("<?php echo asset_url('images/button-light-bg-short.png')?>");
    transform: scale(1.05);
}

/* === Modal styling === */
.modal-content {
    border-radius: 12px;
    background-color: #fff;
}
.modal-header {
    border: none;
}
.modal-body textarea {
    border: 2px solid #9D0F82;
    border-radius: 10px;
    width: 100%;
    padding: 12px;
    font-size: 14px;
    resize: none;
    outline: none;
}
.btn-epr-purple {
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 25px;
    padding: 8px 24px;
    transition: all .3s ease;
}
.btn-epr-purple:hover {
}

@media (max-width: 992px) {
    .attendees-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 600px) {
    .attendees-grid {
        grid-template-columns: 1fr;
    }
}

</style>

<div class="attendees-container">
    <div class="attendees-header">
        <h2 style="color: #5a0a5e !important;">Attendees</h2>
        <form class="search-box" method="get">
            <input type="text" name="attendee_name" placeholder="Search by name..." />
        </form>
    </div>

    <div class="alphabet-filter">
        <span>Sort By:</span>
        <?php if (!empty($alphabet_count)): ?>
            <?php foreach ($alphabet_count as $alphabet): ?>
                <a href="?sort=<?php echo $alphabet['firstCharacter']; ?>"><?php echo $alphabet['firstCharacter']; ?></a>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="<?php echo base_url('attendees/attendees'); ?>">View All</a>
    </div>

    <div class="attendees-grid">
        <?php if (!empty($attendees)): ?>
            <?php foreach ($attendees as $attendee): ?>
                <?php
                    $firstLetter = strtoupper(substr($attendee['firstname'], 0, 1));
                    $image = base_url('uploads/attendee_pictures/' . $attendee['profile_picture']);
                ?>
                <div class="attendee-card">
                    <img src="<?php echo $image; ?>"
                         alt="<?php echo $attendee['firstname']; ?>"
                         onerror="this.src='<?php echo asset_url('images/default_pics/'.$firstLetter.'3.png'); ?>';">

                    <div class="attendee-name">
                        <?php echo ucfirst(strtolower($attendee['firstname'])) . ' ' . ucfirst(strtolower($attendee['lastname'])); ?>
                    </div>

                    <button class="say-hello-btn epr-btn-four" data-toggle="modal" data-target=".chat-<?php echo $attendee['attendee_id']; ?>">
                        Say Hello
                    </button>
                </div>

                <!-- 💬 Message Modal -->
                <div class="modal fade chat-<?php echo $attendee['attendee_id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="attendee-message-form">
                                    <textarea rows="6" name="d_message" placeholder="Type your message here..." required></textarea>
                                    <input type="hidden" name="attendee_id_to" value="<?php echo $attendee['attendee_id']; ?>">
                                    <input type="hidden" name="attendee_id_from" value="<?php echo session('attendee_id'); ?>">
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn-epr-purple">Send</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No attendees found.</p>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>

<script>
$(document).ready(function() {
    $(".attendee-message-form").on("submit", function(e) {
        e.preventDefault();
        const form = $(this);
        const data = {
            sender_id: form.find('input[name=attendee_id_from]').val(),
            receiver_id: form.find('input[name=attendee_id_to]').val(),
            message: form.find('textarea[name=d_message]').val()
        };

        $.ajax({
            url: "<?php echo base_url('api/messages/send'); ?>",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function(res) {
                if (res.status === "success") {
                    alert("✅ Message sent successfully.");
                    form[0].reset();
                    $('.modal').modal('hide');
                } else {
                    alert("⚠️ Message could not be sent.");
                }
            },
            error: function() {
                alert("❌ An error occurred while sending the message.");
            }
        });
    });
});
</script>
