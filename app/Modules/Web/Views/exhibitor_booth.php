<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 02/11/2025
 * Time: 00:14
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<style>
body {
  background: url('<?php echo asset_url('images/brain-Events7-Portal-2025.png'); ?>') no-repeat center center fixed;
  background-size: cover;
  color: #fff;
}

.booth-container {
  padding: 100px 10%;
  min-height: 100vh;
}

.booth-video {
  width: 100%;
  max-width: 900px;
  height: 500px;
  border-radius: 15px;
  overflow: hidden;
  margin: 0 auto 40px auto;
  box-shadow: 0 0 25px rgba(255,255,255,0.3);
}

.booth-details {
  text-align: center;
}

.booth-details h2 {
  color: #EFB11E;
  font-weight: 700;
}

.booth-details p {
  max-width: 700px;
  margin: 10px auto;
}

.booth-actions button {
  margin: 15px 8px;
  border-radius: 25px;
  border: none;
  background: linear-gradient(90deg, #9D0F82, #EFB11E);
  color: #fff;
  padding: 10px 25px;
  font-weight: 600;
}
.booth-actions button:hover {
  background: linear-gradient(90deg, #EFB11E, #9D0F82);
  transform: scale(1.05);
}
</style>

<div class="booth-container text-center">
  <div class="booth-video">
    <iframe src="https://player.vimeo.com/video/<?php echo $exhibitor['vimeo_id']; ?>" frameborder="0" allowfullscreen></iframe>
  </div>

  <div class="booth-details">
    <h2><?php echo esc($exhibitor['company_name']); ?></h2>
    <p><?php echo esc($exhibitor['tagline']); ?></p>
    <p><strong>Website:</strong> <a href="<?php echo esc($exhibitor['website']); ?>" target="_blank" style="color:#EFB11E;"><?php echo esc($exhibitor['website']); ?></a></p>
  </div>

  <div class="booth-actions">
    <button data-toggle="modal" data-target="#messageModal">Send Message</button>
  </div>
</div>

<!-- 💬 Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-dark p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title">Message <?php echo esc($exhibitor['company_name']); ?></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form class="exhibitor-message-form">
          <textarea name="message" rows="5" class="form-control mb-3" placeholder="Type your message here..."></textarea>
          <input type="hidden" name="exhibitor_id" value="<?php echo $exhibitor['id']; ?>">
          <div class="text-center">
            <button type="submit" class="btn btn-epr-purple">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
