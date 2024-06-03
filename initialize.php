<?php include 'includes/header.php'; ?>

    <div>
        <h2>Initialization</h2>
        <p>Product data is retrieved from H&M's website using an API, then stored locally. Once the data is fetched and saved, the database tables regarding product information will be reset. You can update this information whenever you like to keep the information up-to-date.</p>
    </div>
    <div class="rowcontainer">
        <div style="width: 40%; margin-right: auto">
            <div>    
            <h3>Data Retrival</h3>
                <p>If you would like to view current data or fetch the newest data, click below. Note that you will not see the changes before initializing the database.</p>
            </div>
            <div class="centerbutton">
                <a href="/data.php">Data</a>
            </div>
        </div>

        <div style="width: 40%; margin-left: auto">
            <h3>Database Initialization</h3>
            <p>If you would like to reset the database, click here. <b>The local database and all previously changed data will be reset according to the current fetched raw data upon database initialization.</b> </p>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>