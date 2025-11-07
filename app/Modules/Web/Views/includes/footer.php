<?php
/**
 * Created by PhpStorm.
 * User: Oluwamayowa Steepe
 * Project: eprglobal
 * Date: 29/08/2021
 * Time: 02:50
 */
?>

<div class="notification-slide">
    <div class="slider-arrow text-white">
        <i id="ringing" class='fa fa-bell faa-ring animated fa-5x ringing-bell'></i>

        <img class="slide-out" src="<?php echo asset_url('images/slider-arrow-reverse.png')?>">
        <img class="slide-in" src="<?php echo asset_url('images/slider-arrow.png')?>" style="display: none;">
    </div>
    <div class="text-center" style="position: relative; top: -34px;">
        <h5 style="color: #EFB11E;">ANNOUNCEMENTS</h5>
    </div>
    <div id="announcements_div">
        <?php
        if(isset($announcements)){
            foreach ($announcements as $announcement){
                //var_dump($announcement);
                ?>
                <div class="card announcement-card">
                    <div class="card-header">
                        <small class="float-right"><?php echo date('M d', $announcement['created_at']);?></small>
                        <h6><strong>
                                <?php
                                // Check if $announcement is set and has the 'title' key
                                if (isset($announcement) && isset($announcement['title']) && $announcement['title'] !== "") {
                                    echo $announcement['title'];
                                }
                                ?>

                            </strong></h6>
                    </div>
                    <div class="card-body">
                        <p class="card-text font-12" style="margin-top: -30px;"><?php echo $announcement['announcement'];?></p>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>

</div>

<!--<footer class="footer text-center text-sm-left fixed-bottom font-12" style="background-color: #9D0F82; padding-left: 10px; padding-right: 10px; color: #ffffff;">
    <div class="boxed-footer">
        &copy; <?php /*echo date("Y");*/?> Powered By EPR Global. <span class="d-none d-sm-inline-block float-right">Crafted with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://www.linkedin.com/in/steepe/" target="_blank" style="color: #EFB11E;">Crèyatif</a> </span>
    </div>
</footer>-->
<!--end footer-->
</div>
</div>
<!-- end page-wrapper -->

