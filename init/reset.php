<div style="margin:10px 4px; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica Neue, sans-serif;">
<div>
    <p><a href="/initialize.php">Back</a></p>
</div>
<h3>Initialize Everything</h3>

<?php include '../init/includes/generate_mock_data.php'; ?>
<?php include '../init/includes/create_database.php'; ?>
<?php include '../init/includes/populate_database_all.php'; ?>
<?php include '../init/includes/redirect.php'; ?>
</div>