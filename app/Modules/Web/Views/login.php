<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 05:07
 */

 echo module_view('Web', 'includes/auth_header');
 ?>

<style>
    .form-control{
        background: transparent !important;
        border-radius: 10px;
        font-size: 13px;
        padding-left: 35px;
    }

    /* Placeholder text color (cross-browser support) */
    ::placeholder {
        color: #ffffff !important;
        opacity: 1; /* Ensure full visibility in Firefox */
    }

    /* For older browsers */
    ::-webkit-input-placeholder {
        color: #ffffff !important;
    }

    ::-moz-placeholder {
        color: #ffffff !important;
    }

    :-ms-input-placeholder {
        color: #ffffff !important;
    }

    :-moz-placeholder {
        color: #ffffff !important;
    }

</style>

<body class="login-bg">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5 mx-auto mt-5">
            <div class="text-center mb-5">
                <img src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="logo" width="250">
            </div>


            <form id="LoginForm" style="width:80%;margin:auto; margin-top: 200px;">
                <div class="form-group mb-2">
                    <span style="color: #ffffff; margin-left: 15px; font-size: 13px;">Login to Continue</span>
                </div>
                <div class="form-group mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                </div>
                <div class="form-group mb-4">
                    <input type="password" class="form-control" name="password" placeholder="Enter Password" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn epr-btn-one">LOGIN</button>
                </div>

                <p class="text-center text-white mt-3">
                    Don’t have an account?
                    <a href="<?php echo base_url('attendees/register'); ?>" style="color:#EFB11E;">Register</a>
                </p>
            </form>
        </div>
    </div>
</div>

<div id="toast" class="alert text-center d-none"
     style="position:fixed;top:20px;left:50%;transform:translateX(-50%);
     z-index:2000;width:300px;"></div>

<?php echo module_view('Web', 'includes/scripts'); ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("LoginForm");
    const toast = document.getElementById("toast");

    const showToast = (msg, type="success") => {
        toast.className = `alert alert-${type} text-center`;
        toast.textContent = msg;
        toast.classList.remove("d-none");
        setTimeout(() => toast.classList.add("d-none"), 3500);
    };

    form.addEventListener("submit", async e => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form));

        const res = await fetch("<?php echo base_url('api/login'); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-API-KEY": "<?php echo env('api.securityKey'); ?>"
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();

        if (result.status === "success") {
            showToast("Login successful!");
            setTimeout(() => {
                window.location.href = "<?php echo base_url('attendees/home'); ?>";
            }, 1500);
        } else {
            showToast(result.message || "Invalid credentials.", "danger");
        }
    });
});
</script>
</body>
</html>
