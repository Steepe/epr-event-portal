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
        const pendingConnectionsUrl = config.pendingConnectionsUrl || "";
        let socket = null;
        let reconnectTimer = null;
        let reconnectDelay = 1200;
        let pollingTimer = null;
        const receivedConnectionIds = new Set();
        document.querySelectorAll("[data-connection-id]").forEach(function(row) {
            const connectionId = Number(row.dataset.connectionId || 0);
            if (connectionId) {
                receivedConnectionIds.add(connectionId);
            }
        });

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

        function setBellCount(count) {
            const badge = document.querySelector(".rc-bell-badge");
            if (!badge) {
                return;
            }

            const next = Math.max(0, Number(count) || 0);
            badge.textContent = String(next);
            badge.hidden = next === 0;
        }

        function appendConnectionRequest(event) {
            const group = document.querySelector('.notification-group[data-group="TODAY"]');
            if (!group || !event.connection_id || group.querySelector('[data-connection-id="' + event.connection_id + '"]')) {
                return;
            }

            const label = group.querySelector(".rc-notification-group-title, .rc-mono");
            const row = document.createElement("div");
            row.className = "notification-row rc-notification-row";
            row.dataset.category = "Networking";
            row.dataset.unread = "1";
            row.dataset.connectionId = String(event.connection_id);
            row.innerHTML =
                '<span class="rc-notification-tile" style="background:#efb11e;color:#260029">' +
                    '<i class="fa fa-user-plus" aria-hidden="true"></i>' +
                '</span>' +
                '<div class="rc-notification-copy">' +
                    '<div><span class="notification-dot"></span><strong>Connection request</strong></div>' +
                    '<p data-connection-message></p>' +
                '</div>' +
                '<div class="rc-notification-actions">' +
                    '<span class="rc-notification-time">JUST NOW</span>' +
                    '<span class="rc-connection-actions">' +
                        '<button type="button" data-connection-action="accept">Accept</button>' +
                        '<button type="button" data-connection-action="decline">Decline</button>' +
                    '</span>' +
                '</div>';

            row.querySelector("[data-connection-message]").textContent = (event.requester_name || "An attendee") + " wants to connect with you.";
            group.insertBefore(row, label ? label.nextSibling : group.firstChild);
        }

        function handleRealtimeEvent(event) {
            if (!event || !event.type) {
                return;
            }

            if (event.type === "connection_request") {
                const connectionId = Number(event.connection_id || 0);
                if (connectionId && receivedConnectionIds.has(connectionId)) {
                    return;
                }
                if (connectionId) {
                    receivedConnectionIds.add(connectionId);
                }
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

        function pollPendingConnections() {
            if (!userId || !pendingConnectionsUrl) {
                return;
            }

            fetch(pendingConnectionsUrl, { credentials: "same-origin" })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error("pending-connections-failed");
                    }
                    return response.json();
                })
                .then(function(payload) {
                    const connections = payload && payload.data && Array.isArray(payload.data.connections)
                        ? payload.data.connections
                        : [];
                    connections.forEach(function(event) {
                        handleRealtimeEvent(event);
                    });
                    if (payload && payload.data && typeof payload.data.pending_count !== "undefined") {
                        setBellCount(payload.data.pending_count);
                    }
                })
                .catch(function() {
                    // The next interval retries; notifications remain non-blocking.
                });
        }

        function startPolling() {
            if (pollingTimer || !pendingConnectionsUrl) {
                return;
            }

            pollPendingConnections();
            pollingTimer = window.setInterval(pollPendingConnections, 5000);
        }

        function connectRealtime() {
            if (!userId) {
                return;
            }

            // Keep this lightweight reconciliation active even while a socket
            // is open: it covers a dropped or missed socket event without a
            // browser refresh. Duplicate connection IDs are ignored above.
            startPolling();

            if (!wsUrl || !("WebSocket" in window)) {
                return;
            }

            try {
                socket = new WebSocket(wsUrl);
            } catch (error) {
                startPolling();
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
                startPolling();
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
        let activeModal = null;

        function removeBootstrapBackdrops() {
            document.querySelectorAll(".modal-backdrop").forEach(function(backdrop) {
                backdrop.remove();
            });
        }

        function notify(modal, eventName) {
            if (window.jQuery) {
                window.jQuery(modal).trigger(eventName);
            }
        }

        function closeModal(modal, restoreFocus) {
            if (!modal) {
                return;
            }

            modal.classList.remove("show");
            modal.setAttribute("aria-hidden", "true");
            modal.removeAttribute("aria-modal");
            modal.style.display = "none";
            removeBootstrapBackdrops();
            document.body.classList.remove("modal-open");
            document.body.style.paddingRight = "";
            notify(modal, "hidden.bs.modal");

            const opener = modal._rcModalOpener;
            modal._rcModalOpener = null;
            if (restoreFocus && opener && document.contains(opener)) {
                opener.focus();
            }

            if (activeModal === modal) {
                activeModal = null;
            }
        }

        function openModal(modal, opener) {
            if (!modal) {
                return;
            }

            if (activeModal && activeModal !== modal) {
                closeModal(activeModal, false);
            }

            removeBootstrapBackdrops();
            modal._rcModalOpener = opener || document.activeElement;
            modal.style.display = "block";
            modal.classList.add("show");
            modal.setAttribute("aria-hidden", "false");
            modal.setAttribute("aria-modal", "true");
            activeModal = modal;
            document.body.classList.add("modal-open");
            notify(modal, "shown.bs.modal");

            window.setTimeout(function() {
                const autofocus = modal.querySelector("[autofocus], [data-rc-modal-autofocus], button, input, textarea, select, [tabindex]:not([tabindex='-1'])");
                (autofocus || modal).focus();
            }, 0);
        }

        function closeOverlay(overlay) {
            if (!overlay) {
                return;
            }

            overlay.classList.remove("is-open");
            overlay.setAttribute("aria-hidden", "true");
        }

        function modalFromTrigger(trigger) {
            const selector = trigger.getAttribute("data-target");
            if (!selector) {
                return null;
            }

            try {
                return document.querySelector(selector);
            } catch (error) {
                return null;
            }
        }

        document.addEventListener("click", function(event) {
            const overlayClose = event.target.closest("[data-rc-close-overlay]");
            if (overlayClose) {
                const overlay = overlayClose.closest(".rc-modal-overlay");
                if (overlay) {
                    event.preventDefault();
                    closeOverlay(overlay);
                }
                return;
            }

            const trigger = event.target.closest('[data-toggle="modal"][data-target]');
            if (trigger) {
                event.preventDefault();
                event.stopImmediatePropagation();
                openModal(modalFromTrigger(trigger), trigger);
                return;
            }

            const close = event.target.closest('[data-dismiss="modal"], [data-rc-close-modal], .modal .close');
            if (close) {
                const modal = close.closest(".modal");
                if (modal) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    closeModal(modal, true);
                }
                return;
            }

            const modal = event.target.classList && event.target.classList.contains("modal") ? event.target : null;
            if (modal) {
                closeModal(modal, true);
                return;
            }

            const overlay = event.target.classList && event.target.classList.contains("rc-modal-overlay") ? event.target : null;
            if (overlay) {
                closeOverlay(overlay);
            }
        }, true);

        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                const overlay = document.querySelector(".rc-modal-overlay.is-open");
                if (overlay) {
                    closeOverlay(overlay);
                } else {
                    closeModal(activeModal || document.querySelector(".modal.show"), true);
                }
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
