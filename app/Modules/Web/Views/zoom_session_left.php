<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo esc($sessionName); ?></title>
  <style>
    html, body { width: 100%; height: 100%; margin: 0; background: #12050f; color: #fff; font-family: Arial, sans-serif; }
    main { min-height: 100%; box-sizing: border-box; display: grid; place-items: center; padding: 24px; text-align: center; }
    section { max-width: 430px; }
    h1 { margin: 0 0 10px; font-size: 22px; }
    p { margin: 0 0 20px; color: rgba(255,255,255,.76); line-height: 1.55; }
    a { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; padding: 0 18px; border-radius: 8px; background: #d8198e; color: #fff; font-weight: 700; text-decoration: none; }
  </style>
</head>
<body>
  <main>
    <section>
      <h1>You have left this Zoom session</h1>
      <p>You can rejoin when you are ready.</p>
      <a href="<?php echo esc($rejoinUrl, 'attr'); ?>">Rejoin session</a>
    </section>
  </main>
</body>
</html>
