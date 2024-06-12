<?php
class ViewDB {

    private $conn;
    private $tableName;

    // Constructor to initialize the connection and table name
    public function __construct($conn, $tableName) {
        $this->conn = $conn;
        $this->tableName = $tableName;
    }

    // Function to display the table contents
    public function displayTable() {
        // Query to fetch all rows from the table
        $sql = "SELECT * FROM " . $this->tableName;
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            // Start of the HTML table
            echo "<table border='1'>
                    <tr>";

            // Fetch the field names
            while ($fieldinfo = $result->fetch_field()) {
                echo "<th>{$fieldinfo->name}</th>";
            }

            echo "</tr>";

            // Fetch the rows and display them
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>{$cell}</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "0 results for " . $this->tableName;
        }
    }
}
?>