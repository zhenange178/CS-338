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

    public function listProducts($sql, $isSample) {
        echo $this->getStyles($sql);
    
        $result = $this->conn->query($sql);
        $selectedColumns = ['productID', 'productName', 'sellingAttribute', 'stock', 'mainCategory'];
    
        if ($result->num_rows > 0) {
            echo $result->num_rows . " results found.";
            echo "<table border='1'>";
            
            // Display table headers
            echo "<tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Main Category</th>
                <th>Availability</th>
                <th>Attribute</th>
                </tr>";
    
            // Fetch rows and create table data cells
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                $productId = $row['productID'];
                $productName = $row['productName'];
    
                // Display product ID and product name as clickable links
                if ($isSample) {
                    echo "<td><a href='../product.php?id={$productId}&data=sample'>{$productId}</a></td>";
                } else {
                    echo "<td><a href='../product.php?id={$productId}'>{$productId}</a></td>";
                }
                echo "<td>{$productName}</td>";
    
                // Query to get price information
                $priceQuery = "SELECT priceType, price FROM productPrices WHERE productID = '{$productId}'";
                $priceResult = $this->conn->query($priceQuery);
    
                $whitePrice = null;
                $redPrice = null;
    
                if ($priceResult->num_rows > 0) {
                    while ($priceRow = $priceResult->fetch_assoc()) {
                        if ($priceRow['priceType'] == 'whitePrice') {
                            $whitePrice = $priceRow['price'];
                        } elseif ($priceRow['priceType'] == 'redPrice') {
                            $redPrice = $priceRow['price'];
                        }
                    }
                }
    
                // Display price
                echo "<td>";
                if ($redPrice !== null) {
                    echo "<span style='text-decoration: line-through;'>\${$whitePrice}</span><br/>\${$redPrice}";
                } else {
                    echo "\${$whitePrice}";
                }
                echo "</td>";
    
                // Display main category
                $mainCategory = $row['mainCategory'] ? $row['mainCategory'] : 'N/A';
                echo "<td>{$mainCategory}</td>";
    
                // Display availability (stock)
                echo "<td>{$row['stock']}</td>";
    
                // Display attribute
                echo "<td>{$row['sellingAttribute']}</td>";
    
                echo "</tr>";
            }
    
            echo "</table>";
        } else {
            echo "0 results";
        }
    }

    

}
?>
