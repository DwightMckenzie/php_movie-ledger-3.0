<?php
declare(strict_types=1);

/** Page footer. Closes the document and loads scripts. */

$base = app_base_url();
$page_scripts = $page_scripts ?? [];
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="<?= e($base) ?>assets/js/app.js"></script>
<?php foreach ($page_scripts as $script): ?>
	<script src="<?= e($base . $script) ?>"></script>
<?php endforeach; ?>
</body>
</html>
