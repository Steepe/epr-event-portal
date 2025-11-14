<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 14/11/2025
 * Time: 05:24
 */

 echo module_view('MobileApp', 'includes/header'); ?>

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

    .profile-container {
        width: 92%;
        text-align: center;
        margin: 80px auto auto;
    }

    .profile-photo-wrapper {
        position: relative;
        margin: auto;
        width: 160px;
        height: 160px;
    }

    .profile-photo {
        width: 100%;
        height: 100%;
        border-radius: 16px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .camera-btn {
        position: absolute;
        bottom: -5px;
        right: -10px;
        width: 42px;
        height: 42px;
        background: #A70B91;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .camera-btn input {
        opacity: 0;
        position: absolute;
        width: 100%;
        height: 100%;
    }

    .profile-name {
        margin-top: 15px;
        font-size: 20px;
        font-weight: 700;
        color: #A70B91;
    }

    .profile-info {
        margin-top: 5px;
        font-size: 14px;
        color: #777;
    }

    .edit-btn {
        margin-top: 25px;
        padding: 12px 20px;
        background: #9D0F82;
        color: white;
        border-radius: 10px;
        border: none;
        width: 100%;
        max-width: 260px;
        font-size: 15px;
        font-weight: 600;
    }

    .edit-btn:hover { opacity: 0.9; }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-box {
        width: 90%;
        max-width: 420px;
        background: #fff;
        border-radius: 14px;
        padding: 25px;
        animation: fadeIn .2s ease;
    }

    @keyframes fadeIn {
        from { transform: translateY(10px); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }

    .modal-close {
        text-align: right;
        font-size: 20px;
        cursor: pointer;
    }

    .modal-title {
        text-align: center;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #A70B91;
    }

    .form-group {
        margin-bottom: 12px;
        text-align: left;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #bbb;
        border-radius: 8px;
        font-size: 14px;
    }

    .form-group input:focus {
        border-color: #A70B91;
        box-shadow: 0 0 0 2px rgba(157,15,130,0.15);
    }

    .save-btn {
        background: #A70B91;
        color: white;
        padding: 12px;
        width: 100%;
        border-radius: 10px;
        border: none;
        font-size: 15px;
        font-weight: 600;
    }

    .mobile-social-links {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 35px;
    }

    .mobile-social-links svg path,
    .mobile-social-links svg rect,
    .mobile-social-links svg circle {
        stroke: #701b75;
        transition: 0.2s ease;
    }

    .mobile-social-links svg:hover path,
    .mobile-social-links svg:hover rect,
    .mobile-social-links svg:hover circle {
        stroke: #000 !important;
        fill: #000 !important;
    }


</style>


<div class="profile-container">

    <div class="profile-photo-wrapper">
        <img src="<?php echo base_url('uploads/attendee_pictures/' . $profile['profile_picture']); ?>"
             class="profile-photo"
             onerror="this.src='<?php echo asset_url('images/user.png'); ?>';">

        <div class="camera-btn">
            <i class="fa fa-camera text-white"></i>
            <input type="file" id="uploadPhoto" accept="image/*">
        </div>
    </div>

    <div class="profile-name">
        <?php echo esc($profile['firstname'].' '.$profile['lastname']); ?>
    </div>

    <div class="profile-info">
        <?php echo esc($profile['city']); ?>, <?php echo esc($profile['country']); ?>
    </div>

    <button class="edit-btn" id="openEdit">Edit Profile</button>

    <div class="mobile-social-links">

        <!-- FACEBOOK -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <path d="M18 2H15C12.79 2 11 3.79 11 6V9H8V13H11V22H15V13H18L19 9H15V6C15 5.45 15.45 5 16 5H19V2H18Z"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        <!-- X / TWITTER -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <path d="M4 4L20 20M20 4L4 20" stroke-width="2" stroke-linecap="round"/>
        </svg>

        <!-- LINKEDIN -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="9" width="4" height="12" stroke-width="2"/>
            <circle cx="4" cy="4" r="2" stroke-width="2"/>
            <path d="M10 9H14V11C15 9.5 17 9 18.5 9C21 9 22 10.5 22 13V21H18V14C18 13 17.5 12 16.5 12C15.5 12 14 13 14 14V21H10V9Z"
                  stroke-width="2" stroke-linejoin="round"/>
        </svg>

        <!-- INSTAGRAM -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="18" height="18" rx="5" stroke-width="2"/>
            <circle cx="12" cy="12" r="4" stroke-width="2"/>
            <circle cx="17" cy="7" r="1.2" fill="#701b75"/>
        </svg>

    </div>

</div>


<!-- Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-close" onclick="closeEditModal()">×</div>
        <div class="modal-title">Edit Profile</div>

        <form action="<?php echo base_url('mobile/profile/update'); ?>" method="post" enctype="multipart/form-data">

            <input type="hidden" name="attendee_id" value="<?php echo $profile['attendee_id']; ?>">

            <div class="form-group">
                <label>First Name</label>
                <input name="firstname" value="<?php echo esc($profile['firstname']); ?>" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input name="lastname" value="<?php echo esc($profile['lastname']); ?>" required>
            </div>

            <div class="form-group">
                <label>Telephone</label>
                <input name="telephone" value="<?php echo esc($profile['telephone']); ?>">
            </div>

            <div class="form-group">
                <label>Country</label>
                <input name="country" value="<?php echo esc($profile['country']); ?>">
            </div>

            <div class="form-group">
                <label>City</label>
                <input name="city" value="<?php echo esc($profile['city']); ?>">
            </div>

            <div class="form-group">
                <label>State</label>
                <input name="state" value="<?php echo esc($profile['state']); ?>">
            </div>

            <div class="form-group">
                <label>Company</label>
                <input name="company" value="<?php echo esc($profile['company']); ?>">
            </div>

            <div class="form-group">
                <label>Position</label>
                <input name="position" value="<?php echo esc($profile['position']); ?>">
            </div>

            <div class="form-group">
                <label>Change Profile Picture</label>
                <input type="file" name="profile_picture" accept="image/*">
            </div>

            <button class="save-btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById("editModal");
document.getElementById("openEdit").onclick = () => (modal.style.display = "flex");
function closeEditModal() { modal.style.display = "none"; }

// AJAX photo upload
document.getElementById("uploadPhoto").addEventListener("change", function () {
    let file = this.files[0];
    if (!file) return;

    let formData = new FormData();
    formData.append("profile_picture", file);

    fetch("<?php echo base_url('mobile/profile/upload-photo'); ?>", {
        method: "POST",
        body: formData
    })
        .then(r => r.json())
        .then(d => {
            if (d.status === "success") {
                document.querySelector(".profile-photo").src = d.url;
            } else {
                alert(d.message);
            }
        });
});
</script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
