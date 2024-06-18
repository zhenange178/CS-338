<?php
class ViewDB {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to display the table contents
    public function displayTable($sql) {
        echo "<style>
        table {
            max-width: 100%;
            max-width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            font-size: 14px;
        }
        .wrap-text {
            word-wrap: break-word; 
            word-break: break-all; 
        }
        </style>";        
        
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            // Start of the HTML table
            echo "<table>";
            $fieldinfo_array = [];
            while ($fieldinfo = $result->fetch_field()) {
                echo "<th>{$fieldinfo->name}</th>";
                $fieldinfo_array[] = $fieldinfo; // Store field info for later use
            }

            echo "</tr>";

            // Fetch rows and create table data cells
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($fieldinfo_array as $fieldinfo) {
                    $cellValue = $row[$fieldinfo->name];
                    $class = ($fieldinfo->name == 'productURL' || $fieldinfo->name == 'productImage') ? 'wrap-text' : '';
                    echo "<td class='$class'>{$cellValue}</td>";
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