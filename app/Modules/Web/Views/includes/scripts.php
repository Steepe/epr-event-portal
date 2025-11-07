<?php
/**
 * Created by PhpStorm.
 * User: Oluwamayowa Steepe
 * Project: innovate
 * Date: 25/05/2022
 * Time: 00:08
 */

?>
<!-- jQuery  -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="<?php echo asset_url('js/jquery.slimscroll.js');?>"></script>
<script src="<?php echo asset_url('js/bootstrap.bundle.min.js');?>"></script>
<script src="<?php // echo asset_url('js/toast.js');?>"></script>
<script src="<?php //echo asset_url('js/signalr.js');?>"></script>
<script src="<?php //echo asset_url('js/hira.js');?>"></script>



<script type="text/javascript">
    $(document).ready(function(){

        $('.nav-link').on('click',function(){

            //Remove any previous active classes
            //$(this).removeClass('text-white');

            //Add active class to the clicked item
            $(this).addClass('cractive');
        });

        $('.slide-out').on( "click",function(){
            $('.notification-slide').stop().animate({
                right: 0
            }, 350);
            $('.ringing-bell').hide();
            $('.slide-out').hide();
            $('.slide-in').show();
        });

        $('.slide-in').on( "click",function(){
            $('.notification-slide').stop().animate({
                right: '-350px'
            }, 350);
            $('.slide-in').hide();
            $('.slide-out').show();
        });


    });

    function move() {
        var elem = document.getElementById("myBar");
        var width = 1;
        var id = setInterval(frame, 10);
        function frame() {
            if (width >= 100) {
                clearInterval(id);
            } else {
                width++;
                elem.style.width = width + '%';
            }
        }
    }
</script>

