<?php include 'includes/header.php'; ?>

<div>
    <h2>Initialization</h2>
    <p>Product data is retrieved from H&M's website using an API, then stored locally. Once the data is fetched and saved, the database tables regarding product information should be updated. You can one-click initialize everything, or choose select areas to reset below.</p>
</div>
<div>
    Click below to initialize and reset everything. <b>This will reset all data and databases.</b><br/><br/>
    <a href="/" class="initbutton">Initialize Everything (work in progress)</a> <!--update link-->
    <br/><br/>
</div>
<div class="divider"></div> 
<div>
    <b>Other actions:</b>
    <p><a href="data.php" class="button">View Data</a> (View or update raw product data)</p>
    <p><a href="/" class="button">View Database</a> (View or reset the database) (work in progress)</p>
</div>

<!--
<div>
    Click below to view or resest the database.<br/><br/>
    <a href="/data.php" class="initbutton">View Database</a>
    <br/><br/>
</div>
-->

<?php include 'includes/footer.php'; ?>