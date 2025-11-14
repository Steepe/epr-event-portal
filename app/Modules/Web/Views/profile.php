<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 14/11/2025
 * Time: 01:55
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<style>
    /* RESET */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Inter", sans-serif;
    }

    body {
        background: #f2d9ef; /* plain background, no image */
        overflow-x: hidden;
    }

    /* ===================== PROFILE HERO ===================== */
    .profile-hero {
        width: 100%;
        height: 360px;
        background: url("<?php echo asset_url('images/epr-profile-bg.png'); ?>") center/cover no-repeat;
        display: flex;
        justify-content: center;
        align-items: flex-end;
        padding-bottom: 35px;
    }

    .profile-photo-wrapper {
        position: relative;
        width: 200px;
        height: 200px;
        top: 135px;
    }

    .profile-photo {
        width: 100%;
        height: 100%;
        border-radius: 14px;
        object-fit: cover;
        border: 4px solid #fff;
    }

    /* Floating Camera Button */
    .camera-btn {
        position: absolute;
        bottom: 10px;
        right: -14px;
        width: 46px;
        height: 46px;
        background: #b31bb7;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .camera-btn svg path {
        stroke: white;
        transition: 0.2s ease;
    }

    .camera-btn input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
    }

    /* ===================== PROFILE BODY ===================== */
    .profile-body {
        width: 100%;
        min-height: 420px;
        padding-top: 70px;
        text-align: center;
        margin-top: 100px;
    }

    .edit-btn {
        padding: 12px 40px;
        background: #a1289b;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
    }

    .edit-btn:hover {
        filter: brightness(1.1);
    }

    .social-links {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 35px;
        font-size: 24px;
    }

    /* SVG social icons color + hover */
    .social-links svg path,
    .social-links svg rect,
    .social-links svg circle {
        stroke: #701b75;
        transition: 0.2s ease;
    }

    .social-links svg:hover path,
    .social-links svg:hover rect,
    .social-links svg:hover circle {
        stroke: #000 !important;
        fill: #000 !important;
    }

    @media (max-width: 768px) {
        .profile-photo-wrapper {
            width: 160px;
            height: 160px;
            top: 110px;
        }
    }

    .modal-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0,0,0,0.55);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 99999;
    }

    .modal-box {
        width: 95%;
        max-width: 520px;
        background: #fff;
        border-radius: 18px;
        padding: 30px;
        position: relative;
        animation: fadeIn .25s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .modal-title {
        font-size: 22px;
        font-weight: 700;
        color: #9D0F82;
        margin-bottom: 15px;
        text-align: center;
    }

    .modal-close {
        position: absolute;
        top: 15px;
        right: 18px;
        font-size: 22px;
        color: #444;
        cursor: pointer;
    }

    .form-group {
        margin-bottom: 14px;
        text-align: left;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #444;
        margin-bottom: 5px;
        display: block;
    }

    .form-group input, .form-group select {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #bbb;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }

    .form-group input:focus {
        border-color: #9D0F82;
        box-shadow: 0 0 0 2px rgba(157,15,130,0.12);
    }

    .save-btn {
        background: #9D0F82;
        color: white;
        width: 100%;
        padding: 13px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s ease;
        margin-top: 10px;
    }

    .save-btn:hover {
        background: #7a0c63;
    }
</style>


<!-- =================== HERO SECTION WITH BACKGROUND =================== -->
<section class="profile-hero">
    <div class="profile-photo-wrapper">

        <img src="<?php echo base_url('uploads/attendee_pictures/' . $profile['profile_picture'] ); ?>"
             class="profile-photo"
             onerror="this.src='<?php echo asset_url("images/user.png"); ?>';">

        <div class="camera-btn">
            <!-- SVG CAMERA ICON -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M12 17.25C14.347 17.25 16.25 15.347 16.25 13C16.25 10.653 14.347 8.75 12 8.75C9.653 8.75 7.75 10.653 7.75 13C7.75 15.347 9.653 17.25 12 17.25Z"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4 7H7L9 4H15L17 7H20C21.105 7 22 7.895 22 9V19C22 20.105 21.105 21 20 21H4C2.895 21 2 20.105 2 19V9C2 7.895 2.895 7 4 7Z"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            <input type="file" id="uploadPhoto" accept="image/*">
        </div>

    </div>
</section>


<!-- =================== PROFILE BODY =================== -->
<section class="profile-body">
    <button class="edit-btn">Edit Profile</button>

    <div class="social-links">

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
</section>

<div class="modal-overlay" id="editProfileModal">
    <div class="modal-box">
        <div class="modal-close" onclick="closeEditModal()">×</div>
        <div class="modal-title">Edit Profile</div>

        <form action="<?php echo base_url('attendees/profile/update'); ?>"
              method="post"
              enctype="multipart/form-data">

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
    function openEditModal() {
        document.getElementById("editProfileModal").style.display = "flex";
    }

    function closeEditModal() {
        document.getElementById("editProfileModal").style.display = "none";
    }

    // Connect modal to your edit button
    document.querySelector(".edit-btn").addEventListener("click", openEditModal);

        document.getElementById("uploadPhoto").addEventListener("change", function () {
        let file = this.files[0];
        if (!file) return;

        let formData = new FormData();
        formData.append("profile_picture", file);

        fetch("<?php echo base_url('attendees/profile/upload-photo'); ?>", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
        if (data.status === "success") {
        // Update picture instantly
        document.querySelector(".profile-photo").src = data.url;
    } else {
        alert(data.message);
    }
    })
        .catch(() => alert("Upload failed."));
    });
</script>

<?php echo module_view('Web', 'includes/scripts'); ?>
