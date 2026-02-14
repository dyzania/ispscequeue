<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Analyzer | E-Queue Insights</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="box">
    <h2>Client Feedback</h2>

    <form method="POST">
        <textarea name="text" placeholder="Type or paste your text here..." required></textarea>
        <button type="submit">Analyze Sentiment</button>
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $text = $_POST["text"];

    $data = json_encode(["text" => $text]);

    $ch = curl_init("http://127.0.0.1:8000/analyze");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        echo "<div class='result error'>Error: Unable to connect to API. " . htmlspecialchars($error) . "</div>";
    } else {
        curl_close($ch);
        $result = json_decode($response, true);
        if ($result && isset($result["label"]) && isset($result["score"])) {
            $label_map = [
                "Very Negative" => "Very Negative",
                "Negative" => "Negative",
                "Neutral" => "Neutral",
                "Positive" => "Positive",
                "Very Positive" => "Very Positive"
            ];
            $sentiment = isset($label_map[$result["label"]]) ? $label_map[$result["label"]] : $result["label"];

            // Determine class for styling
            $class_map = [
                "Very Negative" => "negative",
                "Negative" => "negative",
                "Neutral" => "neutral",
                "Positive" => "positive",
                "Very Positive" => "positive"
            ];
            $result_class = isset($class_map[$sentiment]) ? $class_map[$sentiment] : "neutral";

            // Emoji map
            $emoji_map = [
                "Very Negative" => "😞",
                "Negative" => "😞",
                "Neutral" => "😐",
                "Positive" => "😊",
                "Very Positive" => "😊"
            ];
            $emoji = isset($emoji_map[$sentiment]) ? $emoji_map[$sentiment] : "🤔";
            
            echo "<div class='result " . $result_class . "'>";
            echo "<div class='sentiment-label'>" . $emoji . " " . htmlspecialchars($sentiment) . "</div>";
            echo "<div class='confidence'>Confidence: " . htmlspecialchars($result["score"]) . "</div>";
            echo "</div>";
        } else {
            echo "<div class='result error'>Error: Invalid response from API. Response: " . htmlspecialchars($response) . "</div>";
        }
    }
}
?>

</div>

</body>
</html>