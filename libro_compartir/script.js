document.addEventListener("DOMContentLoaded", function () {
    fetchReviews();

    document.getElementById("reviewForm").addEventListener("submit", function (event) {
        event.preventDefault();

        let formData = new FormData(this);

        fetch("submit_review.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            fetchReviews(); // Reload reviews
            document.getElementById("reviewForm").reset();
        })
        .catch(error => console.error("Error:", error));
    });
});

function fetchReviews() {
    fetch("fetch_reviews.php")
        .then(response => response.json())
        .then(data => {
            let reviewsTable = document.getElementById("reviewsTableBody");
            reviewsTable.innerHTML = "";

            data.forEach(review => {
                let row = `
                    <tr>
                        <td>${review.first_name} ${review.last_name}</td>
                        <td>${review.review_text}</td>
                        <td>${review.rating} Stars</td>
                        <td>${review.review_date}</td>
                    </tr>
                `;
                reviewsTable.innerHTML += row;
            });
        });
}
