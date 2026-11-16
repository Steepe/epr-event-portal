<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo esc($sessionName); ?></title>
  <style>
    html, body, #zoom-root { width: 100%; height: 100%; margin: 0; background: #12050f; color: #fff; font-family: Arial, sans-serif; }
    #zoom-status { min-height: 100%; display: grid; place-items: center; padding: 24px; box-sizing: border-box; text-align: center; line-height: 1.5; }
    #zoom-status p { max-width: 460px; margin: 0; color: rgba(255,255,255,.78); }
  </style>
</head>
<body>
  <div id="zoom-root">
    <div id="zoom-status"><p><?php echo $configured ? 'Preparing your interactive Zoom session…' : 'This Zoom session has not been configured yet. Please contact the event organiser.'; ?></p></div>
  </div>

  <?php if ($configured): ?>
    <script src="https://source.zoom.us/<?php echo esc($sdkVersion, 'attr'); ?>/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/<?php echo esc($sdkVersion, 'attr'); ?>/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/<?php echo esc($sdkVersion, 'attr'); ?>/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/<?php echo esc($sdkVersion, 'attr'); ?>/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/<?php echo esc($sdkVersion, 'attr'); ?>/lib/vendor/lodash.min.js"></script>
    <script src="https://source.zoom.us/<?php echo esc($sdkVersion, 'attr'); ?>/zoom-meeting-<?php echo esc($sdkVersion, 'attr'); ?>.min.js"></script>
    <script>
      (() => {
        const status = document.getElementById('zoom-status');
        const signatureUrl = <?php echo json_encode($signatureUrl); ?>;
        const leaveUrl = <?php echo json_encode($leaveUrl); ?>;
        const sdkVersion = <?php echo json_encode($sdkVersion); ?>;

        const fail = message => {
          status.innerHTML = '';
          const text = document.createElement('p');
          text.textContent = message;
          status.appendChild(text);
        };

        fetch(signatureUrl, { credentials: 'same-origin', cache: 'no-store' })
          .then(response => response.ok ? response.json() : response.json().catch(() => ({})).then(data => Promise.reject(data)))
          .then(config => {
            ZoomMtg.setZoomJSLib(`https://source.zoom.us/${sdkVersion}/lib`, '/av');
            ZoomMtg.preLoadWasm();
            ZoomMtg.prepareWebSDK();
            ZoomMtg.i18n.load('en-US');
            ZoomMtg.i18n.reload('en-US');
            ZoomMtg.init({
              leaveUrl,
              patchJsMedia: true,
              success: () => ZoomMtg.join({
                sdkKey: config.sdkKey,
                signature: config.signature,
                meetingNumber: config.meetingNumber,
                passWord: config.passWord,
                userName: config.userName,
                userEmail: config.userEmail,
                success: () => {},
                error: error => fail(error?.reason || 'Zoom could not join this session.')
              }),
              error: error => fail(error?.reason || 'Zoom could not initialise this session.')
            });
          })
          .catch(error => fail(error?.message || 'Zoom could not prepare this session.'));
      })();
    </script>
  <?php endif; ?>
</body>
</html>
