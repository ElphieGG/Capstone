<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews</title>
    <link rel="stylesheet" href="book_reviews.css">
</head>
<body>
    <h1>Book Review System</h1>

    <form id="reviewForm">
        <input type="hidden" id="book_id" name="book_id" value="1"> <!-- Example Book ID -->
        <input type="hidden" id="user_id" name="user_id" value="2"> <!-- Example User ID -->

        <label for="review_text">Your Review:</label>
        <textarea id="review_text" name="review_text" required></textarea>

        <label for="rating">Rating:</label>
        <select id="rating" name="rating">
            <option value="1">1 Star</option>
            <option value="2">2 Stars</option>
            <option value="3">3 Stars</option>
            <option value="4">4 Stars</option>
            <option value="5">5 Stars</option>
        </select>

        <button type="submit">Submit Review</button>
    </form>

    <div id="reviewsContainer">
        <h3>Book Reviews</h3>
        <table>
            <thead>
                <tr>
                    <th>Reviewer</th>
                    <th>Review</th>
                    <th>Rating</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody id="reviewsTableBody">
                <!-- Reviews will be displayed dynamically -->
            </tbody>
        </table>
    </div>

    <script src="script.js"></script>
</body>
</html>
