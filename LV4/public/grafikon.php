<?php
$pageTitle = 'Grafikon';
$extraCSS = ['styles.css'];
require_once __DIR__ . '/includes/header.php';
?>

<h1>Most popular movie genres</h1>
<div class="center">
    <div class="grafikon-large">
        <dl>
            <dd class="percentage percentage-10"><span class="text"> Action 10%</span></dd>
            <dd class="percentage percentage-20"><span class="text"> Adventure 20%</span></dd>
            <dd class="percentage percentage-24"><span class="text"> Animated 24%</span></dd>
            <dd class="percentage percentage-12"><span class="text"> Comedy 12%</span></dd>
            <dd class="percentage percentage-9"><span class="text"> Drama 9%</span></dd>
            <dd class="percentage percentage-10"><span class="text"> Horror 10%</span></dd>
            <dd class="percentage percentage-15"><span class="text"> Sci Fi 15%</span></dd>
        </dl>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
