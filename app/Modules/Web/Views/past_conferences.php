<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 21:00
 */


echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<style>
body {
    background: url('<?php echo asset_url('images/brain-Events-Archive-BG.png'); ?>') no-repeat center center fixed;
    background-size: cover;
    overflow-x: hidden;
    color: #fff;
    font-family: 'Poppins', sans-serif;
}

.past-container {
    padding: 100px 8%;
    min-height: 100vh;
    backdrop-filter: blur(3px);
}

.past-container h2 {
    text-align: center;
    font-weight: 700;
    color: #fff;
    margin-bottom: 60px;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Grid layout for conference cards */
.conference-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 50px;
    justify-items: center;
}

/* Individual conference card */
.conference-card {
    position: relative;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    transition: all 0.4s ease;
}

.conference-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 0 25px rgba(239, 177, 30, 0.4);
}

/* Thumbnail image */
.conference-thumb {
    width: 100%;
    height: 200px;
    object-fit: cover;
    filter: brightness(0.9);
    transition: filter 0.4s ease;
}

.conference-card:hover .conference-thumb {
    filter: brightness(1);
}

/* Info panel */
.conference-info {
    padding: 20px;
    text-align: center;
}

.conference-info h5 {
    font-weight: 600;
    color: #EFB11E;
    margin-bottom: 5px;
}

.conference-info p {
    color: #ddd;
    font-size: 14px;
}

.conference-info .view-btn {
    margin-top: 15px;
    background: linear-gradient(90deg, #9D0F82, #EFB11E);
    border: none;
    color: #fff;
    border-radius: 30px;
    padding: 8px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.conference-info .view-btn:hover {
    background: linear-gradient(90deg, #EFB11E, #9D0F82);
    transform: scale(1.05);
}

/* Modal for viewing highlights */
.modal-content {
    border-radius: 15px;
    background: #fff;
    color: #333;
}
.modal-header {
    border: none;
}
.modal-body {
    font-size: 15px;
}
.modal-body img {
    border-radius: 10px;
    margin-bottom: 15px;
    width: 100%;
}
</style>

<div class="past-container">
    <h2>Past Conferences</h2>

    <div class="conference-grid">
        <?php
        // Example mock data — replace with dynamic API data later
        $pastConfs = [
            [
                'title' => 'EPR Innovate 2024',
                'date' => 'Nov 2024',
                'image' => asset_url('images/past-conf1.jpg'),
                'desc' => 'Exploring innovation, leadership, and resilience in a digital world.',
            ],
            [
                'title' => 'EPR Global Women 2023',
                'date' => 'Nov 2023',
                'image' => asset_url('images/past-conf2.jpg'),
                'desc' => 'Celebrating global female changemakers redefining impact.',
            ],
            [
                'title' => 'EPR Emergence 2022',
                'date' => 'Nov 2022',
                'image' => asset_url('images/past-conf3.jpg'),
                'desc' => 'A convergence of ideas, leadership, and purpose.',
            ],
        ];

        foreach ($pastConfs as $i => $conf): ?>
            <div class="conference-card">
                <img src="<?php echo $conf['image']; ?>" alt="<?php echo $conf['title']; ?>" class="conference-thumb">
                <div class="conference-info">
                    <h5><?php echo $conf['title']; ?></h5>
                    <p><?php echo $conf['date']; ?></p>
                    <p><?php echo $conf['desc']; ?></p>
                    <button class="view-btn" data-toggle="modal" data-target="#confModal<?php echo $i; ?>">
                        View Highlights
                    </button>
                </div>
            </div>

            <!-- 🖼️ Modal -->
            <div class="modal fade" id="confModal<?php echo $i; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo $conf['title']; ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <img src="<?php echo $conf['image']; ?>" alt="<?php echo $conf['title']; ?>">
                            <p>
                                <?php echo $conf['desc']; ?><br><br>
                                Highlights from this year’s event included keynote speeches, networking sessions, and panel discussions featuring leading voices in business, innovation, and leadership.
                            </p>
                            <p>
                                <a href="#" target="_blank" style="color:#9D0F82;font-weight:600;">Watch Replay</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
