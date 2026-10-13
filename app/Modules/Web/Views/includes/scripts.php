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
<script src="<?php echo asset_url('js/jquery.min.js');?>"></script>
<script src="<?php echo asset_url('js/jquery.slimscroll.js');?>"></script>
<script src="<?php echo asset_url('js/bootstrap.bundle.min.js');?>"></script>
<script src="<?php // echo asset_url('js/toast.js');?>"></script>
<script src="<?php //echo asset_url('js/signalr.js');?>"></script>
<script src="<?php //echo asset_url('js/hira.js');?>"></script>



<script type="text/javascript">
    (function() {
        const config = window.EPR_PORTAL_REALTIME || {};
        const userId = Number(config.userId || 0);
        const wsUrl = config.wsUrl || "";
        let socket = null;
        let reconnectTimer = null;
        let reconnectDelay = 1200;

        function showToast(message, isError) {
            let toast = document.querySelector("[data-rc-toast]");
            if (!toast) {
                toast = document.createElement("div");
                toast.className = "rc-toast";
                toast.setAttribute("data-rc-toast", "");
                document.body.appendChild(toast);
            }

            toast.textContent = message;
            toast.classList.toggle("is-error", !!isError);
            toast.classList.add("is-open");
            window.clearTimeout(toast._timer);
            toast._timer = window.setTimeout(function() {
                toast.classList.remove("is-open");
            }, 4200);
        }

        function updateBellCount(increment) {
            const badge = document.querySelector(".rc-bell-badge");
            if (!badge) {
                return;
            }

            const current = Number(badge.textContent || 0);
            const next = Math.max(0, current + increment);
            badge.textContent = String(next);
            badge.hidden = next === 0;
        }

        function appendConnectionRequest(event) {
            const group = document.querySelector('.notification-group[data-group="TODAY"]');
            if (!group || !event.connection_id || group.querySelector('[data-connection-id="' + event.connection_id + '"]')) {
                return;
            }

            const label = group.querySelector(".rc-mono");
            const row = document.createElement("div");
            row.className = "notification-row";
            row.dataset.category = "Networking";
            row.dataset.unread = "1";
            row.dataset.connectionId = String(event.connection_id);
            row.style.cssText = "display:grid;grid-template-columns:44px minmax(0,1fr) auto;gap:14px;align-items:start;padding:14px 16px;border-top:1px solid rgba(255,255,255,.06);background:rgba(157,15,130,.16)";
            row.innerHTML =
                '<span style="width:44px;height:44px;border-radius:12px;background:#efb11e;color:#260029;display:flex;align-items:center;justify-content:center;font-size:17px">' +
                    '<i class="fa fa-user-plus" aria-hidden="true"></i>' +
                '</span>' +
                '<span style="display:flex;flex-direction:column;gap:4px;min-width:0">' +
                    '<strong style="font-size:14px;font-weight:600;line-height:1.35;color:#fff">Connection request</strong>' +
                    '<span data-connection-message style="font-size:13px;color:rgba(255,255,255,.72);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"></span>' +
                    '<span class="rc-mono" data-connection-email style="font-size:10px;letter-spacing:.08em;color:rgba(255,255,255,.5)"></span>' +
                '</span>' +
                '<span style="display:flex;align-items:center;gap:8px">' +
                    '<button type="button" class="rc-outline-pill" data-connection-action="accept" style="border-color:rgba(74,222,128,.55);color:#4ade80;background:rgba(74,222,128,.10)">Accept</button>' +
                    '<button type="button" class="rc-outline-pill" data-connection-action="decline" style="border-color:rgba(255,255,255,.18);color:rgba(255,255,255,.76);background:rgba(255,255,255,.05)">Decline</button>' +
                    '<span class="notification-dot" style="width:8px;height:8px;border-radius:50%;background:#d8198e;"></span>' +
                '</span>';

            row.querySelector("[data-connection-message]").textContent = (event.requester_name || "An attendee") + " wants to connect with you.";
            row.querySelector("[data-connection-email]").textContent = event.requester_email || "";
            group.insertBefore(row, label ? label.nextSibling : group.firstChild);
        }

        function handleRealtimeEvent(event) {
            if (!event || !event.type) {
                return;
            }

            if (event.type === "connection_request") {
                updateBellCount(1);
                appendConnectionRequest(event);
                showToast((event.requester_name || "An attendee") + " sent you a connection request.");
                return;
            }

            if (event.type === "connection_update") {
                showToast(event.status === "accepted"
                    ? "Your connection request was accepted."
                    : "Your connection request was declined.");
            }
        }

        function connectRealtime() {
            if (!userId || !wsUrl || !("WebSocket" in window)) {
                return;
            }

            try {
                socket = new WebSocket(wsUrl);
            } catch (error) {
                return;
            }

            socket.addEventListener("open", function() {
                reconnectDelay = 1200;
                socket.send(JSON.stringify({ type: "register", user_id: userId }));
            });

            socket.addEventListener("message", function(message) {
                try {
                    handleRealtimeEvent(JSON.parse(message.data));
                } catch (error) {
                }
            });

            socket.addEventListener("close", function() {
                window.clearTimeout(reconnectTimer);
                reconnectTimer = window.setTimeout(connectRealtime, reconnectDelay);
                reconnectDelay = Math.min(reconnectDelay * 1.6, 10000);
            });
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", connectRealtime, { once: true });
        } else {
            connectRealtime();
        }
    })();

    (function() {
        function forceCloseModalElement(modal) {
            if (!modal) {
                return;
            }

            modal.classList.remove("show");
            modal.setAttribute("aria-hidden", "true");
            modal.removeAttribute("aria-modal");
            modal.style.display = "none";
            document.querySelectorAll(".modal-backdrop").forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove("modal-open");
            document.body.style.paddingRight = "";
        }

        document.addEventListener("click", function(event) {
            const close = event.target.closest('[data-dismiss="modal"], [data-rc-close-modal], .modal .close');
            if (close) {
                const modal = close.closest(".modal");
                if (modal) {
                    event.preventDefault();
                    event.stopPropagation();
                    forceCloseModalElement(modal);
                }
                return;
            }

            const modal = event.target.classList && event.target.classList.contains("modal") ? event.target : null;
            if (modal) {
                forceCloseModalElement(modal);
            }
        }, true);

        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                forceCloseModalElement(document.querySelector(".modal.show"));
            }
        });
    })();

    if (window.jQuery) {
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

        function forceCloseModal($modal) {
            if (!$modal || !$modal.length) {
                return;
            }

            try {
                if (typeof $modal.modal === "function") {
                    $modal.modal("hide");
                }
            } catch (error) {
                // Bootstrap may not own every modal on the redesigned screens.
            }

            window.setTimeout(function() {
                $modal.removeClass("show")
                    .attr("aria-hidden", "true")
                    .removeAttr("aria-modal")
                    .hide();
                $(".modal-backdrop").remove();
                $("body").removeClass("modal-open").css("padding-right", "");
            }, 80);
        }

        $(document).on("click", '[data-dismiss="modal"], [data-rc-close-modal], .modal .close', function(event) {
            var $modal = $(this).closest(".modal");
            if ($modal.length) {
                event.preventDefault();
                event.stopPropagation();
                forceCloseModal($modal);
            }
        });

        $(document).on("click", ".modal", function(event) {
            if (event.target === this) {
                forceCloseModal($(this));
            }
        });

        $(document).on("keydown", function(event) {
            if (event.key === "Escape") {
                forceCloseModal($(".modal.show").last());
            }
        });


    });
    }

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
