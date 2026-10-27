<?php
include 'db_connect.php';

if (isset($_GET['offer_id'])) {
    $offer_id = intval($_GET['offer_id']);

    // Get offer info
    $offer = $conn->query("SELECT * FROM Offer WHERE offer_id = $offer_id")->fetch_assoc();
    $applies_to = $offer['applies_to'];

    // Revert affected prices
    if ($applies_to === 'service' || $applies_to === 'all') {
        $conn->query("UPDATE Service SET price = original_price WHERE original_price IS NOT NULL");
    }

    if ($applies_to === 'product' || $applies_to === 'all') {
        $conn->query("UPDATE Product SET price = original_price WHERE original_price IS NOT NULL");
    }


    // Disable the offer by setting discount to NULL
    $conn->query("UPDATE Offer SET discount_percentage = NULL WHERE offer_id = $offer_id");

    header("Location: Offers Management.php?reverted=1");
    exit();
} else {
    echo "Offer ID not provided.";
}
?>
