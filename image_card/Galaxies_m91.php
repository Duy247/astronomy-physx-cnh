<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>


<?php
// Load site data from JSON
$data = json_decode(file_get_contents(__DIR__ . '/card_data/site_data_Galaxies_m91.json'), true);
$title = $data['title'] ?? '';
$object = $data['object'] ?? '';
$image = $data['image'] ?? [];
$details = $data['details'] ?? [];
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/image_card.php'; ?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
