<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 11:49
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

    <style>
        body {
            background: url('<?php echo asset_url('images/brain-Events9-Portal-2025.png'); ?>') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .points-container {
            padding: 120px 10% 80px;
            min-height: 100vh;
        }

        .points-header h2 {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 40px;
        }

        .points-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
            text-align: center;
        }

        .points-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 40px 20px 25px;
            transition: transform .3s ease, box-shadow .3s ease;
            backdrop-filter: blur(5px);
        }

        .points-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 20px rgba(239, 177, 30, 0.4);
        }

        .points-card img {
            width: 70px;
            height: 70px;
            margin-bottom: 15px;
        }

        .points-card h5 {
            font-size: 15px;
            color: #fff;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .points-card button {
            background-image: url("<?php echo asset_url('images/button-bg-short.png')?>");
            background-repeat: repeat-y;
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 30px;
            padding: 8px 25px;
            font-size: 12px;
            transition: all .3s ease;
        }

        .points-card button:hover {
            background-image: url("<?php echo asset_url('images/button-light-bg-short.png')?>");
            transform: scale(1.05);
        }
    </style>

    <div class="points-container">
        <div class="points-header">
            <h2 class="epr-text-purple">EARN POINTS</h2>
            <p class="text-gray-300">Complete activities and earn reward points to boost your conference experience.</p>
        </div>

        <div class="points-grid">
            <?php if (!empty($point_guides)): ?>
                <?php foreach ($point_guides as $guide): ?>
                    <?php
                    $image_file = asset_url('images/' . $guide['image']);
                    ?>
                    <div class="points-card">
                        <img src="<?php echo $image_file; ?>" alt="<?php echo esc($guide['activity']); ?>">
                        <h5><?php echo esc($guide['activity']); ?></h5>
                        <button>Earn <?php echo esc($guide['points']); ?> Points</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-400">No activities available yet. Check back soon!</p>
            <?php endif; ?>
        </div>
    </div>

<?php echo module_view('Web', 'includes/scripts'); ?>