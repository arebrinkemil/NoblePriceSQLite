<?php
$databaseFile = "nobel.db";
$db = new SQLite3($databaseFile);

function sanitizeInput($data)
{
    return htmlspecialchars(trim($data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = sanitizeInput($_POST["first_name"]);
    $lastName = sanitizeInput($_POST["last_name"]);
    $birthDate = sanitizeInput($_POST["birth_date"]);
    $country = sanitizeInput($_POST["country"]);
    $year = sanitizeInput($_POST["year"]);
    $amount = sanitizeInput($_POST["amount"]);
    $reason = sanitizeInput($_POST["reason"]);
    $category = sanitizeInput($_POST["category"]);
    $organization = sanitizeInput($_POST["organization"]);

    $insertWinnerQuery = "INSERT INTO Winners (first_name, last_name, birth_date, country)
                          VALUES ('$firstName', '$lastName', '$birthDate', '$country')";
    $db->exec($insertWinnerQuery);

    $newWinnerId = $db->lastInsertRowID();

    $insertPrizeQuery = "INSERT INTO Prizes (year, amount, reason, category_id)
                         VALUES ('$year', '$amount', '$reason', '$category')";
    $db->exec($insertPrizeQuery);

    $newPrizeId = $db->lastInsertRowID();

    $insertWinnerPrizeQuery = "INSERT INTO WinnerPrizes (winner_id, prize_id)
                               VALUES ('$newWinnerId', '$newPrizeId')";
    $db->exec($insertWinnerPrizeQuery);

    $insertPrizeOrganizationQuery = "INSERT INTO PrizeOrganization (prize_id, organization_id)
                                     VALUES ('$newPrizeId', '$organization')";
    $db->exec($insertPrizeOrganizationQuery);

    header("Location: index.php");

    exit();
}

$query = "SELECT Winners.first_name, Winners.last_name, Winners.birth_date, Winners.country,
                         Prizes.year, Prizes.amount, Prizes.reason,
                         Categories.category_name,
                         Organizations.organization_name
                  FROM WinnerPrizes
                  JOIN Winners ON WinnerPrizes.winner_id = Winners.id
                  JOIN Prizes ON WinnerPrizes.prize_id = Prizes.id
                  JOIN Categories ON Prizes.category_id = Categories.id
                  JOIN PrizeOrganization ON Prizes.id = PrizeOrganization.prize_id
                  JOIN Organizations ON PrizeOrganization.organization_id = Organizations.id";

$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nobel Prize Information</title>
</head>

<body>
    <h1>Nobel Prize Information</h1>

    <form method="post" action="">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br>

        <label for="birth_date">Birth Date:</label>
        <input type="date" id="birth_date" name="birth_date" required><br>

        <label for="country">Country:</label>
        <input type="text" id="country" name="country" required><br>

        <label for="year">Year:</label>
        <input type="number" id="year" name="year" required><br>

        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required><br>

        <label for="reason">Reason:</label>
        <input type="text" id="reason" name="reason" required><br>

        <label for="category">Category:</label>
        <input type="number" id="category" name="category" required><br>

        <label for="organization">Organization:</label>
        <input type="number" id="organization" name="organization" required><br>

        <input type="submit" value="Add Winner">
    </form>

    <hr>

    <table border="1" style="
    width: 100vw;
    height: 80vh;">
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Birth Date</th>
            <th>Country</th>
            <th>Year</th>
            <th>Amount</th>
            <th>Reason</th>
            <th>Category</th>
            <th>Organization</th>
        </tr>
        <?php


        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            echo "<tr>
                    <td>{$row['first_name']}</td>
                    <td>{$row['last_name']}</td>
                    <td>{$row['birth_date']}</td>
                    <td>{$row['country']}</td>
                    <td>{$row['year']}</td>
                    <td>{$row['amount']}</td>
                    <td>{$row['reason']}</td>
                    <td>{$row['category_name']}</td>
                    <td>{$row['organization_name']}</td>
                </tr>";
        }

        $db->close();
        ?>
    </table>
</body>

</html>