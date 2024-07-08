<?php
class ViewDB {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getStyles(){
        return "<style>
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
    }

    // Function to display the table contents
    public function displayTable($sql) {
        echo $this->getStyles();
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table border='1'>";
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

    public function listProducts($sql, $isSample){
        
        echo $this->getStyles($sql);
        $result = $this->conn->query($sql);
        $selectedColumns = ['productID', 'productName', 'sellingAttribute', 'stock'];

        if ($result->num_rows > 0) {
            echo $result->num_rows . " results found.";
            echo "<table border='1'>";
            
            // Display table headers
            echo "<tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Attribute</th>
                <th>Availability</th>
                <th>Link</th>
                </tr>";

            // Fetch rows and create table data cells
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                $productId = $row['productID'];
                foreach ($selectedColumns as $column) {
                    $cellValue = $row[$column];
                    echo "<td>{$cellValue}</td>";
                }
                if ($isSample) {
                    echo "<td><a href = '..?product.php?id={productID}&data=sample'>Details</a></td>";
                }
               else {
                   echo "<td><a href='..?product.php?id={productID}'>Details</a></td>";
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
