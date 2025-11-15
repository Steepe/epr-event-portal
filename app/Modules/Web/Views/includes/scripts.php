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
<!-- 💬 Tawk.to Live Chat -->
<script type="text/javascript">
    var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function () {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/6378b6c0daff0e1306d84ae3/1gi7ojojo';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>

