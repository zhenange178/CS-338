<?php
class ViewDB {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to display the table contents
    public function displayTable($sql) {
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            // Start of the HTML table
            echo "<table border='1'>
                    <tr>";

            while ($fieldinfo = $result->fetch_field()) {
                echo "<th>{$fieldinfo->name}</th>";
            }

            echo "</tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>{$cell}</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "0 results";
        }
    }
}
?>