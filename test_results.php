<?php
$csv = __DIR__ . '/data/test_results.csv';
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html><meta charset="utf-8"><title>Test Results</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<div class="container py-3">
<h3>Test Results</h3>
<?php if (!file_exists($csv)): ?>
  <div class="alert alert-warning">No results yet.</div>
<?php else: ?>
  <table class="table table-sm table-striped">
    <?php
    $f = fopen($csv, 'r'); $row=0;
    while (($data = fgetcsv($f)) !== false) {
        echo '<tr>';
        foreach ($data as $c) echo '<td>'.htmlspecialchars($c, ENT_QUOTES, 'UTF-8').'</td>';
        echo '</tr>';
        $row++;
    }
    fclose($f);
    ?>
  </table>
<?php endif; ?>
</div>
