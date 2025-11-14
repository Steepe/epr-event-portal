<?php
/**
 * Created by PhpStorm.
 * User: Oluwamayowa Steepe
 * Project: eprglobal
 * Date: 29/08/2021
 * Time: 02:42
 */

//var_dump($new_messages_count);
//exit;
// ✅ Default fallback image
$profile_picture = asset_url('images/user.png');

// ✅ Only use attendee image if the variable exists and isn't empty
if (isset($global_attendee_details)
    && !empty($global_attendee_details['profile_picture'])) {
    $profile_picture = base_url('uploads/attendee_pictures/')
        . $global_attendee_details['profile_picture'];
}
?>

<body class="lobby-bg">

    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: transparent;">

        <a class="navbar-brand float-left" href="<?php echo base_url();?>">
            <img src="<?php echo asset_url('images/eventslogo.png');?>" width="250px" alt="emergence-logo">
        </a>

        <ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
            <li class="nav-item" style="margin-right: 15px; margin-top: 10px;">
                <a class="nav-item mr-md-2 text-white" href="<?php //echo base_url('attendee/p/messages');?>">
                    <?php
                    // ✅ Ensure variable
                    $new_messages_count = $new_messages_count ?? ['message_count' => 0];

                    // ✅ Display badge only if there are unread messages
                    if (!empty($new_messages_count['message_count']) && $new_messages_count['message_count'] > 0):
                        ?>
                        <span class="rounded-circle epr-pink float-right notification-badge">
        <?php echo $new_messages_count['message_count']; ?>
    </span>
                    <?php endif; ?>
                </a>
            </li>

            <li style="margin-right: 20px;">
                <a class="nav-item nav-link dropdown-toggle mr-md-2 text-white" href="#" id="bd-versions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $profile_picture;?>" width="30" height="30" class="rounded-circle"></a>
                <div class="dropdown-menu dropdown-menu-right" style="margin-top: -20px; margin-right: 5px;">
                    <a class="dropdown-item" href="<?php echo base_url('attendees/profile')?>">Profile</a>
                    <a class="dropdown-item" href="<?php echo base_url('attendees/logout');?>">Logout</a>
                </div>
            </li>

        </ul>
    </nav>
